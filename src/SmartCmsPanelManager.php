<?php

namespace SmartCms\Core;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema as FacadesSchema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use SmartCms\Core\Admin\Pages\Auth\Login;
use SmartCms\Core\Admin\Pages\Auth\Profile;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\ListStaticPages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Singletone\Languages;
use SmartCms\Core\Services\Singletone\Pages;
use SmartCms\Core\Services\Singletone\Settings;

class SmartCmsPanelManager extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $this->app->singleton('_settings', function () {
            return new Settings;
        });
        $this->app->singleton('_lang', function () {
            return new Languages;
        });
        $this->app->singleton('_page', function () {
            return new Pages;
        });
        if (! FacadesSchema::hasTable('settings')) {
            return $panel->default()
                ->id('smart_cms_admin')
                ->path('admin');
        }
        Field::macro('translatable', function () {
            if (is_multi_lang()) {
                return $this->hint('Translatable')
                    ->hintIcon('heroicon-m-language');
            }

            return $this;
        });
        $this->registerDynamicResources();
        $this->registerBranding();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(config('shared.admin.locales', []));
        });
        FilamentAsset::register([
            Css::make('custom-stylesheet', asset('/smart_cms_core/index.css')),
            JS::make('custom-script', asset('/smart_cms_core/index.js')),
        ]);

        return $panel
            ->default()
            ->id('smart_cms_admin')
            ->path('admin')
            ->login(Login::class)
            ->colors(config('shared.admin.colors', []))
            ->emailVerification()
            ->authGuard('admin')
            ->profile(Profile::class)
            ->spa(config('app.spa',false))
            ->font('Roboto')
            ->darkMode(false)
            ->brandName(company_name() ?? 'SmartCms')
            ->resources($this->getResources())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            // ->breadcrumbs(false)
            ->databaseNotifications()
            ->widgets(config('shared.admin.widgets', []))
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->navigationGroups(
                $this->getNavigationGroups()
            )
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
                    ->pages(
                        config('shared.admin.settings_pages', [])
                    ),
            ]);
    }

    public function registerDynamicResources()
    {
        Filament::serving(function () {
            $pageResourceClass = config('shared.admin.page_resource', StaticPageResource::class);
            $menuSections = MenuSection::query()->get();
            $items = [
                NavigationItem::make(_nav('pages'))->sort(1)
                    ->url(config('shared.admin.page_resource')::getUrl('index'))
                    ->group(_nav('pages'))
                    ->isActiveWhen(function () {
                        return request()->route()->getName() === ListStaticPages::getRouteName() &&
                            (! request('activeTab') || request('activeTab') == 'all');
                    })->badge(function () use ($menuSections) {
                        return Page::query()->whereNull('parent_id')->whereNotIn('id', $menuSections->pluck('id')->toArray())
                            ->count();
                    }),
            ];
            foreach ($menuSections as $section) {
                $items[] = NavigationItem::make(_nav('items'))
                    ->url($pageResourceClass::getUrl('index', ['activeTab' => $section->name]))
                    ->sort($section->sorting + 2)
                    ->group($section->name)
                    ->isActiveWhen(function () use ($section) {
                        return request()->route()->getName() === ListStaticPages::getRouteName() && (! request('activeTab') || request('activeTab') == $section->name);
                    });
                if ($section->is_categories) {
                    $items[] = NavigationItem::make(_nav('categories'))
                        ->url($pageResourceClass::getUrl('index', ['activeTab' => $section->name._nav('categories')]))
                        ->sort($section->sorting + 1)
                        ->group($section->name)
                        ->isActiveWhen(function () use ($section) {
                            return request()->route()->getName() === ListStaticPages::getRouteName() && request('activeTab') == $section->name._nav('categories');
                        });
                }
            }
            Filament::registerNavigationItems($items);
        });
    }

    public function getResources(): array
    {
        return array_merge(config('shared.admin.resources'), [
            config('shared.admin.page_resource'),
        ]);
    }

    public function getNavigationGroups(): array
    {
        $reference = [];
        $groups = config('shared.admin.navigation_groups', []);
        foreach ($groups as $group) {
            if (! isset($group['name'])) {
                continue;
            }
            if ($group['name'] == 'menu_sections') {
                $menuSections = MenuSection::query()->get();
                foreach ($menuSections as $section) {
                    $icon = $section->icon ?? $group['icon'] ?? 'heroicon-m-book-open';
                    $reference[] = \Filament\Navigation\NavigationGroup::make($section->name)->icon($icon);
                }
            } else {
                $icon = $group['icon'] ?? 'heroicon-m-book-open';
                $reference[] = NavigationGroup::make(_nav($group['name']))->icon($icon);
            }
        }

        return $reference;
        // $groups = [
        //     \Filament\Navigation\NavigationGroup::make(_nav('catalog'))->icon('heroicon-m-shopping-bag'),
        //     \Filament\Navigation\NavigationGroup::make(_nav('communication'))->icon('heroicon-m-megaphone'),
        //     \Filament\Navigation\NavigationGroup::make(_nav('pages'))->icon('heroicon-m-book-open'),
        // ];
        // $menuSections = MenuSection::query()->get();

        // foreach ($menuSections as $section) {
        //     $groups[] = \Filament\Navigation\NavigationGroup::make($section->name)->icon($section->icon ?? 'heroicon-m-book-open');
        // }
        // $groups[] = \Filament\Navigation\NavigationGroup::make(_nav('design-template'))->icon('heroicon-m-light-bulb');
        // $groups[] = \Filament\Navigation\NavigationGroup::make(_nav('system'))->icon('heroicon-m-cog-6-tooth');

        // return $groups;
    }

    public function registerBranding()
    {
        Filament::registerRenderHook(
            PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER,
            function (): string {
                return <<<'HTML'
                <div class="text-xs text-center text-gray-500">
                    <p>Powered by <a href="https://smartcms.com" target="_blank" class="hover:text-gray-700">SmartCms</a></p>
                </div>
                HTML;

                return view('filament.components.footer-branding')->render();
            }
        );
        Filament::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            function (): string {
                return <<<'HTML'
            <a href="/" target="_blank" class="flex items-center justify-center p-2 font-semibold rounded-lg h-9 text-primary-600 bg-primary-500/10">
                View Website
            </a>
            HTML;
            }
        );
    }
}
