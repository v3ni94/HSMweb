# Abnahmebericht (Stand 18.09.2026, lokale Testumgebung)

Testumgebung: PHP 8.4.19 Entwicklungsserver (`php -S`), Chromium (Playwright 1.56) in 390 px und 1.280 px Breite, Node 22. Keine Produktions- oder Staging-Umgebung, kein echter SMTP-Zugang. Ergebnisse gelten nur für diese Umgebung.

## Routing, Status, XML

- 53 Sitemap-URLs (43 Seiten, 7 Stellen, 3 Ratgeber) liefern HTTP 200 (`tests/smoke.sh`).
- 301: `/home/`, `/heizung` (ohne Slash), `/karriere/heizungsbauer/`. 410: `/danke/`. 404: unbekannte Pfade, `/config/site.php`, `/.git/HEAD`, `/composer.json`, `/content/company.json`, `/storage/logs/mail.log`. 405 bei PUT. 302 bei GET auf Formularendpunkte.
- Sitemap enthält weder `/projekte/` (leer, noindex) noch Formularendpunkte, Ergebnisseiten oder `/csrf-token`. `lastmod` aus den Inhaltsdateien.
- Canonical je Seite, `noindex` auf Projekten, Fehler- und Ergebnisseiten; Staging-Modus setzt `noindex` global.

## Sicherheit

- Header: CSP (`default-src 'self'`, kein Inline-Script), `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`; HSTS nur bei HTTPS.
- Formularseiten und -antworten `Cache-Control: no-store`; CSRF-Token zusätzlich über `/csrf-token` (no-store) aktualisiert.
- Getestet: fehlendes CSRF-Token 422; Honeypot stiller „Erfolg“ ohne Versand; Validierungsfehler mit Feldzuordnung (JSON und HTML); Upload einer PHP-Datei als `bild.jpg` abgewiesen (Inhaltsprüfung); Rate Limit greift nach 5 Versuchen in 15 Minuten; temporäre Uploads nach Verarbeitung gelöscht (0 Dateien übrig).
- Ohne SMTP-Konfiguration: 503 mit verständlicher Meldung, Telefon/E-Mail, alle Eingaben bleiben im Formular erhalten; keine vorgetäuschte Erfolgsmeldung. Log enthält nur `mail_not_configured`, keine Inhalte.
- Öffentliche Regeldatei ohne `internalNotes`; keine Geheimnisse in JS/JSON.

## Fördercheck

- 30 Regressionstests bestanden (`tests/funding-calc.test.js`): Grundförderung 8.400 EUR, Klimabonus 12.880 EUR, Einkommensstufen 22.400 / 19.600 / 15.680 EUR, Kind erhöht Schwelle, Kostenobergrenze, Mehrfamilienhaus-Obergrenze, Vermieter ohne Boni, Unternehmen (459-Hinweis), WEG manuell, Datumswechsel 2027 (angekündigt, nicht berechnet), Datum vor Regelstand, überfällige Quellenprüfung deaktiviert Berechnung, 455-B geschlossen nicht eingerechnet, Pflegekasse 4.180 / tatsächliche Kosten / 16.720 EUR Deckel, ohne Pflegegrad kein Zuschuss, Kredit/Zuschuss/Steuer getrennt, § 35c-Warnung, BAFA nur Prüfung, negative/ungültige Eingaben, Vorhabenbeginn, keine Altanlage, Bundesländerfilter, Rundung.
- Browser-Ende-zu-Ende (390 px und 1.280 px): 28.000 EUR, Selbstnutzer, Einkommen bis 30.000 EUR ergibt 22.400,00 EUR.
- Einkommens- und Pflegeangaben verlassen den Browser nicht (kein Request, keine URL-Parameter, keine Speicherung). Übernahme ins Formular nur nach Klick und nur nicht sensibler Daten.

## Barrierearme Bedienung und Layout

- Kein horizontaler Überlauf in 390 px auf 7 geprüften Seiten; genau eine H1 je Seite; keine JavaScript-Fehler in der Konsole.
- Erster Tab erreicht den Sprunglink; Navigation, Akkordeons (`details`), Chips und Formulare per Tastatur bedienbar; sichtbarer Fokus; `prefers-reduced-motion` respektiert; Formularfehler mit `aria-invalid`/`aria-describedby` und Fehlerbox mit `role="alert"`.
- Mobile Leiste wird bei Fokus im Formular ausgeblendet, damit sie keine Felder verdeckt.
- Kontraste rechnerisch geprüft (siehe `brand-guide.md`). Screenreader-Test mit realer Software und externe WCAG-Prüfung stehen aus.

## Nicht getestet / offen

- Realer SMTP-Versand, Reply-To, SPF/DKIM/DMARC (kein Zugang).
- Apache-`.htaccess` in echter Hosting-Umgebung (lokal nur PHP-Router). Insbesondere HTTPS-/Host-Redirect und Sperre von `.json`/`.md` außerhalb `assets/data`.
- Core Web Vitals mit realen Nutzern; lokal keine belastbaren Felddaten. Seitengewicht ohne Bilder: HTML ca. 20 bis 40 KB, CSS ca. 14 KB, JS ca. 6 + 9 + 8 KB unkomprimiert.
- Migration der echten alten URLs (Bestandswebsite nicht erreichbar, siehe `inventory.md`).
