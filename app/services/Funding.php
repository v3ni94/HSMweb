<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Lädt das zentrale Förderregelwerk (content/funding/programs.json), validiert es
 * und erzeugt die bereinigte öffentliche Regeldatei (public/assets/data/funding-rules.json).
 */
final class Funding
{
    private const REQUIRED = ['id', 'name', 'provider', 'region', 'fundingType', 'status', 'applicantTypes', 'measures',
        'eligibility', 'costRules', 'rates', 'caps', 'combinations', 'exclusions', 'validFrom', 'validUntil',
        'checkedAt', 'reviewDueAt', 'sourceUrls', 'ruleVersion', 'calculationApproved'];
    private const STATUS = ['aktiv', 'derzeit geschlossen', 'angekündigt', 'ausgelaufen', 'Prüfung erforderlich'];

    private ?array $data = null;

    public function __construct(private readonly string $file)
    {
    }

    public function all(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }
        if (!is_file($this->file)) {
            throw new RuntimeException('programs.json fehlt');
        }
        try {
            $json = json_decode((string)file_get_contents($this->file), true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new RuntimeException('programs.json ungültig');
        }
        $this->validate($json);
        return $this->data = $json;
    }

    public function programs(): array
    {
        return $this->all()['programs'];
    }

    public function validate(array $json): array
    {
        $errors = [];
        if (!isset($json['programs']) || !is_array($json['programs'])) {
            throw new RuntimeException('programs.json: "programs" fehlt');
        }
        $ids = [];
        foreach ($json['programs'] as $i => $p) {
            foreach (self::REQUIRED as $k) {
                if (!array_key_exists($k, $p)) {
                    $errors[] = "Programm #$i: Feld '$k' fehlt";
                }
            }
            if (isset($p['id'])) {
                if (isset($ids[$p['id']])) {
                    $errors[] = 'Doppelte Programm-ID ' . $p['id'];
                }
                $ids[$p['id']] = true;
            }
            if (isset($p['status']) && !in_array($p['status'], self::STATUS, true)) {
                $errors[] = ($p['id'] ?? "#$i") . ': unbekannter Status ' . $p['status'];
            }
            foreach (['validFrom', 'checkedAt', 'reviewDueAt'] as $d) {
                if (isset($p[$d]) && $p[$d] !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$p[$d])) {
                    $errors[] = ($p['id'] ?? "#$i") . ": $d kein Datum JJJJ-MM-TT";
                }
            }
            if (($p['calculationApproved'] ?? false) === true && ($p['status'] ?? '') !== 'aktiv') {
                $errors[] = ($p['id'] ?? "#$i") . ': calculationApproved nur bei Status aktiv zulässig';
            }
            if (($p['calculationApproved'] ?? false) === true && empty($p['sourceUrls'])) {
                $errors[] = ($p['id'] ?? "#$i") . ': Berechnung ohne Quelle nicht zulässig';
            }
        }
        if ($errors) {
            throw new RuntimeException("Förderregelwerk fehlerhaft:\n- " . implode("\n- ", $errors));
        }
        return $errors;
    }

    /**
     * Öffentliche, bereinigte Regeldatei: nur Felder, die der Browser-Rechner braucht.
     * Interne Notizen (internalNotes) werden entfernt.
     */
    public function publicExport(): array
    {
        $all = $this->all();
        $out = ['generatedFrom' => 'content/funding/programs.json', 'ruleSetVersion' => $all['ruleSetVersion'], 'programs' => []];
        foreach ($all['programs'] as $p) {
            unset($p['internalNotes']);
            $out['programs'][] = $p;
        }
        return $out;
    }

    /** Programme, deren Prüfdatum überschritten ist (Wartungshinweis). */
    public function overdue(string $today): array
    {
        return array_values(array_filter($this->programs(), fn($p) => $p['reviewDueAt'] !== null && $p['reviewDueAt'] < $today));
    }
}
