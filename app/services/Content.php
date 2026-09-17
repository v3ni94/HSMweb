<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Lädt und validiert die dateibasierten Inhalte (JSON). Einzige Quelle je Inhaltstyp.
 */
final class Content
{
    private array $cache = [];

    public function __construct(private readonly string $dir)
    {
    }

    public function load(string $name): array
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        $file = $this->dir . '/' . $name . '.json';
        if (!is_file($file)) {
            throw new RuntimeException("Inhaltsdatei fehlt: {$name}.json");
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new RuntimeException("Inhaltsdatei nicht lesbar: {$name}.json");
        }
        try {
            $data = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // Beschädigte Datei: Fehler ohne Detailausgabe nach außen.
            throw new RuntimeException("Inhaltsdatei ungültig: {$name}.json");
        }
        if (!is_array($data)) {
            throw new RuntimeException("Inhaltsdatei hat kein Objekt: {$name}.json");
        }
        return $this->cache[$name] = $data;
    }

    public function company(): array
    {
        return $this->load('company');
    }

    /** @return array<string, array> Seiten indiziert nach Pfad */
    public function pages(): array
    {
        if (isset($this->cache['_pagesByPath'])) {
            return $this->cache['_pagesByPath'];
        }
        $pages = $this->load('pages')['pages'] ?? [];
        $byPath = [];
        foreach ($pages as $page) {
            if (!isset($page['path'], $page['template'], $page['title'])) {
                throw new RuntimeException('Seite ohne path/template/title in pages.json');
            }
            if (isset($byPath[$page['path']])) {
                throw new RuntimeException('Doppelter Pfad in pages.json: ' . $page['path']);
            }
            $byPath[$page['path']] = $page;
        }
        return $this->cache['_pagesByPath'] = $byPath;
    }

    public function page(string $path): ?array
    {
        return $this->pages()[$path] ?? null;
    }

    /** Kinder einer Seite in Reihenfolge der Datei. */
    public function children(string $parentPath): array
    {
        $out = [];
        foreach ($this->pages() as $p) {
            if (($p['parent'] ?? null) === $parentPath) {
                $out[] = $p;
            }
        }
        return $out;
    }

    public function jobs(bool $onlyOpen = false): array
    {
        $jobs = $this->load('jobs')['jobs'] ?? [];
        if ($onlyOpen) {
            $jobs = array_values(array_filter($jobs, fn($j) => $this->isJobOpen($j)));
        }
        return $jobs;
    }

    public function job(string $slug): ?array
    {
        foreach ($this->jobs() as $j) {
            if ($j['slug'] === $slug) {
                return $j;
            }
        }
        return null;
    }

    public function isJobOpen(array $job): bool
    {
        if (($job['status'] ?? '') !== 'open') {
            return false;
        }
        if (!empty($job['validThrough']) && strtotime($job['validThrough']) < time()) {
            return false;
        }
        return true;
    }

    public function projects(bool $onlyApproved = true): array
    {
        $projects = $this->load('projects')['projects'] ?? [];
        if ($onlyApproved) {
            $projects = array_values(array_filter($projects, fn($p) => ($p['approved'] ?? false) === true));
        }
        return $projects;
    }

    public function redirects(): array
    {
        return $this->load('redirects');
    }

    public function guides(): array
    {
        return $this->load('guides')['guides'] ?? [];
    }

    public function guide(string $slug): ?array
    {
        foreach ($this->guides() as $g) {
            if ($g['slug'] === $slug) {
                return $g;
            }
        }
        return null;
    }
}
