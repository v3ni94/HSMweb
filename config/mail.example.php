<?php
declare(strict_types=1);

/**
 * Vorlage. Kopieren nach config/mail.local.php und mit echten Werten füllen.
 * mail.local.php ist in .gitignore und darf niemals ins Repository.
 */
return [
    'enabled' => false,                 // true erst nach erfolgreichem autorisierten SMTP-Test
    'host' => 'smtp.example.invalid',
    'port' => 587,
    'encryption' => 'tls',              // 'tls' (STARTTLS) oder 'ssl' (SMTPS, meist Port 465)
    'username' => '',
    'password' => '',
    'fromAddress' => 'website@hsm-tec.de',   // autorisierter Domain-Absender (SPF/DKIM prüfen)
    'fromName' => 'HSM Tec GmbH Website',
    'toAddress' => 'info@hsm-tec.de',        // fester Empfänger, nie aus POST-Daten
    'timeout' => 15,
    'sendConfirmationToVisitor' => true,
    // Nur privat: SMTP-Debugausgabe in storage/logs/mail-debug.log (0 = aus)
    'debugLevel' => 0,
];
