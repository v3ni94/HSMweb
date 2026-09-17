# URL-/Redirect-Matrix (privat)

Quelle: `content/redirects.json`. Status siehe `inventory.md`: die alten Pfade sind bis zur Sichtung der echten `page-sitemap.xml` Annahmen.

| Alte URL (Annahme) | Neue URL | Status | Anmerkung |
|---|---|---|---|
| `/home/`, `/startseite/` | `/` | 301 | prüfen |
| `/leistung/` | `/leistungen/` | 301 | prüfen |
| `/heizungsbau/`, `/heizungstechnik/` | `/heizung/` | 301 | prüfen |
| `/waermepumpe/` | `/heizung/waermepumpen/` | 301 | prüfen |
| `/sanitaertechnik/` | `/sanitaer/` | 301 | prüfen |
| `/bad/`, `/badsanierung/` | `/sanitaer/badsanierung/` | 301 | prüfen |
| `/leckortung/` | `/wasserschaden/leckortung/` | 301 | prüfen |
| `/trocknung/`, `/bautrocknung/` | `/wasserschaden/technische-trocknung/` | 301 | prüfen |
| `/renovierung/` | `/sanierung/` | 301 | prüfen |
| `/ueber-uns/`, `/team/` | `/unternehmen/` | 301 | prüfen |
| `/jobs/`, `/stellenangebote/` | `/karriere/` | 301 | prüfen |
| `/karriere/heizungsbauer/` | `/karriere/anlagenmechaniker-shk/` | 301 | gemeinsame Anzeige (Abschnitt 11) |
| `/datenschutzerklaerung/` | `/datenschutz/` | 301 | prüfen |
| `/danke/` | – | 410 | alte Dankeseite ohne Ersatz |
| Pfad ohne abschließenden `/` | Pfad mit `/` | 301 | automatisch, wenn Zielseite existiert |
| alles andere | – | 404 | eigene Fehlerseite |

Neue Seitenstruktur: siehe `content/pages.json` (43 redaktionelle Seiten) plus 8 Stellenseiten und 3 Ratgeber. Sitemap: `/sitemap.xml` (53 URLs, ohne `/projekte/` solange leer, ohne Formularendpunkte und Ergebnisseiten).
