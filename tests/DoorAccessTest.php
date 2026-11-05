<?php

declare(strict_types=1);

// Einfacher Test ohne Contao: php tests/DoorAccessTest.php
// Wer die Seite mit dem Kalender nicht sehen darf, bekommt auch keine Türchen.

require __DIR__.'/../src/Security/DoorAccess.php';

use Nordwerk\AdventCalendarBundle\Security\DoorAccess as A;

$failures = 0;

function check(string $name, bool $actual, bool $expected): void
{
    global $failures;

    if ($actual === $expected) {
        echo "✓ {$name}\n";

        return;
    }

    ++$failures;
    echo '✗ '.$name."\n  erwartet: ".($expected ? 'sichtbar' : 'gesperrt')."\n";
}

$now = 1_800_000_000;
$element = ['invisible' => '', 'start' => '', 'stop' => '', 'protected' => '', 'groups' => null, 'ptable' => 'tl_article'];
$article = ['published' => '1', 'start' => '', 'stop' => '', 'protected' => '', 'groups' => null];
$page = ['published' => '1', 'start' => '', 'stop' => '', 'protected' => '', 'groups' => null];
$root = $page + ['type' => 'root'];
$guest = static fn (array $groups): bool => false;
$member = static fn (array $groups): bool => in_array(2, $groups, true);
$visible = static fn (?array $elements = null, ?array $art = null, ?array $pages = null, bool $preview = false, ?callable $who = null): bool => A::isVisible(
    $elements ?? [$element],
    func_num_args() >= 2 ? $art : $article,
    $pages ?? [$page, $root],
    $now,
    $preview,
    $who ?? $guest,
);
$protected = ['protected' => '1', 'groups' => serialize(['2'])];

check('Öffentliche Seite: sichtbar', $visible(), true);
check('Element versteckt: gesperrt', $visible([['invisible' => '1'] + $element]), false);
check('Element erst ab morgen: gesperrt', $visible([['start' => (string) ($now + 86400)] + $element]), false);
check('Element abgelaufen: gesperrt', $visible([['stop' => (string) $now] + $element]), false);
check('Element im Zeitraum: sichtbar', $visible([['start' => (string) ($now - 10), 'stop' => (string) ($now + 10)] + $element]), true);
check('Element nur für Mitglieder, Gast: gesperrt', $visible([$protected + $element]), false);
check('Element nur für Mitglieder, Mitglied: sichtbar', $visible([$protected + $element], $article, null, false, $member), true);

check('Kein Artikel (z. B. in einer Nachricht): gesperrt', $visible(null, null), false);
check('Artikel unveröffentlicht: gesperrt', $visible(null, ['published' => ''] + $article), false);
check('Artikel abgelaufen: gesperrt', $visible(null, ['stop' => (string) ($now - 1)] + $article), false);
check('Artikel nur für Mitglieder, Gast: gesperrt', $visible(null, $protected + $article), false);
check('Artikel nur für Mitglieder, Mitglied: sichtbar', $visible(null, $protected + $article, null, false, $member), true);

check('Seite unveröffentlicht: gesperrt', $visible(null, $article, [['published' => ''] + $page, $root]), false);
check('Elternseite unveröffentlicht: gesperrt', $visible(null, $article, [$page, ['published' => ''] + $page, $root]), false);
check('Startseite unveröffentlicht: gesperrt', $visible(null, $article, [$page, ['published' => ''] + $root]), false);
check('Seite erst ab morgen: gesperrt', $visible(null, $article, [['start' => (string) ($now + 60)] + $page, $root]), false);
check('Seite nur für Mitglieder, Gast: gesperrt', $visible(null, $article, [$protected + $page, $root]), false);
check('Seite nur für Mitglieder, Mitglied: sichtbar', $visible(null, $article, [$protected + $page, $root], false, $member), true);
check('Elternseite geschützt, Unterseite nicht: Gast gesperrt', $visible(null, $article, [$page, $protected + $page, $root]), false);
check('Unterseite für andere Gruppe als die Elternseite: die Unterseite gilt', $visible(null, $article, [['protected' => '1', 'groups' => serialize(['2'])] + $page, ['protected' => '1', 'groups' => serialize(['9'])] + $page, $root], false, $member), true);
check('Geschützt ohne Gruppen: gesperrt', $visible(null, $article, [['protected' => '1', 'groups' => null] + $page, $root], false, $member), false);
check('Keine Seiten gefunden: gesperrt', $visible(null, $article, []), false);

check('Umgebendes Element versteckt: gesperrt', $visible([$element, ['invisible' => '1'] + $element]), false);
check('Umgebendes Element nur für Mitglieder, Gast: gesperrt', $visible([$element, $protected + $element]), false);

check('Vorschau: unveröffentlichte Seite sichtbar', $visible(null, ['published' => ''] + $article, [['published' => ''] + $page, $root], true), true);
check('Vorschau: Mitgliederschutz bleibt', $visible(null, $article, [$protected + $page, $root], true), false);

echo $failures ? "\n{$failures} Fehler\n" : "\nAlle Tests bestanden.\n";
exit($failures ? 1 : 0);
