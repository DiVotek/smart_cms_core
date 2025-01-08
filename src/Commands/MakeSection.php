<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class MakeSection extends Command
{
    protected $signature = 'make:section {name}';

    protected $description = 'Make section for current scms template';

    public function handle()
    {
        $name = $this->argument('name');
        $templatePath = scms_template_path(template());
        $sectionPath = $templatePath.'/sections/'.$name;
        if (File::exists($sectionPath)) {
            $this->error('Section already exists');

            return;
        }
        File::makeDirectory($sectionPath, 0755, true);
        $defaultConfig = [
            'name' => $name,
            'description' => '',
            'preview' => '',
            'schema' => [],
        ];
        $yamlConfig = Yaml::dump($defaultConfig, 2);
        $sectionConfigPath = $sectionPath.'/'.$name.'.yaml';
        File::put($sectionConfigPath, $yamlConfig);
        File::put($sectionPath.'/'.$name.'.blade.php', '');
    }
}
