# Inhaltspflege

Alle Inhalte liegen als JSON in `content/`. Es gibt keinen Browser-Adminbereich. Pflege per Editor, dann `php bin/validate-content.php`, dann SFTP-Upload (atomar: erst `.tmp`, dann umbenennen). Beschädigte JSON-Dateien führen zu einer Fehlerseite ohne Detailausgabe, niemals zu einer scheinbar gültigen Seite.

## company.json (führende Faktenbasis)

Speist Header, Footer, Kontakt, Impressum, JSON-LD, KI-Seiten und die Bestätigungsmail. Regel aus dem Auftrag: genau zwei Geschäftsführer (Guido Bauer, David Enns) und genau zwei Holdings (Enns Holding GmbH seit 2024, Müller Holding AG seit 2026). Die Validierung erzwingt die Anzahl. Keine weiteren Namen, keine Wechselgeschichte, kein Hinweis auf Ausscheiden. `factsUpdated` nur bei echter Faktenänderung anpassen. `legal.*` erst nach interner Freigabe füllen; leere Felder blenden das Impressum-Element aus und zeigen einen Hinweis.

## pages.json

Ein Eintrag je Seite. Pflichtfelder: `path` (mit abschließendem `/`), `template`, `title`, `metaTitle` (max. 70 Zeichen), `metaDescription` (max. 175 Zeichen). Optional: `parent` (für Breadcrumbs), `h1`, `lead`, `sections` (`heading`, `paragraphs`, `list`), `steps`, `limits`, `faq`, `funding`, `related`, `hubChildren`, `form` (`contact`, `damage`, `application`, `none`), `formTopic`, `noindex`, `excludeFromSitemap`, `lastmod` (nur bei inhaltlicher Änderung). Neue Seiten brauchen kein Routing: das Register ist die Allowlist.

Templates: `home`, `hub`, `service`, `foerderung`, `foerderrechner`, `unternehmen`, `projekte`, `ratgeber`, `karriere`, `kontakt`, `ki-openai`, `ki-anthropic`, `impressum`, `legal`.

Themen im Kontaktformular (`formTopic`) müssen in der Allowlist in `app/controllers/FormController.php` und `app/views/components/form.php` stehen.

## jobs.json

`status: open|closed`, `type: job|initiative`, `datePosted` nur bei echter Neuveröffentlichung ändern, `validThrough` optional (JJJJ-MM-TT). Geschlossene Stellen bleiben mit Hinweis erreichbar, erhalten `noindex`, verschwinden aus Sitemap und Bewerbungsauswahl und verlieren die `JobPosting`-Auszeichnung automatisch. Die Initiativbewerbung erhält nie `JobPosting`. Arbeitsort und Beschäftigungsmodell sind vom Betrieb zu bestätigen (aktuell mit „zu bestätigen“ gekennzeichnet).

## projects.json

Nur `approved: true` wird veröffentlicht. Solange leer, ist `/projekte/` `noindex` und nicht in der Sitemap; die Startseite blendet den Block aus. Bildrechte in `asset-register.md` eintragen.

## guides.json

Ratgeberartikel mit `slug`, `title`, `metaDescription`, `published`, `updated`, `intro`, `sections`, optional `faq`, `related`, `formTopic`.

## redirects.json

`redirects` (301) und `gone` (410). Quelle darf keine aktive Seite sein; Ziel muss existieren (Validierung).

## Gesperrte Begriffe

`docs/forbidden-terms.txt` wird bei der Validierung gegen alle Inhalte und Templates geprüft (ohne Groß-/Kleinschreibung). Frühere Personen- oder Firmennamen dort eintragen, sobald der Betreiber sie benennt, damit sie nicht zurückkehren können.

## Abschlusskontrolle vor jeder Veröffentlichung

Alle Texte, Metadaten, JSON-LD, KI-Seiten, Sitemap, Dateinamen und Mailvorlagen gegen Abschnitt 2 des Auftrags prüfen: nur aktuelle Struktur, keine erfundenen Zahlen, Siegel, Bewertungen, Partnerschaften, Niederlassungen oder Reaktionszeiten.
