<?php

namespace SmartCms\Core;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SmartCmsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('smart_cms_core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations($this->getMigrations())
            ->hasRoute(__DIR__ . '/Routes/web.php');
    }
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/auth.php',
            'auth'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/settings.php', 'settings'
        );
        $this->mergeConfigFrom(__DIR__ . '/../config/core.php', 'smart_cms');
    }
}
