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
                @import "EXT:calendar_example/Configuration/TypoScript/setup.typoscript"
                @import "EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript"
            ',
        ],
    ],
];
