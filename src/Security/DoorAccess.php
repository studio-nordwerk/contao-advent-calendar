<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\Security;

/**
 * Darf ein Besucher die Türchen eines Kalender-Elements abrufen? Nur wenn er auch die Seite
 * mit dem Element sehen darf: Element, Artikel und alle Seiten darüber sind veröffentlicht,
 * im Zeitraum und, falls geschützt, für seine Mitgliedergruppe freigegeben.
 *
 * Ohne Contao, damit es sich einfach testen lässt. Die Zeilen kommen direkt aus der Datenbank.
 */
final class DoorAccess
{
    /**
     * @param list<array<string, mixed>>     $elements       Das Kalender-Element, dann umgebende Elemente (verschachtelte Inhalte), innen nach außen
     * @param array<string, mixed>|null      $article        Artikel mit dem äußersten Element; null, wenn das Element nicht in einem Artikel liegt
     * @param list<array<string, mixed>>     $pages          Seite des Artikels, dann ihre Eltern bis zur Startseite, innen nach außen
     * @param callable(list<int>): bool       $memberInGroups Ist der Besucher Mitglied in einer dieser Gruppen?
     * @param bool                           $preview        Contao-Vorschau: unveröffentlichte Inhalte zählen mit, der Mitgliederschutz bleibt
     */
    public static function isVisible(array $elements, array|null $article, array $pages, int $now, bool $preview, callable $memberInGroups): bool
    {
        if ([] === $elements || null === $article || [] === $pages) {
            return false;
        }

        foreach ($elements as $element) {
            if (!$preview && (!empty($element['invisible']) || !self::inPeriod($element, $now))) {
                return false;
            }

            if (!self::allowed($element, $memberInGroups)) {
                return false;
            }
        }

        if (!$preview && (empty($article['published']) || !self::inPeriod($article, $now))) {
            return false;
        }

        if (!self::allowed($article, $memberInGroups)) {
            return false;
        }

        // Wie Contao: Ein Schutz gilt auch für Unterseiten; die nächste geschützte Seite bestimmt die Gruppen.
        $pageProtection = null;

        foreach ($pages as $page) {
            if (!$preview && (empty($page['published']) || !self::inPeriod($page, $now))) {
                return false;
            }

            if (null === $pageProtection && !empty($page['protected'])) {
                $pageProtection = $page;
            }
        }

        return null === $pageProtection || self::allowed($pageProtection, $memberInGroups);
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function inPeriod(array $row, int $now): bool
    {
        $start = (string) ($row['start'] ?? '');
        $stop = (string) ($row['stop'] ?? '');

        return !(('' !== $start && (int) $start > $now) || ('' !== $stop && (int) $stop <= $now));
    }

    /**
     * @param array<string, mixed>       $row
     * @param callable(list<int>): bool  $memberInGroups
     */
    private static function allowed(array $row, callable $memberInGroups): bool
    {
        if (empty($row['protected'])) {
            return true;
        }

        $groups = \is_string($row['groups'] ?? null) ? @unserialize($row['groups'], ['allowed_classes' => false]) : ($row['groups'] ?? []);

        return \is_array($groups) && $memberInGroups(array_values(array_map('intval', $groups)));
    }
}
