<?php

namespace SmartCms\Core;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SmartCms\Core\Commands\Install;
use SmartCms\Core\Commands\Update;
use SmartCms\Core\Models\Translation;

class SmartCmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Install::class,
            Update::class,
        ]);
        $this->mergeAuthConfig();
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

    public function boot()
    {
        Blade::componentNamespace('SmartCms\\Core\\Components', 's');
        View::addNamespace('templates', scms_templates_path());
        if (Schema::hasTable(Translation::getDb())) {
            $this->app->bind('translations', function () {
                return Cache::rememberForever('translations', function () {
                    return Translation::query()->get();
                });
            });
        }
        // php artisan filament:assets
        // php artisan filament-phone-input:install
    }
}
