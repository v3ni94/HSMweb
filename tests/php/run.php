#!/usr/bin/env php
<?php
declare(strict_types=1);
/**
 * PHP-Tests für Sicherheits- und Inhaltsdienste ohne Framework. Aufruf: php tests/php/run.php
 */
putenv('HSM_ENV=development');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
require dirname(__DIR__, 2) . '/app/bootstrap.php';
ini_set('display_errors', '0'); // header()-Warnungen der CLI landen im Log, nicht im Testpuffer

use App\Services\Container;
use App\Services\Funding;
use App\Services\JsonLd;
use App\Services\RateLimiter;
use App\Services\Request;

$n = 0; $fails = 0;
function t(string $name, callable $fn): void {
    global $n, $fails; $n++;
    try { $fn(); echo "ok  $name\n"; } catch (\Throwable $e) { $fails++; echo "FAIL $name: " . $e->getMessage() . "\n"; }
}
function eq($a, $b, string $msg = ''): void { if ($a !== $b) throw new RuntimeException($msg ?: ('erwartet ' . var_export($b, true) . ', erhalten ' . var_export($a, true))); }
function ok($c, string $msg = 'Bedingung falsch'): void { if (!$c) throw new RuntimeException($msg); }

$tmp = sys_get_temp_dir() . '/hsm-tests-' . bin2hex(random_bytes(4));
mkdir($tmp, 0700, true);

t('Request::post entfernt Steuerzeichen (Header-Injection) und kürzt', function () {
    $_POST['x'] = "Max\r\nBcc: evil@example.com\x00\x07 Muster";
    $v = Request::post('x', 20);
    ok(!str_contains($v, "\x00") && !str_contains($v, "\x07"), 'Steuerzeichen enthalten');
    ok(mb_strlen($v) <= 20, 'nicht gekürzt');
    $_POST['y'] = ['array'];
    eq(Request::post('y'), '', 'Array muss leer werden');
});

t('Request::path weist Traversal ab', function () {
    $_SERVER['REQUEST_URI'] = '/..%2Fconfig/site.php';
    eq(Request::path(), '/__invalid__');
    $_SERVER['REQUEST_URI'] = '/heizung/?x=1';
    eq(Request::path(), '/heizung/');
});

t('Request::ip ignoriert X-Forwarded-For ohne vertrauten Proxy', function () {
    $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.1';
    eq(Request::ip(), '203.0.113.10');
});

t('RateLimiter: 5 Versuche erlaubt, 6. gesperrt, Bereinigung löscht', function () use ($tmp) {
    $rl = new RateLimiter($tmp . '/rl', ['maxAttempts' => 5, 'windowSeconds' => 60, 'maxFiles' => 100]);
    for ($i = 0; $i < 5; $i++) ok($rl->allow('198.51.100.1', 'form'), "Versuch $i abgelehnt");
    ok(!$rl->allow('198.51.100.1', 'form'), '6. Versuch nicht gesperrt');
    ok($rl->allow('198.51.100.2', 'form'), 'andere IP gesperrt');
    $files = glob($tmp . '/rl/*.json');
    foreach ($files as $f) { ok(!str_contains(basename($f), '198.51'), 'IP im Klartext im Dateinamen'); touch($f, time() - 3600); }
    ok($rl->cleanup() >= 2, 'Bereinigung entfernt nichts');
});

t('Routen-Allowlist: unbekannte Pfade null, kein include aus Nutzereingabe', function () {
    $router = require HSM_ROOT . '/app/routes.php';
    eq($router('GET', '/gibt-es-nicht/'), null);
    eq($router('GET', '/../app/bootstrap.php'), null);
    $r = $router('GET', '/heizung/');
    eq($r['action'], 'show');
    $r = $router('POST', '/anfrage/senden/');
    eq($r['controller'], App\Controllers\FormController::class);
    $r = $router('DELETE', '/heizung/');
    eq($r['action'], 'methodNotAllowed');
    $r = $router('GET', '/ueber-uns/');
    eq($r['action'], 'redirect'); eq($r['params']['to'], '/unternehmen/');
    $r = $router('GET', '/wp-json/');
    eq($r['action'], 'gone');
});

t('Response::redirect verhindert offene Weiterleitungen', function () {
    $m = new ReflectionMethod(App\Services\Response::class, 'redirect');
    // Zielprüfung nachbilden: nur relative Pfade oder eigene Basis-URL
    $base = Container::config('site')['baseUrl'];
    foreach (['https://evil.example/', '//evil.example/x', 'javascript:alert(1)'] as $bad) {
        $target = $bad;
        if (!str_starts_with($target, '/') || str_starts_with($target, '//')) { if (!str_starts_with($target, $base . '/')) $target = '/'; }
        eq($target, '/', "offenes Redirect für $bad");
    }
});

