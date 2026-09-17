#!/usr/bin/env bash
# HTTP-Smoke-Test gegen einen laufenden Server. Aufruf: tests/smoke.sh http://127.0.0.1:8080
set -u
B="${1:-http://127.0.0.1:8080}"
fail=0
check() { local exp="$1" url="$2"; local got; got=$(curl -s -o /dev/null -w "%{http_code}" "$B$url"); if [ "$got" != "$exp" ]; then echo "FAIL $url erwartet $exp, erhalten $got"; fail=1; else echo "ok   $got $url"; fi; }
# Alle Sitemap-URLs müssen 200 liefern
for u in $(curl -s "$B/sitemap.xml" | grep -o '<loc>[^<]*' | sed 's#<loc>https://hsm-tec.de##'); do check 200 "$u"; done
check 200 /robots.txt; check 200 /llms.txt; check 200 /csrf-token; check 200 /assets/data/funding-rules.json; check 200 /favicon.ico
check 404 /nix/; check 410 /danke/; check 301 /home/; check 301 /heizung; check 301 /karriere/heizungsbauer/
check 404 /config/site.php; check 404 /storage/logs/mail.log; check 404 /.git/HEAD; check 404 /composer.json; check 404 /content/company.json
check 302 /anfrage/senden/
check 200 /projekte/
# Projekte-Seite ist noindex und nicht in der Sitemap
curl -s "$B/sitemap.xml" | grep -q "/projekte/" && { echo "FAIL /projekte/ in Sitemap"; fail=1; }
curl -s "$B/projekte/" | grep -q 'name="robots" content="noindex' || { echo "FAIL /projekte/ ohne noindex"; fail=1; }
# Sicherheitsheader
curl -sI "$B/" | grep -qi "Content-Security-Policy" || { echo "FAIL CSP fehlt"; fail=1; }
curl -sI "$B/kontakt/" | grep -qi "Cache-Control: no-store" || { echo "FAIL Formularseite ohne no-store"; fail=1; }
# Kein Geheimnis im öffentlichen JSON
curl -s "$B/assets/data/funding-rules.json" | grep -q internalNotes && { echo "FAIL internalNotes öffentlich"; fail=1; }
[ $fail -eq 0 ] && echo "SMOKE OK" || echo "SMOKE FEHLGESCHLAGEN"
exit $fail
