<?php

namespace SmartCms\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Exceptions\TemplateConfigException;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Models\Translation;
use Symfony\Component\Yaml\Yaml;

class Config
{
    public array $config;

    public function __construct(bool $force = false)
    {
        if (config('app.env') == 'production' && ! $force) {
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
        $yamlConfig = $templateConfigPath.'/config.yaml';
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
                if (! isset($menuSection['layout'])) {
                    throw TemplateConfigException::menuSectionSchemaNotExists($menuSection['name'], $config['name']);
                }
                if (! isset($menuSection['categories'])) {
                    throw TemplateConfigException::menuSectionCategoriesNotExists($menuSection['name'], $config['name']);
                }
                if (! isset($menuSection['items'])) {
                    throw TemplateConfigException::menuSectionItemsNotExists($menuSection['name'], $config['name']);
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
        $dir = $path.'layouts';
        if (! File::exists($dir) || ! File::isDirectory($dir)) {
            throw TemplateConfigException::layoutsNotExists($config['name']);
        }
        $mainLayout = $dir.'/main.blade.php';
        $mainlayoutConfig = $dir.'/main.yaml';
        if (! File::exists($mainLayout) || ! File::exists($mainlayoutConfig)) {
            throw TemplateConfigException::mainLayoutNotExists($config['name']);
        }
    }

    public function validateSections(string $path, array $config)
    {
        $dir = $path.'sections';
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
        $dir = $templateConfigPath.'sections';
        $dirs = File::directories($dir);
        foreach ($dirs as $directory) {
            foreach (File::files($directory) as $file) {
                if (File::extension($file) === 'yaml') {
                    $config = Yaml::parse(File::get($file));
                    $fileName = File::name($file);
                    if (File::exists($directory.'/'.$fileName.'.blade.php')) {
                        $config['path'] = $fileName.'/'.$fileName;
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
        $dir = $templateConfigPath.'layouts';
        $configs = [];
        $mainLayout = $dir.'/main.yaml';
        $mainLayoutConfig = Yaml::parse(File::get($mainLayout));
        $mainLayoutConfig['path'] = 'main';
        $configs[] = $mainLayoutConfig;
        $dirs = File::directories($dir);
        foreach ($dirs as $directory) {
            foreach (File::files($directory) as $file) {
                if (File::extension($file) === 'yaml') {
                    $config = Yaml::parse(File::get($file));
                    $fileName = File::name($file);
                    if (File::exists($directory.'/'.$fileName.'.blade.php')) {
                        $config['path'] = $fileName.'/'.$fileName;
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

    public function initLayouts($isUpdate = false)
    {
        $layouts = $this->getLayouts();
        foreach ($layouts as $layout) {
            $this->initLayout($layout['path'], $isUpdate);
        }
    }

    public function initLayout(string $path, bool $isUpdate = false)
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
            $original = Layout::query()->where('path', $path)->where('template', template())->first();
            if (! $original) {
                $layout = Layout::query()->create([
                    'name' => $name,
                    'path' => $layout['path'],
                    'schema' => $schema,
                    'status' => 1,
                    'template' => template(),
                    'value' => [],
                    'can_be_used' => true,
                ]);
                if ($isUpdate && $path == 'main') {
                    Page::query()->withoutGlobalScopes()->update([
                        'layout_id' => $layout->id,
                        'layout_settings' => [],
                    ]);
                }
            } else {
                $original->update([
                    'name' => $name,
                    'schema' => $schema,
                ]);
            }
        }
    }

    public function initMenuSections()
    {
        $menuSections = $this->getMenuSections();
        foreach ($menuSections as $menuSection) {
            $name = $menuSection['name'];
            $icon = $menuSection['icon'];
            $isCategories = $menuSection['categories']['enabled'] ?? false;
            $categoriesLayout = $menuSection['categories']['layout'] ?? null;
            $itemsLayout = $menuSection['items']['layout'] ?? null;
            $items_layout_id = Layout::query()->where('template', template())->where('path', $itemsLayout.'/'.$itemsLayout)->first()->id ?? null;
            $categories_layout_id = Layout::query()->where('template', template())->where('path', $categoriesLayout.'/'.$categoriesLayout)->first()->id ?? null;
            if ($categories_layout_id) {
                Layout::query()->where('id', $categories_layout_id)->update(['can_be_used' => false]);
            }
            if ($items_layout_id) {
                Layout::query()->where('id', $items_layout_id)->update(['can_be_used' => false]);
            }
            $customFields = $menuSection['items']['schema'] ?? [];
            $slug = \Illuminate\Support\Str::slug($name);
            $parent_id = null;
            $existedSection = MenuSection::query()->where('name', $name)->first();
            if (! $existedSection || $existedSection->parent_id == null) {
                if (Page::query()->where('slug', $slug)->exists()) {
                    $parent_id = Page::query()->where('slug', $slug)->first()->id;
                } else {
                    try {
                        $page = Page::query()->create([
                            'name' => $name ?? $slug,
                            'slug' => $slug,
                        ]);
                        $parent_id = $page->id;
                    } catch (\Exception $e) {
                        dd($e, get_defined_vars());
                        $parent_id = null;
                    }
                }
            } else {
                $parent_id = $existedSection->parent_id;
            }
            $menu_section_page_layout = $menuSection['layout'] ?? null;
            if ($menu_section_page_layout) {
                $page_layout = Layout::query()->where('template', template())->where('path', $menu_section_page_layout.'/'.$menu_section_page_layout)->first();
                if ($page_layout) {
                    Page::query()->where('id', $parent_id)->update(['layout_id' => $page_layout->id]);
                }
            }
            $data = [
                'icon' => $icon,
                'parent_id' => $parent_id,
                'is_categories' => $isCategories,
                'custom_fields' => $customFields,
                'sorting' => MenuSection::query()->max('sorting') + 1,
                'categories_layout_id' => $categories_layout_id,
                'items_layout_id' => $items_layout_id,
            ];
            MenuSection::query()->updateOrCreate(['name' => $name], $data);
        }
    }

    public function initSections()
    {
        $sections = $this->getSections();
        foreach ($sections as $section) {
            if (isset($section['type']) && TemplateSection::query()->withoutGlobalScopes()->where('type', $section['type'])->exists()) {
                TemplateSection::query()->withoutGlobalScopes()->where('type', $section['type'])->update([
                    'schema' => $section['schema'] ?? [],
                    'template' => template(),
                ]);
            } else {
                $model = TemplateSection::query()->withoutGlobalScopes()->where('template', template())->where('design', $section['path'])->where('type', $section['type'] ?? null)->first();
                if ($model) {
                    $model->update([
                        'schema' => $section['schema'] ?? [],
                    ]);
                } else {
                    TemplateSection::query()->withoutGlobalScopes()->create([
                        'name' => $section['name'],
                        'template' => template(),
                        'design' => $section['path'],
                        'schema' => $section['schema'] ?? [],
                        'type' => $section['type'] ?? null,
                    ]);
                }
            }
        }
    }

    public function init($isUpdate = false)
    {
        $this->initSections($isUpdate);
        $this->initLayouts($isUpdate);
        $this->initMenuSections();
        $this->initTranslates();
    }
}
