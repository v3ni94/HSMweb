# Deployment

## Zielumgebung

- Gewöhnliches PHP-Webhosting mit HTTPS, Apache mit `mod_rewrite` (Regeln in `public/.htaccess`). Für nginx liegt eine ungetestete Beispielkonfiguration in `docs/nginx.example.conf`.
- PHP 8.2 oder neuer; entwickelt und getestet mit PHP 8.4.19. Vor Deployment die tatsächlich unterstützte Version des Hosters prüfen (php.net/supported-versions) und PHP 8.5 nach Erscheinen testen.
- Benötigte Erweiterungen: `json`, `mbstring`, `fileinfo`, `openssl`, `ctype`, `session`. Optional `gd` (nur für `getimagesize`, Fallback vorhanden), `zlib` (Komprimierung).
- Kein Composer, npm oder SSH auf dem Server erforderlich: `vendor/` ist im Paket enthalten.

## DocumentRoot

Ziel ist `public/` als DocumentRoot. `app/`, `config/`, `content/`, `storage/`, `vendor/` liegen eine Ebene darüber und sind damit nicht abrufbar.

Kann das Hosting den DocumentRoot nicht setzen: gesamtes Projekt in ein Unterverzeichnis außerhalb des Webroots hochladen (z. B. `/hsm-app/`) und im Webroot nur den Inhalt von `public/` ablegen. In `public/index.php` dann den Pfad `dirname(__DIR__) . '/app/bootstrap.php'` auf den echten Pfad anpassen. Diese Variante ist ausdrücklich zu testen (siehe `tests/smoke.sh`), insbesondere dass `/config/`, `/storage/`, `/.git/` und `/composer.json` mit 404 oder 403 antworten.

## Schreibrechte

Nur `storage/` und Unterverzeichnisse müssen für den PHP-Benutzer schreibbar sein (z. B. 750 bzw. 770 je nach Benutzer/Gruppe). Keine `777`. Alle anderen Verzeichnisse nur lesbar.

## Konfiguration

1. `config/mail.example.php` nach `config/mail.local.php` kopieren, SMTP-Host, Port, Verschlüsselung, Benutzer, Passwort, autorisierten Absender eintragen, `enabled` auf `true` setzen.
2. Beim Mailanbieter SPF, DKIM und DMARC für den Absender prüfen.
3. `config/site.php`: `environment` (`production` oder `staging`), `assetVersion` bei Asset-Änderungen erhöhen, `trustedProxies` nur setzen, wenn ein vorgeschalteter Proxy existiert.
4. Umgebungsvariable `HSM_ENV=staging` auf Staging setzt `noindex` und Robots-Sperre; auf Produktion darf sie nicht gesetzt sein.

## Upload-Limits

`config/security.php` (3 Dateien, je 4 MiB, gesamt 10 MiB) muss zu `php.ini` bzw. `.htaccess` (`upload_max_filesize`, `post_max_size`, `max_file_uploads`) und zum SMTP-Anbieter (maximale Mailgröße) passen. Optionaler Virenscanner über `scanCommand`; ohne Konfiguration erfolgt kein Scan, das wird nicht als Prüfung ausgegeben.

## Ablauf

1. Lokal: `php bin/validate-content.php && php bin/export-public-data.php && node tests/funding-calc.test.js`
2. SFTP-Upload, JSON-Dateien atomar (erst `datei.json.tmp`, dann umbenennen).
3. Nach Upload: `tests/smoke.sh https://staging-host` ausführen.
4. Externer Test: `config/`, `storage/`, `.git`, Backups nicht abrufbar; keine Geheimnisse in JS/JSON.
5. Realer autorisierter SMTP-Test aller drei Formulare mit freigegebenem Empfänger.
6. OPcache ist nach Deployment ggf. zu leeren (Hoster-Panel oder PHP-Neustart).

## Staging

Zugriffsschutz (HTTP Basic Auth) im Hoster-Panel oder per `.htaccess` ergänzen. `robots.txt` ersetzt keinen Zugriffsschutz. Vor Produktion prüfen, dass `HSM_ENV` nicht auf `staging` steht und kein Basic Auth aktiv bleibt.

## Wartung

- Monatlich: Förderquellen prüfen (`funding-maintenance.md`), Stellenstatus, Kontaktangaben.
- Bei PHP-/PHPMailer-Updates: lokal `composer update phpmailer/phpmailer`, Tests, dann `vendor/` neu hochladen.
- Backups: Gesamtes Projektverzeichnis inklusive `config/mail.local.php` (verschlüsselt ablegen). Wiederherstellung = erneuter Upload.
- `bin/cleanup.php` kann per Cron laufen, ist aber nicht erforderlich: Rate-Limit-Dateien und temporäre Uploads werden opportunistisch bei Anfragen bereinigt.
