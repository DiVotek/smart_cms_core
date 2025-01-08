<?php

use Filament\Support\Colors\Color;

return [
    'page_model' => \SmartCms\Core\Models\Page::class,
    'admin' => [
        'locales' => ['en', 'uk'],
        'colors' => [
            'primary' => '#28a0e7',
            'danger' => Color::Rose,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'success' => Color::Emerald,
            'warning' => Color::Orange,
        ],
    ],
];
