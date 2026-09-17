# Bestandsaufnahme der Bestandswebsite (Protokoll)

Datum: 17.09.2026, Entwicklungsumgebung (Remote-Container mit Egress-Proxy).

## Ergebnis

Alle Abrufe von `https://hsm-tec.de/` sind fehlgeschlagen:

| URL | Ergebnis |
|---|---|
| `https://hsm-tec.de/` | Proxy: `CONNECT tunnel failed, response 403` |
| `https://hsm-tec.de/page-sitemap.xml` | 403 (Proxy) |
| `https://hsm-tec.de/sitemap.xml`, `/sitemap_index.xml` | 403 (Proxy) |
| `https://hsm-tec.de/kontakt/`, `/impressum/`, `/robots.txt` | 403 (Proxy) |

Die Sperre stammt vom Netzwerk-Egress der Entwicklungsumgebung („Access to hsm-tec.de is blocked by the network egress proxy“), nicht von der Bestandswebsite. Eine nicht abrufbare Sitemap ist kein Beweis, dass sie fehlt.

## Konsequenzen

- URL-, Themen- und Medieninventar der alten Website konnte nicht erhoben werden. `content/redirects.json` enthält deshalb typische WordPress-Pfade als **Annahmen**, jede Zeile ist mit „prüfen“ markiert.
- Logo und Bestandsfarben konnten nicht gesichert werden (siehe `brand-guide.md`, `asset-register.md`).
- Leistungsbeschreibungen wurden aus dem Auftrag (Abschnitte 5 bis 7) und den Auftraggebervorgaben erstellt, nicht aus alten Seitentexten. Dadurch ist sichergestellt, dass keine überholten Personen- oder Unternehmensbezüge übernommen wurden.

## Vor Go-live nachzuholen

1. Aus einer Umgebung mit Zugriff: `page-sitemap.xml`, ggf. Sitemap-Index, alle Seiten, Medienbibliothek und Logo-Pfad (HTML/CSS) inventarisieren.
2. Jede alte URL in `content/redirects.json` einem neuen Ziel zuordnen (301), ohne Ersatz 410 (`gone`), Rest 404. Keine pauschale Weiterleitung auf die Startseite.
3. Alte Medien nur nach Rechteprüfung übernehmen; keine Archivpfade öffentlich lassen.
4. Bestehende Produktion erst nach Betreiberfreigabe ersetzen.
