<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\Layout;

class MakeLayout extends Command
{
    protected $signature = 'make:layout {name}';

    protected $description = 'Make layout for current scms template';

    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('.blade.php', '', $name);
        $name = str_replace('/', '.', $name);
        $path = resource_path('views/layouts/'.$name.'.blade.php');
        if (File::exists($path)) {
            $this->error('Layout already exists');

            return;
        }
        $stub = <<<EOT
{{-- @section_meta
{
    "name": "$name",
    "schema": [

    ]
}
@endsection_meta --}}

EOT;
        if (! File::exists(resource_path('views/layouts'))) {
            File::makeDirectory(resource_path('views/layouts'), 0755, true);
        }
        File::put($path, $stub);
        Layout::query()->create([
            'name' => ucfirst($name),
            'path' => $name,
            'schema' => [],
            'value' => [],
        ]);
        $this->info('Layout created successfully');
    }
}
