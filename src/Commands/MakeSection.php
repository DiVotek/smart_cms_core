<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\TemplateSection;

class MakeSection extends Command
{
    protected $signature = 'make:section {name}';

    protected $description = 'Make section for current scms template';

    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('.blade.php', '', $name);
        $name = str_replace('/', '.', $name);
        $path = resource_path('views/sections/' . $name . '.blade.php');
        if (File::exists($path)) {
            $this->error('Section already exists');

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
        if (! File::exists(resource_path('views/sections'))) {
            File::makeDirectory(resource_path('views/sections'), 0755, true);
        }
        File::put($path, $stub);
        TemplateSection::query()->create([
            'name' => $name,
            'design' => $name,
            'schema' => [],
            'value' => [],
            'template' => template(),
        ]);
        $this->info('Section created successfully');
    }
}
