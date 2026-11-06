# Adventskalender für Contao

24 Türchen auf der eigenen Webseite – mit Tagesangeboten, Gutscheinen, Rezepten oder kleinen Geschichten.

- **Backend „Inhalte → Adventskalender“:** Kalender anlegen, darin bis zu 24 Türchen mit Dachzeile, Überschrift, Bild, Text, Preis (mit altem Preis), Gutscheincode und Button.
- **Inhaltselement „Adventskalender“:** zeigt die Türchen auf einer Seite, gemischt oder der Reihe nach, Türchen 24 doppelt so groß. Ein Klick öffnet das Türchen in einem Fenster.
- **Kein Mogeln:** Der Inhalt eines Türchens steht nicht im Quelltext. Er wird beim Klick vom Server geholt, und der Server prüft das Datum. Wer die Uhr am Rechner verstellt, sieht trotzdem nichts vorab.
- **Vergangene Türchen** bleiben offen oder gehen wieder zu (einstellbar).
- **Jahr leer lassen:** Dann gilt der Kalender jedes Jahr wieder.
- **Demo-Modus:** bis zum 30. November alle Türchen offen, zum Vorzeigen und Prüfen. Am 1. Dezember endet er von selbst, wer das Ausschalten vergisst, verrät also keine Türchen. Geöffnete Türchen werden dabei nicht gemerkt, nach dem Neuladen ist alles wieder zu.
- **Geöffnete Türchen** zeigen hinter der Tür ein kleines Bild des Inhalts und bleiben beim nächsten Besuch offen (im Browser des Besuchers gemerkt).
- **Vorschau:** In der Contao-Vorschau (Backend angemeldet) sind alle Türchen offen.
- Ohne Cookies, ohne Tracking, ohne jQuery. Leichter Schneefall, der bei „Bewegung reduzieren“ ausbleibt.

## Voraussetzungen

Contao 5.3 bis 6.0 (getestet mit 5.3, 5.7 und 6.0), PHP 8.2 oder neuer (Contao 6 braucht PHP 8.4).

## Installation

1. Im Contao Manager auf „Pakete hochladen“ klicken und die ZIP-Datei auswählen.
2. „Änderungen anwenden“, danach die Datenbank aktualisieren.
3. Im Backend unter „Inhalte → Adventskalender“ einen Kalender anlegen und die Türchen eintragen.
4. Auf einer Seite das Inhaltselement „Adventskalender“ einfügen und den Kalender auswählen. Das Element gehört in einen Artikel, in Nachrichten oder Events liefert es keine Türchen aus.

Der Paketname ist `nordwerk/contao-advent-calendar-bundle`.

### Umstieg von Version 0.2

Bis 0.2 hieß das Paket `heike-wiese/contao-advent-calendar-bundle`. Vor dem Update das alte Paket im Contao Manager entfernen, dann das neue installieren und die Datenbank aktualisieren. Kalender und Türchen bleiben erhalten, die Tabellen sind dieselben. Eigene Stylesheets, die auf `bundles/heikewiesecontaoadventcalendar/…` verweisen, auf `bundles/nordwerkadventcalendar/…` umstellen.

## Wer sieht die Türchen?

- Ein Türchen kommt erst an seinem Tag vom Server, vorher steht es weder im Quelltext noch in einer Datei der Seite.
- Es gelten dieselben Regeln wie für die Seite: Ist die Seite, der Artikel oder das Element unveröffentlicht, abgelaufen oder nur für Mitglieder, bekommen andere Besucher auch keine Türchen. Das gilt für Gutscheincodes ebenso wie für Texte und Bilder.
- **Bilder in einen nicht öffentlichen Ordner legen.** Liegt ein Türchenbild in einem öffentlichen Ordner (Dateiverwaltung → „Öffentlich“), lässt es sich über seine Adresse schon vorab aufrufen. Die Türchenliste im Backend warnt, wenn das so ist. Aus nicht öffentlichen Ordnern erzeugt Contao eigene, nicht erratbare Bildadressen.

## Musterkalender

