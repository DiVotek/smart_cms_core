<?php

namespace SmartCms\Core;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_smart_cms_settings_table',
                'create_smart_cms_languages_table',
                'create_smart_cms_translations_table',
                'create_smart_cms_translates_table',
                'create_smart_cms_admins_table',
                'create_smart_cms_forms_table',
                'create_smart_cms_contact_forms_table',
                'create_smart_cms_template_sections_table',
                'create_smart_cms_templates_table',
                'create_smart_cms_seo_table',
                'create_smart_cms_pages_table',
            ]);
        // ->publishes([
        //     'config' => config_path('core.php'),
        // ])
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'smart_cms');
    }
}
