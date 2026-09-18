# Brand Guide

## Herkunft

Grundlage ist das Originallogo der Bestandswebsite (`hsm-tec-logo.png`, gesichert 18.09.2026, siehe `asset-register.md`). Eine schriftliche Markenrichtlinie liegt nicht vor; dieses Dokument leitet die Werte aus der Logodatei ab und schlägt neutrale Ergänzungen vor.

## Logo

- Wortmarke „HSM“ in Schwarz mit vertikalem „Tec“, darunter drei versetzte Streifen in Rot, Cyan, Gelb und „GmbH“. Format 1360×455, Seitenverhältnis rund 3:1.
- Darstellung nur auf weißem oder sehr hellem Grund; auf dunklen Flächen (Footer) wird es nicht eingesetzt, dort steht der Firmenname als Text. Eine autorisierte Negativversion existiert nicht und wird nicht künstlich erzeugt.
- Keine Umfärbung, keine Verzerrung, Mindesthöhe 40 px im Header (44 px mobil, 52 px Desktop umgesetzt).
- Symbolvariante (Streifen ohne Schriftzug) nur als Favicon und Touch-Icon, wie auf der Bestandsseite bereits praktiziert.
- Ein SVG liegt nicht vor. Bei der Agentur oder dem Betreiber anfragen; bis dahin PNG mit `width`/`height` zur Vermeidung von Layoutsprüngen.

## Farben aus dem Logo (gemessen)

| Token | Wert | Herkunft | Verwendung |
|---|---|---|---|
| `--logo-black` | `#000000` | Wortmarke | Reserve; Fließtext nutzt Anthrazit |
| `--brand` | `#9C1325` | linker Streifen (Rot) | Primärbuttons, Links, aktive Zustände |
| `--brand-dark` | `#7E0F1E` | abgeleitet (dunkler) | Hover |
| `--brand-tint` | `#F6E9EB` | abgeleitet (aufgehellt) | aktive Navigation, Flächen |
| `--logo-cyan` | `#00FFFF` | mittlerer Streifen | nur dekorativ (Streifenlinie unter dem Header) |
| `--logo-yellow` | `#FFFF00` | rechter Streifen | nur dekorativ |

Cyan und Gelb erreichen auf Weiß keinen ausreichenden Kontrast und werden deshalb nie für Text, Buttons oder Fokusringe verwendet.

## Neutrale Ergänzungen (Vorschlag, keine Bestands-CI)

| Token | Wert | Verwendung |
|---|---|---|
| `--white` | `#FFFFFF` | Seitenhintergrund |
| `--surface` | `#F5F7F8` | Flächen, Formulare, Callouts |
| `--ink` | `#17232D` | Fließtext, Footer |
| `--ink-soft` | `#3E4C57` | Sekundärtext |
| `--line` | `#E0E6EA` | Trennlinien |
| `--accent` | `#17232D` | Fokusring |

## Kontraste (rechnerisch, WCAG 2.2 AA Ziel 4,5:1 Text, 3:1 UI)

| Kombination | Verhältnis |
|---|---|
| Ink `#17232D` auf Weiß | 14,6:1 |
| Weiß auf Brand `#9C1325` | 8,3:1 |
| Brand auf Weiß (Links) | 8,3:1 |
| Brand-dark auf Brand-tint | 9,4:1 |
| Ink-soft auf Surface | 8,6:1 |
| Fokusring `#17232D` auf Weiß | 14,6:1 |

## Typografie und Raster

Systemschrift ohne externe Aufrufe, Fließtext 17 bis 18 px, fließende Überschriften, 4/8-px-Raster, Inhaltsbreite 1.240 px, Textspalten bis 70 Zeichen, Radien 8/14 px, Bedienelemente mindestens 44 px, `prefers-reduced-motion` respektiert.

## Bilder

Nur echte, freigegebene Projekt- und Teamfotos. Keine Stockbilder als eigene Projekte, keine Herstellerlogos ohne belegte Partnerschaft.
