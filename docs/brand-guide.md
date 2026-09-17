# Brand Guide (Arbeitsstand)

## Ausgangslage, ehrlich

Das Originallogo der HSM Tec GmbH und die exakten Bestandsfarbcodes lagen bei der Umsetzung nicht als verifizierte Assets vor. Die Bestandswebsite `https://hsm-tec.de/` war aus der Entwicklungsumgebung nicht erreichbar (Egress-Proxy, HTTP 403 auf alle Pfade am 17.09.2026). Deshalb:

- Die Website zeigt bis zur Freigabe den ausgeschriebenen Firmennamen als Text im Header. Das ist kein neues Logo.
- Das Favicon (`public/assets/brand/favicon.svg`) ist ein neutraler Platzhalter (Anthrazit-Quadrat mit „HSM“), kein Ersatzlogo, und ist vor Go-live durch eine freigegebene Variante zu ersetzen oder zu entfernen.
- Die Markenfarben `--brand` und `--accent` in `public/assets/css/site.css` sind vorläufige Arbeitswerte. Sie sind aus dem echten Logo abzuleiten und zu ersetzen. Es wird keine vorhandene Markenrichtlinie behauptet.

## Logo-Regeln nach Freigabe

- Originaldatei (bevorzugt SVG, sonst PNG/WebP in ausreichender Auflösung) unter `public/assets/brand/` ablegen und in `content/company.json` als `logoPath` eintragen. Damit erscheint es in Header und JSON-LD.
- Proportionen, Schriftzug und Farben unverändert. Keine Umfärbung, keine künstliche Negativversion. Auf dunklen Flächen erhält das Logo eine helle Fläche.
- Vor Verwendung prüfen, dass das Logo zur HSM Tec GmbH in Düren gehört und nicht zu einem ähnlich benannten Unternehmen. Herkunft in `asset-register.md` dokumentieren.

## Neutrale Ergänzungsfarben (Vorschlag, keine Bestands-CI)

| Token | Wert | Verwendung |
|---|---|---|
| `--white` | `#FFFFFF` | Seitenhintergrund |
| `--surface` | `#F5F7F8` | Flächen, Formulare, Callouts |
| `--ink` | `#17232D` | Fließtext, Footer |
| `--ink-soft` | `#3E4C57` | Sekundärtext |
| `--line` | `#E0E6EA` | Trennlinien, Rahmen |
| `--brand` (vorläufig) | `#0B5A8A` | Primärbuttons, Links, Akzente |
| `--brand-dark` (vorläufig) | `#08466B` | Hover |
| `--accent` (vorläufig) | `#C8571A` | Fokusring |

Kontraste (rechnerisch): Ink auf Weiß 14,6:1; Weiß auf Brand 7,4:1; Brand auf Weiß 7,4:1; Ink-Soft auf Surface 8,6:1. Nach Austausch der Markenfarbe alle Kombinationen erneut prüfen (Ziel WCAG 2.2 AA: 4,5:1 Text, 3:1 große Schrift und UI-Komponenten).

## Typografie und Raster

- Systemschrift (`system-ui`-Stack), keine externen Schriftaufrufe. Eine lizenzierte Schrift kann lokal eingebunden werden.
- Fließtext 17 bis 18 px (`clamp`), Überschriften fließend skalierend. Höchstens zwei bis drei Schnitte.
- Abstandsraster 4/8 px (`--space-1` bis `--space-8`), Inhaltsbreite 1.240 px, Textspalten bis 70 Zeichen.
- Radien 8/14 px, dezenter Schatten, Bedienelemente mindestens 44 px hoch, Fokus 3 px sichtbar.
- Bewegung minimal; `prefers-reduced-motion` deaktiviert Übergänge.

## Bilder

Nur echte, freigegebene Projekt- und Teamfotos. Ohne Porträts textbasierte Personenprofile (umgesetzt). Keine Stockbilder als eigene Projekte, keine Vorher-nachher-Montagen aus fremdem Material.
