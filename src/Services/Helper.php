<?php

namespace SmartCms\Core\Services;

use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\Form;

class Helper
{
    public static function getComponents(): array
    {
        $sections = [];
        $directoryPath = scms_template_path(template() . '/sections');
        if (! File::exists($directoryPath)) {
            return [];
        }
        $config = scms_template_config();
        $files = File::files($directoryPath);
        $configSections = $config['sections'] ?? [];
        foreach ($configSections as $section) {
            if (isset($section['name']) && isset($section['file'])) {
                $fileName = $section['file'];
                $sections[$fileName] = $section['name'];
            }
        }
        foreach ($files as $file) {
            $name = basename($file, '.blade.php');
            if (in_array($name, $sections)) {
                continue;
            }
            $sections[$name] = ucfirst($name);
        }
        $directories = File::directories($directoryPath);
        foreach ($directories as $directory) {
            foreach (File::files($directory) as $file) {
                $lastDir = basename($directory);
                $files[$lastDir . '/' . basename($file)] = ucfirst($lastDir) . ' - ' . ucfirst(basename($file, '.blade.php'));
            }
        }

        return $sections;
    }

    public static function getComponentSchema(string $component): array
    {
        $file = null;
        $schema = [];
        $isSchema = false;
        $config = scms_template_config();
        $sections = $config['sections'] ?? [];
        foreach ($sections as $section) {
            if (! isset($section['file'])) {
                continue;
            }
            if ($section['file'] == $component) {
                if (isset($section['schema'])) {
                    $schema = $section['schema'];
                    $isSchema = true;
                } else {
                    $file = scms_template_path(template()) . 'sections/' . $section['file'] . '.blade.php';
                }
            }
        }
        if (!$isSchema) {
            if (! $file) {
                $file = scms_template_path(template()) . 'sections/' . strtolower($component) . '.blade.php';
            }
            if (! File::exists($file)) {
                return [];
            }
            $file = File::get($file);
            $variables = self::extractVariables($file);
            foreach ($variables as $key => $variable) {
                if (is_array($variable)) {
                    $schema[$key] = [
                        'name' => $key,
                        'type' => 'array',
                        'schema' => $variable,
                    ];
                } else {
                    $schema[$key] = $variable;
                }
            }
        }
        $newSchema = [];
        foreach($schema as $key => $value){
            $newSchema[$key] = self::getVariableSchema($value);
        }
        return $newSchema;
    }

    public static function getComponentClass(string $component): array
    {
        $schema = self::getComponentSchema($component);
        $fields = [];
        foreach($schema as $key => $value){
            $fields = array_merge($fields,self::parseVariable($value, 'value.'));
        }
        return $fields;
        $config = scms_template_config();
        $sections = $config['sections'] ?? [];
        foreach ($sections as $section) {
            if (! isset($section['file'])) {
                continue;
            }
            if ($section['file'] == $component) {
                if (isset($section['schema'])) {
                    return self::parseSchema($section['schema'], 'value.');
                } else {
                    $file = scms_template_path(template()) . 'sections/' . $section['file'] . '.blade.php';
                }
            }
        }
        if (! $file) {
            $file = scms_template_path(template()) . 'sections/' . strtolower($component) . '.blade.php';
        }
        if (! File::exists($file)) {
            return [];
        }
        $file = File::get($file);
        $variables = self::extractVariables($file);
        foreach ($variables as $key => &$variable) {
            if (is_array($variable)) {
                $variables[$key] = [
                    'name' => $key,
                    'type' => 'array',
                    'schema' => $variable,
                ];
            }
        }
        $schema = self::parseSchema($variables, 'value.');
        return $schema;
    }

    public static function getLabelFromField(string $field): string
    {
        $field = preg_replace('/(?<!^)([A-Z])/', ' $1', $field);
        $field = explode('_', $field);
        $field = implode(' ', $field);

        return __(ucfirst(strtolower($field)));
    }

    public static function getTemplateByComponent(string $name): array
    {
        $template = setting(config('settings.template'), 'Base');
        if (! $template) {
            return [];
        }
        $types = [];
        $templatePath = base_path('templates/' . $template . '/' . $name . '/');
        try {
            $files = File::files($templatePath);
        } catch (\Exception $e) {
            return [];
        }
        foreach ($files as $file) {
            $types[$file->getFilenameWithoutExtension()] = $file->getFilenameWithoutExtension();
        }

        return $types;
    }