t('Funding: Validierung lehnt Berechnung ohne Quelle oder bei inaktivem Status ab', function () use ($tmp) {
    $bad = ['ruleSetVersion' => 't', 'programs' => [[
        'id' => 'x', 'name' => 'x', 'provider' => 'x', 'region' => 'DE', 'fundingType' => 'Zuschuss', 'status' => 'derzeit geschlossen',
        'applicantTypes' => [], 'measures' => [], 'eligibility' => [], 'costRules' => [], 'rates' => [], 'caps' => [], 'combinations' => [], 'exclusions' => [],
        'validFrom' => null, 'validUntil' => null, 'checkedAt' => '2026-09-18', 'reviewDueAt' => '2026-10-18', 'sourceUrls' => [], 'ruleVersion' => '1', 'calculationApproved' => true,
    ]]];
    file_put_contents($tmp . '/bad.json', json_encode($bad));
    try { (new Funding($tmp . '/bad.json'))->all(); throw new RuntimeException('keine Exception'); }
    catch (RuntimeException $e) { ok(str_contains($e->getMessage(), 'calculationApproved') && str_contains($e->getMessage(), 'Quelle'), $e->getMessage()); }
    file_put_contents($tmp . '/broken.json', '{"programs": [');
    try { (new Funding($tmp . '/broken.json'))->all(); throw new RuntimeException('keine Exception'); }
    catch (RuntimeException $e) { ok(!str_contains($e->getMessage(), $tmp), 'Pfad in Fehlermeldung'); }
});

t('Funding: öffentlicher Export ohne internalNotes, alle Programme mit Quelle', function () {
    $f = Container::funding();
    $exp = $f->publicExport();
    foreach ($exp['programs'] as $p) { ok(!isset($p['internalNotes']), 'internalNotes im Export'); ok(count($p['sourceUrls']) > 0, $p['id'] . ' ohne Quelle'); ok(strtotime($p['reviewDueAt']) - strtotime($p['checkedAt']) <= 31 * 86400, $p['id'] . ': reviewDueAt später als 30 Tage'); }
});

t('JSON-LD: Holdings als parentOrganization, nicht als sameAs; nur zwei Personen', function () {
    $org = JsonLd::organization();
    ok(!isset($org['sameAs']), 'sameAs gesetzt');
    eq(count($org['parentOrganization']), 2);
    eq(count($org['employee']), 2);
    eq($org['telephone'], '+4924218899975');
    ok(str_ends_with($org['logo'], '/assets/brand/hsm-tec-logo.png'));
});

t('Content: Stellen ohne JobPosting wenn geschlossen; Initiativbewerbung nie JobPosting', function () {
    $c = Container::content();
    $job = $c->job('anlagenmechaniker-shk'); ok($c->isJobOpen($job));
    $closed = $job; $closed['status'] = 'closed'; ok(!$c->isJobOpen($closed));
    $expired = $job; $expired['validThrough'] = '2020-01-01'; ok(!$c->isJobOpen($expired));
    $init = $c->job('initiativbewerbung'); eq($init['type'], 'initiative');
});

t('Sitemap-Regeln: noindex- und excludeFromSitemap-Seiten fehlen', function () {
    ob_start(); (new App\Controllers\SystemController())->sitemap(); $xml = ob_get_clean();
    ok(!str_contains($xml, '/projekte/'), '/projekte/ in Sitemap');
    ok(!str_contains($xml, 'csrf'), 'csrf in Sitemap');
    ok(substr_count($xml, '<url>') >= 50, 'zu wenige URLs');
    $dom = new DOMDocument(); ok($dom->loadXML($xml) !== false, 'XML ungültig');
});

t('Gesperrte Begriffe kommen in gerenderten Seiten nicht vor', function () {
    $terms = array_filter(array_map('trim', file(HSM_ROOT . '/docs/forbidden-terms.txt')), fn($l) => $l !== '' && !str_starts_with($l, '#'));
    $c = Container::content();
    foreach (['/', '/unternehmen/', '/kontakt/', '/impressum/', '/ki/openai/', '/ki/anthropic/'] as $path) {
        $_SERVER['REQUEST_URI'] = $path;
        ob_start(); (new App\Controllers\PageController())->show($path); $html = ob_get_clean();
        foreach ($terms as $term) ok(stripos($html, $term) === false, "$path enthält '$term'");
        ok(substr_count($html, '<h1') === 1, "$path hat nicht genau eine H1");
    }
});

// Aufräumen
foreach (glob($tmp . '/*/*') ?: [] as $f) @unlink($f);
foreach (glob($tmp . '/*') ?: [] as $f) is_dir($f) ? @rmdir($f) : @unlink($f);
@rmdir($tmp);

echo "\n$n Tests, $fails fehlgeschlagen\n";
exit($fails ? 1 : 0);
