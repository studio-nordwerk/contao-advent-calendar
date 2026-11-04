<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;
use Contao\StringUtil;
use Contao\System;

// Ein Türchen: Tag 1–24 mit Bild, Text, Preis, Gutscheincode und Button
$GLOBALS['TL_DCA']['tl_advent_calendar_door'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_advent_calendar',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,day' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['day'],
            'headerFields' => ['title', 'year'],
            'panelLayout' => 'limit',
            'child_record_callback' => static function (array $row): string {
                $title = StringUtil::specialchars(StringUtil::decodeEntities((string) $row['headline']));
                $hidden = $row['published'] ? '' : ' <span class="tl_gray">('.($GLOBALS['TL_LANG']['tl_advent_calendar_door']['hiddenLabel'] ?? 'unsichtbar').')</span>';

                return '<div class="tl_content_left"><strong>'.(int) $row['day'].'.</strong> &nbsp; '.$title.$hidden.'</div>';
            },
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
            ],
            'show',
        ],
    ],
    'palettes' => [
        'default' => '{door_legend},day,kicker,headline;{content_legend},singleSRC,text;{offer_legend},price,oldPrice,couponCode;{link_legend},url,target,linkTitle;{publish_legend},published',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_advent_calendar.title',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'day' => [
            'inputType' => 'select',
            'options' => range(1, 24),
            'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'save_callback' => [
                // Jeder Tag darf in einem Kalender nur einmal vorkommen
                static function ($value, DataContainer $dc) {
                    $taken = System::getContainer()->get('database_connection')->fetchOne(
                        'SELECT id FROM tl_advent_calendar_door WHERE pid = ? AND day = ? AND id != ?',
                        [(int) $dc->activeRecord?->pid, (int) $value, (int) $dc->id],
                    );

                    if (false !== $taken) {
                        throw new RuntimeException(sprintf($GLOBALS['TL_LANG']['tl_advent_calendar_door']['dayTaken'] ?? 'Für Tag %s gibt es schon ein Türchen.', $value));
                    }

                    return $value;
                },
            ],
            'sql' => "tinyint(2) unsigned NOT NULL default '0'",
        ],
        'kicker' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'headline' => [
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr long'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => '%contao.image.valid_extensions%', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'text' => [
            'inputType' => 'textarea',
            'search' => true,
            'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
            'sql' => 'mediumtext NULL',
        ],
        'price' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 32, 'placeholder' => '24,95 €', 'tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'oldPrice' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 32, 'placeholder' => '44,95 €', 'tl_class' => 'w50'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'couponCode' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'url' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'clr w50'],
            'sql' => 'text NULL',
        ],
        'target' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'linkTitle' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'placeholder' => 'Zum Angebot', 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'published' => [
            'toggle' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default '1'",
        ],
    ],
];
