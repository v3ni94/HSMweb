<?php
declare(strict_types=1);

namespace App\Services;

/**
 * CSRF-Schutz auf Sitzungsbasis. Tokens werden nie in gecachte Dokumente eingebettet:
 * Formularseiten senden Cache-Control: no-store (siehe Response).
 */
final class Csrf
{
    public function __construct(private readonly int $ttl)
    {
    }

    public function token(): string
    {
        Session::start();
        $now = time();
        $tok = $_SESSION['csrf'] ?? null;
        if (!is_array($tok) || ($tok['exp'] ?? 0) < $now) {
            $tok = ['value' => bin2hex(random_bytes(32)), 'exp' => $now + $this->ttl];
            $_SESSION['csrf'] = $tok;
        }
        return $tok['value'];
    }

    public function validate(?string $submitted): bool
    {
        Session::start();
        $tok = $_SESSION['csrf'] ?? null;
        if (!is_array($tok) || !is_string($submitted) || $submitted === '') {
            return false;
        }
        if (($tok['exp'] ?? 0) < time()) {
            return false;
        }
        return hash_equals($tok['value'], $submitted);
    }
}
