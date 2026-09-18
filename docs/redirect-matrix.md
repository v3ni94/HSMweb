# URL-/Redirect-Matrix (privat)

Quelle: `content/redirects.json`. Grundlage: `page-sitemap.xml` der Bestandswebsite (7 URLs, 18.09.2026) und im HTML verlinkte Pfade. Alle Bestandsseiten sind zugeordnet.

| Alte URL | Neue URL | Status | Quelle |
|---|---|---|---|
| `/` | `/` | 200 (bleibt) | Sitemap |
| `/leistungen/` | `/leistungen/` | 200 (bleibt) | Sitemap |
| `/ueber-uns/` | `/unternehmen/` | 301 | Sitemap |
| `/kontakt/` | `/kontakt/` | 200 (bleibt) | Sitemap |
| `/impressum/` | `/impressum/` | 200 (bleibt) | Sitemap |
| `/datenschutz/` | `/datenschutz/` | 200 (bleibt) | Sitemap |
| `/cookie-einstellungen/` | `/cookie-einstellungen/` | 200 (bleibt) | Sitemap |
| `/feed/` | `/ratgeber/` | 301 | Bestands-HTML |
| `/comments/feed/` | `/` | 301 | Bestands-HTML |
| `/wp-json/`, `/wp-login.php`, `/xmlrpc.php` | – | 410 | Bestands-HTML / WordPress |
| `/sitemap_index.xml`, `/page-sitemap.xml` | – | 410 (neue Sitemap: `/sitemap.xml`) | robots.txt alt |
| `/karriere/heizungsbauer/` | `/karriere/anlagenmechaniker-shk/` | 301 | Auftrag Abschnitt 11 |
| `/home/`, `/leistung/`, `/badsanierung/`, `/leckortung/`, `/jobs/`, `/datenschutzerklaerung/` | passende Seite | 301 | vorsorglich, nicht in Sitemap |
| Pfad ohne abschließenden `/` | Pfad mit `/` | 301 | automatisch |
| alles andere | – | 404 | eigene Fehlerseite |

Nach Go-live: neue `sitemap.xml` in der Search Console einreichen, alten Sitemap-Index entfernen (Vorbereitung, Zugang liegt beim Betreiber).
