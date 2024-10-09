<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use SmartCms\Core\Services\Helper;

class DesignSetting extends BaseSettings
{
    public static function getNavigationGroup(): ?string
    {
        return strans('admin.design-template');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationBadge(): ?string
    {
        return 8;
    }

    public function schema(): array|Closure
    {
        $templateConfig = [];
        $templateConfigPath = scms_template_path(_settings('template', 'default')) . '/config.json';
        if (file_exists($templateConfigPath)) {
            $templateConfig = json_decode(file_get_contents($templateConfigPath), true);
        }
        $headerSchema = [];
        $footerSchema = [];
        $tabs = [];
        if (isset($templateConfig['layout'])) {
            $headerSchema = Helper::parseSchema($templateConfig['layout']['header'] ?? [], 'header');
            $footerSchema = Helper::parseSchema($templateConfig['layout']['footer'] ?? [], 'footer');
        }
        if (isset($templateConfig['defaultVariables'])) {
            $tabs[] = Tab::make(_nav('default_variables'))
                ->schema(Helper::parseSchema($templateConfig['defaultVariables'] ?? [], 'default_variables'));
        }
        if (isset($templateConfig['theme'])) {
            $colors = [];
            foreach ($templateConfig['theme'] as $key => $value) {
                $colors[] = ColorPicker::make('theme.' . $key)
                    ->label(ucfirst($key))
                    ->default($value);
            }
            $tabs[] = Tab::make(_nav('theme'))
                ->schema([Grid::make()
                    ->columns(2)
                    ->schema($colors)]);
        }
        return [
            Tabs::make(strans('admin.settings'))
                ->schema([
                    Tabs\Tab::make(strans('admin.header'))
                        ->schema([Group::make($headerSchema)]),
                    Tabs\Tab::make(strans('admin.footer'))
                        ->schema($footerSchema),
                    ...$tabs
                ]),
        ];
    }
}
