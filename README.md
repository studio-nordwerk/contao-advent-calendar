# Adventskalender für Contao

24 Türchen auf der eigenen Webseite – mit Tagesangeboten, Gutscheinen, Rezepten oder kleinen Geschichten.

- **Backend „Inhalte → Adventskalender“:** Kalender anlegen, darin bis zu 24 Türchen mit Dachzeile, Überschrift, Bild, Text, Preis (mit altem Preis), Gutscheincode und Button.
- **Inhaltselement „Adventskalender“:** zeigt die Türchen auf einer Seite, gemischt oder der Reihe nach, Türchen 24 doppelt so groß. Ein Klick öffnet das Türchen in einem Fenster.
- **Kein Mogeln:** Der Inhalt eines Türchens steht nicht im Quelltext. Er wird beim Klick vom Server geholt, und der Server prüft das Datum. Wer die Uhr am Rechner verstellt, sieht trotzdem nichts vorab.
- **Vergangene Türchen** bleiben offen oder gehen wieder zu (einstellbar).
- **Jahr leer lassen:** Dann gilt der Kalender jedes Jahr wieder.
- **Demo-Modus:** alle Türchen offen, zum Vorzeigen vor dem 1. Dezember. Geöffnete Türchen werden dabei nicht gemerkt, nach dem Neuladen ist alles wieder zu.
- **Geöffnete Türchen** zeigen hinter der Tür ein kleines Bild des Inhalts und bleiben beim nächsten Besuch offen (im Browser des Besuchers gemerkt).
- **Vorschau:** In der Contao-Vorschau (Backend angemeldet) sind alle Türchen offen.
- Ohne Cookies, ohne Tracking, ohne jQuery. Leichter Schneefall, der bei „Bewegung reduzieren“ ausbleibt.

## Voraussetzungen

Contao 5.3 oder neuer, PHP 8.2 oder neuer.

## Installation

1. Im Contao Manager auf „Pakete hochladen“ klicken und die ZIP-Datei auswählen.
2. „Änderungen anwenden“, danach die Datenbank aktualisieren.
3. Im Backend unter „Inhalte → Adventskalender“ einen Kalender anlegen und die Türchen eintragen.
4. Auf einer Seite das Inhaltselement „Adventskalender“ einfügen und den Kalender auswählen.

## Musterkalender

Bei der ersten Installation legt das Paket einen **Muster-Adventskalender** mit 24 Beispiel-Türchen an: Seifen, Düfte, Pflege für Männer, Gutscheine und Tipps, acht davon mit Bild (Ordner `files/adventskalender-muster`). Der Demo-Modus ist eingeschaltet, so lässt sich alles sofort ausprobieren. Texte, Bilder und Links einfach überschreiben – oder den Kalender löschen und einen eigenen anlegen. Gibt es beim Installieren schon einen Kalender, wird kein Musterkalender angelegt.

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

Die Templates `content_element/advent_calendar` (Kalender und Hinweistexte) und `advent_calendar/door` (Inhalt eines Türchens) lassen sich im Template Studio überschreiben.

## Bildnachweis

`public/hintergrund-weihnachtsstern.jpg` (Weihnachtsstern) und die Beispielbilder in `muster/`: KI-generiert mit Gemini, für dieses Paket erstellt.

## Lizenz

MIT, siehe [LICENSE](LICENSE). Kostenlos nutzen, anpassen und weitergeben – der Hinweis „© Nordwerk“ bleibt dabei erhalten.
© 2026 Nordwerk, [nordwerk.studio](https://nordwerk.studio)
