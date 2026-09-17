<?php
declare(strict_types=1);

/**
 * Bootstrap: Autoload, Konfiguration, Fehlerbehandlung, Sitzung.
 * Wird von public/index.php und den bin/-Skripten eingebunden.
 */

define('HSM_ROOT', dirname(__DIR__));

require HSM_ROOT . '/vendor/autoload.php';
require HSM_ROOT . "/app/helpers.php";

$config = [
    'site' => require HSM_ROOT . '/config/site.php',
    'security' => require HSM_ROOT . '/config/security.php',
    'mail' => is_file(HSM_ROOT . '/config/mail.local.php')
        ? require HSM_ROOT . '/config/mail.local.php'
        : null,
];

$isProduction = $config['site']['environment'] === 'production';

// Fehler niemals öffentlich anzeigen; privat protokollieren.
ini_set('display_errors', $isProduction ? '0' : '1');
ini_set('log_errors', '1');
ini_set('error_log', HSM_ROOT . '/storage/logs/php-error.log');
error_reporting(E_ALL);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Berlin');

App\Services\Container::init($config);

return $config;
