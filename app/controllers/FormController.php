<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Container;
use App\Services\Csrf;
use App\Services\Request;
use App\Services\Response;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Verarbeitet Kontakt-, Schaden- und Bewerbungsformulare.
 * Empfänger, Absender und Betreffmuster sind fest; nur validierte Werte gelangen in die Mail.
 */
final class FormController extends BaseController
{
    /** Zulässige Themen je Formular (Allowlist). */
    private const TOPICS = [
        'contact' => ['Allgemeine Anfrage', 'Heizung', 'Wärmepumpe', 'Heizungsmodernisierung', 'Heizungswartung', 'Heizungsreparatur',
            'Fußbodenheizung', 'Hydraulischer Abgleich', 'Heizkörper', 'Bad und Sanitär', 'Badsanierung', 'Barrierearmes Bad',
            'Trinkwasserinstallation', 'Warmwasserbereitung', 'Rohrreparatur', 'Wasseraufbereitung', 'Sanierung', 'Komplettsanierung',
            'Wohnungssanierung', 'Altbausanierung', 'Hausverwaltung', 'Gewerbekunden', 'Förderung', 'Fördercheck', 'Wasserschaden'],
        'damage' => ['Leckortung', 'Rohrbruch', 'Feuchtigkeit', 'Technische Trocknung', 'Wiederherstellung', 'Unklare Ursache'],
        'application' => [],
    ];

    private const DAMAGE_TYPES = ['Rohrbruch / Leitungswasser', 'Feuchte Wand oder Decke', 'Wasser im Boden / Estrich', 'Undichte Stelle im Bad',
        'Nasser Keller', 'Schaden nach Unwetter', 'Unklare Ursache'];

    private const PLZ_OK = '/^\d{5}$/';

    public function contact(): void
    {
        $this->handle('contact');
    }

    public function damage(): void
    {
        $this->handle('damage');
    }

    public function application(): void
    {
        $this->handle('application');
    }

