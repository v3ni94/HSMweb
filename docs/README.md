# HSM Tec GmbH Website (PHP, ohne Datenbank)

Mehrseitige Unternehmenswebsite für die HSM Tec GmbH, Düren. Stack: PHP 8.2+ (entwickelt und getestet mit 8.4), semantisches HTML5, CSS, Vanilla JavaScript, PHPMailer per Composer. Keine Datenbank, kein CMS, kein Node-Server auf Produktion.

## Struktur

| Verzeichnis | Inhalt |
|---|---|
| `public/` | Einziges DocumentRoot: `index.php` (Front Controller), `.htaccess`, `robots.txt`, `assets/` (CSS, JS, Marke, `data/funding-rules.json` als erzeugter Export) |
| `app/` | `bootstrap.php`, `routes.php` (Allowlist), `controllers/`, `services/`, `views/` |
| `config/` | `site.php`, `security.php`, `mail.example.php`; `mail.local.php` mit echten Zugangsdaten wird nie eingecheckt |
| `content/` | Redaktion als JSON: `pages.json`, `company.json`, `jobs.json`, `projects.json`, `guides.json`, `redirects.json`, `funding/programs.json` |
| `storage/` | Laufzeitdateien (Rate Limits, Sessions, temporäre Uploads, Logs), schreibbar, nicht öffentlich |
| `bin/` | `validate-content.php`, `export-public-data.php`, `cleanup.php` |
| `tests/` | `php/run.php` (PHP), `funding-calc.test.js` (Node), `smoke.sh` (HTTP) |
| `docs/` | Diese Dokumentation |

## Lokal starten

```bash
composer install
php bin/validate-content.php
php bin/export-public-data.php
HSM_ENV=development php -S 127.0.0.1:8080 -t public public/router.php
```

Tests: `php tests/php/run.php` (Sicherheits- und Inhaltsdienste), `node tests/funding-calc.test.js` (Fördercheck) und `tests/smoke.sh http://127.0.0.1:8080` (HTTP gegen laufenden Server).

## Pflege

Inhalte werden ausschließlich über die JSON-Dateien in `content/` gepflegt (siehe `content-maintenance.md`, `funding-maintenance.md`). Nach jeder Änderung: validieren, exportieren, per SFTP atomar hochladen (erst `.tmp`, dann umbenennen).

## Dokumente

- `deployment.md`: Hosting, PHP-Erweiterungen, Schreibrechte, SMTP
- `brand-guide.md`: Designsystem, Farbherkunft, Vorbehalte
- `asset-register.md`: Herkunft und Freigabestatus aller Medien
- `inventory.md`: Bestandsaufnahme der alten Website (Protokoll)
- `redirect-matrix.md`: URL-/Redirect-Matrix (privat)
- `acceptance-report.md`: Testergebnisse
- `release-checklist.md`: erledigt, getestet, offen, Go-live-blockierend
