<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'scms:update';

    protected $description = 'Update Smart CMS';

    public function handle()
    {
        $this->info('Updating Smart CMS...');
        exec('composer update smart-cms/core');
        $this->call('vendor:publish', [
            '--provider' => "SmartCms\Core\SmartCmsServiceProvider",
            '--tag' => 'public',
            '--force' => true,
        ]);
        $this->call('vendor:publish', [
            '--provider' => "SmartCms\Core\SmartCmsServiceProvider",
            '--tag' => 'templates',
        ]);
        $this->call('migrate');
        $this->info('Updated Smart CMS');
    }
}