    private function handle(string $type): void
    {
        $sec = Container::config('security');
        $errors = [];
        $data = [];

        // CSRF
        if (!Container::csrf()->validate(Request::post('csrf_token', 200))) {
            $this->fail($type, ['Die Sitzung ist abgelaufen. Bitte senden Sie das Formular erneut.'], $this->collect($type), 'csrf');
            return;
        }
        // Honeypot und Mindestzeit (datensparsame Spamabwehr)
        if (Request::post($sec['spam']['honeypotField'], 100) !== '') {
            // Wie Erfolg behandeln, aber nichts senden
            $this->success($type, false);
            return;
        }
        $started = (int)Request::post('form_started', 20);
        if ($started > 0 && time() - $started < (int)$sec['spam']['minSecondsToSubmit']) {
            $errors[] = 'Das Formular wurde sehr schnell abgeschickt. Bitte prüfen Sie Ihre Angaben und senden Sie erneut.';
        }
        // Rate Limit
        if (!Container::rateLimiter()->allow(Request::ip(), 'form')) {
            $this->fail($type, ['Es wurden zu viele Anfragen in kurzer Zeit gesendet. Bitte versuchen Sie es in einigen Minuten erneut oder rufen Sie uns an.'], $this->collect($type), 'ratelimit');
            return;
        }

        $data = $this->collect($type);

        // Pflichtfelder
        if (mb_strlen($data['name']) < 2) {
            $errors['name'] = 'Bitte geben Sie Ihren Namen an.';
        }
        $emailOk = $data['email'] !== '' && PHPMailer::validateAddress($data['email']);
        $phoneOk = $data['phone'] !== '' && preg_match('/^[+0-9 ()\/.-]{6,25}$/', $data['phone']);
        if ($data['email'] !== '' && !$emailOk) {
            $errors['email'] = 'Die E-Mail-Adresse ist nicht gültig.';
        }
        if ($data['phone'] !== '' && !$phoneOk) {
            $errors['phone'] = 'Die Telefonnummer ist nicht gültig.';
        }
        if (!$emailOk && !$phoneOk) {
            $errors['contact'] = 'Bitte geben Sie mindestens eine E-Mail-Adresse oder eine Telefonnummer an.';
        }
        if ($data['preferred'] === 'E-Mail' && !$emailOk) {
            $errors['preferred'] = 'Für eine Rückmeldung per E-Mail benötigen wir Ihre E-Mail-Adresse.';
        }
        if ($data['preferred'] === 'Telefon' && !$phoneOk) {
            $errors['preferred'] = 'Für einen Rückruf benötigen wir Ihre Telefonnummer.';
        }
        if ($data['plz'] !== '' && !preg_match(self::PLZ_OK, $data['plz'])) {
            $errors['plz'] = 'Die Postleitzahl muss aus fünf Ziffern bestehen.';
        }
        if (!$data['privacy']) {
            $errors['privacy'] = 'Bitte bestätigen Sie den Hinweis zur Datenverarbeitung.';
        }

        if ($type === 'contact') {
            if (!in_array($data['topic'], self::TOPICS['contact'], true)) {
                $errors['topic'] = 'Bitte wählen Sie ein Anliegen.';
            }
            if (mb_strlen($data['message']) < 10) {
                $errors['message'] = 'Bitte beschreiben Sie Ihr Anliegen kurz (mindestens 10 Zeichen).';
            }
        } elseif ($type === 'damage') {
            if (!in_array($data['damageType'], self::DAMAGE_TYPES, true)) {
                $errors['damageType'] = 'Bitte wählen Sie die Schadenart.';
            }
            if (!in_array($data['topic'], self::TOPICS['damage'], true)) {
                $data['topic'] = 'Leckortung';
            }
            if (mb_strlen($data['objectRef']) < 3) {
                $errors['objectRef'] = 'Bitte geben Sie den Objektbezug an (Adresse oder Bezeichnung).';
            }
            if (mb_strlen($data['message']) < 10) {
                $errors['message'] = 'Bitte beschreiben Sie den Schaden kurz.';
            }
        } else {
            $job = Container::content()->job($data['jobSlug']);
            if ($job === null) {
                $errors['jobSlug'] = 'Die gewählte Stelle ist nicht bekannt.';
            } else {
                $data['topic'] = $job['title'];
            }
        }

        // Uploads
        $uploads = Container::uploads();
        $files = ['files' => [], 'errors' => []];
        if ($uploads->enabled()) {
            $files = $uploads->process('attachments');
            foreach ($files['errors'] as $fe) {
                $errors[] = $fe;
            }
        }

        if ($errors) {
            $uploads->discard($files['files']);
            $this->fail($type, $errors, $data, 'validation');
            return;
        }

        // Mail aufbauen
        $subject = match ($type) {
            'contact' => 'Website-Anfrage | ' . $data['topic'],
            'damage' => 'Schadenmeldung | ' . $data['topic'],
            default => 'Bewerbung | ' . $data['topic'],
        };
        $body = $this->buildBody($type, $data, $files['files']);
        $mail = Container::mail();
        $result = $mail->sendInternal($subject, $body, $emailOk ? $data['email'] : null, $data['name'], $files['files']);
        $uploads->discard($files['files']);

        if (!$result['ok']) {
            $msg = $result['error'] === 'not_configured'
                ? 'Der Versand über die Website ist derzeit nicht eingerichtet. Ihre Eingaben bleiben unten erhalten. Bitte rufen Sie uns an oder schreiben Sie uns direkt per E-Mail.'
                : 'Die Nachricht konnte nicht übermittelt werden. Ihre Eingaben bleiben unten erhalten. Bitte versuchen Sie es erneut, rufen Sie uns an oder schreiben Sie uns direkt per E-Mail.';
            $this->fail($type, [$msg], $data, 'send', 503);
            return;
        }
        if ($emailOk) {
            $label = match ($type) { 'contact' => 'Anfrage', 'damage' => 'Schadenmeldung', default => 'Bewerbung' };
            $mail->sendConfirmation($data['email'], $data['name'], $label);
        }
        $this->success($type, true);
    }

    private function collect(string $type): array
    {
        return [
            'name' => Request::post('name', 120),
            'email' => mb_strtolower(Request::post('email', 200)),
            'phone' => Request::post('phone', 40),
            'preferred' => in_array(Request::post('preferred', 20), ['E-Mail', 'Telefon', 'Egal'], true) ? Request::post('preferred', 20) : 'Egal',
            'company' => Request::post('company', 120),
            'plz' => Request::post('plz', 10),
            'topic' => Request::post('topic', 80),
            'message' => Request::post('message', 5000),
            'objectRef' => Request::post('object_ref', 200),
            'damageType' => Request::post('damage_type', 80),
            'noticedAt' => Request::post('noticed_at', 40),
            'appointment' => Request::post('appointment', 200),
            'jobSlug' => Request::post('job_slug', 80),
            'experience' => Request::post('experience', 1000),
            'startDate' => Request::post('start_date', 60),
            'privacy' => Request::post('privacy', 5) === '1',
            'returnTo' => Request::post('return_to', 200),
        ];
    }

