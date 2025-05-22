<?php

namespace SmartCms\Core\Admin\Support;

use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\FieldResource;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Admin\Resources\LayoutResource;
use SmartCms\Core\Admin\Resources\MenuResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Admin\Resources\TranslationResource;

class SetupResources extends BaseSetup
{
    private const RESOURCES = [
        StaticPageResource::class,
        AdminResource::class,
        ContactFormResource::class,
        FormResource::class,
        TranslationResource::class,
        TemplateSectionResource::class,
        MenuResource::class,
        FieldResource::class,
        LayoutResource::class,
    ];

    public function handle(): array
    {
        return array_merge($this->extender->getResources(), self::RESOURCES);
    }
}
