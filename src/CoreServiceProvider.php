<?php

namespace SmartCms\Core;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('smart_cms_core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
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
            ])
            ->hasRoute(__DIR__.'/Routes/web.php');
        // ->publishes([
        //     'config' => config_path('core.php'),
        // ])
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'smart_cms');
    }
}
