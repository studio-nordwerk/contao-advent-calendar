<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;

// Kalender, z. B. „Adventskalender 2026“
$GLOBALS['TL_DCA']['tl_advent_calendar'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_advent_calendar_door'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['title'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['title', 'year'],
            'format' => '%s <span class="label-info">[%s]</span>',
            // Jahr 0 = „jedes Jahr“ statt „[0]“
            'label_callback' => static fn (array $row, string $label): string => sprintf(
                '%s <span class="label-info">[%s]</span>',
                $row['title'],
                (int) $row['year'] ?: $GLOBALS['TL_LANG']['tl_advent_calendar']['everyYear'],
            ),
        ],
        // Wie in Contao 5.7 bei Nachrichtenarchiv und Kalender sind beide Knöpfe
        // sichtbar ('primary'), aber „Türchen bearbeiten“ steht vorn und ist das
        // Ziel beim Klick auf die Zeile. „Kalender bearbeiten“ (Einstellungen,
        // Hintergrundbild) folgt mit Contaos Standardknopf.
        'operations' => [
            'children' => [
                'href' => 'table=tl_advent_calendar_door',
                'icon' => 'bundles/heikewiesecontaoadventcalendar/icons/door.svg',
                'prefetch' => true,
                'attributes' => 'data-contao--deeplink-target="primary"',
                'primary' => true,
            ],
            'edit',
            'copy',
            'delete',
            'show',
        ],
    ],
    'palettes' => [
        '__selector__' => ['backgroundType'],
        'default' => '{title_legend},title,year;{rules_legend},keepOpen,shuffle,demoMode;{look_legend},backgroundType,snow',
    ],
    'subpalettes' => [
        'backgroundType_image' => 'singleSRC',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'year' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural', 'minval' => 2020, 'maxlength' => 4, 'tl_class' => 'w50'],
            // 0 = jedes Jahr: im Formular leer zeigen, sonst meldet „mindestens 2020“
            // einen Fehler
            'load_callback' => [static fn ($value) => 0 === (int) $value ? '' : $value],
            'sql' => "smallint(4) unsigned NOT NULL default '0'",
        ],
        'keepOpen' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'shuffle' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'demoMode' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr w50'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'backgroundType' => [
            'inputType' => 'select',
            'options' => ['poinsettia', 'image', 'none'],
            'reference' => &$GLOBALS['TL_LANG']['tl_advent_calendar']['backgroundTypes'],
            'default' => 'poinsettia',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => '%contao.image.valid_extensions%', 'mandatory' => true, 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'snow' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
    ],
];
