#!/usr/bin/env php
<?php
declare(strict_types=1);
/** Löscht abgelaufene Rate-Limit-Dateien, temporäre Uploads und alte Sessions. Für Cron oder manuellen Aufruf. */
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Services\Container;

$r = Container::rateLimiter()->cleanup();
$u = Container::uploads()->cleanup(0);
$s = 0;
foreach (glob(Container::config('site')['storageDir'] . '/sessions/sess_*') ?: [] as $f) {
    if (filemtime($f) < time() - 7200) { @unlink($f); $s++; }
}
echo "Bereinigt: $r Rate-Limit-Dateien, $u temporäre Uploads, $s Sessions\n";