    private function buildBody(string $type, array $d, array $files): string
    {
        $label = match ($type) { 'contact' => 'Kontaktanfrage', 'damage' => 'Schadenmeldung', default => 'Bewerbung' };
        $lines = [
            "Formulartyp: {$label}",
            "Thema: {$d['topic']}",
            "Eingang: " . date('d.m.Y H:i'),
            '',
            "Name: {$d['name']}",
            'E-Mail: ' . ($d['email'] ?: 'nicht angegeben'),
            'Telefon: ' . ($d['phone'] ?: 'nicht angegeben'),
            "Bevorzugte Rückmeldung: {$d['preferred']}",
        ];
        if ($d['company'] !== '') {
            $lines[] = "Unternehmen: {$d['company']}";
        }
        if ($d['plz'] !== '') {
            $lines[] = "Objekt-PLZ: {$d['plz']}";
        }
        if ($type === 'damage') {
            $lines[] = "Objektbezug: {$d['objectRef']}";
            $lines[] = "Schadenart: {$d['damageType']}";
            $lines[] = 'Festgestellt am: ' . ($d['noticedAt'] ?: 'nicht angegeben');
            $lines[] = 'Terminpräferenz (unverbindlich): ' . ($d['appointment'] ?: 'keine');
        }
        if ($type === 'application') {
            $lines[] = 'Erfahrung / Qualifikation: ' . ($d['experience'] ?: 'nicht angegeben');
            $lines[] = 'Möglicher Beginn: ' . ($d['startDate'] ?: 'nicht angegeben');
        }
        if ($type === 'contact' && $d['appointment'] !== '') {
            $lines[] = "Terminwunsch (unverbindlich): {$d['appointment']}";
        }
        $lines[] = '';
        $lines[] = 'Nachricht:';
        $lines[] = $d['message'] !== '' ? $d['message'] : '(keine)';
        $lines[] = '';
        $lines[] = 'Anhänge: ' . ($files ? implode(', ', array_map(fn($f) => $f['name'] . ' (' . round($f['size'] / 1024) . ' KB)', $files)) : 'keine');
        $lines[] = '';
        $lines[] = 'Diese E-Mail wurde über das Formular auf hsm-tec.de erzeugt. Antwort an den Absender über „Antworten“ (Reply-To).';
        return implode("\n", $lines);
    }

    private function fail(string $type, array $errors, array $data, string $reason, int $status = 422): void
    {
        if (Request::wantsJson()) {
            Response::json(['ok' => false, 'reason' => $reason, 'errors' => array_values($errors), 'fieldErrors' => array_filter($errors, 'is_string', ARRAY_FILTER_USE_KEY)], $status);
            return;
        }
        $page = $this->resultPage($type, 'Bitte prüfen Sie Ihre Eingaben');
        $this->renderPage($page, 'form-result', ['type' => $type, 'ok' => false, 'errors' => $errors, 'data' => $data, 'reason' => $reason], $status, true);
    }

    private function success(string $type, bool $sent): void
    {
        if (Request::wantsJson()) {
            Response::json(['ok' => true]);
            return;
        }
        $page = $this->resultPage($type, 'Vielen Dank für Ihre Nachricht');
        $this->renderPage($page, 'form-result', ['type' => $type, 'ok' => true, 'errors' => [], 'data' => [], 'reason' => null], 200, true);
    }

    private function resultPage(string $type, string $title): array
    {
        $parent = match ($type) { 'damage' => '/wasserschaden/', 'application' => '/karriere/', default => '/kontakt/' };
        return ['path' => $parent . 'ergebnis', 'parent' => $parent, 'title' => $title, 'metaTitle' => $title . ' | HSM Tec GmbH', 'metaDescription' => '', 'template' => 'form-result', 'noindex' => true, 'excludeFromSitemap' => true, 'form' => $type];
    }
}
