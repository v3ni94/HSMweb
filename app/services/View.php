<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Einfache Template-Engine auf PHP-Basis mit Allowlist der Templates.
 */
final class View
{
    private const DIR = HSM_ROOT . '/app/views';

    public static function render(string $template, array $vars = []): string
    {
        $file = self::resolve('pages/' . $template);
        return self::include($file, $vars);
    }

    public static function component(string $name, array $vars = []): string
    {
        return self::include(self::resolve('components/' . $name), $vars);
    }

    public static function layout(string $layout, array $vars): string
    {
        return self::include(self::resolve('layouts/' . $layout), $vars);
    }

    private static function resolve(string $rel): string
    {
        if (!preg_match('~^[a-z0-9/_-]+$~', $rel)) {
            throw new \RuntimeException('Ungültiger Template-Name');
        }
        $file = self::DIR . '/' . $rel . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Template fehlt: ' . $rel);
        }
        return $file;
    }

    private static function include(string $file, array $vars): string
    {
        extract($vars, EXTR_SKIP);
        ob_start();
        try {
            include $file;
        } finally {
            $out = ob_get_clean();
        }
        return (string)$out;
    }
}

