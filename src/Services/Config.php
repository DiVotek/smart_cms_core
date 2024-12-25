<?php

namespace SmartCms\Core\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\Translate;
use SmartCms\Core\Models\Translation;
use Symfony\Component\Yaml\Yaml;

class Config
{
    public array $config;

    public function __construct()
    {
        if (config('app.env') == 'production') {
            $this->config = $this->getCachedConfig();
        } else {
            $this->config = $this->parseConfig();
            // $this->initTranslates();
        }
    }

    public function getCachedConfig(): array
    {
        return Cache::rememberForever('scms_template_config', function () {
            $config = $this->parseConfig();
            // $this->initTranslates();
            return $config;
        });
    }

    public function parseConfig()
    {
        $config = [];
        $template = template();
        $templateConfigPath = scms_template_path($template);
        $yamlConfig = $templateConfigPath . '/config.yaml';
        if (File::exists($yamlConfig)) {
            $config = Yaml::parse(File::get($yamlConfig));
        } else {
            $jsonConfig = $templateConfigPath . '/config.json';
            if (File::exists($jsonConfig)) {
                $config = json_decode(File::get($jsonConfig), true);
            }
        }
        if (empty($config)) {
            throw new Exception('Config file not found');
        }
        $this->validateConfig($config);
        return $config;
    }

    public function validateConfig(array $config)
    {
        $required = ['name', 'description', 'author', 'version', 'sections'];
        foreach ($required as $key) {
            if (! array_key_exists($key, $config)) {
                throw new Exception('Config file is missing required key: ' . $key);
            }
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getTheme(): array
    {
        return $this->config['theme'] ?? [];
    }

    public function getSections(): array
    {
        return $this->config['sections'] ?? [];
    }

    public function getCustomFields(): array
    {
        return $this->config['custom_fields'] ?? [];
    }

    public function getEmailTemplates(): array
    {
        return $this->config['email_templates'] ?? [];
    }

    public function getTranslates(): array
    {
        return $this->config['translates'] ?? [];
    }

    public function initTranslates()
    {
        $translates = $this->getTranslates();
        foreach ($translates as $translate) {
            if(Translation::query()->where('key', $translate['key'])->doesntExist()) {
                $translate['language_id'] = main_lang_id();
                Translation::create($translate);
            }
        }
    }

}
