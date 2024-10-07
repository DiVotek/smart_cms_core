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
        $templateConfigPath = scms_template_path(_settings('template', 'default')).'/config.json';
        if (file_exists($templateConfigPath)) {
            $templateSchema = json_decode(file_get_contents($templateConfigPath), true);
        } else {
            $templateSchema = [];
        }

        return [
            Tabs::make(strans('admin.settings'))
                ->schema([
                    Tabs\Tab::make(__('General'))->schema([
                        Select::make('template')
                            ->label(_fields('template'))
                            ->options(Helper::getTemplates())
                            ->native(false)
                            ->searchable(),
                        // ...Setting::getStyleForm(),
                    ]),
                    Tabs\Tab::make(strans('admin.header'))
                        ->schema([
                            Select::make(sconfig('design.header'))
                                ->label(strans('admin.design'))
                                ->options(Helper::getLayoutTemplate())
                                ->native(false)
                                ->searchable(),
                            Schema::getLinkBuilder(sconfig('menu.header'))->label(_fields('menu_header')),
                        ]),
                    Tabs\Tab::make(strans('admin.footer'))
                        ->schema([
                            Select::make(sconfig('design.footer'))
                                ->label(strans('admin.design'))
                                ->options(Helper::getLayoutTemplate(true))
                                ->native(false)
                                ->searchable(),
                            Schema::getLinkBuilder(sconfig('menu.footer'))->label(_fields('menu_footer')),
                        ]),
                ]),
        ];
    }
}
