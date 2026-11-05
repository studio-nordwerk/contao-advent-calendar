<?php

declare(strict_types=1);

namespace Nordwerk\AdventCalendarBundle\Calendar;

/**
 * Entscheidet, ob ein Türchen aufgehen darf. Ohne Contao, damit es sich einfach
 * testen lässt.
 */
final class DoorRules
{
    public const OPEN = 'open';
    public const EARLY = 'early';
    public const PAST = 'past';

    /**
     * @param int  $year     Kalenderjahr; 0 = jedes Jahr (das Jahr von $today)
     * @param bool $keepOpen Vergangene Türchen bleiben offen
     * @param bool $demo     Demo-Modus: vor dem 1. Dezember sind alle Türchen offen
     */
    public static function state(int $day, \DateTimeImmutable $today, int $year = 0, bool $keepOpen = true, bool $demo = false): string
    {
        if ($day < 1 || $day > 24) {
            return self::EARLY;
        }

        if (self::demoActive($today, $year, $demo)) {
            return self::OPEN;
        }

        $year = $year ?: (int) $today->format('Y');
        $doorDate = \sprintf('%04d-12-%02d', $year, $day);
        $todayDate = $today->format('Y-m-d');

        if ($todayDate < $doorDate) {
            return self::EARLY;
        }

        if ($todayDate === $doorDate || $keepOpen) {
            return self::OPEN;
        }

        return self::PAST;
    }

    /**
     * Der Demo-Modus gilt nur bis zum 30. November. Wer vergisst, ihn auszuschalten, verrät im
     * Dezember trotzdem keine Türchen.
     */
    public static function demoActive(\DateTimeImmutable $today, int $year, bool $demo): bool
    {
        if (!$demo) {
            return false;
        }

        $year = $year ?: (int) $today->format('Y');

        return $today->format('Y-m-d') < \sprintf('%04d-12-01', $year);
    }

    /**
     * Feste, aber gemischte Reihenfolge der Türchen; bleibt für einen Kalender
     * immer gleich.
     *
     * @return list<int>
     */
    public static function order(int $calendarId, bool $shuffle): array
    {
        $days = range(1, 24);

        if ($shuffle) {
            usort($days, static fn (int $a, int $b): int => crc32($calendarId.'-'.$a) <=> crc32($calendarId.'-'.$b));
        }

        return $days;
    }
}
