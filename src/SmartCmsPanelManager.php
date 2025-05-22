<?php

namespace SmartCms\Core;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
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
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditMenuSection;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditSeo;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditStaticPage;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditTemplate;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\ListStaticPages;
use SmartCms\Core\Admin\Support\SetupBranding;
use SmartCms\Core\Admin\Support\SetupMacro;
use SmartCms\Core\Admin\Support\SetupPages;
use SmartCms\Core\Admin\Support\SetupResources;
use SmartCms\Core\Admin\Support\SetupSettingsPages;
use SmartCms\Core\Admin\Support\SetupWidgets;
use SmartCms\Core\Middlewares\NoIndex;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Traits\HasHooks;

class SmartCmsPanelManager extends PanelProvider
{
    use HasHooks;

    public function panel(Panel $panel): Panel
    {
        if (defined('INSTALLER')) {
            return $panel->default()
                ->id('smart_cms_admin')
                ->path('admin');
        }
        if (! FacadesSchema::hasTable('settings')) {
            return $panel->default()
                ->id('smart_cms_admin')
                ->path('admin');
        }
        $this->registerDynamicResources();
        SetupMacro::run();

        return $panel
            ->default()
            ->id('smart_cms_admin')
            ->path('admin')
            ->login(Login::class)
            ->colors(config('shared.admin.colors', []))
            ->emailVerification()
            ->authGuard('admin')
            ->profile(Profile::class, isSimple: false)
            ->spa(_settings('system.spa_mode', true))
            ->font('Roboto')
            ->darkMode(false)
            ->favicon(validateImage(_settings('branding.favicon', '/favicon.ico')))
            ->brandName(SetupBranding::run())
            ->resources($this->getResources())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages($this->getPages())
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->databaseNotifications()
            ->widgets($this->getWidgets())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                NoIndex::class,
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
            $items = [];
            foreach ($menuSections as $section) {
                $badgeQuery = Page::query()->withoutGlobalScopes();
                if ($section->is_categories) {
                    $categories = Page::query()->where('parent_id', $section->parent_id)->pluck('id')->toArray();
                    $badgeQuery = $badgeQuery->whereIn('parent_id', $categories);
                } else {
                    $badgeQuery = $badgeQuery->where('parent_id', $section->parent_id);
                }
                $items[] = NavigationItem::make(_nav('items'))
                    ->url(StaticPageResource::getUrl('index', ['activeTab' => $section->name]))
                    ->sort($section->sorting + 2)
                    ->group($section->name)
                    ->badge($badgeQuery->count())
                    ->isActiveWhen(function () use ($section) {
                        return request()->route()->getName() === ListStaticPages::getRouteName() && (request('activeTab') == $section->name);
                    });
                if ($section->is_categories) {
                    $items[] = NavigationItem::make(_nav('categories'))
                        ->url(StaticPageResource::getUrl('index', ['activeTab' => $section->name._nav('categories')]))
                        ->sort($section->sorting + 1)
                        ->group($section->name)
                        ->badge(Page::query()->where('parent_id', $section->parent_id)->count())
                        ->isActiveWhen(function () use ($section) {
                            return request()->route()->getName() === ListStaticPages::getRouteName() && request('activeTab') == $section->name._nav('categories');
                        });
                }
                $items[] = NavigationItem::make(_nav('settings'))->sort($section->sorting + 3)
                    ->url(StaticPageResource::getUrl('edit', ['record' => $section->parent_id]))
                    ->isActiveWhen(function () use ($section) {
                        $route = request()->route()->getName();
                        $activeRoutes = [
                            EditStaticPage::getRouteName() => request('record') == $section->parent_id,
                            EditSeo::getRouteName() => request('record') == $section->parent_id,
                            EditTemplate::getRouteName() => request('record') == $section->parent_id,
                            EditMenuSection::getRouteName() => request('record') == $section->parent_id,
                        ];
                        foreach ($activeRoutes as $activeRoute => $isActive) {
                            if ($route === $activeRoute && $isActive) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->group($section->name);
            }
            Filament::registerNavigationItems($items);
            $groups = $this->getNavigationGroups();
            Filament::registerNavigationGroups($groups);
        });
    }

    public function getResources(): array
    {
        $resources = SetupResources::run();
        self::applyHook('navigation.resources', $resources);

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
            // [
            //     'name' => 'pages',
            //     'icon' => 'heroicon-m-book-open',
            // ],
            [
                'name' => 'menu_sections',
                'icon' => 'heroicon-m-book-open',
            ],
            [
                'name' => 'design-template',
                'icon' => 'heroicon-m-light-bulb',
            ],
            [
                'name' => 'modules',
                'icon' => 'heroicon-m-cube',
            ],
            [
                'name' => 'system',
                'icon' => 'heroicon-m-cog-6-tooth',
            ],
        ];
        self::applyHook('navigation.groups', $groups);
        foreach ($groups as $group) {
            if (! isset($group['name'])) {
                continue;
            }
            if ($group['name'] == 'menu_sections') {
                $menuSections = MenuSection::query()->get();
                foreach ($menuSections as $section) {
                    $icon = $section->icon ?? $group['icon'] ?? 'heroicon-m-book-open';
                    $reference[] = \Filament\Navigation\NavigationGroup::make($section->name)->icon($icon)->collapsed();
                }
            } else {
                $icon = $group['icon'] ?? 'heroicon-m-book-open';
                $reference[] = NavigationGroup::make(_nav($group['name']))->icon($icon)->collapsed();
            }
        }

        return $reference;
    }

    public function getPages(): array
    {
        $pages = SetupPages::run();
        self::applyHook('navigation.pages', $pages);

        return $pages;
    }

    public function getSettingsPages(): array
    {
        $pages = SetupSettingsPages::run();
        self::applyHook('navigation.settings_pages', $pages);

        return $pages;
    }

    public function getWidgets(): array
    {
        $widgets = SetupWidgets::run();
        self::applyHook('widgets', $widgets);

        return $widgets;
    }
}
