<?php
declare(strict_types=1);

use App\Services\Container;

function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

function url(string $path): string
{
    return rtrim(Container::config('site')['baseUrl'], '/') . $path;
}

function asset(string $path): string
{
    $v = Container::config('site')['assetVersion'];
    return '/assets/' . ltrim($path, '/') . '?v=' . rawurlencode($v);
}

/** Absätze aus Text mit Leerzeilen, escaped. */
function paragraphs(array|string $text): string
{
    $parts = is_array($text) ? $text : preg_split('/\n{2,}/', $text);
    $out = '';
    foreach ($parts as $p) {
        $p = trim((string)$p);
        if ($p !== '') {
            $out .= '<p>' . e($p) . '</p>';
        }
    }
    return $out;
}

function fmt_date(string $iso): string
{
    $t = strtotime($iso);
    return $t ? date('d.m.Y', $t) : $iso;
}

function fmt_eur(int $cents): string
{
    return number_format($cents / 100, 2, ',', '.') . ' EUR';
}
