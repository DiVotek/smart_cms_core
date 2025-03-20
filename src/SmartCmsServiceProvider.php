<?php

namespace SmartCms\Core;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use SmartCms\Core\Commands\ChangeVite;
use SmartCms\Core\Commands\Install;
use SmartCms\Core\Commands\MakeLayout;
use SmartCms\Core\Commands\MakeSection;
use SmartCms\Core\Commands\MakeTemplate;
use SmartCms\Core\Commands\Update;
use SmartCms\Core\Middlewares\HtmlMinifier;
use SmartCms\Core\Middlewares\Lang;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Translation;
use SmartCms\Core\Services\ExceptionHandler;
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
            MakeTemplate::class,
            ChangeVite::class,
        ]);
        $this->mergeAuthConfig();
        $this->mergePanelConfig();
        $this->mergeConfigFrom(
            __DIR__.'/../config/auth.php',
            'auth-2'
        );
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
            __DIR__.'/../public/' => public_path('smart_cms_core'),
        ], 'public');
        $this->publishes([
            __DIR__.'/../resources/templates' => scms_templates_path(),
        ], 'templates');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'smart_cms');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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
        Blade::componentNamespace('SmartCms\\Core\\Components', 's');
        View::addNamespace('templates', scms_templates_path());
        View::addNamespace('template', scms_template_path(template()));
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
            View::composer('template::*', function ($view) use ($host) {
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
                ];
                $data = $this->applyHook('view_share', $data);
                static::$viewShare = $data;
                foreach ($data as $key => $value) {
                    $view->with($key, $value);
                }
            });
        }
        $router->aliasMiddleware('lang', Lang::class);
        $router->aliasMiddleware('html.minifier', HtmlMinifier::class);
    }

    private function bootBladeComponents(): void
    {
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade) {
            $prefix = config('smart_cms.kit.prefix', '');
            /** @var BladeComponent $component */
            foreach (config('smart_cms.kit.components', []) as $alias => $component) {
                $blade->component($component, $alias, $prefix);
            }
        });
        $this->app->extend(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            function ($handler, $app) {
                return new ExceptionHandler($app, $handler);
            }
        );
    }
}
