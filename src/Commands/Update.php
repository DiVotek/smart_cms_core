<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use SmartCms\Core\Traits\HasHooks;

class Update extends Command
{
    use HasHooks;

    protected $signature = 'scms:update';

    protected $description = 'Update Smart CMS';

    public function handle()
    {
        $this->info('Updating Smart CMS...');
        $process = new \Symfony\Component\Process\Process(['composer', 'update', 'smart-cms/core']);
        $process->setTimeout(120);
        $process->run();
        if (! $process->isSuccessful()) {
            $this->error('Update process failed.');
            $this->error($process->getErrorOutput());
        } else {
            $this->info('Update process was successful.');
        }
        $this->call('vendor:publish', [
            '--provider' => "SmartCms\Core\SmartCmsServiceProvider",
            '--tag' => 'public',
            '--force' => true,
        ]);
        $this->call('vendor:publish', [
            '--provider' => "SmartCms\Core\SmartCmsServiceProvider",
            '--tag' => 'templates',
        ]);
        $this->call('migrate', ['--force' => true]);
        $this->applyHook('update', $this);
        $this->info('Updated Smart CMS');
    }
}
