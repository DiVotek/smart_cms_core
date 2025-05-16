<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Install extends Command
{
    protected $signature = 'scms:install';

    protected $description = 'Install Smart CMS';

    public function handle()
    {
        $this->info('Installing Smart CMS...');
        $this->info('Publishing configuration...');
        $this->info('Publishing assets...');
        $this->call('vendor:publish', [
            '--provider' => "SmartCms\Core\SmartCmsServiceProvider",
            '--tag' => 'public',
        ]);
        $this->info('Publishing templates...');
        $this->call('filament-phone-input:install');
        $this->call('filament:assets');
        $this->info('Installing admin panel...');
        $this->call('filament:install');
        $this->info('Publishing newer livewire...');
        $this->call('vendor:publish', [
            '--tag' => 'livewire:assets',
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'theme',
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'smart_cms.resources',
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'menu_sections',
        ]);
        $this->info('Migrating database...');
        $this->call('migrate');
        $this->info('Install storage...');
        $this->call('storage:link');
        $this->info('Installed Smart CMS');
        if (File::exists(public_path('robots.txt'))) {
            File::move(public_path('robots.txt'), public_path('robots.txt.backup'));
        }
        if (File::exists(public_path('sitemap.xml'))) {
            File::move(public_path('sitemap.xml'), public_path('sitemap.xml.backup'));
        }
        if ($this->confirm('Do you wish to install node?')) {
            exec('npm install');
        }
    }
}
