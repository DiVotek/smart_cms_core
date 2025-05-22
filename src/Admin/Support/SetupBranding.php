<?php

namespace SmartCms\Core\Admin\Support;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Models\ContactForm;

class SetupBranding extends BaseSetup
{
    private const LANGUAGES = ['en', 'uk'];
    public function handle(): string
    {
        $brandName = company_name();
        if (strlen($brandName) == 0) {
            $brandName = 'SmartCms';
        }
        $this->registerBranding();
        $this->registerLanguageSwitch();
        $this->registerAssets();
        return $brandName;
    }

    public function registerBranding()
    {
        Filament::registerRenderHook(
            PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER,
            function (): string {
                $version = \Composer\InstalledVersions::getPrettyVersion('smart-cms/core');

                return <<<HTML
                <div class="text-xs text-center text-gray-500">
                    <p>Powered by <a href="https://s-cms.dev" target="_blank" class="hover:text-gray-700">SmartCms</a> v.{$version}</p>
                </div>
                HTML;
            }
        );
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn(): string => '<meta name="robots" content="noindex, nofollow" />',
        );
        Filament::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            function (): string {
                return Action::make('contact_form')
                    ->label(_nav('inbox'))
                    ->badge(ContactForm::query()->where('status', 'New')->count())
                    ->badgeColor('gray')
                    ->icon('heroicon-o-envelope')
                    ->outlined()
                    ->size('sm')
                    ->color('gray')
                    ->url(ContactFormResource::getUrl('index'))
                    ->render()
                    ->__toString();
            }
        );
        Filament::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            function (): string {
                return Action::make('view')
                    ->label(_actions('_view'))
                    ->icon('heroicon-o-eye')
                    ->outlined()
                    ->size('sm')
                    ->color('gray')
                    ->url(url('/'))
                    ->openUrlInNewTab()
                    ->render()
                    ->__toString();
            }
        );
    }

    public function registerLanguageSwitch()
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(self::LANGUAGES);
        });
    }

    public function registerAssets()
    {
        FilamentAsset::register([
            Css::make('scms-stylesheet', asset('/smart_cms_core/index.css')),
            Js::make('scms-script', asset('/smart_cms_core/index.js')),
        ]);
    }
}
