<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Dateibasiertes Rate Limit ohne Datenbank.
 * Schlüssel = HMAC(IP, Tagesgeheimnis) -> pseudonym, kurzlebig. Dateien laufen nach dem Fenster ab.
 */
final class RateLimiter
{
    public function __construct(private readonly string $dir, private readonly array $cfg)
    {
    }

    public function allow(string $ip, string $scope): bool
    {
        if (!is_dir($this->dir) && !mkdir($this->dir, 0750, true)) {
            return true; // Verfügbarkeit vor Blockade; wird protokolliert
        }
        $this->opportunisticCleanup();
        $file = $this->dir . '/' . $this->key($ip, $scope) . '.json';
        $fh = fopen($file, 'c+');
        if ($fh === false) {
            return true;
        }
        try {
            if (!flock($fh, LOCK_EX)) {
                return true;
            }
            $raw = stream_get_contents($fh);
            $data = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
            $now = time();
            $window = (int)$this->cfg['windowSeconds'];
            $hits = is_array($data['hits'] ?? null) ? array_filter($data['hits'], fn($t) => $t > $now - $window) : [];
            if (count($hits) >= (int)$this->cfg['maxAttempts']) {
                return false;
            }
            $hits[] = $now;
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, json_encode(['hits' => array_values($hits)]));
            return true;
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    private function key(string $ip, string $scope): string
    {
        $secret = $this->dailySecret();
        return hash_hmac('sha256', $scope . '|' . $ip, $secret);
    }

    /** Tagesgeheimnis in privater Datei; rotiert täglich, dadurch keine dauerhafte IP-Zuordnung. */
    private function dailySecret(): string
    {
        $file = $this->dir . '/.secret-' . date('Ymd');
        if (is_file($file)) {
            return (string)file_get_contents($file);
        }
        $secret = bin2hex(random_bytes(32));
        file_put_contents($file, $secret, LOCK_EX);
        @chmod($file, 0600);
        return $secret;
    }

    /** Opportunistische Bereinigung ohne Cron: bei ~1/25 der Aufrufe abgelaufene Dateien entfernen. */
    private function opportunisticCleanup(): void
    {
        if (random_int(1, 25) !== 1) {
            return;
        }
        $this->cleanup();
    }

    public function cleanup(): int
    {
        $removed = 0;
        $window = (int)$this->cfg['windowSeconds'];
        $files = glob($this->dir . '/*.json') ?: [];
        $now = time();
        foreach ($files as $f) {
            if (filemtime($f) < $now - $window) {
                @unlink($f);
                $removed++;
            }
        }
        // Verzeichnisgröße begrenzen
        $files = glob($this->dir . '/*.json') ?: [];
        if (count($files) > (int)$this->cfg['maxFiles']) {
            usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
            foreach (array_slice($files, 0, count($files) - (int)$this->cfg['maxFiles']) as $f) {
                @unlink($f);
                $removed++;
            }
        }
        foreach (glob($this->dir . '/.secret-*') ?: [] as $s) {
            if (basename($s) !== '.secret-' . date('Ymd') && basename($s) !== '.secret-' . date('Ymd', $now - 86400)) {
                @unlink($s);
                $removed++;
            }
        }
        return $removed;
    }
}
