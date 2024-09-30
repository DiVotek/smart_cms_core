<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use SmartCms\Core\Services\Helper;
use SmartCms\Core\Services\Schema;

class DesignSetting extends BaseSettings
{
    public static function getNavigationGroup(): ?string
    {
        return strans('admin.design-template');
    }

    public static function getNavigationBadge(): ?string
    {
        return 8;
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make(strans('admin.settings'))
                ->schema([
                    // Tabs\Tab::make(__('General'))->schema([
                    //     ...Setting::getStyleForm(),
                    // ]),
                    Tabs\Tab::make(strans('admin.header'))
                        ->schema([
                            Select::make(config('settings.design.header'))
                                ->label(strans('admin.design'))
                                ->options(Helper::getLayoutTemplate())
                                ->native(false)
                                ->searchable(),
                            Schema::getLinkBuilder(config('settings.menu.header')),
                        ]),
                    Tabs\Tab::make(strans('admin.footer'))
                        ->schema([
                            Select::make(config('settings.design.footer'))
                                ->label(strans('admin.design'))
                                ->options(Helper::getLayoutTemplate(true))
                                ->native(false)
                                ->searchable(),
                            Schema::getLinkBuilder(config('settings.menu.footer')),
                        ]),
                ]),
        ];
    }
}
