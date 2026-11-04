<?php

declare(strict_types=1);

// Einfacher Test ohne Contao: php tests/DoorRulesTest.php

require __DIR__.'/../src/Calendar/DoorRules.php';

use HeikeWiese\ContaoAdventCalendarBundle\Calendar\DoorRules as R;

$failures = 0;

function check(string $name, mixed $actual, mixed $expected): void
{
    global $failures;

    if ($actual === $expected) {
        echo "✓ {$name}\n";

        return;
    }

    ++$failures;
    echo "✗ {$name}\n  erwartet: ".json_encode($expected, JSON_UNESCAPED_UNICODE)."\n  erhalten: ".json_encode($actual, JSON_UNESCAPED_UNICODE)."\n";
}

$tz = new DateTimeZone('Europe/Berlin');
$at = static fn (string $d): DateTimeImmutable => new DateTimeImmutable($d, $tz);

check('Ende Oktober: Türchen 1 noch zu', R::state(1, $at('2026-10-28 12:00'), 2026), R::EARLY);
check('1. Dezember kurz nach Mitternacht: Türchen 1 offen', R::state(1, $at('2026-12-01 00:01'), 2026), R::OPEN);
check('1. Dezember: Türchen 2 noch zu', R::state(2, $at('2026-12-01 23:59'), 2026), R::EARLY);
check('10. Dezember: Türchen 3 bleibt offen', R::state(3, $at('2026-12-10'), 2026, true), R::OPEN);
check('10. Dezember: Türchen 3 wieder zu, wenn so eingestellt', R::state(3, $at('2026-12-10'), 2026, false), R::PAST);
check('10. Dezember: heutiges Türchen offen, auch wenn vergangene zugehen', R::state(10, $at('2026-12-10'), 2026, false), R::OPEN);
check('Heiligabend: Türchen 24 offen', R::state(24, $at('2026-12-24 08:00'), 2026), R::OPEN);
check('Januar danach: offen bei „bleiben offen“', R::state(5, $at('2027-01-03'), 2026, true), R::OPEN);
check('Januar danach: zu ohne „bleiben offen“', R::state(5, $at('2027-01-03'), 2026, false), R::PAST);
check('Jahr leer = jedes Jahr: im Oktober des Folgejahres wieder zu', R::state(1, $at('2027-10-01'), 0), R::EARLY);
check('Jahr leer = jedes Jahr: am 5.12. des Folgejahres offen', R::state(5, $at('2027-12-05'), 0), R::OPEN);
check('Kalender für 2027 ist im Dezember 2026 noch zu', R::state(1, $at('2026-12-20'), 2027), R::EARLY);
check('Demo-Modus öffnet alles', R::state(24, $at('2026-10-28'), 2026, false, true), R::OPEN);
check('Tag 25 gibt es nicht', R::state(25, $at('2026-12-25'), 2026, true, true), R::EARLY);
check('Tag 0 gibt es nicht', R::state(0, $at('2026-12-25'), 2026), R::EARLY);

$order = R::order(7, true);
check('Gemischt: alle 24 Tage genau einmal', (
    static function () use ($order) {
        $o = $order;
        sort($o);

        return $o;
    }
)(), range(1, 24));
check('Gemischt: nicht der Reihe nach', $order !== range(1, 24), true);
check('Gemischt: immer dieselbe Reihenfolge', R::order(7, true), $order);
check('Nicht gemischt: 1 bis 24', R::order(7, false), range(1, 24));

echo $failures ? "\n{$failures} Fehler\n" : "\nAlle Tests bestanden.\n";
exit($failures ? 1 : 0);
