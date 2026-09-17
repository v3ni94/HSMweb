<?php
declare(strict_types=1);

/**
 * Feste Allowlist der Routen. Kein dynamisches Einbinden von Dateien aus Nutzereingaben.
 * Rückgabe: ['controller' => Klasse, 'action' => Methode, 'params' => []]
 */

use App\Controllers\FormController;
use App\Controllers\PageController;
use App\Controllers\SystemController;
use App\Services\Container;

return static function (string $method, string $path): ?array {
    // Systemrouten
    $system = [
        '/sitemap.xml' => 'sitemap',
        '/llms.txt' => 'llms',
        '/csrf-token' => 'csrfToken',
    ];
    if ($method === 'GET' && isset($system[$path])) {
        return ['controller' => SystemController::class, 'action' => $system[$path], 'params' => []];
    }

    // Formularendpunkte (nur POST)
    $forms = [
        '/anfrage/senden/' => 'contact',
        '/wasserschaden/melden/' => 'damage',
        '/karriere/bewerben/' => 'application',
    ];
    if (isset($forms[$path])) {
        if ($method === 'POST') {
            return ['controller' => FormController::class, 'action' => $forms[$path], 'params' => []];
        }
        // GET auf Formularendpunkt: zurück zur passenden Seite
        return ['controller' => SystemController::class, 'action' => 'redirectToForm', 'params' => ['form' => $forms[$path]]];
    }

    if ($method !== 'GET' && $method !== 'HEAD') {
        return ['controller' => SystemController::class, 'action' => 'methodNotAllowed', 'params' => []];
    }

    $content = Container::content();

    // Redaktionelle Seiten aus dem Seitenregister
    if ($content->page($path) !== null) {
        return ['controller' => PageController::class, 'action' => 'show', 'params' => ['path' => $path]];
    }

    // Stellen
    if (preg_match('~^/karriere/([a-z0-9-]+)/$~', $path, $m) && $content->job($m[1]) !== null) {
        return ['controller' => PageController::class, 'action' => 'job', 'params' => ['slug' => $m[1]]];
    }

    // Ratgeber
    if (preg_match('~^/ratgeber/([a-z0-9-]+)/$~', $path, $m) && $content->guide($m[1]) !== null) {
        return ['controller' => PageController::class, 'action' => 'guide', 'params' => ['slug' => $m[1]]];
    }

    // Redirect-Matrix (alte URLs)
    $redirects = $content->redirects();
    $normalized = rtrim($path, '/') . '/';
    foreach (['redirects' => 301, 'gone' => 410] as $key => $status) {
        foreach ($redirects[$key] ?? [] as $entry) {
            if ($entry['from'] === $path || $entry['from'] === $normalized) {
                return ['controller' => SystemController::class, 'action' => $status === 301 ? 'redirect' : 'gone', 'params' => ['to' => $entry['to'] ?? null]];
            }
        }
    }

    // Pfade ohne Schrägstrich am Ende kanonisieren, wenn eine Seite existiert
    if ($path !== '/' && !str_ends_with($path, '/') && $content->page($path . '/') !== null) {
        return ['controller' => SystemController::class, 'action' => 'redirect', 'params' => ['to' => $path . '/']];
    }

    return null;
};
