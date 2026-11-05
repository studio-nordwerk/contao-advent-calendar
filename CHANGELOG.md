# Changelog

## 0.3.0 – 05.11.2026

- Sicherheit: Türchen kommen nur noch heraus, wenn der Besucher die Seite mit dem Kalender sehen darf. Vorher ließen sich Türchen samt Gutscheincodes auch von unveröffentlichten Seiten und Seiten nur für Mitglieder abrufen. Geprüft werden Element, Artikel und alle Seiten darüber (veröffentlicht, Zeitraum, Mitgliedergruppen).
- Neuer Paketname `nordwerk/contao-advent-calendar-bundle` wie bei den anderen Nordwerk-Paketen. Vor dem Update das alte Paket entfernen, Kalender und Türchen bleiben erhalten (siehe README).
- Demo-Modus endet am 1. Dezember von selbst. Wer vergisst, ihn auszuschalten, verrät im Dezember keine Türchen.
- Backend: Die Türchenliste warnt, wenn Bilder in einem öffentlichen Ordner liegen und so vorab aufrufbar sind.
- Texte im Kalender stehen jetzt in den Sprachdateien (Deutsch und Englisch) und lassen sich dort ändern. Türchen sprechen die Sprache der Website.
- Templates mit Blöcken zum gezielten Überschreiben. Stylesheet und Skript kommen über `{% add %}` in die Seite.
- Die Markierung „heute“ richtet sich nach der Zeitzone der Website statt nach der Uhr des Besuchers.
- Ändert man einen Kalender, leert Contao den Seiten-Cache.
- Musterkalender: Bricht das Anlegen ab, wird es beim nächsten Datenbank-Update wiederholt.
- Getestet mit Contao 5.3, 5.7 und 6.0.

## 0.2.0 – 04.11.2026

- Neu: Bei der ersten Installation entsteht ein „Muster-Adventskalender“ mit 24 Beispiel-Türchen (Seifen, Düfte, Pflege für Männer, Gutscheine, Tipps) und 8 Beispielbildern (KI-generiert mit Gemini) in files/adventskalender-muster. Demo-Modus ist an. Gibt es schon einen Kalender, wird nichts angelegt.
- Türchen-Bilder ohne eingestellte Bildgröße werden auf 1200 px verkleinert. Vorher fehlte das große Bild, wenn der Bilderordner nicht öffentlich war.
- Backend: Ein Kalender ohne Jahr („jedes Jahr“) ließ sich nicht speichern, weil das Feld „0“ zeigte und „mindestens 2020“ verlangte. Das Feld bleibt jetzt leer, und die Kalenderliste zeigt „jedes Jahr“ statt „[0]“.
- Lizenz: jetzt MIT (Open Source), wie Nordwerks Widerrufs-Paket.
- Neu: Auswahl „Hintergrund“ unter „Kalender bearbeiten“ mit „Weihnachtsstern (mitgeliefert)“, „Eigenes Bild“ und „Kein Bild“. Neue Kalender starten mit dem Weihnachtsstern, das Bild ist im Paket enthalten (KI-generiert mit Gemini).
- Bestehende Kalender behalten beim Datenbank-Update ihr Aussehen: mit Bild „Eigenes Bild“, sonst „Kein Bild“.
- CSS: neue Variablen für Hintergrundbilder (`--ac-top-space`, `--ac-image-position`, `--ac-image-height`, `--ac-image-fill`, `--ac-image-mask`), Standard wie bisher.

## 0.1.4 – 31.10.2026

- Backend: In der Kalenderliste steht „Türchen bearbeiten“ jetzt als erster, sichtbarer Knopf; ein Klick auf die Zeile öffnet ebenfalls die Türchen. „Kalender bearbeiten“ (Einstellungen, Hintergrundbild) bleibt als zweiter Knopf sichtbar. Vorher steckte „Türchen bearbeiten“ in Contao 5.7 im „…“-Menü.

## 0.1.3 – 28.10.2026

- Lizenz und Autor auf Nordwerk umgestellt.

## 0.1.2 – 28.10.2026

- Versionsnummer an CSS und JavaScript, damit Updates sofort im Browser ankommen.

## 0.1.1 – 28.10.2026

- Bild hinter geöffneten Türchen; im Demo-Modus merkt sich der Kalender nichts im Browser.

## 0.1.0 – 28.10.2026

- Erste Version: 24 Türchen mit Angeboten, Gutscheinen oder Geschichten; der Server prüft das Datum.
