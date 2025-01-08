<?php

namespace SmartCms\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Exceptions\TemplateConfigException;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
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
        }
    }

    public function getCachedConfig(): array
    {
        return Cache::rememberForever('scms_template_config', function () {
            $config = $this->parseConfig();

            return $config;
        });
    }

    public function parseConfig()
    {
        $config = [];
        $template = template();
        $templateConfigPath = scms_template_path($template);
        $yamlConfig = $templateConfigPath . '/config.yaml';
        if (! File::exists($yamlConfig)) {
            throw TemplateConfigException::notFound($template);
        }
        $config = Yaml::parse(File::get($yamlConfig));
        if (empty($config)) {
            throw TemplateConfigException::empty($template);
        }
        $this->validateConfig($config);
        $this->validateLayouts($templateConfigPath, $config);
        $this->validateSections($templateConfigPath, $config);

        return $config;
    }

    public function validateConfig(array $config)
    {
        if (! isset($config['name'])) {
            throw TemplateConfigException::nameNotExists($config['name']);
        }
        if (! isset($config['description'])) {
            throw TemplateConfigException::descriptionNotExists($config['name']);
        }
        if (! isset($config['author'])) {
            throw TemplateConfigException::authorNotExists($config['name']);
        }
        if (! isset($config['version'])) {
            throw TemplateConfigException::versionNotExists($config['name']);
        }
        if (! isset($config['theme'])) {
            throw TemplateConfigException::themeNotExists($config['name']);
        }
        if (isset($config['menu_sections'])) {
            foreach ($config['menu_sections'] as $menuSection) {
                if (! isset($menuSection['name'])) {
                    throw TemplateConfigException::menuSectionNameNotExists($config['name']);
                }
                if (! isset($menuSection['icon'])) {
                    throw TemplateConfigException::menuSectionIconNotExists($menuSection['name'], $config['name']);
                }
                if (! isset($menuSection['description'])) {
                    throw TemplateConfigException::menuSectionDescriptionNotExists($menuSection['name'], $config['name']);
                }
                if (! isset($menuSection['schema'])) {
                    throw TemplateConfigException::menuSectionSchemaNotExists($menuSection['name'], $config['name']);
                }
            }
        }
        if (isset($config['translates'])) {
            if (! is_array($config['translates'])) {
                throw TemplateConfigException::translatesNotValid($config['name']);
            }
        }
    }

    public function validateLayouts(string $path, array $config)
    {
        $dir = $path . 'layouts';
        if (! File::exists($dir) || ! File::isDirectory($dir)) {
            throw TemplateConfigException::layoutsNotExists($config['name']);
        }
        $mainLayout = $dir . '/main.blade.php';
        $mainlayoutConfig = $dir . '/main.yaml';
        if (! File::exists($mainLayout) || ! File::exists($mainlayoutConfig)) {
            throw TemplateConfigException::mainLayoutNotExists($config['name']);
        }
    }

    public function validateSections(string $path, array $config)
    {
        $dir = $path . 'sections';
        if (! File::exists($dir) || ! File::isDirectory($dir)) {
            throw TemplateConfigException::sectionsNotExists($config['name']);
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
        $sections = [];
        $templateConfigPath = scms_template_path(template());
        $dir = $templateConfigPath . 'sections';
        $dirs = File::directories($dir);
        foreach ($dirs as $directory) {
            foreach (File::files($directory) as $file) {
                if (File::extension($file) === 'yaml') {
                    $config = Yaml::parse(File::get($file));
                    $fileName = File::name($file);
                    if (File::exists($directory . '/' . $fileName . '.blade.php')) {
                        $config['path'] = $fileName . '/' . $fileName;
                    } else {
                        continue;
                    }
                    $sections[] = $config;
                }
            }
        }

        return array_filter($sections);
    }

    public function getLayouts(): array
    {
        $templateConfigPath = scms_template_path(template());
        $dir = $templateConfigPath . 'layouts';
        $configs = [];
        $mainLayout = $dir . '/main.yaml';
        $mainLayoutConfig = Yaml::parse(File::get($mainLayout));
        $mainLayoutConfig['path'] = 'main';
        $configs[] = $mainLayoutConfig;
        $dirs = File::directories($dir);
        foreach ($dirs as $directory) {
            foreach (File::files($directory) as $file) {
                if (File::extension($file) === 'yaml') {
                    $config = Yaml::parse(File::get($file));
                    $fileName = File::name($file);
                    if (File::exists($directory . '/' . $fileName . '.blade.php')) {
                        $config['path'] = $fileName . '/' . $fileName;
                    } else {
                        continue;
                    }
                    $configs[] = $config;
                }
            }
        }

        return array_filter($configs);
    }

    public function getMenuSections(): array
    {
        return $this->config['menu_sections'] ?? [];
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
            foreach (get_active_languages() as $lang) {
                if (Translation::query()->where('key', $translate['key'])->where('language_id', $lang->id)->doesntExist()) {
                    $translate['language_id'] = $lang->id;
                    Translation::create($translate);
                }
            }
        }
    }

    public function initLayouts()
    {
        $layouts = $this->getLayouts();
        foreach ($layouts as $layout) {
            $name = $layout['name'];
            $schema = $layout['schema'];
            if (! is_array($schema)) {
                $schema = [];
            }
            $value = [];
            Layout::query()->updateOrCreate(['name' => $name], [
                'path' => $layout['path'],
                'schema' => $schema,
                'value' => $value,
            ]);
        }
    }

    public function initLayout(string $path)
    {
        $layouts = $this->getLayouts();
        foreach ($layouts as $layout) {
            if ($layout['path'] != $path) {
                continue;
            }
            $name = $layout['name'];
            $schema = $layout['schema'];
            if (! is_array($schema)) {
                $schema = [];
            }
            $value = [];
            Layout::query()->updateOrCreate(['name' => $name], [
                'path' => $layout['path'],
                'schema' => $schema,
                'value' => $value,
            ]);
        }
    }

    public function initMenuSections()
    {
        $menuSections = $this->getMenuSections();
        foreach ($menuSections as $menuSection) {
            $name = $menuSection['name'];
            $icon = $menuSection['icon'];
            $isCategories = $menuSection['is_categories'] ?? false;
            $customFields = $menuSection['schema'] ?? [];
            $slug = \Illuminate\Support\Str::slug($name);
            $parent_id = null;
            if (Page::query()->where('slug', $slug)->exists()) {
                $parent_id = Page::query()->where('slug', $slug)->first()->id;
            } else {
                $page = Page::query()->create([
                    'name' => $name,
                    'slug' => $slug,
                ]);
                $parent_id = $page->id;
            }
            MenuSection::query()->updateOrCreate(['name' => $name], [
                'icon' => $icon,
                'parent_id' => $parent_id,
                'is_categories' => $isCategories,
                'custom_fields' => $customFields,
                'sorting' => MenuSection::query()->max('sorting') + 1,
            ]);
        }
    }
}
