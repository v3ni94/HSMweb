<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Temporäre Uploads außerhalb des Webroots; Inhaltsprüfung; Löschung nach Verarbeitung.
 * Rückgabe: ['files' => [[path, name, mime, size]], 'errors' => [...]]
 */
final class UploadService
{
    public function __construct(private readonly string $dir, private readonly array $cfg)
    {
    }

    public function enabled(): bool
    {
        return (bool)($this->cfg['enabled'] ?? false);
    }

    public function process(string $field): array
    {
        $result = ['files' => [], 'errors' => []];
        if (!$this->enabled() || empty($_FILES[$field]) || !is_array($_FILES[$field]['name'])) {
            return $result;
        }
        $names = $_FILES[$field]['name'];
        $count = count(array_filter($names, fn($n) => $n !== ''));
        if ($count === 0) {
            return $result;
        }
        if ($count > (int)$this->cfg['maxFiles']) {
            $result['errors'][] = sprintf('Bitte höchstens %d Dateien anhängen.', $this->cfg['maxFiles']);
            return $result;
        }
        if (!is_dir($this->dir) && !mkdir($this->dir, 0750, true)) {
            $result['errors'][] = 'Dateianhänge sind derzeit nicht möglich. Bitte senden Sie die Anfrage ohne Anhang.';
            return $result;
        }
        $this->opportunisticCleanup();
        $total = 0;
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        foreach ($names as $i => $name) {
            if ($name === '') {
                continue;
            }
            $err = (int)$_FILES[$field]['error'][$i];
            $tmp = (string)$_FILES[$field]['tmp_name'][$i];
            $size = (int)$_FILES[$field]['size'][$i];
            $safeName = $this->safeName((string)$name);
            if ($err !== UPLOAD_ERR_OK) {
                $result['errors'][] = "Datei „{$safeName}“ konnte nicht übernommen werden.";
                continue;
            }
            if ($size > (int)$this->cfg['maxFileBytes']) {
                $result['errors'][] = sprintf('Datei „%s“ ist größer als %d MiB.', $safeName, intdiv((int)$this->cfg['maxFileBytes'], 1048576));
                continue;
            }
            $total += $size;
            if ($total > (int)$this->cfg['maxTotalBytes']) {
                $result['errors'][] = sprintf('Die Anhänge überschreiten zusammen %d MiB.', intdiv((int)$this->cfg['maxTotalBytes'], 1048576));
                break;
            }
            if (!is_uploaded_file($tmp)) {
                $result['errors'][] = "Datei „{$safeName}“ wurde abgewiesen.";
                continue;
            }
            $mime = (string)$finfo->file($tmp);
            $ext = strtolower(pathinfo((string)$name, PATHINFO_EXTENSION));
            if (!in_array($mime, $this->cfg['allowedMime'], true) || !in_array($ext, $this->cfg['allowedExt'], true) || !$this->mimeMatchesExt($mime, $ext)) {
                $result['errors'][] = "Datei „{$safeName}“ hat ein nicht zugelassenes Format. Erlaubt sind PDF, JPG, PNG und WebP.";
                continue;
            }
            if (str_starts_with($mime, 'image/') && @getimagesize($tmp) === false) {
                $result['errors'][] = "Datei „{$safeName}“ ist keine gültige Bilddatei.";
                continue;
            }
            $target = $this->dir . '/' . bin2hex(random_bytes(16)) . '.bin';
            if (!move_uploaded_file($tmp, $target)) {
                $result['errors'][] = "Datei „{$safeName}“ konnte nicht gespeichert werden.";
                continue;
            }
            @chmod($target, 0600);
            if (!$this->scan($target)) {
                @unlink($target);
                $result['errors'][] = "Datei „{$safeName}“ wurde von der Sicherheitsprüfung abgewiesen.";
                continue;
            }
            $result['files'][] = ['path' => $target, 'name' => $safeName, 'mime' => $mime, 'size' => $size];
        }
        return $result;
    }

    public function discard(array $files): void
    {
        foreach ($files as $f) {
            if (!empty($f['path']) && is_file($f['path'])) {
                @unlink($f['path']);
            }
        }
    }

    private function mimeMatchesExt(string $mime, string $ext): bool
    {
        $map = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        return ($map[$ext] ?? null) === $mime;
    }

    private function safeName(string $name): string
    {
        $name = preg_replace('/[^\p{L}\p{N}._ -]/u', '_', $name) ?? 'datei';
        return mb_substr($name, 0, 80);
    }

    /** Externer Scanner nur, wenn konfiguriert. Ohne Konfiguration keine Prüfung, dies wird nicht als Prüfung ausgegeben. */
    private function scan(string $path): bool
    {
        $cmd = (string)($this->cfg['scanCommand'] ?? '');
        if ($cmd === '') {
            return true;
        }
        $full = sprintf($cmd, escapeshellarg($path));
        exec($full, $out, $code);
        return $code === 0;
    }

    private function opportunisticCleanup(): void
    {
        if (random_int(1, 20) === 1) {
            $this->cleanup();
        }
    }

    public function cleanup(int $olderThanSeconds = 3600): int
    {
        $n = 0;
        foreach (glob($this->dir . '/*.bin') ?: [] as $f) {
            if (filemtime($f) < time() - $olderThanSeconds) {
                @unlink($f);
                $n++;
            }
        }
        return $n;
    }
}
