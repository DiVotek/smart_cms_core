<?php

namespace SmartCms\Core\Admin\Support;

class SetupPages extends BaseSetup
{
    private const PAGES = [
        \Filament\Pages\Dashboard::class,
    ];

    public function handle(): array
    {
        return array_merge($this->extender->getPages(), self::PAGES);
    }
}
