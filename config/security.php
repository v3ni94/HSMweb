<?php
declare(strict_types=1);

return [
    'session' => [
        'name' => 'hsmsid',
        'lifetime' => 0,
        'cookieSecure' => true,
        'cookieSameSite' => 'Lax',
    ],
    'csrf' => [
        'ttlSeconds' => 7200,
    ],
    'rateLimit' => [
        // Formularversand: max. Versuche je Pseudonym-Schlüssel im Zeitfenster
        'maxAttempts' => 5,
        'windowSeconds' => 900,
        // Verzeichnisgröße begrenzen (Anzahl Dateien), ältere werden opportunistisch gelöscht
        'maxFiles' => 2000,
    ],
    'upload' => [
        'enabled' => true,
        'maxFiles' => 3,
        'maxFileBytes' => 4 * 1024 * 1024,
        'maxTotalBytes' => 10 * 1024 * 1024,
        'allowedMime' => ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'],
        'allowedExt' => ['pdf', 'jpg', 'jpeg', 'png', 'webp'],
        // Optionaler Virenscanner-Befehl, z. B. 'clamdscan --no-summary %s'. Leer = kein Scan (wird ehrlich dokumentiert).
        'scanCommand' => '',
    ],
    'spam' => [
        'honeypotField' => 'website_url',
        'minSecondsToSubmit' => 3,
    ],
];
