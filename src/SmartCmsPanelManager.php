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
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema as FacadesSchema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use SmartCms\Core\Admin\Pages\Auth\Login;
use SmartCms\Core\Admin\Pages\Auth\Profile;
use SmartCms\Core\Admin\Pages\TemplatePage;
use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\FieldResource;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Admin\Resources\LayoutResource;
use SmartCms\Core\Admin\Resources\MenuResource;
use SmartCms\Core\Admin\Resources\MenuSectionResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\ListStaticPages;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Admin\Widgets\TopContactForms;
use SmartCms\Core\Admin\Widgets\TopStaticPages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Singletone\Languages;
use SmartCms\Core\Services\Singletone\Settings;
use SmartCms\Core\Services\Singletone\Translates;

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
        $this->app->singleton('_trans', function () {
            return new Translates;
        });
        if (! FacadesSchema::hasTable('settings')) {
            return $panel->default()
                ->id('smart_cms_admin')
                ->path('admin');
        }
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
        $this->addMacro();

        return $panel
            ->default()
            ->id('smart_cms_admin')
            ->path('admin')
            ->login(Login::class)
            ->colors(config('shared.admin.colors', []))
            ->emailVerification()
            ->authGuard('admin')
            ->profile(Profile::class)
            ->spa(config('app.spa', false))
            ->font('Roboto')
            ->darkMode(false)
            ->brandName($this->getBrandName())
            ->resources($this->getResources())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages($this->getPages())
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->databaseNotifications()
            ->widgets($this->getWidgets())
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
                FilamentSettingsPlugin::make()->pages($this->getSettingsPages()),
            ]);
    }

    public function registerDynamicResources()
    {
        Filament::serving(function () {
            $menuSections = MenuSection::query()->get();
            $items = [
                NavigationItem::make(_nav('pages'))->sort(1)
                    ->url(StaticPageResource::getUrl('index'))
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
                    ->url(StaticPageResource::getUrl('index', ['activeTab' => $section->name]))
                    ->sort($section->sorting + 2)
                    ->group($section->name)
                    ->isActiveWhen(function () use ($section) {
                        return request()->route()->getName() === ListStaticPages::getRouteName() && (request('activeTab') == $section->name);
                    });
                if ($section->is_categories) {
                    $items[] = NavigationItem::make(_nav('categories'))
                        ->url(StaticPageResource::getUrl('index', ['activeTab' => $section->name._nav('categories')]))
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
        $resources = [
            StaticPageResource::class,
            AdminResource::class,
            MenuSectionResource::class,
            ContactFormResource::class,
            FormResource::class,
            TranslationResource::class,
            TemplateSectionResource::class,
            MenuResource::class,
            FieldResource::class,
            LayoutResource::class,
        ];
        Event::dispatch('cms.admin.navigation.resources', [&$resources]);

        return $resources;
    }

    public function getNavigationGroups(): array
    {
        $reference = [];
        $groups = [
            [
                'name' => 'communication',
                'icon' => 'heroicon-m-megaphone',
            ],
            [
                'name' => 'pages',
                'icon' => 'heroicon-m-book-open',
            ],
            [
                'name' => 'menu_sections',
                'icon' => 'heroicon-m-book-open',
            ],
            [
                'name' => 'design-template',
                'icon' => 'heroicon-m-light-bulb',
            ],
            [
                'name' => 'system',
                'icon' => 'heroicon-m-cog-6-tooth',
            ],
        ];
        Event::dispatch('cms.admin.navigation.groups', [&$groups]);
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
    }

    public function registerBranding()
    {
        Filament::registerRenderHook(
            PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER,
            function (): string {
                return <<<'HTML'
                <div class="text-xs text-center text-gray-500">
                    <p>Powered by <a href="https://s-cms.dev" target="_blank" class="hover:text-gray-700">SmartCms</a></p>
                </div>
                HTML;
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
        // Filament::registerRenderHook(
        //     PanelsRenderHook::SIDEBAR_NAV_END,
        //     function (): string {
        //         return <<<'HTML'
        //     <div class="flex justify-center gap-2">
        //     <a href="/" target="_blank" class="flex items-center justify-center p-2 font-semibold rounded-lg h-9 text-primary-600 bg-primary-500/10">
        //         Docs
        //     </a>
        //     <a href="/" target="_blank" class="relative flex items-center justify-center px-2 py-2 transition duration-75 bg-gray-100 rounded-lg outline-none fi-sidebar-item-button gap-x-3 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 dark:bg-white/5">
        //         Website
        //     </a>
        //     </div>
        //     HTML;
        //     }
        // );
    }

    public function getBrandName(): string
    {
        $brandName = company_name();
        if (strlen($brandName) == 0) {
            $brandName = 'SmartCms';
        }

        return $brandName;
    }

    public function addMacro()
    {
        Table::configureUsing(function (Table $table): void {
            $table
                // ->persistFiltersInSession()
                // ->persistSortInSession()
                ->paginationPageOptions([10, 25, 50, 100, 'all'])->defaultPaginationPageOption(25);
        });
        Field::macro('translatable', function () {
            if (is_multi_lang()) {
                return $this->hint('Translatable')
                    ->hintIcon('heroicon-m-language');
            }

            return $this;
        });
    }

    public function getPages(): array
    {
        $pages = [
            \Filament\Pages\Dashboard::class,
            TemplatePage::class,
        ];
        Event::dispatch('cms.admin.navigation.pages', [&$pages]);

        return $pages;
    }

    public function getSettingsPages(): array
    {
        $pages = [
            \SmartCms\Core\Admin\Pages\Settings\Settings::class,
        ];
        Event::dispatch('cms.admin.navigation.settings_pages', [&$pages]);

        return $pages;
    }

    public function getWidgets(): array
    {
        $widgets = [
            TopStaticPages::class,
            TopContactForms::class,
        ];
        Event::dispatch('cms.admin.widgets', [&$widgets]);

        return $widgets;
    }
}
