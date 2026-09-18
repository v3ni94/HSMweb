# Brand Guide

## Herkunft

Grundlage ist das Originallogo der Bestandswebsite (`hsm-tec-logo.png`, gesichert 18.09.2026, siehe `asset-register.md`). Eine schriftliche Markenrichtlinie liegt nicht vor; dieses Dokument leitet die Werte aus der Logodatei ab und schlägt neutrale Ergänzungen vor.

## Logo

- Wortmarke „HSM“ in Schwarz mit vertikalem „Tec“, darunter drei versetzte Streifen in Rot, Cyan, Gelb und „GmbH“. Format 1360×455, Seitenverhältnis rund 3:1.
- Darstellung nur auf weißem oder sehr hellem Grund; im dunklen Header deshalb auf einer weißen Kachel (siehe Gestaltungsprinzipien). Im Footer steht der Firmenname als Text. Eine autorisierte Negativversion existiert nicht und wird nicht künstlich erzeugt.
- Keine Umfärbung, keine Verzerrung, Mindesthöhe 40 px im Header (40 px mobil, 48 px ab 960 px umgesetzt, Kachel 52 bzw. 60 px hoch).
- Symbolvariante (Streifen ohne Schriftzug) nur als Favicon und Touch-Icon, wie auf der Bestandsseite bereits praktiziert.
- Ein SVG liegt nicht vor. Bei der Agentur oder dem Betreiber anfragen; bis dahin PNG mit `width`/`height` zur Vermeidung von Layoutsprüngen.

## Farben aus dem Logo (gemessen)

| Token | Wert | Herkunft | Verwendung |
|---|---|---|---|
| `--logo-black` | `#000000` | Wortmarke | tiefste Stufe (`--bg-deep`): Hero, Fördercheck-Ergebnis, Footer; Druckausgabe |
| `--brand` | `#9C1325` | linker Streifen (Rot) | auf Dunkel nur als gefüllte Fläche mit weißem Inhalt; auf hellen Inseln zusätzlich Linkfarbe, Haken, Icon |
| `--brand-dark` | `#7E0F1E` | abgeleitet (dunkler) | Hover Primärbutton, Link-Hover auf Inseln |
| `--brand-tint` | `#F6E9EB` | abgeleitet (aufgehellt) | nur in Inseln: Icon-Kacheln, Checklisten-Kreise |
| `--logo-cyan` | `#00FFFF` | mittlerer Streifen | nur dekorativ: Streifenlinie, Eyebrow-Marker, Kartenoberkante, SVG-Motiv |
| `--logo-yellow` | `#FFFF00` | rechter Streifen | nur dekorativ, wie Cyan |

Cyan und Gelb werden nie für Text, Fokusringe, Ränder oder Haken verwendet. Rot erscheint auf dunklen Flächen nie als Text, Linie oder Fortschrittsfüllung (2,20:1 auf `#0E161D`, 1,93:1 auf `#17232D`).

## Neutrale Ergänzungen (Design v3)

| Token | Wert | Verwendung |
|---|---|---|
| `--bg` | `#0E161D` | Seitengrund, Header, Seitenkopf, Abschnitte |
| `--bg-deep` | `#000000` | Hero, Fördercheck-Ergebnisband, Footer |
| `--surface` | `#17232D` | erhabene Flächen: Karten, Panels, Akkordeon, Formularpanel, Faktenkarte, Bänder |
| `--line` | `rgba(255,255,255,.12)` | dekorative Linien und Ränder |
| `--line-strong`, `--ring` | `rgba(255,255,255,.45)` | Ränder mit 3:1-Pflicht (Ghost, Chip) und 1-px-Ring um rote Flächen |
| `--text` | `#E9EEF2` | Fließtext auf Dunkel |
| `--text-strong` | `#FFFFFF` | Überschriften, Labels, Links auf Dunkel |
| `--text-soft`, `--text-muted` | `#AEBBC5` | Nebentext, Eyebrow, Meta, Hinweise auf Dunkel |
| `--focus` | `#FFFFFF` auf Dunkel, `#17232D` in Inseln | 3-px-Fokusring mit 3 px Abstand |
| Insel `--bg` / `--surface` | `#F5F7F8` / `#FFFFFF` | helle Leseinseln (Klasse `island`) |
| Insel `--text` / `--text-strong` | `#17232D` / `#0E161D` | Text und Überschriften in Inseln |
| Insel `--text-soft` / `--text-muted` | `#3E4C57` / `#5B6975` | Sekundärtext und Hinweise in Inseln (ersetzt `#6B7A85` als Textfarbe) |
| Insel `--line` / `--line-strong` | `#E0E6EA` / `#6B7A85` | Linien und Ränder mit 3:1-Pflicht in Inseln |
| Felder | Grund `#FFFFFF`, Text `#17232D`, Rand `#6B7A85`, Platzhalter `#3E4C57`, Fokusrand `#9C1325` | alle Eingabefelder in beiden Welten, `color-scheme: light` |
| Funktionsfarben | `--ok #1F6B3A`, `--warn #8A4B00`, `--err #A3231A` | nur in Inseln: Status-Pills auf `#EAF5EE`, `#FFF6E5`, `#F3E9E8`, Notice-Linien |

