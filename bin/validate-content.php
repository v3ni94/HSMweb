#!/usr/bin/env php
<?php
declare(strict_types=1);
/** Validiert alle Inhaltsdateien und das Förderregelwerk. Exit 0 = ok. */
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Services\Container;

$errors = [];
try {
    $c = Container::content();
    $pages = $c->pages();
    echo count($pages) . " Seiten gelesen\n";
    foreach ($pages as $path => $p) {
        if (!preg_match('~^/([a-z0-9-]+/)*$~', $path)) $errors[] = "Ungültiger Pfad: $path";
        foreach (['metaTitle', 'metaDescription'] as $k) {
            if (empty($p[$k])) $errors[] = "$path: $k fehlt";
        }
        if (isset($p['metaDescription']) && mb_strlen($p['metaDescription']) > 175) $errors[] = "$path: metaDescription länger als 175 Zeichen (" . mb_strlen($p['metaDescription']) . ")";
        if (isset($p['metaTitle']) && mb_strlen($p['metaTitle']) > 70) $errors[] = "$path: metaTitle länger als 70 Zeichen";
        if (!empty($p['parent']) && !isset($pages[$p['parent']])) $errors[] = "$path: parent {$p['parent']} existiert nicht";
        foreach (array_merge($p['hubChildren'] ?? [], $p['related'] ?? [], [$p['funding'] ?? null]) as $l) {
            if ($l && !isset($pages[$l]) && !preg_match('~^/ratgeber/~', $l)) $errors[] = "$path: Link $l zeigt auf keine Seite";
        }
        if (!is_file(HSM_ROOT . '/app/views/pages/' . $p['template'] . '.php')) $errors[] = "$path: Template {$p['template']} fehlt";
    }
    $company = $c->company();
    // Redaktionsregel Abschnitt 2: nur die zwei Geschäftsführer und zwei Holdings
    if (count($company['management']) !== 2) $errors[] = 'company.json: genau zwei Geschäftsführer erwartet';
    if (count($company['holdings']) !== 2) $errors[] = 'company.json: genau zwei Holdings erwartet';
    $jobs = $c->jobs();
    $slugs = [];
    foreach ($jobs as $j) {
        foreach (['id', 'slug', 'title', 'status', 'datePosted', 'summary', 'employmentTypeSchema'] as $k) if (!isset($j[$k])) $errors[] = "jobs.json {$j['slug']}: $k fehlt";
        if (isset($slugs[$j['slug']])) $errors[] = 'jobs.json: doppelter slug ' . $j['slug'];
        $slugs[$j['slug']] = 1;
        if (isset($pages['/karriere/' . $j['slug'] . '/'])) $errors[] = 'jobs.json: slug kollidiert mit Seite ' . $j['slug'];
    }
    foreach ($c->guides() as $g) {
        foreach (['slug', 'title', 'metaDescription', 'published', 'updated', 'intro', 'sections'] as $k) if (!isset($g[$k])) $errors[] = "guides.json {$g['slug']}: $k fehlt";
    }
    $red = $c->redirects();
    foreach ($red['redirects'] as $r) {
        if (!isset($pages[$r['to']]) && !preg_match('~^/(karriere|ratgeber)/[a-z0-9-]+/$~', $r['to'])) $errors[] = 'redirects.json: Ziel existiert nicht: ' . $r['to'];
        if (isset($pages[$r['from']])) $errors[] = 'redirects.json: Quelle ist eine aktive Seite: ' . $r['from'];
    }
    $f = Container::funding();
    $f->all();
    echo count($f->programs()) . " Förderprogramme validiert\n";
    $today = date('Y-m-d');
    foreach ($f->overdue($today) as $p) {
        echo "WARNUNG: Quellenprüfung überfällig: {$p['id']} (fällig {$p['reviewDueAt']})\n";
    }
    // Verbotene Inhalte: keine Vorgängernamen o. ä. dürfen hier eingetragen werden; Liste in docs/content-maintenance.md pflegen
    $forbidden = file(HSM_ROOT . '/docs/forbidden-terms.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $forbidden = array_filter($forbidden, fn($l) => !str_starts_with($l, '#'));
    $haystack = '';
    foreach (glob(HSM_ROOT . '/content/*.json') as $file) $haystack .= file_get_contents($file);
    foreach (glob(HSM_ROOT . '/app/views/**/*.php') as $file) $haystack .= file_get_contents($file);
    foreach ($forbidden as $term) {
        if ($term !== '' && stripos($haystack, $term) !== false) $errors[] = "Gesperrter Begriff gefunden: $term";
    }
} catch (\Throwable $e) {
    $errors[] = $e->getMessage();
}
if ($errors) {
    echo "FEHLER:\n- " . implode("\n- ", $errors) . "\n";
    exit(1);
}
echo "Inhalte gültig.\n";
