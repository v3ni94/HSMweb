<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Serverseitiger Versand über PHPMailer mit authentifiziertem SMTP.
 * Empfänger und Absender stammen ausschließlich aus config/mail.local.php.
 */
final class MailService
{
    public function __construct(private readonly ?array $cfg, private readonly string $logDir)
    {
    }

    public function isConfigured(): bool
    {
        return is_array($this->cfg)
            && ($this->cfg['enabled'] ?? false) === true
            && !empty($this->cfg['host'])
            && !empty($this->cfg['username'])
            && !empty($this->cfg['fromAddress'])
            && !empty($this->cfg['toAddress']);
    }

    /**
     * @param array<int, array{path:string,name:string,mime:string}> $attachments
     * @return array{ok: bool, error: ?string}
     */
    public function sendInternal(string $subject, string $body, ?string $replyTo, ?string $replyToName, array $attachments = []): array
    {
        if (!$this->isConfigured()) {
            $this->log('mail_not_configured');
            return ['ok' => false, 'error' => 'not_configured'];
        }
        try {
            $mail = $this->mailer();
            $mail->addAddress($this->cfg['toAddress']);
            if ($replyTo !== null && PHPMailer::validateAddress($replyTo)) {
                $mail->addReplyTo($replyTo, $this->cleanHeader($replyToName ?? ''));
            }
            $mail->Subject = $this->cleanHeader($subject);
            $mail->Body = $body;
            foreach ($attachments as $a) {
                $mail->addAttachment($a['path'], $a['name'], PHPMailer::ENCODING_BASE64, $a['mime']);
            }
            $mail->send();
            $this->log('mail_sent ' . preg_replace('/[^\w| -]/u', '', $subject));
            return ['ok' => true, 'error' => null];
        } catch (MailException $e) {
            $this->log('mail_failed ' . $e->getMessage());
            return ['ok' => false, 'error' => 'send_failed'];
        }
    }

    public function sendConfirmation(string $to, string $name, string $formLabel): bool
    {
        if (!$this->isConfigured() || !($this->cfg['sendConfirmationToVisitor'] ?? false) || !PHPMailer::validateAddress($to)) {
            return false;
        }
        try {
            $mail = $this->mailer();
            $mail->addAddress($to, $this->cleanHeader($name));
            $mail->Subject = 'Eingangsbestätigung | HSM Tec GmbH';
            $company = Container::content()->company();
            $mail->Body = "Guten Tag,\n\n"
                . "wir haben Ihre {$formLabel} über unsere Website erhalten. Wir melden uns bei Ihnen.\n\n"
                . "Diese Nachricht wurde automatisch erzeugt. Bitte antworten Sie bei Rückfragen an {$company['email']} oder rufen Sie uns an: {$company['phoneDisplay']}.\n\n"
                . "HSM Tec GmbH\n{$company['address']['street']}\n{$company['address']['postalCode']} {$company['address']['city']}\n";
            $mail->send();
            return true;
        } catch (MailException $e) {
            $this->log('confirmation_failed ' . $e->getMessage());
            return false;
        }
    }

    private function mailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->Host = $this->cfg['host'];
        $mail->Port = (int)($this->cfg['port'] ?? 587);
        $mail->SMTPAuth = true;
        $mail->Username = $this->cfg['username'];
        $mail->Password = $this->cfg['password'];
        $mail->SMTPSecure = ($this->cfg['encryption'] ?? 'tls') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout = (int)($this->cfg['timeout'] ?? 15);
        $mail->SMTPDebug = (int)($this->cfg['debugLevel'] ?? 0);
        if ($mail->SMTPDebug > 0) {
            $logFile = $this->logDir . '/mail-debug.log';
            $mail->Debugoutput = static function (string $str) use ($logFile): void {
                file_put_contents($logFile, date('c') . ' ' . $str . "\n", FILE_APPEND | LOCK_EX);
            };
        }
        $mail->setFrom($this->cfg['fromAddress'], $this->cleanHeader($this->cfg['fromName'] ?? 'HSM Tec GmbH'));
        $mail->isHTML(false);
        return $mail;
    }

    private function cleanHeader(string $v): string
    {
        return trim(preg_replace('/[\r\n\t]+/', ' ', $v) ?? '');
    }

    /** Nur technische Metadaten, keine Inhalte. */
    private function log(string $event): void
    {
        $line = date('c') . ' ' . $event . "\n";
        @file_put_contents($this->logDir . '/mail.log', $line, FILE_APPEND | LOCK_EX);
    }
}