## Gestaltungsprinzipien (Design v3, 18.09.2026)

- Drei Anthrazit-Stufen tragen die Seite: `#000000` für Hero, Fördercheck-Ergebnis und Footer, `#0E161D` als Seitengrund, `#17232D` für erhabene Flächen. Tiefe entsteht über diese Stufen und 1-px-Linien mit 12 Prozent Weiß, nicht über Schatten oder Verläufe. Der einzige Verlauf ist der rote radiale Lichtschein in Hero und Ergebnisband.
- Weiß ist die Aktions- und Ergebnisfarbe: Felder, Finder-Ergebnis, aktive Chips, Sekundärbutton, Fördercheck-Karte, Logo-Kachel.
- Rot nur als Fläche mit weißem Inhalt (Primärbutton, Nummernkreise, KPI, Hero-Karte Wasserschaden, Akkordeon-Icon offen, Fehler-Badge), auf Dunkel immer mit 1-px-Ring `rgba(255,255,255,.45)`, damit die Fläche selbst 3:1 erreicht. Nie als Text, Linie oder Fortschrittsfüllung auf Dunkel.
- Fokus überall sichtbar: 3-px-Ring, Weiß auf Dunkel, `#17232D` auf Hell; Felder zusätzlich mit rotem 2-px-Rand.
- Insel-Prinzip: lange Lektüre und Datentabellen liegen auf hellen Flächen (Klasse `island`), die über semantische Tokens umschalten. Inselflächen: Ratgeber-Artikelkörper, Datenschutz, Cookie-Einstellungen, Barrierefreiheit, Impressum, Programmtabelle der Förderseiten, Tabelle auf /ki/anthropic/, Finder-Ergebnis, Fördercheck-Karte, Notfall-, Fehler- und Erfolgshinweise sowie der noscript-Hinweis des Förderchecks. Jedes Band und jede Insel trägt die 4-px-Streifennaht an der Oberkante.
- Gelb und Cyan bleiben Dekor: Streifenlinien, Eyebrow-Marker, Kartenoberkanten, SVG-Motiv.
- Bestandskorrekturen: Feldrand `#6B7A85` statt `#C5CFD6`, Hinweistext auf Hell `#5B6975`, Sekundärtext auf Hell `#3E4C57`, `#6B7A85` nur noch als Rand.
- Logo-Kachel im Header: Das Original-PNG steht auf einer weißen Kachel mit 10 px Radius (Logohöhe 40 px, ab 960 px 48 px). Das ist eine Übergangslösung bis zur Freigabe einer Negativ- oder SVG-Version; es wird keine Negativversion selbst erzeugt. Rückfalloption ohne Codeänderung: Klasse `island` am Header ergibt den weißen Masthead.
- Interaktion: Header verdichtet sich beim Scrollen, Projektfinder als echte Tabs (Pfeiltasten, Pos1, Ende), Stellenzähler aus dem DOM, Timeline zeichnet Linie und Nummernkreise beim Sichtbarwerden, Lesefortschritt auf Ratgeber und Rechtstexten, Fördercheck mit Fortschrittstext und KPI-Werten ohne Zählanimation. Alle Animationen entfallen bei `prefers-reduced-motion`; ohne JavaScript ist der gesamte Inhalt sichtbar, `noscript.css` klappt die Hauptnavigation unter 960 px auf.
- Keine Inline-Styles und keine Inline-Skripte (Content-Security-Policy `style-src 'self'; script-src 'self'`), Systemschrift, `theme-color #0E161D`.

## Kontraste (nachgerechnet, WCAG 2.2 AA Ziel 4,5:1 Text, 3:1 UI)

Formel (L1+0,05)/(L2+0,05), sRGB-linearisiert, Overlays als Komposit über dem jeweiligen Grund.

