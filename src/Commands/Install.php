<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;

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
            '--tag' => 'translates',
        ]);
        // $this->call('vendor:publish', [
        //     '--tag' => 'settings',
        // ]);
        $this->info('Migrating database...');
        $this->call('migrate');
        $this->info('Install storage...');
        $this->call('storage:link');
        $this->info('Installed Smart CMS');
        if ($this->confirm('Do you wish to install node?')) {
            exec('npm install');
        }
    }
}
