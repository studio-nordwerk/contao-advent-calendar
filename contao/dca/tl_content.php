<?php

declare(strict_types=1);

// Inhaltselement „Adventskalender“
$GLOBALS['TL_DCA']['tl_content']['palettes']['advent_calendar'] = '{type_legend},type,headline;{advent_calendar_legend},adventCalendar;{image_legend},size;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['adventCalendar'] = [
    'inputType' => 'select',
    'foreignKey' => 'tl_advent_calendar.title',
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default '0'",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];
