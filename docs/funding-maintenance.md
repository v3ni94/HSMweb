# Pflege des Förderregelwerks

Führende Datei: `content/funding/programs.json`. Daraus entstehen die öffentliche Regeldatei `public/assets/data/funding-rules.json` (per `php bin/export-public-data.php`, entfernt `internalNotes`) und die Förderübersichten auf `/foerderung/*`. Die öffentliche Datei nie direkt bearbeiten.

## Felder je Programm

`id`, `name`, `provider`, `region` (`DE` oder Bundesland-Kürzel wie `NRW`), `fundingType` (Zuschuss, Kredit, Darlehen, Steuerermäßigung …), `status` (`aktiv`, `derzeit geschlossen`, `angekündigt`, `ausgelaufen`, `Prüfung erforderlich`), `applicantTypes`, `measures`, `eligibility`, `costRules`, `rates`, `caps`, `combinations`, `exclusions`, `validFrom`, `validUntil`, `checkedAt`, `reviewDueAt`, `sourceUrls`, `ruleVersion`, `calculationApproved`, optional `periods` (datierte Regelstände) und `internalNotes`.

Regeln der Validierung: `calculationApproved: true` nur bei `status: aktiv` und mit mindestens einer Quelle. Geld in Cent, Prozentsätze in ganzen Punkten.

## Aktiv versus berechnet

Ein Programm kann aktiv sein, ohne dass der Rechner Beträge ausgibt (`calculationApproved: false`). Dann zeigt der Fördercheck Status, Voraussetzungen und Quelle. Ein gesperrter Rechenzweig bedeutet nicht, dass kein Anspruch besteht. Aktuell berechnet: KfW 458 (Periode ab 21.07.2026), BAFA Heizungsoptimierung (15 %, ohne iSFP-Bonus, bis 5 WE) und Pflegekasse Wohnumfeldverbesserung. Angekündigte KfW-458-Perioden ab 01.02.2027 sind hinterlegt, aber nicht freigegeben.

## 30-Tage-Regel

`reviewDueAt` = `checkedAt` + höchstens 30 Tage. Ist das Datum überschritten, deaktiviert der Rechner die Berechnung des Programms automatisch und weist darauf hin; `bin/validate-content.php` gibt eine Warnung aus. `checkedAt` wird ausschließlich manuell nach tatsächlicher Quellenprüfung gesetzt, nie automatisch.

## Änderungsablauf

1. Quellen (`sourceUrls`) aufrufen, Änderungen notieren.
2. Werte in `programs.json` anpassen, `ruleVersion` und `ruleSetVersion` erhöhen, `checkedAt`/`reviewDueAt` setzen. Bei widersprüchlichen Quellen `calculationApproved: false` oder `status: Prüfung erforderlich`.
3. Erwartungswerte in `tests/funding-calc.test.js` anpassen und `node tests/funding-calc.test.js` ausführen.
4. `php bin/validate-content.php && php bin/export-public-data.php`.
5. Beide JSON-Dateien atomar hochladen; Asset-Version in `config/site.php` erhöhen, damit Browser die neue Regeldatei laden.

Kein Live-Scraping. Eine optionale Änderungsprüfung darf Hinweise liefern, Werte aber nicht überschreiben.

## Quellenprüfung 18.09.2026

Abgerufen und bestätigt: BAFA Heizungsoptimierung (15 %, iSFP 5 %, Mindestinvestition 300 EUR, Höchstgrenzen 30.000/15.000/8.000 EUR, max. 5 WE, Anlage älter als 2 Jahre, fossil max. 20 Jahre, EEE), BAFA Emissionsminderung (50 %), Pflegekasse (4.180 EUR je Person, bis 16.720 EUR; BMG-Stand 14.09.2026), NRW.BANK.Gebäudesanierung (150.000 EUR, bis 100 %), NRW-Eigentumsförderung Modernisierung (220.000 EUR, Tilgungsnachlass 25 % bis 50 %), progres.nrw Geothermie (30 EUR/Bohrmeter, 15.000 EUR, ab 3 WE, Richtlinie bis 30.06.2027), § 35c EStG (7/7/6 %, 40.000 EUR, Gebäude älter als 10 Jahre), § 35a EStG (20 %, 1.200 EUR).

Nicht erreichbar: alle KfW-Seiten (Verbindung ohne Antwort aus der Entwicklungsumgebung). Die KfW-Werte beruhen weiterhin auf der Auftragsgrundlage vom 18.09.2026 und sind manuell im Browser gegenzuprüfen.

## Offene Prüfpunkte (Stand 18.09.2026)

- KfW 458, 459, 455-B, 159, 358/359, 261: Live-Abruf nicht möglich, manuell prüfen.
- KfW 459: Kostenobergrenzen für Unternehmen fehlen für eine Betragsberechnung.
- BAFA iSFP-Bonus: Mindestvolumen-Logik nicht automatisiert, nur Hinweis.
- Kommunale Programme (Düren, Kreis Düren): nur nach belastbarer Quelle ergänzen.
