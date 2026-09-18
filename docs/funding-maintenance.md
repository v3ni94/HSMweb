# Pflege des Förderregelwerks

Führende Datei: `content/funding/programs.json`. Daraus entstehen die öffentliche Regeldatei `public/assets/data/funding-rules.json` (per `php bin/export-public-data.php`, entfernt `internalNotes`) und die Förderübersichten auf `/foerderung/*`. Die öffentliche Datei nie direkt bearbeiten.

## Felder je Programm

`id`, `name`, `provider`, `region` (`DE` oder Bundesland-Kürzel wie `NRW`), `fundingType` (Zuschuss, Kredit, Darlehen, Steuerermäßigung …), `status` (`aktiv`, `derzeit geschlossen`, `angekündigt`, `ausgelaufen`, `Prüfung erforderlich`), `applicantTypes`, `measures`, `eligibility`, `costRules`, `rates`, `caps`, `combinations`, `exclusions`, `validFrom`, `validUntil`, `checkedAt`, `reviewDueAt`, `sourceUrls`, `ruleVersion`, `calculationApproved`, optional `periods` (datierte Regelstände) und `internalNotes`.

Regeln der Validierung: `calculationApproved: true` nur bei `status: aktiv` und mit mindestens einer Quelle. Geld in Cent, Prozentsätze in ganzen Punkten.

## Aktiv versus berechnet

Ein Programm kann aktiv sein, ohne dass der Rechner Beträge ausgibt (`calculationApproved: false`). Dann zeigt der Fördercheck Status, Voraussetzungen und Quelle. Ein gesperrter Rechenzweig bedeutet nicht, dass kein Anspruch besteht. Aktuell berechnet: KfW 458 (Periode ab 21.07.2026) und Pflegekasse Wohnumfeldverbesserung. Angekündigte KfW-458-Perioden ab 01.02.2027 sind hinterlegt, aber nicht freigegeben.

## 30-Tage-Regel

`reviewDueAt` = `checkedAt` + höchstens 30 Tage. Ist das Datum überschritten, deaktiviert der Rechner die Berechnung des Programms automatisch und weist darauf hin; `bin/validate-content.php` gibt eine Warnung aus. `checkedAt` wird ausschließlich manuell nach tatsächlicher Quellenprüfung gesetzt, nie automatisch.

## Änderungsablauf

1. Quellen (`sourceUrls`) aufrufen, Änderungen notieren.
2. Werte in `programs.json` anpassen, `ruleVersion` und `ruleSetVersion` erhöhen, `checkedAt`/`reviewDueAt` setzen. Bei widersprüchlichen Quellen `calculationApproved: false` oder `status: Prüfung erforderlich`.
3. Erwartungswerte in `tests/funding-calc.test.js` anpassen und `node tests/funding-calc.test.js` ausführen.
4. `php bin/validate-content.php && php bin/export-public-data.php`.
5. Beide JSON-Dateien atomar hochladen; Asset-Version in `config/site.php` erhöhen, damit Browser die neue Regeldatei laden.

Kein Live-Scraping. Eine optionale Änderungsprüfung darf Hinweise liefern, Werte aber nicht überschreiben.

## Offene Prüfpunkte (Stand 18.09.2026)

- Live-Quellenprüfung aus der Entwicklungsumgebung nicht möglich: `www.kfw.de`, `www.bafa.de`, `www.bundesgesundheitsministerium.de` und weitere Fördergeber-Domains sind in der Netzwerkrichtlinie gesperrt (Stand 18.09.2026). Für die monatliche Prüfung diese Domains freigeben oder die Prüfung manuell im Browser durchführen und `checkedAt` setzen.

- BAFA Heizungsoptimierung und Emissionsminderung: Direktabruf war blockiert, Sätze und iSFP-Bonus unbestätigt.
- KfW 459: 30 % bestätigt, Kostenobergrenzen für Unternehmen fehlen für eine Betragsberechnung.
- § 35a EStG: begünstigte Kosten und Doppelbegünstigung vor Berechnung prüfen.
- progres.nrw Geothermie: Verfügbarkeit erneut prüfen.
- Kommunale Programme (Düren, Kreis Düren): nur nach belastbarer Quelle ergänzen.
