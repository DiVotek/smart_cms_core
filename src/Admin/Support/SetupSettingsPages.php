<?php

namespace SmartCms\Core\Admin\Support;

use SmartCms\Core\Admin\Pages\Settings\Settings;

class SetupSettingsPages extends BaseSetup
{
    private const SETTINGS_PAGES = [
        Settings::class,
    ];

    public function handle(): array
    {
        return array_merge($this->extender->getSettingsPages(), self::SETTINGS_PAGES);
    }
}
