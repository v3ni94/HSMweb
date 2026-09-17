<?php
declare(strict_types=1);

namespace App\Services;

final class Request
{
    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);
        // Path Traversal und Steuerzeichen abweisen
        if (str_contains($path, "\0") || str_contains($path, '..')) {
            return '/__invalid__';
        }
        return $path;
    }

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        $site = Container::config('site');
        if (!empty($site['trustedProxies']) && in_array($_SERVER['REMOTE_ADDR'] ?? '', $site['trustedProxies'], true)) {
            return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        }
        return false;
    }

    /** Client-IP; Proxy-Header nur von konfigurierten Proxys. */
    public static function ip(): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $site = Container::config('site');
        if (!empty($site['trustedProxies']) && in_array($remote, $site['trustedProxies'], true)) {
            $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
            $first = trim(explode(',', $xff)[0] ?? '');
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return $remote;
    }

    public static function post(string $key, int $maxLen = 5000): string
    {
        $v = $_POST[$key] ?? '';
        if (!is_string($v)) {
            return '';
        }
        $v = str_replace(["\r\n", "\r"], "\n", $v);
        // Steuerzeichen außer Zeilenumbruch/Tab entfernen (Header-Injection-Schutz)
        $v = preg_replace('/[^\P{C}\n\t]/u', '', $v) ?? '';
        return mb_substr(trim($v), 0, $maxLen);
    }

    public static function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }
}
