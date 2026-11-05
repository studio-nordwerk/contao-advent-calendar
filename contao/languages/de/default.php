<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['MOD']['advent_calendar'] = ['Adventskalender', '24 Türchen mit Angeboten, Gutscheinen oder Geschichten pflegen'];
$GLOBALS['TL_LANG']['CTE']['advent_calendar'] = ['Adventskalender', 'Zeigt einen Adventskalender mit 24 Türchen.'];

$GLOBALS['TL_LANG']['tl_advent_calendar']['title'] = ['Bezeichnung', 'Nur für das Backend, z. B. „Adventskalender 2026“.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['year'] = ['Jahr', 'Leer lassen, dann gilt der Kalender jedes Jahr wieder.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['keepOpen'] = ['Vergangene Türchen bleiben offen', 'Ohne Haken lässt sich nur das heutige Türchen öffnen.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['shuffle'] = ['Türchen gemischt anordnen', 'Wie bei einem echten Kalender. Die Reihenfolge bleibt immer gleich.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['demoMode'] = ['Demo-Modus: bis 30. November alle Türchen offen', 'Zum Vorzeigen und Prüfen. Endet am 1. Dezember von selbst. Mit echten Gutscheincodes trotzdem vorher ausschalten, sonst sind die Codes bis dahin für alle sichtbar.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['backgroundType'] = ['Hintergrund', 'Der Weihnachtsstern ist im Paket enthalten. Für ein eigenes Bild „Eigenes Bild“ wählen.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['backgroundTypes'] = ['poinsettia' => 'Weihnachtsstern (mitgeliefert)', 'image' => 'Eigenes Bild', 'none' => 'Kein Bild'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['singleSRC'] = ['Hintergrundbild', 'Querformat, mindestens 1600 Pixel breit.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['snow'] = ['Schneefall', 'Leichter Schneefall über dem Kalender. Wer „Bewegung reduzieren“ eingestellt hat, sieht keinen.'];
$GLOBALS['TL_LANG']['tl_advent_calendar']['everyYear'] = 'jedes Jahr';
$GLOBALS['TL_LANG']['tl_advent_calendar']['title_legend'] = 'Kalender';
$GLOBALS['TL_LANG']['tl_advent_calendar']['rules_legend'] = 'Türchen';
$GLOBALS['TL_LANG']['tl_advent_calendar']['look_legend'] = 'Aussehen';
$GLOBALS['TL_LANG']['tl_advent_calendar']['edit'] = 'Kalender bearbeiten';
$GLOBALS['TL_LANG']['tl_advent_calendar']['children'] = 'Türchen bearbeiten';

$GLOBALS['TL_LANG']['tl_advent_calendar_door']['day'] = ['Tag', 'An welchem Dezembertag geht das Türchen auf?'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['kicker'] = ['Dachzeile', 'Kleine Zeile über der Überschrift, z. B. „Unser Tagesangebot“.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['headline'] = ['Überschrift', 'Z. B. „Rucksack 30 Liter“ oder „25 € Gutschein“.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['singleSRC'] = ['Bild', 'Optional. Am besten aus einem nicht öffentlichen Ordner, sonst lässt sich das Bild vorab über seine Adresse aufrufen. Die Bildgröße stellen Sie im Inhaltselement ein.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['text'] = ['Text', 'Zwei, drei Sätze reichen.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['price'] = ['Preis', 'Optional, z. B. „24,95 €“.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['oldPrice'] = ['Alter Preis', 'Optional, wird durchgestrichen angezeigt.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['couponCode'] = ['Gutscheincode', 'Optional. Besucher können ihn mit einem Klick kopieren.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['url'] = ['Link', 'Optional, z. B. zum Produkt oder zum Shop.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['target'] = ['In neuem Fenster öffnen', ''];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['linkTitle'] = ['Button-Text', 'Standard: „Zum Angebot“.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['published'] = ['Türchen anzeigen', 'Ohne Haken bleibt das Türchen leer.'];
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['door_legend'] = 'Türchen';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['content_legend'] = 'Inhalt';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['offer_legend'] = 'Angebot';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['link_legend'] = 'Button';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['publish_legend'] = 'Veröffentlichen';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['hiddenLabel'] = 'unsichtbar';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['dayTaken'] = 'Für Tag %s gibt es in diesem Kalender schon ein Türchen.';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['publicFolder'] = 'Türchen %s: Die Bilder liegen in einem öffentlichen Ordner (%s). Wer die Adresse kennt oder errät, sieht sie schon vor dem Tag. Sicherer: die Bilder in einen Ordner legen, der in der Dateiverwaltung nicht öffentlich ist.';
$GLOBALS['TL_LANG']['tl_advent_calendar_door']['toggle'] = 'Türchen anzeigen/verstecken';

$GLOBALS['TL_LANG']['tl_content']['adventCalendar'] = ['Kalender', 'Welcher Adventskalender soll angezeigt werden?'];
$GLOBALS['TL_LANG']['tl_content']['advent_calendar_legend'] = 'Adventskalender';

// Texte im Kalender (Vorlagen content_element/advent_calendar und advent_calendar/door)
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarlyKicker'] = 'Noch ein bisschen Geduld';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarly'] = 'Dieses Türchen öffnet sich am %s. Dezember.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEarlyText'] = 'Bis dahin: Schauen Sie gern ins heutige Türchen.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPastKicker'] = 'Schon vorbei';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPast'] = 'Dieses Türchen hat sich wieder geschlossen.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarPastText'] = 'Das heutige Türchen wartet auf Sie.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarEmpty'] = 'Hier ist heute leider nichts drin.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarError'] = 'Das hat gerade nicht geklappt.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarErrorText'] = 'Bitte versuchen Sie es gleich noch einmal.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDemo'] = 'Vorschau: Bis zum 30. November sind alle Türchen zum Ausprobieren geöffnet.';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDoor'] = 'Türchen %s';
$GLOBALS['TL_LANG']['MSC']['adventCalendarDialog'] = 'Türchen';
$GLOBALS['TL_LANG']['MSC']['adventCalendarClose'] = 'Schließen';
$GLOBALS['TL_LANG']['MSC']['adventCalendarCode'] = 'Ihr Code:';
$GLOBALS['TL_LANG']['MSC']['adventCalendarCopy'] = 'Code kopieren';
$GLOBALS['TL_LANG']['MSC']['adventCalendarButton'] = 'Zum Angebot';
