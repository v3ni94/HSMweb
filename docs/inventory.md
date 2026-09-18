# Bestandsaufnahme der Bestandswebsite

## Protokoll

- 17.09.2026: Alle Abrufe von `https://hsm-tec.de/` scheiterten am Egress-Proxy der Entwicklungsumgebung (HTTP 403 auf `CONNECT`). Die Sperre lag nicht bei der Bestandswebsite.
- 18.09.2026: Nach Freigabe der Domain in der Netzwerkrichtlinie vollständige Aufnahme durchgeführt. Alle Angaben unten stammen von diesem Abruf.

## Technik der Bestandsseite

WordPress (Generator-Angabe 7.1.1), Theme `wp_basictheme1`, Plugins Yoast SEO (Sitemaps), Contact Form 7, Real Cookie Banner. `robots.txt` erlaubt alles und verweist auf `sitemap_index.xml`.

## URL-Inventar (page-sitemap.xml, 7 URLs; post-sitemap.xml leer)

| Alte URL | lastmod | Inhalt | Neue Zuordnung |
|---|---|---|---|
| `/` | 2025-04-15 | Start: „Heizung · Sanitär · Messtechnik“, vier Kacheln (Badsanierung, Trinkwasseraufbereitung, Heizungswartung, Hydraulischer Abgleich), Kontaktblock | `/` (bleibt) |
| `/leistungen/` | 2024-11-30 | Rohrbrucharbeiten, Altbaumodernisierung, Badmodernisierung, Wartungsarbeiten, Prüfung Gasleitung, Reparaturen | `/leistungen/` (bleibt, neu strukturiert) |
| `/ueber-uns/` | 2024-11-30 | Kurztext, Gründungsjahr, Herstellerlogos | 301 → `/unternehmen/` |
| `/kontakt/` | 2024-11-30 | Formular (Betreff: Heizung, Sanitär, Messtechnik), Anschrift, Ansprechpartner | `/kontakt/` (bleibt) |
| `/impressum/` | 2023-07-10 | § 5 TMG, Anschrift, Vertretung, Kontakt, ODR-Link, Schlichtungshinweis | `/impressum/` (bleibt, § 5 DDG, ohne ODR-Link) |
| `/datenschutz/` | 2023-07-10 | Standardtext | `/datenschutz/` (bleibt, neu verfasst) |
| `/cookie-einstellungen/` | 2023-07-10 | Cookie-Banner-Seite | `/cookie-einstellungen/` (bleibt) |

Weitere im HTML verlinkte Pfade: `/feed/`, `/comments/feed/`, `/wp-json/` (Redirect bzw. 410, siehe `redirect-matrix.md`).

## Bestätigte Fakten aus dem Bestandsimpressum

- HSM Tec GmbH, Kreuzauer Straße 70, 52355 Düren, Telefon 02421 8899975, info@hsm-tec.de. Deckt sich mit den Auftraggebervorgaben.
- „Vertreten durch: Guido Bauer – Heizung und Sanitärmeister / David Enns – Geschäftsführer“. Die Meisterbezeichnung ist damit belegt, bleibt aber vor Veröffentlichung zu bestätigen.
- Keine Registerangaben, keine Umsatzsteuer-ID, keine Kammer im alten Impressum. Diese Lücken bestehen also auch im Bestand und sind für den Relaunch zu schließen.

## Inhalte, die ausdrücklich nicht übernommen werden (Abschnitt 2 des Auftrags)

- Footer- und Header-Bezüge „Ein Unternehmen der Eßer Unternehmensgruppe“, „Eßer Sanierungstechnik“, „Aquapro GmbH“, „Job Aktuell GmbH“. In `docs/forbidden-terms.txt` gesperrt.
- Ansprechpartner „Daniel Enns, Controlling / Strategische Planung und Assistenz der Geschäftsführung“ auf der Kontaktseite. Nicht Teil der aktuellen Geschäftsführungsdarstellung, gesperrt.
- Aussagen „rund um die Uhr für Sie da“, „garantieren wir“, „langjährige Erfahrung“, „Top Service“ (unbelegt, teils widersprüchlich zur Gründungsangabe).
- Herstellerlogos (Viessmann, Buderus, Junkers, Bosch, Weishaupt, Vaillant, Pogenwisch, GC Gruppe): keine belegte Partnerschaft, Markenrechte ungeklärt. Nicht übernommen.
- Drei Unsplash-Stockfotos (`r-architecture-*`, `sidekix-media-*`): Lizenz nicht dokumentiert, keine eigenen Projekte. Nicht übernommen.
- `imprint_logo.png` ist das Logo der Webagentur, nicht HSM Tec.

## Hinweise für die Redaktion (zu bestätigen, nicht veröffentlicht)

- Gründungsjahr laut Bestandsseite 2020. Kann nach Bestätigung auf `/unternehmen/` ergänzt werden; die Jahre 2024/2026 bleiben Zugehörigkeitsangaben.
- Leistung „Prüfung Gasleitung“ (Dichtheitsprüfung) und Betreff „Messtechnik“ im alten Formular: eigene Seite erst bei bestätigtem Angebot und Qualifikationsnachweis (Abschnitt 6 und 7).
- Social-Profile der Bestandsseite: Facebook `people/HSM-Tec-GmbH/100064073793889`, Instagram `hsmtecgmbh`. Nach Bestätigung der Inhaberschaft als `sameAs` und im Footer ergänzbar.
- Kontaktformular alt: Vorname, Name, Telefon, E-Mail, Betreff, Nachricht, Datenschutz. Neues Formular deckt dies ab.

## Medien

Gesichert und geprüft: `uploads/2023/07/logo.png` (1360×455, identisch mit Theme-Logo) → `public/assets/brand/hsm-tec-logo.png`; `uploads/2023/06/favicon.png` (500×500, Streifensymbol) → `public/assets/brand/hsm-tec-symbol.png`. Details in `asset-register.md`.
