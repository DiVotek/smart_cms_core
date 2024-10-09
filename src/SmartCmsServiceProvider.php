<?php

namespace SmartCms\Core;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SmartCms\Core\Models\Translation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SmartCmsServiceProvider extends ServiceProvider // extends PackageServiceProvider
{
    // public function configurePackage(Package $package): void
    // {
    //     $package
    //         ->name('smart_cms_core')
    //         // ->hasConfigFile()
    //         ->hasViews()
    //         ->hasMigrations($this->getMigrations())
    //         ->hasRoute(__DIR__.'/Routes/web.php');
    // }

    public function getMigrations(): array
    {
        return [
            'create_settings_table',
            'create_languages_table',
            'create_translations_table',
            'create_translates_table',
            'create_admins_table',
            'create_forms_table',
            'create_contact_forms_table',
            'create_template_sections_table',
            'create_templates_table',
            'create_seo_table',
            'create_pages_table',
        ];
    }

    public function register()
    {
        $this->mergeAuthConfig();
        $this->mergeConfigFrom(
            __DIR__.'/../config/auth.php',
            'auth-2'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/settings.php',
            'settings'
        );
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'smart_cms');

        $this->publishes([
            __DIR__.'/../resources/admin' => public_path('smart_cms_core'),
        ], 'public');
        $this->publishes([
            __DIR__.'/../resources/templates' => scms_templates_path(),
        ], 'templates');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'smart_cms');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
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
    }
}