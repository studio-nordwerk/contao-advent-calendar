#!/usr/bin/env bash
# Installiert Contao mit diesem Paket in Docker und prüft als anonymer Besucher, dass Türchen nur
# dort herauskommen, wo die Seite öffentlich ist. Aufruf: dev/check.sh 5.3   oder   dev/check.sh 6.0
# Prüft den Stand im Arbeitsverzeichnis. Stack danach stoppen: dev/check.sh 5.3 down
set -euo pipefail

CONTAO="${1:-5.3}"
cd "$(dirname "$0")"

case "$CONTAO" in
    5.3) PHP_VERSION=8.3 PORT=8141 ;;
    5.7) PHP_VERSION=8.3 PORT=8142 ;;
    6.0) PHP_VERSION=8.4 PORT=8143 ;;
    *) echo "Unbekannte Contao-Version: $CONTAO (5.3, 5.7 oder 6.0)"; exit 2 ;;
esac
export CONTAO PHP_VERSION PORT
dc() { docker compose -f compose.yaml "$@"; }

if [ "${2:-}" = down ]; then
    dc down
    exit 0
fi

APP="app-$CONTAO"
mkdir -p "$APP/public"
cat > "$APP/composer.json" <<JSON
{
    "type": "project",
    "require": {
        "contao/managed-edition": "~$CONTAO.0",
        "nordwerk/contao-advent-calendar-bundle": "@dev"
    },
    "repositories": [
        { "type": "path", "url": "../..", "options": { "symlink": true, "versions": { "nordwerk/contao-advent-calendar-bundle": "0.3.x-dev" } } }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true,
    "config": {
        "allow-plugins": { "contao-components/installer": true, "contao/manager-plugin": true, "php-http/discovery": true }
    },
    "extra": { "contao-component-dir": "assets" },
    "scripts": { "post-install-cmd": ["@php vendor/bin/contao-setup"], "post-update-cmd": ["@php vendor/bin/contao-setup"] }
}
JSON

dc down --remove-orphans >/dev/null 2>&1 || true
dc up -d --build --quiet-pull
dc exec -T php composer update --no-interaction --no-progress --quiet
# Frische Datenbank (liegt im RAM), also auch frische Dateien, sonst legt die Migration keinen Musterkalender an
dc exec -T php sh -c 'rm -rf files/adventskalender-muster'
dc exec -T php php bin/console contao:migrate --no-interaction --no-backup >/dev/null
dc exec -T db mariadb -ucontao -pcontao contao < seed.sql
dc exec -T php sh -c 'php bin/console cache:clear --no-warmup >/dev/null && chown -R www-data:www-data var assets files public 2>/dev/null; true'

BASE="http://127.0.0.1:$PORT"
failures=0
ok() { echo "✓ $1"; }
fail() { echo "✗ $1"; failures=$((failures + 1)); }

# Türchen 1 eines Elements abrufen: erwarteter Status, Text, der vorkommen muss, Text, der fehlen muss
door() {
    local id="$1" expect_status="$2" must="$3" mustnot="$4" label="$5" body status
    body=$(curl -s -w '\n%{http_code}' "$BASE/_advent_calendar/$id?day=1")
    status="${body##*$'\n'}"
    body="${body%$'\n'*}"

    if [ "$status" != "$expect_status" ]; then
        fail "$label: Status $status statt $expect_status"
    elif [ -n "$must" ] && [[ "$body" != *"$must"* ]]; then
        fail "$label: »$must« fehlt in $body"
    elif [ -n "$mustnot" ] && [[ "$body" == *"$mustnot"* ]]; then
        fail "$label: »$mustnot« ist zu sehen"
    else
        ok "$label"
    fi
}

echo "Contao $CONTAO, PHP $PHP_VERSION"
door 100 200 'GEHEIM-1' '' 'Öffentliche Seite: Türchen offen'
door 100 200 'Ihr Code:' 'Your code' 'Türchen in der Sprache der Website'
door 109 200 '"state":"early"' 'ZUKUNFT-1' 'Öffentliche Seite, Türchen noch nicht dran: kein Inhalt'
door 116 200 'GEHEIM-1' '' 'In einer Elementgruppe auf öffentlicher Seite: Türchen offen'
door 101 404 '' 'GEHEIM' 'Seite nur für Mitglieder: gesperrt'
door 112 404 '' 'GEHEIM' 'Unterseite einer Mitgliederseite, in Elementgruppe: gesperrt'
door 102 404 '' 'GEHEIM' 'Unveröffentlichte Seite: gesperrt'
door 103 404 '' 'GEHEIM' 'Unterseite einer unveröffentlichten Seite: gesperrt'
door 104 404 '' 'GEHEIM' 'Abgelaufene Seite: gesperrt'
door 105 404 '' 'GEHEIM' 'Unveröffentlichter Artikel: gesperrt'
door 106 404 '' 'GEHEIM' 'Artikel nur für Mitglieder: gesperrt'
door 107 404 '' 'GEHEIM' 'Verstecktes Element: gesperrt'
door 108 404 '' 'GEHEIM' 'Element nur für Mitglieder: gesperrt'
door 110 404 '' 'GEHEIM' 'Element außerhalb eines Artikels: gesperrt'
door 114 404 '' 'GEHEIM' 'In versteckter Elementgruppe: gesperrt'
door 99999 404 '' '' 'Unbekanntes Element: 404'

headers=$(curl -s -D - -o /dev/null "$BASE/_advent_calendar/100?day=1")
if grep -qi '^cache-control:.*no-store' <<<"$headers" && grep -qi '^cache-control:.*private' <<<"$headers"; then ok 'Antwort wird nicht zwischengespeichert'; else fail "Cache-Control fehlt: $headers"; fi

page=$(curl -s "$BASE/kalender")
[ -n "$page" ] || page=$(curl -s "$BASE/kalender.html")
if [[ "$page" == *'data-advent-calendar="/_advent_calendar/100"'* ]]; then ok 'Seite zeigt den Kalender'; else fail "Kalender fehlt auf der Seite: $(head -c 400 <<<"$page")"; fi
if [[ "$page" != *GEHEIM* ]]; then ok 'Kein Türchen-Inhalt im Quelltext'; else fail 'Gutscheincode steht im Quelltext'; fi
head_part="${page%%</head>*}"
if [[ "$head_part" == *'nordwerkadventcalendar/advent-calendar.css?v='* ]]; then ok 'Stylesheet im <head>'; else fail 'Stylesheet fehlt im <head>'; fi
if [[ "$page" == *'nordwerkadventcalendar/advent-calendar.js?v='* ]] && [[ "$head_part" != *'advent-calendar.js'* ]]; then ok 'Skript am Ende der Seite'; else fail 'Skript fehlt oder steht im <head>'; fi
if [ "$(grep -o 'advent-calendar.js?v=' <<<"$page" | wc -l | tr -d ' ')" = 1 ]; then ok 'Skript nur einmal, obwohl drei Kalender auf der Seite stehen'; else fail 'Skript mehrfach eingebunden'; fi
# Die Hinweise stehen als maskiertes HTML im Attribut; ein rohes < würde das Attribut zerbrechen.
early=$(grep -o 'data-text-early="[^"]*"' <<<"$page" | head -1)
decoded=$(python3 -c 'import html, sys; print(html.unescape(sys.stdin.read()))' <<<"$early")
if [[ "$early" == 'data-text-early="&'* ]] && [[ "$decoded" == *'<h2>Dieses Türchen öffnet sich am %s. Dezember.</h2>'* ]]; then ok 'Hinweistexte aus den Sprachdateien, sauber maskiert'; else fail "Hinweistext kaputt: $early"; fi
if [[ "$page" == *'aria-label="Türchen 1"'* ]]; then ok 'Türchen beschriftet (Türchen 1)'; else fail 'Beschriftung „Türchen 1“ fehlt'; fi
if [[ "$page" == *'data-timezone="Europe/Berlin"'* ]]; then ok 'Zeitzone der Website für die Markierung „heute“'; else fail 'data-timezone fehlt'; fi
status=$(curl -s -o /dev/null -w '%{http_code}' "$BASE/bundles/nordwerkadventcalendar/advent-calendar.js")
if [ "$status" = 200 ]; then ok 'Paketdateien unter bundles/nordwerkadventcalendar'; else fail "bundles/nordwerkadventcalendar/advent-calendar.js: $status"; fi

sample=$(dc exec -T db mariadb -ucontao -pcontao contao -N -e "SELECT COUNT(*) FROM tl_advent_calendar_door d JOIN tl_advent_calendar c ON c.id = d.pid WHERE c.title = 'Muster-Adventskalender'")
if [ "$sample" = 24 ]; then ok 'Musterkalender mit 24 Türchen angelegt'; else fail "Musterkalender: $sample Türchen"; fi

echo
if [ "$failures" -gt 0 ]; then
    echo "$failures Fehler. Logs: docker compose -f dev/compose.yaml logs php"
    exit 1
fi
echo "Alles in Ordnung (Contao $CONTAO)."