    public static function getLayoutTemplate($isFooter = false): array
    {
        $files = glob(base_path('template/layout/*.blade.php'));
        $reference = [];
        foreach ($files as $file) {
            $name = basename($file, '.blade.php');
            if (str_contains($name, 'footer') && ! $isFooter) {
                continue;
            }
            if (str_contains($name, 'header') && $isFooter) {
                continue;
            }
            $name = explode('-', $name);
            $prettyName = ucfirst($name[1]) . ' from collection ' . strtoupper($name[0]);
            $reference[basename($file, '.blade.php')] = $prettyName;
        }

        return $reference;
        $files = File::allFiles(base_path('template/layout'));
        $templates = [];
        $footers = [];
        $headers = [];
        foreach ($files as $file) {
            $name = $file->getFilenameWithoutExtension();
            $frontName = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);
            $backName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
            if (str_contains($name, 'Footer')) {
                $footers[$backName] = $frontName;
            }
            if (str_contains($name, 'Header')) {
                $headers[$backName] = $frontName;
            }
        }
        if ($isFooter) {
            return $footers;
        }

        return $headers;
    }

    public static function extractVariables($fileContent)
    {
        $variables = [];
        $arrayProperties = [];

        // Check for components like <x-heading> and <x-description>
        if (str_contains($fileContent, '<x-heading')) {
            $variables[] = 'heading';
        }
        if (str_contains($fileContent, '<x-description')) {
            $variables[] = 'description';
        }
        if (str_contains($fileContent, 'form-builder')) {
            $variables[] = 'form';
        }

        // Match {{ $variable }} and {{ $variable['key'] }}
        preg_match_all('/\{\{\s*\$([a-zA-Z_][a-zA-Z0-9_]*)/', $fileContent, $matches1);
        $variables = array_merge($variables, $matches1[1]);

        // Match component attributes like :options="$options" or :anyVar="$anyVar"
        preg_match_all('/:\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*"\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*"/', $fileContent, $matches2);
        $variables = array_merge($variables, $matches2[2]);

        // Updated regex for @foreach to handle both ($key => $value) and ($value) syntax
        preg_match_all('/@foreach\s*\(\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s+as\s*(?:\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=>\s*)?\$([a-zA-Z_][a-zA-Z0-9_]*)\)/', $fileContent, $matches3);
        $arrayVariables = $matches3[1]; // Capture plural form (e.g., $modules)
        $singularVariables = $matches3[3]; // Capture singular form (e.g., $module)
        foreach ($arrayVariables as $var) {
            if ($var == 'categories' || $var == 'news' || $var == 'products' || $var = 'blogCategories' || $var = 'blogArticles') {
                $variables[] = $var;
            }
        }
        // Loop over all foreaches and handle multiple cases
        for ($i = 0; $i < count($arrayVariables); $i++) {
            $arrayVar = $arrayVariables[$i];
            $singularVar = $singularVariables[$i];
            // Remove singular variables from global variables list
            $variables = array_diff($variables, [$singularVar]);
            // Match $singular['key'] inside the foreach and map it to the plural form
            preg_match_all('/\$\s*' . preg_quote($singularVar) . '\[\'([a-zA-Z_][a-zA-Z0-9_]*)\'\]/', $fileContent, $matches4);
            foreach ($matches4[1] as $property) {
                $arrayProperties[$arrayVar][] = $property;
            }

            // Match singular variables directly used (e.g., {{$module['subtitle']}})
            preg_match_all('/\{\{\s*\$' . preg_quote($singularVar) . '\[' . '\'([a-zA-Z_][a-zA-Z0-9_]*)\'\]\s*\}\}/', $fileContent, $matches5);
            foreach ($matches5[1] as $property) {
                $arrayProperties[$arrayVar][] = $property;
            }
        }
        // Remove duplicates from the variables
        $variables = array_unique($variables);
        // Combine variables and array properties
        $result = [];
        foreach ($variables as $variable) {
            if (! isset($arrayProperties[$variable])) {
                $result[] = $variable;
            }
        }

        foreach ($arrayProperties as $key => &$value) {
            $value = array_unique($value);
        }

        $result = array_merge($result, $arrayProperties);

        // Clean up unnecessary variables
        if (array_search('options', $result) !== false) {
            unset($result[array_search('options', $result)]);
        }
        if (array_search('title', $result) !== false) {
            unset($result[array_search('title', $result)]);
        }
        if (array_search('key', $result) !== false) {
            unset($result[array_search('key', $result)]);
        }
        if (array_search('breadcrumbs', $result) !== false) {
            unset($result[array_search('breadcrumbs', $result)]);
        }

        return $result;
    }

    public static function getTemplates(): array
    {
        $templates = [];
        $templatesPath = base_path('scms/templates/');
        $files = File::directories($templatesPath);
        foreach ($files as $file) {
            $templateName = basename($file);
            $templates[$templateName] = $templateName;
        }

        return $templates;
    }

    public static function getFormTemplates()
    {
        $path = scms_template_path(template() . '/forms');
        if (File::exists($path) && File::isDirectory($path)) {
            $dirs = File::directories($path);
            $data = [];
            foreach ($dirs as $dir) {
                $data[basename($dir)] = ucfirst(basename($dir));
            }

            return $data;
        }
    }

    public static function parseSchema(array $schema, string $prefix = ''): array
    {
        $vars = [];
        $varSchema = [];
        foreach ($schema as $var) {
            $vars = array_merge($vars, self::parseVariable($var, $prefix));
            $varSchema[] = self::getVariableSchema($var, $prefix);
        }
        $vars[] = Hidden::make('value.schema')->default($varSchema);
        return $vars;
    }

    public static function getVariableSchema(array|string $var, string $prefix = ''): array
    {
        if (is_array($var) && isset($var['name']) && isset($var['type'])) {
            return $var;
        }
        $variable = [
            'name' => $var,
        ];
        if (str_contains('heading', $var)) {
            $variable['type'] = VariableTypes::HEADING->value;
        }
        if (str_contains('description', $var)) {
            $variable['type'] = VariableTypes::DESCRIPTION->value;
        }
        if (str_contains('image', $var)) {
            $variable['type'] = VariableTypes::IMAGE->value;
        }
        if (str_contains('status', $var)) {
            $variable['type'] = VariableTypes::BOOLEAN->value;
        }
        if (str_contains('links', $var)) {
            $variable['type'] = VariableTypes::LINKS->value;
        }
        if (str_contains($var, 'phone')) {
            $variable['type'] = VariableTypes::PHONE->value;
        }
        if (str_contains($var, 'phones')) {
            $variable['type'] = VariableTypes::PHONES->value;
        }
        if (str_contains($var, 'email')) {
            $variable['type'] = VariableTypes::EMAIL->value;
        }
        if (str_contains($var, 'address')) {
            $variable['type'] = VariableTypes::ADDRESS->value;
        }
        if (str_contains($var, 'addresses')) {
            $variable['type'] = VariableTypes::ADDRESSES->value;
        }
        if (str_contains($var, 'schedule')) {
            $variable['type'] = VariableTypes::SCHEDULE->value;
        }
        if (str_contains($var, 'schedules')) {
            $variable['type'] = VariableTypes::SCHEDULES->value;
        }
        if (str_contains($var, 'socials')) {
            $variable['type'] = VariableTypes::SOCIALS->value;
        }
        if (str_contains($var, 'page')) {
            $variable['type'] = VariableTypes::PAGE->value;
        }
        if (str_contains($var, 'pages')) {
            $variable['type'] = VariableTypes::PAGES->value;
        }
        if (! isset($variable['type'])) {
            $variable['type'] = VariableTypes::TEXT->value;
        }
        if (str_contains($var, 'form')) {
            $variable['type'] = VariableTypes::FORM->value;
        }

        return $variable;
    }

    public static function parseVariable(array|string $var, string $prefix = ''): array
    {
        // if (is_array($var) && isset($var['type']) && $var['type'] == 'array') {
        //     return [];
        // }
        if (is_array($var) && isset($var['name']) && isset($var['type'])) {
            return self::parseVariableByType($var, $prefix);
        }
        $variable = self::getVariableSchema($var, $prefix);

        return self::parseVariableByType($variable, $prefix);
    }

    public static function parseVariableByType(array $var, string $prefix = ''): array
    {
        try {
            return VariableTypes::fromType($var['type'])->toFilamentField($var, $prefix);
        } catch (Exception $exception) {
            dd($var, $exception->getMessage());
        }
    }
}
