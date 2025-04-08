<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\Layout;

class MakeLayout extends Command
{
    protected $signature = 'make:layout {name}';

    protected $description = 'Make layout for current scms template';

    /**
     * Get the layouts directory and create it if it doesn't exist
     *
     * @param string $subDir Optional subdirectory path
     * @return string The full path to the directory
     */
    protected function getLayoutDirectory(string $subDir = ''): string
    {
        $basePath = resource_path('views/layouts');
        $fullPath = $basePath;

        if (!empty($subDir)) {
            $fullPath = $basePath . '/' . trim($subDir, '/');
        }

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
            $this->info("Created directory: {$fullPath}");
        }

        return $fullPath;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('.blade.php', '', $name);
        $name = str_replace('.', '/', $name);

        // Extract directory part from the name if it contains slashes
        $dirPart = '';
        $layoutName = $name;

        if (str_contains($name, '/')) {
            $parts = explode('/', $name);
            $layoutName = array_pop($parts);
            $dirPart = implode('/', $parts);
        }

        // Get or create the directory
        $directory = $this->getLayoutDirectory($dirPart);
        $path = $directory . '/' . $layoutName . '.blade.php';

        if (File::exists($path)) {
            $this->info('Layout already exists');
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

        File::put($path, $stub);
        $name = str_replace('/', '.', $name);

        Layout::query()->create([
            'name' => ucfirst(str_replace('.', ' ', $name)),
            'path' => $name,
            'schema' => [],
            'value' => [],
        ]);

        $this->info('Layout created successfully');
    }
}
