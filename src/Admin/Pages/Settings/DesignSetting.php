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
                    // Tabs\Tab::make(__('Cookie'))
                    //    ->schema([
                    //       Select::make(config('settings.design.cookie'))
                    //          ->label(__('Design'))
                    //          ->options(Helper::getTemplateByComponent('Layout/Topbar'))
                    //          ->native(false)
                    //          ->searchable(),
                    //       Toggle::make(config('settings.cookie'))
                    //          ->label(__('Enable Cookies')),
                    //    ]),
                    // Tabs\Tab::make(__('Scrolltop'))
                    //     ->schema([
                    //         Radio::make(config('settings.scroll_top.position'))
                    //             ->label(__('Position'))
                    //             ->options([
                    //                 'left' => 'Left',
                    //                 'right' => 'Right',
                    //             ])->default('right'),
                    //         TextInput::make(config('settings.scroll_top.margin'))->numeric()->label(__('Margin'))->default(0),
                    //         Toggle::make(config('settings.preloader.status'))->label(__('Preloader'))->default(0),
                    //     ]),
                ]),
        ];
    }
}
