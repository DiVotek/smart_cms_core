<?php

namespace SmartCms\Core\Admin\Support;

use SmartCms\Core\Admin\Widgets\HealthCheck;
use SmartCms\Core\Admin\Widgets\TopContactForms;
use SmartCms\Core\Admin\Widgets\TopStaticPages;
use SmartCms\Core\Admin\Widgets\VersionCheck;

class SetupWidgets extends BaseSetup
{
    private const WIDGETS = [
        TopStaticPages::class,
        TopContactForms::class,
        HealthCheck::class,
        VersionCheck::class,
    ];

    public function handle(): array
    {
        return array_merge($this->extender->getWidgets(), self::WIDGETS);
    }
}
