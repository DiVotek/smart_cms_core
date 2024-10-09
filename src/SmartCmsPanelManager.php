<?php

namespace SmartCms\Core;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use SmartCms\Core\Admin\Pages\Auth\Login;
use SmartCms\Core\Admin\Pages\Auth\Profile;
use SmartCms\Core\Admin\Pages\Settings\DesignSetting;
use SmartCms\Core\Admin\Pages\Settings\Settings;
use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Admin\Resources\SeoResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\ListStaticPages;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Models\Page;

class SmartCmsPanelManager extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $moduleResource = [];
        Field::macro('translatable', function () {
            if (is_multi_lang()) {
                return $this->hint('Translatable')
                    ->hintIcon('heroicon-m-language');
            }

            return $this;
        });
        $this->registerDynamicResources();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ru', 'uk']);
        });
        $widgets = [];

        FilamentAsset::register([
            Css::make('custom-stylesheet', asset('/smart_cms_core/index.css')),
            JS::make('custom-script', asset('/smart_cms_core/index.js')),
        ]);

        return $panel
            ->default()
            ->id('smart_cms_admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => '#28a0e7',
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->emailVerification()
            ->authGuard('admin')
            ->profile(Profile::class)
            ->font('Roboto')
            ->darkMode(false)
            ->brandName('SmartCms')
            ->resources($this->getResources())
            // ->brandLogo(fn() => view('logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            // ->navigationGroups([
            //    NavigationGroup::make(fn() => __('Sales'))->icon('heroicon-m-clipboard-document-list')->collapsed(true)->extraSidebarAttributes(['class' => 'featured-sidebar-group']),
            //    NavigationGroup::make(fn() => __('Promotions'))->icon('heroicon-m-receipt-percent')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Catalog'))->icon('heroicon-m-building-storefront')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Filter'))->icon('heroicon-m-funnel')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Communication'))->icon('heroicon-m-megaphone')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Blog'))->icon('heroicon-m-academic-cap')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Info pages'))->icon('heroicon-m-book-open')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Search'))->icon('heroicon-m-magnifying-glass')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Modules'))->icon('heroicon-m-puzzle-piece')->collapsed(true),
            //    NavigationGroup::make(fn() => __('SEO'))->icon('heroicon-m-globe-alt')->collapsed(true),
            //    NavigationGroup::make(fn() => __('Design/Template'))->icon('heroicon-m-light-bulb')->collapsed(true),
            //    NavigationGroup::make(fn() => __('System'))->icon('heroicon-m-cog-6-tooth')->collapsed(true)->extraSidebarAttributes(['class' => 'featured-sidebar-group']),
            // ])
            ->sidebarCollapsibleOnDesktop()
            ->widgets($widgets)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->resources($moduleResource)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentSettingsPlugin::make()
                    ->pages([
                        Settings::class,
                        // DesignSetting::class,
                    ]),
            ]);
    }

    public function registerDynamicResources()
    {
        Filament::serving(function () {
            $pages = Page::query()->where('is_nav', true)->get();
            $items = [
                NavigationItem::make(_nav('pages'))->sort(1)
                    ->url(StaticPageResource::getUrl('index'))
                    ->group(_nav('pages'))
                    ->isActiveWhen(function () {
                        return request()->route()->getName() === ListStaticPages::getRouteName() && (! request('activeTab') || request('activeTab') == 'all');
                    }),
            ];
            foreach ($pages as $page) {
                $group = _nav('pages');
                if ($page->parent_id && $page->parent->is_nav) {
                    $group = $page->parent->name();
                } elseif ($page->children()->where('is_nav', true)->exists()) {
                    $group = $page->name();
                }
                $items[] = NavigationItem::make($page->name())->url(StaticPageResource::getUrl('index', ['activeTab' => $page->name()]))->sort($page->sorting + 1)
                    ->group($group)
                    ->isActiveWhen(function () use ($page) {
                        return request()->route()->getName() === ListStaticPages::getRouteName() && request('activeTab') == $page->name();
                    })->sort(1000);
            }
            Filament::registerNavigationItems($items);
        });
    }

    public function getResources(): array
    {
        return [
            AdminResource::class,
            SeoResource::class,
            ContactFormResource::class,
            FormResource::class,
            TranslationResource::class,
            StaticPageResource::class,
            TemplateSectionResource::class,
        ];
    }
}
