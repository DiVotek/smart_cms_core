<?php

namespace SmartCms\Core;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use SmartCms\Core\Actions\FormSubmit;
use SmartCms\Core\Commands\Install;
use SmartCms\Core\Commands\MakeAdmin;
use SmartCms\Core\Commands\MakeLayout;
use SmartCms\Core\Commands\MakeSection;
use SmartCms\Core\Commands\Update;
use SmartCms\Core\Hooks\LayoutHooks;
use SmartCms\Core\Hooks\MenuHooks;
use SmartCms\Core\Livewire\Footer;
use SmartCms\Core\Livewire\Header;
use SmartCms\Core\Livewire\Noty;
use SmartCms\Core\Livewire\Page as LivewirePage;
use SmartCms\Core\Middlewares\HtmlMinifier;
use SmartCms\Core\Middlewares\Lang;
use SmartCms\Core\Middlewares\Maintenance;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Translation;
use SmartCms\Core\Services\ExceptionHandler;
use SmartCms\Core\Support\Actions\ActionRegistry;
use SmartCms\Core\Support\Seo;
use SmartCms\Core\Support\Template;
use SmartCms\Core\Traits\HasHooks;

class SmartCmsServiceProvider extends ServiceProvider
{
    use HasHooks;

    public static $viewShare = [];

    public function register()
    {
        $this->commands([
            Install::class,
            Update::class,
            MakeSection::class,
            MakeLayout::class,
            MakeAdmin::class,
        ]);
        $this->mergeAuthConfig();
        $this->mergePanelConfig();
        $this->mergeConfigFrom(
            __DIR__.'/../config/settings.php',
            'settings'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/shared.php',
            'shared'
        );
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'smart_cms');
        $this->publishes([
            __DIR__.'/../resources/admin' => public_path('smart_cms_core'),
            __DIR__.'/../config/theme.php' => config_path('theme.php'),
            __DIR__.'/../config/translates.php' => config_path('translates.php'),
            __DIR__.'/../resources/images/' => storage_path('app/public'),
        ], 'smart_cms.resources');
        $this->publishes([
            __DIR__.'/../resources/views/livewire' => resource_path('views/livewire'),
            __DIR__.'/../resources/views/forms' => resource_path('views/forms'),
        ], 'smart_cms.views');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'smart_cms');
        $this->loadMigrationsFrom(__DIR__.'/../database/new_migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'smart_cms');
        if (File::exists(public_path('robots.txt'))) {
            File::move(public_path('robots.txt'), public_path('robots.txt.backup'));
        }
        if (File::exists(public_path('sitemap.xml'))) {
            File::move(public_path('sitemap.xml'), public_path('sitemap.xml.backup'));
        }
    }

    protected function mergeAuthConfig()
    {
        $packageAuth = require __DIR__.'/../config/auth.php';
        $appAuth = config('auth', []);
        if (isset($packageAuth['guards'])) {
            $appAuth['guards'] = array_merge(
                $appAuth['guards'] ?? [],
                $packageAuth['guards']
            );
        }
        if (isset($packageAuth['providers'])) {
            $appAuth['providers'] = array_merge(
                $appAuth['providers'] ?? [],
                $packageAuth['providers']
            );
        }
        config(['auth' => $appAuth]);
    }

    public function mergePanelConfig()
    {
        $appConfig = config('app', []);
        $appConfig['spa'] = env('SPA_MODE', false);
        config(['app' => $appConfig]);
    }

    public function boot(Router $router)
    {
        $router->aliasMiddleware('lang', Lang::class);
        $router->aliasMiddleware('html.minifier', HtmlMinifier::class);
        $router->aliasMiddleware('maintenance', Maintenance::class);
        if (defined('INSTALLER')) {
            return;
        }
        Blade::componentNamespace('SmartCms\\Core\\Components', 's');
        if (Schema::hasTable(Translation::getDb())) {
            $this->app->bind('translations', function () {
                return Cache::rememberForever('translations', function () {
                    return Translation::query()->get();
                });
            });
        }
        if (config('app.env') == 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        $this->bootBladeComponents();
        if (Schema::hasTable(Page::getDb())) {
            $host = Page::query()->where('slug', '')->first();
            Context::add('host', $host);
            View::composer('*', function ($view) use ($host) {
                if (! empty(static::$viewShare)) {
                    foreach (static::$viewShare as $key => $value) {
                        $view->with($key, $value);
                    }

                    return;
                }
                $data = [
                    'host' => $host->route(),
                    'hostname' => $host->name(),
                    'company_name' => company_name(),
                    'logo' => logo(),
                    'active_languages' => _lang_routes(),
                ];
                $data = $this->applyHook('view_share', $data);
                static::$viewShare = $data;
                foreach ($data as $key => $value) {
                    $view->with($key, $value);
                }
            });
        }
        if (Schema::hasTable('settings')) {
            $this->bindMailer();
            $this->bindTelegram();
            $this->bindName();
        }
        $router->aliasMiddleware('lang', Lang::class);
        $router->aliasMiddleware('html.minifier', HtmlMinifier::class);
        Layout::registerHook('before_update', [LayoutHooks::class, 'beforeUpdate']);
        Menu::registerHook('before_update', [MenuHooks::class, 'beforeUpdate']);
    }

    private function bootBladeComponents(): void
    {
        $this->app->singleton(Seo::class, fn () => new Seo);
        $this->app->singleton(Template::class, fn () => new Template);
        $this->app->alias(Seo::class, 'seo');
        $this->app->alias(Template::class, 'template');
        ActionRegistry::register('form_submit', new FormSubmit);
        Livewire::component('page', LivewirePage::class);
        Livewire::component('noty', Noty::class);
        Livewire::component('header', Header::class);
        Livewire::component('footer', Footer::class);
        Livewire::setUpdateRoute(function ($handle) {
            $isAdmin = request()->is('admin/*');

            return Route::post('/livewire/update', $handle)
                ->middleware($isAdmin ? ['web'] : ['web', Lang::class]);
        });
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade) {
            $prefix = config('smart_cms.kit.prefix', '');
            /** @var BladeComponent $component */
            foreach (config('smart_cms.kit.components', []) as $alias => $component) {
                $blade->component($component, $alias, $prefix);
            }
        });
        Blade::component('layout', \SmartCms\Core\Components\Layout\Layout::class);
        $this->app->extend(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            function ($handler, $app) {
                return new ExceptionHandler($app, $handler);
            }
        );
    }

    public function bindMailer()
    {
        $mailConfig = [
            'transport' => 'smtp',
            'scheme' => 'smtp',
            'host' => _settings('mail.host'),
            'port' => _settings('mail.port'),
            'username' => _settings('mail.username'),
            'password' => _settings('mail.password'),
            'timeout' => 15,
            'encryption' => _settings('mail.encryption'),
        ];
        $mailFrom = [
            'address' => _settings('mail.from'),
            'name' => _settings('mail.name'),
        ];
        $provider = _settings('mail.provider', 'sendmail');
        Config::set('mail.mailers.admin_scms', $mailConfig);
        Config::set('mail.from', $mailFrom);
        if ($provider == 'smtp') {
            Config::set('mail.default', 'admin_scms');
        } else {
            Config::set('mail.default', 'sendmail');
        }
    }

    public function bindTelegram()
    {
        Config::set('services.telegram-bot-api.token', _settings('telegram.token'));
    }

    public function bindName()
    {
        Config::set('app.name', company_name());
        Config::set('app.debug', _settings('system.debug', true));
    }
}
