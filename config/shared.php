<?php

use Filament\Support\Colors\Color;
use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\EmailResource;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Admin\Resources\MenuResource;
use SmartCms\Core\Admin\Resources\MenuSectionResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Admin\Widgets\TopContactForms;
use SmartCms\Core\Admin\Widgets\TopStaticPages;
use SmartCms\Core\Routes\GetForm;
use SmartCms\Core\Routes\GetRobots;
use SmartCms\Core\Routes\GetSitemap;
use SmartCms\Core\Routes\GetSlug;

return [
    'page_model' => \SmartCms\Core\Models\Page::class,
    'routes' => [
        'slug' => [], //invokes after main getSlug page
        'route_handler' => GetSlug::class,
        'form_handler' => GetForm::class,
        'sitemap_handler' => GetSitemap::class,
        'robots_handler' => GetRobots::class,
    ],
    'admin' => [
        'resources' => [
            AdminResource::class,
            MenuSectionResource::class,
            ContactFormResource::class,
            FormResource::class,
            // TranslationResource::class,
            TemplateSectionResource::class,
            MenuResource::class,
            EmailResource::class,
        ],
        'page_resource' => StaticPageResource::class,
        'page_relations' => [],
        'navigation_groups' => [
            [
                'name' => 'catalog',
                'icon' => 'heroicon-m-shopping-bag',
            ],
            [
                'name' => 'communication',
                'icon' => 'heroicon-m-megaphone',
            ],
            [
                'name' => 'menu_sections',
                'icon' => 'heroicon-m-book-open',
            ],
            [
                'name' => 'pages',
                'icon' => 'heroicon-m-book-open',
            ],
            [
                'name' => 'design-template',
                'icon' => 'heroicon-m-light-bulb',
            ],
            [
                'name' => 'system',
                'icon' => 'heroicon-m-cog-6-tooth',
            ],
        ],
        'navigation_items' => [],
        'settings_pages' => [
            \SmartCms\Core\Admin\Pages\Settings\Settings::class,
        ],
        'locales' => ['en', 'ru', 'uk'],
        'colors' => [
            'primary' => '#28a0e7',
            'danger' => Color::Rose,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'success' => Color::Emerald,
            'warning' => Color::Orange,
        ],
        'widgets' => [
            TopStaticPages::class,
            TopContactForms::class,
        ],
    ],
];
