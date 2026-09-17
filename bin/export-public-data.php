#!/usr/bin/env php
<?php
declare(strict_types=1);
/** Erzeugt die bereinigte öffentliche Regeldatei aus content/funding/programs.json. Atomar schreiben. */
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Services\Container;

$f = Container::funding();
$export = $f->publicExport();
$target = HSM_ROOT . '/public/assets/data/funding-rules.json';
$tmp = $target . '.tmp';
$json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
if (file_put_contents($tmp, $json, LOCK_EX) === false) { fwrite(STDERR, "Schreiben fehlgeschlagen\n"); exit(1); }
rename($tmp, $target);
echo "Exportiert: public/assets/data/funding-rules.json (" . count($export['programs']) . " Programme, Version {$export['ruleSetVersion']})\n";