Bei der ersten Installation legt das Paket einen **Muster-Adventskalender** mit 24 Beispiel-Türchen an: Seifen, Düfte, Pflege für Männer, Gutscheine und Tipps, acht davon mit Bild (Ordner `files/adventskalender-muster`, nicht öffentlich). Der Demo-Modus ist eingeschaltet, so lässt sich bis zum 30. November alles sofort ausprobieren; ab dem 1. Dezember gilt das Datum. Texte, Bilder und Links einfach überschreiben – oder den Kalender löschen und einen eigenen anlegen. Gibt es beim Installieren schon einen Kalender, wird kein Musterkalender angelegt.

## Gestaltung

Farben und Formen über CSS-Variablen anpassen, z. B. im eigenen Stylesheet:

```css
.advent-calendar {
    --ac-accent: #458019;          /* Akzentfarbe: heutiges Türchen, Preise, Button */
    --ac-door-bg: #fffdf8;         /* Türchen */
    --ac-door-text: #3a342b;
    --ac-radius: 1rem;
    --ac-door-size: 7rem;          /* Mindestgröße eines Türchens */
    --ac-number-font: "Fraunces", serif;
}
```

### Hintergrund

Unter „Kalender bearbeiten → Aussehen → Hintergrund“ steht die mitgelieferte **Weihnachtsstern**, ein **eigenes Bild** oder **kein Bild** zur Wahl. Für eigene Bilder helfen diese Variablen:

```css
.advent-calendar {
    --ac-top-space: 8rem;              /* freier Streifen über den Türchen, z. B. für ein Motiv oben */
    --ac-image-position: center top;   /* Bildausschnitt */
    --ac-image-height: 100%;           /* Bild nur oben, z. B. min(100%, 34rem) */
    --ac-image-fill: #490709;          /* Farbe unter dem Bild */
    --ac-image-mask: linear-gradient(to bottom, #000 55%, transparent); /* weicher Übergang */
}
```

Die Templates `content_element/advent_calendar` (Kalender) und `advent_calendar/door` (Inhalt eines Türchens) lassen sich im Template Studio überschreiben, einzeln über ihre Blöcke (`grid`, `dialog`, `texts`, `headline`, `coupon`, `action` usw.).

### Texte

Alle Texte im Kalender stehen in den Sprachdateien (Deutsch und Englisch) unter `MSC.adventCalendar…`, z. B. „Noch ein bisschen Geduld“ oder „Ihr Code:“. Ändern lassen sie sich wie jeder Contao-Text in `contao/languages/de/default.php` der eigenen Installation:

```php
$GLOBALS['TL_LANG']['MSC']['adventCalendarButton'] = 'Jetzt sichern';
```

## Entwicklung

```bash
php tests/DoorRulesTest.php     # wann geht welches Türchen auf
php tests/DoorAccessTest.php    # wer darf ein Türchen abrufen
dev/check.sh 5.3                # Contao 5.3 in Docker installieren und als Besucher prüfen (auch 5.7, 6.0)
dev/check.sh 5.3 down           # Testinstallation stoppen
ARTIFACT=0.3.0 dev/check.sh 5.3  # die gebaute ZIP aus dist/ installieren und prüfen
```

Die ZIP für den Contao Manager baut `python3 scripts/build-artifact.py 0.3.0 dist/contao-advent-calendar-bundle-0.3.0.zip` aus dem letzten Commit.

`dev/check.sh` legt öffentliche, geschützte und unveröffentlichte Seiten an und prüft, dass Türchen nur dort herauskommen, wo die Seite öffentlich ist.

## Bildnachweis

`public/hintergrund-weihnachtsstern.jpg` (Weihnachtsstern) und die Beispielbilder in `muster/`: KI-generiert mit Gemini, für dieses Paket erstellt.

## Lizenz

MIT, siehe [LICENSE](LICENSE). Kostenlos nutzen, anpassen und weitergeben – der Hinweis „© Nordwerk“ bleibt dabei erhalten.
© 2026 Nordwerk, [nordwerk.studio](https://nordwerk.studio)
