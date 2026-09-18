<?php
declare(strict_types=1);

/**
 * Öffentliche Standortkonfiguration. Keine Geheimnisse hier.
 * Geheimnisse (SMTP) gehören ausschließlich in config/mail.local.php.
 */
return [
    'baseUrl' => 'https://hsm-tec.de',
    'environment' => getenv('HSM_ENV') ?: 'production', // production | staging | development
    'contentDir' => dirname(__DIR__) . '/content',
    'storageDir' => dirname(__DIR__) . '/storage',
    'publicDir' => dirname(__DIR__) . '/public',
    // Staging: Suchmaschinen aussperren. Auf Produktion muss dies false sein (siehe release-checklist.md).
    'noindexAll' => (getenv('HSM_ENV') ?: 'production') !== 'production',
    'assetVersion' => '2026.09.18-2',
    // Vertrauenswürdige vorgeschaltete Proxys (nur dann X-Forwarded-For auswerten).
    'trustedProxies' => [],
];
