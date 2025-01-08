<?php

use Filament\Support\Colors\Color;
use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\EmailResource;
use SmartCms\Core\Admin\Resources\FieldResource;
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
