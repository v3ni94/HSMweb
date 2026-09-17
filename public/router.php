<?php
// Nur für den PHP-Entwicklungsserver (php -S). Auf Produktion übernimmt .htaccess die Zuordnung.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file(__DIR__ . $path)) {
    return false;
}
require __DIR__ . '/index.php';
