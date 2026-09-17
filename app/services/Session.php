<?php
declare(strict_types=1);

namespace App\Services;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $sec = Container::config('security')['session'];
        $site = Container::config('site');
        session_save_path($site['storageDir'] . '/sessions');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', '7200');
        session_name($sec['name']);
        session_set_cookie_params([
            'lifetime' => $sec['lifetime'],
            'path' => '/',
            'secure' => $sec['cookieSecure'] && Request::isHttps(),
            'httponly' => true,
            'samesite' => $sec['cookieSameSite'],
        ]);
        session_start();
    }
}
