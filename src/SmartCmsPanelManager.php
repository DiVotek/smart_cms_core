<?php

namespace SmartCms\Core;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Actions\Action;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
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
use SmartCms\Core\Admin\Resources\AdminResource;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Admin\Resources\FieldResource;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Admin\Resources\LayoutResource;
use SmartCms\Core\Admin\Resources\MenuResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditMenuSection;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditSeo;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditStaticPage;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\EditTemplate;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages\ListStaticPages;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Admin\Widgets\HealthCheck;
use SmartCms\Core\Admin\Widgets\TopContactForms;
use SmartCms\Core\Admin\Widgets\TopStaticPages;
use SmartCms\Core\Admin\Widgets\VersionCheck;
use SmartCms\Core\Middlewares\NoIndex;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Singletone\Languages;
use SmartCms\Core\Services\Singletone\Settings;
use SmartCms\Core\Services\Singletone\Translates;
use SmartCms\Core\Traits\HasHooks;

class SmartCmsPanelManager extends PanelProvider
{
    use HasHooks;

    protected static $singletonRegistered = false;

    public function panel(Panel $panel): Panel
    {
        if (defined('INSTALLER')) {
            return $panel->default()
                ->id('smart_cms_admin')
                ->path('admin');
        }
        if (! self::$singletonRegistered) {
            $this->app->singleton('_settings', function () {
                return new Settings;
            });

            $this->app->singleton('_lang', function () {
                return new Languages;
            });

            $this->app->singleton('_trans', function () {
                return new Translates;
            });

            self::$singletonRegistered = true;
        }

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
            Css::make('scms-stylesheet', asset('/smart_cms_core/index.css')),
            JS::make('scms-script', asset('/smart_cms_core/index.js')),
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
            ->profile(Profile::class, isSimple: false)
            ->spa(config('app.spa', true))
            ->font('Roboto')
            ->darkMode(false)
            ->favicon(validateImage(_settings('branding.favicon', '/favicon.ico')))
            ->brandName($this->getBrandName())
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
            $items = [
                // NavigationItem::make(_nav('pages'))->sort(1)
                //     ->url(StaticPageResource::getUrl('index'))
                //     ->group(_nav('pages'))
                //     ->isActiveWhen(function () {
                //         return request()->route()->getName() === ListStaticPages::getRouteName() &&
                //             (! request('activeTab') || request('activeTab') == 'all');
                //     })->badge(function () use ($menuSections) {
                //         return Page::query()->whereNull('parent_id')->whereNotIn('id', $menuSections->pluck('id')->toArray())
                //             ->count();
                //     }),
            ];
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
                        ->url(StaticPageResource::getUrl('index', ['activeTab' => $section->name . _nav('categories')]))
                        ->sort($section->sorting + 1)
                        ->group($section->name)
                        ->badge(Page::query()->where('parent_id', $section->parent_id)->count())
                        ->isActiveWhen(function () use ($section) {
                            return request()->route()->getName() === ListStaticPages::getRouteName() && request('activeTab') == $section->name . _nav('categories');
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
        $resources = [
            StaticPageResource::class,
            AdminResource::class,
            ContactFormResource::class,
            FormResource::class,
            TranslationResource::class,
            TemplateSectionResource::class,
            MenuResource::class,
            FieldResource::class,
            LayoutResource::class,
        ];
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
                    ->label(_actions('view'))
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
            $table->paginationPageOptions([10, 25, 50, 100, 'all'])->defaultPaginationPageOption(25);
        });
        Field::macro('translatable', function () {
            if (is_multi_lang()) {
                return $this->hint('Translatable')
                    ->hintIcon('heroicon-m-language');
            }

            return $this;
        });
        Form::configureUsing(function (Form $form): void {
            $form->columns(1);
        });
        Select::configureUsing(function (Select $select): void {
            $select->native(false)->preload()->searchable();
        });
        EditAction::configureUsing(function (EditAction $action): void {
            $action->iconButton();
        });
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->label(_actions('create'))->icon('heroicon-m-plus')->createAnother(false);
        });
        ViewAction::configureUsing(function (ViewAction $action): void {
            $action->iconButton();
        });
        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action->iconButton();
        });
        DetachAction::configureUsing(function (DetachAction $action): void {
            $action->iconButton();
        });
        ActionsCreateAction::configureUsing(function (ActionsCreateAction $action): void {
            $action->label(_actions('create'))->icon('heroicon-m-plus')->createAnother(false);
        });
        AttachAction::configureUsing(function (AttachAction $action): void {
            $action->attachAnother(false);
        });
        ActionsDeleteAction::configureUsing(function (ActionsDeleteAction $action): void {
            $action->icon('heroicon-o-x-circle');
        });
        Action::macro('iconic', function () {
            return $this->iconButton()
                ->size(ActionSize::ExtraLarge);
        });
        Action::macro('create', function () {
            return $this->label(_actions('create'))
                ->modalSubmitActionLabel(_actions('create'))
                ->icon('heroicon-m-plus');
        });
        Action::macro('settings', function () {
            return $this->label(_actions('settings'))
                ->icon('heroicon-m-cog-6-tooth')
                ->iconic()
                ->iconButton()->color('warning')
                ->tooltip(_actions('settings'));
        });
        Action::macro('template', function () {
            return $this->label(_actions('template'))
                ->icon('heroicon-o-square-3-stack-3d')
                ->iconButton()
                ->tooltip(_actions('template'))
                ->color(Color::Blue);
        });
        Action::macro('help', function (string $description = '') {
            return $this->label(_actions('help'))
                ->icon('heroicon-o-question-mark-circle')
                ->iconic()
                ->modalFooterActions([])
                ->modalDescription($description)
                ->tooltip(_actions('help'));
        });
    }

    public function getPages(): array
    {
        $pages = [
            \Filament\Pages\Dashboard::class,
        ];
        self::applyHook('navigation.pages', $pages);

        return $pages;
    }

    public function getSettingsPages(): array
    {
        $pages = [
            \SmartCms\Core\Admin\Pages\Settings\Settings::class,
        ];
        self::applyHook('navigation.settings_pages', $pages);

        return $pages;
    }

    public function getWidgets(): array
    {
        $widgets = [
            TopStaticPages::class,
            TopContactForms::class,
            HealthCheck::class,
            VersionCheck::class,
        ];
        self::applyHook('widgets', $widgets);

        return $widgets;
    }
}
