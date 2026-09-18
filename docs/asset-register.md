# Asset-Register

| Asset | Pfad | Herkunft | Prüfung / Rechte | Status |
|---|---|---|---|---|
| Originallogo (Wortmarke „HSM Tec GmbH“ mit drei Streifen) | `public/assets/brand/hsm-tec-logo.png` (1360×455 PNG, transparent) | Bestandswebsite `wp-content/uploads/2023/07/logo.png`, identisch mit `themes/wp_basictheme1/img/logo.png`; gesichert 18.09.2026 | Zuordnung zur HSM Tec GmbH Düren über Impressum, Kontakt und JSON-LD (`caption: "HSM Tec GmbH"`) der Bestandsseite geprüft. Nutzungsrecht liegt beim Betreiber (eigenes Logo). Kein SVG verfügbar; SVG beim Betreiber oder der Agentur anfragen. | aktiv |
| Streifensymbol | `public/assets/brand/hsm-tec-symbol.png` (500×500) | Bestandswebsite `uploads/2023/06/favicon.png`, dort bereits als Favicon genutzt | Bestandteil des Originallogos, Nutzung als Favicon entspricht der Bestandspraxis | aktiv (Favicon, Apple Touch Icon) |
| favicon.ico | `public/favicon.ico` (16/32/48 px, PNG-Einträge) | aus dem Streifensymbol erzeugt (`imagecopyresampled`) | abgeleitet | aktiv |
| Apple Touch Icon | `public/assets/brand/apple-touch-icon.png` (180×180, weißer Hintergrund) | aus dem Streifensymbol erzeugt | abgeleitet | aktiv |
| Projektfotos | `content/projects.json` leer | keine freigegebenen Bilder | Freigabe der Auftraggeber erforderlich | offen, `/projekte/` noindex |
| Porträts Geschäftsführung | keine | nicht vorhanden | Freigabe der Personen erforderlich | textbasierte Profile aktiv |
| Systemschrift | Browser | keine Lizenz nötig | – | aktiv |

## Verworfen

| Asset | Grund |
|---|---|
| `themes/wp_basictheme1/img/imprint_logo.png` | Logo der Webagentur („Websolutions“), nicht HSM Tec |
| Unsplash-Fotos `r-architecture-P_0tnQ8hb70`, `r-architecture-QMo-jtdyAQU`, `sidekix-media-g51F6-WYzyU` | Stockbilder, Lizenz nicht dokumentiert, keine eigenen Projekte |
| Herstellerlogos Viessmann, Buderus, Junkers, Bosch, Weishaupt, Vaillant, GC Gruppe, `Unbenannt.png` | fremde Marken, keine belegte Partnerschaft |

Regel: Kein Hotlinking auf die alte Installation (alle Dateien lokal), keine kosmetische Umbenennung nicht freigegebener Medien, keine alten Backups unter öffentlichen Pfaden.
