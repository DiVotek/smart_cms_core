<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;

class ChangeVite extends Command
{
    protected $signature = 'scms:vite';

    protected $description = 'Change vite config';

    public function handle()
    {
        $source = __DIR__.'/../../resources/vite.config.js';
        $destination = base_path('vite.config.js');
        copy($source, $destination);
        $this->info('Vite config copied');
    }
}
