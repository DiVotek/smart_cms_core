<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class MakeLayout extends Command
{
    protected $signature = 'make:layout {name}';

    protected $description = 'Make layout for current scms template';

    public function handle()
    {
        $name = $this->argument('name');
        $templatePath = scms_template_path(template());
        $path = $templatePath.'/layouts/'.$name;
        if (File::exists($path)) {
            $this->error('Layout already exists');

            return;
        }
        File::makeDirectory($path, 0755, true);
        $defaultConfig = [
            'name' => $name,
            'schema' => [],
        ];
        $yamlConfig = Yaml::dump($defaultConfig, 2);
        File::put($path.'/'.$name.'.yaml', $yamlConfig);
        File::put($path.'/'.$name.'.blade.php', '');
    }
}
