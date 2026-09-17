<?php
declare(strict_types=1);

namespace App\Services;

final class Response
{
    public static function securityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'; object-src 'none'");
        if (Request::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000');
        }
    }

    public static function html(string $body, int $status = 200, bool $private = false): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        header($private ? 'Cache-Control: no-store' : 'Cache-Control: public, max-age=300');
        echo $body;
    }

    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function xml(string $body): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        echo $body;
    }

    public static function text(string $body): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        echo $body;
    }

    /** Nur relative Ziele oder eigene Host-URLs (keine offenen Weiterleitungen). */
    public static function redirect(string $target, int $status = 302): void
    {
        $base = Container::config('site')['baseUrl'];
        if (!str_starts_with($target, '/') || str_starts_with($target, '//')) {
            if (!str_starts_with($target, $base . '/')) {
                $target = '/';
            }
        }
        http_response_code($status);
        header('Location: ' . $target);
        header('Cache-Control: no-store');
    }
}
