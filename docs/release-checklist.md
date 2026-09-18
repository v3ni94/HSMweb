# Release-Checkliste

## Erledigt

- Mehrseitige PHP-Website ohne Datenbank: 43 redaktionelle Seiten, 8 Stellenseiten, 3 Ratgeber, Sitemap, robots.txt, llms.txt, Fehlerseiten, Redirect-Matrix.
- Wiederverwendbare Formulare (Kontakt, Schaden, Bewerbung) mit CSRF, Honeypot, Mindestzeit, dateibasiertem Rate Limit, Inhaltsprüfung von Uploads, PHPMailer-SMTP-Versand, Reply-To, Bestätigungsmail ohne Werbung, ehrlichem Fehlerverhalten.
- Regelbasierter Fördercheck mit zentralem Regelwerk, öffentlichem Export, datierten Regelständen, 30-Tage-Prüfregel, reiner Rechenfunktion und 30 Regressionstests.
- Projektfinder, FAQ-Akkordeons, Stellenfilter, Breadcrumbs, JSON-LD (LocalBusiness, Service, BreadcrumbList, FAQPage, JobPosting, Article) aus derselben Faktenbasis.
- KI-Informationsseiten `/ki/openai/` und `/ki/anthropic/`, Crawler-Regeln in robots.txt (Suche erlaubt, Training gesperrt bis zur Betreiberentscheidung).
- Bestandsaufnahme der alten Website am 18.09.2026 durchgeführt (7 Sitemap-URLs, Impressum, Medien). Originallogo und Streifensymbol gesichert und eingebunden, Markenfarben aus dem Logo gemessen, Redirect-Matrix auf reale Alt-URLs umgestellt, überholte Bezüge (Eßer-Gruppe, Schwestergesellschaften, weiterer Ansprechpartner) gesperrt.
- Dokumentation: README, Deployment, Brand Guide, Asset-Register, Bestandsaufnahme, Redirect-Matrix, Inhalts- und Förderpflege, Abnahmebericht.

## Getestet (lokal, siehe acceptance-report.md)

- HTTP-Status aller Routen, Redirects, Sperren privater Pfade, Sicherheitsheader.
- Formularsicherheit und Fehlerfälle, Upload-Abweisung, Rate Limit, Temp-Löschung.
- Fördercheck (Node-Tests und Browser), Layout mobil/desktop, Tastatur, keine JS-Fehler.

## Offen (nicht blockierend)

- Quellenprüfung KfW (458, 459, 455-B, 159, 358/359, 261) manuell im Browser nachholen; KfW-Seiten waren aus der Entwicklungsumgebung nicht erreichbar. BAFA, BMG, NRW.BANK, Bezirksregierung Arnsberg und EStG sind am 18.09.2026 geprüft.
- Realer SMTP-Test aller drei Formulare mit freigegebenem Empfänger nach Eintrag von `config/mail.local.php`; SPF/DKIM/DMARC prüfen.
- `.htaccess` auf dem Zielhosting testen (HTTPS-Redirect, Sperren); Alternative ohne eigenes DocumentRoot laut `deployment.md` testen.
- Logo als SVG bei Betreiber oder Agentur anfragen (derzeit PNG 1360×455, ausreichend).
- Social-Profile der Bestandsseite (Facebook, Instagram) nach Bestätigung der Inhaberschaft im Footer und als sameAs ergänzen.
- Gründungsjahr 2020 (laut Bestandsseite) nach Bestätigung auf /unternehmen/ ergänzen.
- Leistungen „Prüfung Gasleitung“ und „Messtechnik“ der Bestandsseite: eigene Seiten nur bei bestätigtem Angebot und Qualifikationsnachweis.
- Screenreader-Test, externe WCAG-2.2-Prüfung, Felddaten für Core Web Vitals nach Go-live.
- Suchmaschinen-Verifikation und Sitemap-Einreichung vorbereitet, nicht erfolgt (kein Zugang).
- Leckortungsverfahren, die tatsächlich verfügbar sind (Akustik, Thermografie, Endoskopie, Tracergas), können nach Bestätigung auf `/wasserschaden/leckortung/` benannt werden.
- Virenscanner für Uploads (`scanCommand`) einrichten oder Uploads bewusst ohne Scan betreiben; alternativ `upload.enabled = false`.
- Entscheidung des Betreibers zu GPTBot/ClaudeBot (Training) in robots.txt.
- Optional: Bewerbungs-Upload für Unterlagen aktiv lassen oder abschalten.

## Go-live-blockierend

1. **Impressum unvollständig**: Registergericht, Registernummer, Umsatzsteuer-ID, Kammer, Aufsichtsbehörde, Verantwortlicher für den Inhalt fehlen in `content/company.json`. Ohne diese Angaben keine Veröffentlichung.
2. **Anschrift und Berufsbezeichnung bestätigen**: Anschrift stimmt mit dem Bestandsimpressum überein; Bestandsimpressum nennt „Heizung und Sanitärmeister“. Beides anhand aktueller Unterlagen bestätigen.
3. **Logo- und Farbfreigabe**: Originallogo und Farbwerte stammen von der Bestandswebsite und sind eingebunden. Der Betreiber bestätigt, dass dies die gültige Fassung ist.
4. **SMTP-Zugangsdaten** und autorisierter Domain-Absender fehlen; ohne sie meldet jedes Formular ehrlich einen Fehler (503) mit Telefon/E-Mail.
5. **Datenschutzerklärung**: Hostinganbieter, Logspeicherdauer, E-Mail-Dienstleister, ggf. Datenschutzbeauftragter eintragen; rechtliche Prüfung durch den Betreiber bzw. seine Beratung.
6. **Geschäftszeiten, Einsatzregion, Bereitschaftsdienst** intern freigeben; bis dahin keine Aussagen dazu auf der Website.
7. **Stellen**: Arbeitsort, Beschäftigungsmodell und Aufgaben je Stelle vom Betrieb bestätigen (aktuell „zu bestätigen“ gekennzeichnet); nicht ausgeschriebene Stellen auf `closed` setzen.
8. **Betreiberfreigabe**: Keine Veröffentlichung und kein Überschreiben der Bestandswebsite ohne gesonderte Freigabe der Geschäftsführung.

Nach Go-live: eigene Caches (OPcache, Browser-Asset-Version) leeren; externe Suchmaschinen-Caches gelten dadurch nicht als bereinigt.
