<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Minimaler Service-Zugriff ohne Framework.
 */
final class Container
{
    private static array $config = [];
    private static array $instances = [];

    public static function init(array $config): void
    {
        self::$config = $config;
        self::$instances = [];
    }

    public static function config(string $section): array|null
    {
        return self::$config[$section] ?? null;
    }

    public static function content(): Content
    {
        return self::$instances['content'] ??= new Content(self::$config['site']['contentDir']);
    }

    public static function csrf(): Csrf
    {
        return self::$instances['csrf'] ??= new Csrf(self::$config['security']['csrf']['ttlSeconds']);
    }

    public static function rateLimiter(): RateLimiter
    {
        return self::$instances['rate'] ??= new RateLimiter(
            self::$config['site']['storageDir'] . '/rate-limits',
            self::$config['security']['rateLimit']
        );
    }

    public static function uploads(): UploadService
    {
        return self::$instances['uploads'] ??= new UploadService(
            self::$config['site']['storageDir'] . '/temporary-uploads',
            self::$config['security']['upload']
        );
    }

    public static function mail(): MailService
    {
        return self::$instances['mail'] ??= new MailService(
            self::$config['mail'],
            self::$config['site']['storageDir'] . '/logs'
        );
    }

    public static function funding(): Funding
    {
        return self::$instances['funding'] ??= new Funding(self::$config['site']['contentDir'] . '/funding/programs.json');
    }
}
