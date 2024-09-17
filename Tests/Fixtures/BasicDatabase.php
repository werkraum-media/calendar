<?php

declare(strict_types=1);

return [
    'pages' => [
        0 => [
            'uid' => 1,
            'pid' => 0,
            'doktype' => 1,
            'is_siteroot' => 1,
            'slug' => '/',
            'title' => 'Page Title',
        ],
    ],
    'sys_template' => [
        0 => [
            'uid' => 1,
            'pid' => 1,
            'root' => 1,
            'clear' => 3,
            'constants' => 'databasePlatform = mysql',
            'config' => '
                <INCLUDE_TYPOSCRIPT: source="FILE:EXT:calendar_example/Configuration/TypoScript/Setup.typoscript">
                <INCLUDE_TYPOSCRIPT: source="FILE:EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript">
            ',
        ],
    ],
];
