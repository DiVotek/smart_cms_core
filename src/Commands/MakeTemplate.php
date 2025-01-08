<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class MakeTemplate extends Command
{
    protected $signature = 'make:template {name}';

    protected $description = 'Make template for scms';

    public function handle()
    {
        $name = $this->argument('name');
        $path = scms_templates_path() . $name;
        if (File::exists($path)) {
            $this->error('Template already exists');
            return;
        }
        File::makeDirectory($path, 0755, true);
        $this->makeDefaultLayout($name);
        File::makeDirectory($path . '/sections', 0755, true);
        File::makeDirectory($path . '/assets', 0755, true);
        File::makeDirectory($path . '/assets/js', 0755, true);
        File::makeDirectory($path . '/assets/css', 0755, true);
        File::put($path . '/config.yaml', Yaml::dump([
            'name' => $name,
            'description' => '',
            'author' => '',
            'version' => '0.1',
            'theme' => [],
            'menu_sections' => [],
        ], 2));
        File::put($path . '/assets/js/app.js', '');
        File::put($path . '/assets/css/app.css', '');
    }

    public function makeDefaultLayout(string $template)
    {
        $path = scms_templates_path() . '/' . $template;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $layoutPath = $path . '/layouts';
        if (File::exists($layoutPath)) {
            $this->error('Layout already exists');
            return;
        }
        File::makeDirectory($layoutPath, 0755, true);
        $defaultConfig = [
            'name' => 'Main',
            'schema' => [],
        ];
        $yamlConfig = Yaml::dump($defaultConfig, 2);
        File::put($layoutPath . '/main.yaml', $yamlConfig);
        File::put($layoutPath . '/main.blade.php', '');
    }
}