| Kombination | Verhältnis |
|---|---|
| Fließtext `#E9EEF2` auf Seitengrund `#0E161D` | 15,62:1 |
| Fließtext `#E9EEF2` auf Fläche `#17232D` (Karten, Panels, Bänder) | 13,67:1 |
| Fließtext `#E9EEF2` auf Overlay 6 % über `#17232D` (`#25303A`, Chips, Ergebniskarten) | 11,50:1 |
| Fließtext `#E9EEF2` auf Notice 6 % über `#0E161D` (`#1C242B`) | 13,45:1 |
| Überschriften und Links `#FFFFFF` auf `#0E161D` | 18,24:1 |
| Überschriften `#FFFFFF` auf `#17232D` | 15,97:1 |
| Nebentext, Eyebrow, Meta `#AEBBC5` auf `#0E161D` | 9,31:1 |
| Nebentext `#AEBBC5` auf `#17232D` | 8,15:1 |
| Nebentext `#AEBBC5` auf Overlay 6 % über `#17232D` (`#25303A`) | 6,86:1 |
| Nebentext `#AEBBC5` auf card-muted 3 % über `#0E161D` (`#151D24`) | 8,69:1 |
| Kontakt-Kachel-Label `#AEBBC5` auf 6 % über `#0E161D` (`#1C242B`) | 8,02:1 |
| Footer Nebentext `#AEBBC5` auf `#000000` | 10,71:1 |
| Hero-Karte Nebentext `#AEBBC5` auf 6 % über `#000000` (`#0F0F0F`) | 9,78:1 |
| KPI-Label `#AEBBC5` auf 8 % über `#000000` (`#141414`) | 9,40:1 |
| KPI-Wert `#FFFFFF` auf 8 % über `#000000` (`#141414`) | 18,42:1 |
| Fließtext `#E9EEF2` auf Lichtschein-Maximum Rot 42 % über Schwarz (`#420810`) | 14,11:1 |
| H1 `#FFFFFF` auf Lichtschein-Maximum (`#420810`) | 16,49:1 |
| Primärbutton, Nummernkreis, KPI is-brand, Badge: `#FFFFFF` auf `#9C1325` | 8,29:1 |
| Primärbutton Hover `#FFFFFF` auf `#7E0F1E` | 10,64:1 |
| KPI is-brand Label `rgba(255,255,255,.85)` über Rot (`#F0DCDE`) auf `#9C1325` | 6,31:1 |
| Sekundärbutton, aktiver Chip, Logo-Kachel-Text: `#0E161D` auf `#FFFFFF` | 18,24:1 |
| Chip-Text `#FFFFFF` auf Chip 6 % über `#17232D` (`#25303A`) | 13,44:1 |
| Chip-Text `#FFFFFF` auf Chip 6 % über `#0E161D` (`#1C242B`, Karriere) | 15,71:1 |
| UI 3:1 Ghost- und Chip-Rand `rgba(255,255,255,.45)` über `#0E161D` (`#7A7F83`) gegen `#0E161D` | 4,51:1 |
| UI 3:1 Ghost-, Chip-Rand und Ring .45 über `#17232D` (`#7F868C`) gegen `#17232D` | 4,33:1 |
| UI 3:1 Ring .45 über `#000000` (`#737373`) gegen `#000000` (KPI im Ergebnisband) | 4,43:1 |
| UI 3:1 Fortschrittsfüllung `#FFFFFF` gegen Spur 14 % über `#17232D` (`#37424A`) | 10,29:1 |
| UI 3:1 Fokusring `#FFFFFF` gegen `#0E161D` | 18,24:1 |
| UI 3:1 Fokusring `#FFFFFF` gegen `#17232D` | 15,97:1 |
| UI 3:1 Fokusring `#FFFFFF` gegen Primärbutton `#9C1325` | 8,29:1 |
| UI 3:1 Nav-Balken aktiv `#FFFFFF` gegen Header `#0E161D` | 18,24:1 |
| UI 3:1 Logo-Kachel `#FFFFFF` gegen Header `#0E161D` | 18,24:1 |
| Feldtext `#17232D` auf Feld `#FFFFFF` | 15,97:1 |
| UI 3:1 weißes Feld gegen Formularpanel `#17232D` | 15,97:1 |
| UI 3:1 Feldrand `#6B7A85` gegen Feld `#FFFFFF` | 4,42:1 |
| Platzhalter `#3E4C57` auf `#FFFFFF` | 8,84:1 |
| UI 3:1 Fokusrand Rot `#9C1325` gegen Feldinneres `#FFFFFF` | 8,29:1 |
| Fehlerbox `#E9EEF2` auf Rot 28 % über `#17232D` (`#3C1F2B`) | 12,63:1 |
| Feldfehler `#FFFFFF` fett auf `#17232D` | 15,97:1 |
| Skip-Link `#0E161D` auf `#FFFFFF` | 18,24:1 |
| UI 3:1 Akkordeon-Icon rote Striche auf weißem Kreis | 8,29:1 |
| Ergebnis-Notice `#E9EEF2` auf 8 % über `#000000` (`#141414`) | 15,77:1 |
| Insel Fließtext `#17232D` auf `#F5F7F8` | 14,86:1 |
| Insel Fließtext `#17232D` auf `#FFFFFF` (Tabelle, Karte, Finder-Ergebnis) | 15,97:1 |
| Insel Überschrift `#0E161D` auf `#F5F7F8` | 16,98:1 |
| Insel Sekundärtext und Tabellenkopf `#3E4C57` auf `#F5F7F8` | 8,22:1 |
| Insel Sekundärtext `#3E4C57` auf `#FFFFFF` | 8,84:1 |
| Insel Hinweis und Meta `#5B6975` auf `#FFFFFF` | 5,64:1 |
| Insel Hinweis und Meta `#5B6975` auf `#F5F7F8` | 5,25:1 |
| Insel Link und Quellenlink `#9C1325` auf `#FFFFFF` | 8,29:1 |
| Insel Link `#9C1325` auf `#F5F7F8` | 7,71:1 |
| Insel Link Hover `#7E0F1E` auf `#FFFFFF` | 10,64:1 |
| UI 3:1 Insel Fokusring `#17232D` gegen `#F5F7F8` | 14,86:1 |
| UI 3:1 Insel Fokusring `#17232D` gegen `#FFFFFF` | 15,97:1 |
| UI 3:1 Insel Ghost-Rand `#6B7A85` gegen `#FFFFFF` | 4,42:1 |
| Insel Sekundärbutton `#FFFFFF` auf `#17232D` | 15,97:1 |
| Insel Status aktiv `#1F6B3A` auf `#EAF5EE` | 5,83:1 |
| Insel Status Prüfung erforderlich, angekündigt `#8A4B00` auf `#FFF6E5` | 6,34:1 |
| Insel Status derzeit geschlossen, ausgelaufen `#A3231A` auf `#F3E9E8` | 6,27:1 |
| Insel Notice-Text `#17232D` auf `#FFF6E5` (Notfall, Fehler, interner Hinweis) | 14,88:1 |
| Insel Notice-ok-Text `#17232D` auf `#EAF5EE` | 14,29:1 |
| Insel Tabellenzeile Hover `#17232D` auf `#ECF0F2` | 13,93:1 |
| UI 3:1 Insel Checklisten-Haken Rot `#9C1325` gegen Kreis `#F6E9EB` | 7,02:1 |
| Inselkante `#F5F7F8` gegen Seite `#0E161D` (informativ) | 16,98:1 |
| Ausgeschlossen: Rot `#9C1325` als Text auf `#0E161D` | 2,20:1 |
| Ausgeschlossen: Rot `#9C1325` als Text auf `#17232D` | 1,93:1 |
| Ausgeschlossen: rote Fläche ohne Ring gegen `#17232D` (UI 3:1), deshalb 1-px-Ring | 1,93:1 |
| Ausgeschlossen: `--ok #1F6B3A` auf `#17232D` | 2,45:1 |
| Ausgeschlossen: `--warn #8A4B00` auf `#17232D` | 2,35:1 |
| Ausgeschlossen: alter Feldrand `#C5CFD6` auf `#FFFFFF` (UI 3:1) | 1,58:1 |
| Ausgeschlossen: alter Hinweistext `#6B7A85` auf `#FFFFFF` (Text 4,5:1) | 4,42:1 |
| Ausgeschlossen: alter Hinweistext `#6B7A85` auf `#F5F7F8` | 4,12:1 |
| Ausgeschlossen: alter Chip-Rand 20 % über `#17232D` (UI 3:1) | 1,91:1 |
| Link-Unterstreichung `#AEBBC5` (2 px) auf `#0E161D`, auf `#17232D` 8,15:1 | 9,31:1 |
| Dekorativ, informativ: Cyan `#00FFFF` auf `#0E161D` | 14,55:1 |
| Dekorativ, informativ: Gelb `#FFFF00` auf `#0E161D` | 16,99:1 |

## Typografie und Raster

Systemschrift ohne externe Aufrufe, Fließtext 17 bis 18 px mit Zeilenhöhe 1,65 (Langtext in Inseln und auf Leistungsseiten 1,7), fließende Überschriften, 4/8-px-Raster, Inhaltsbreite 1.240 px, Textspalten bis 66 Zeichen, Radien 10/18/28 px, Bedienelemente mindestens 44 px (Chips 46, Buttons 48, Felder 50, Kontakt-Kacheln 64), `prefers-reduced-motion` respektiert.

## Bilder

Nur echte, freigegebene Projekt- und Teamfotos. Keine Stockbilder als eigene Projekte, keine Herstellerlogos ohne belegte Partnerschaft.
