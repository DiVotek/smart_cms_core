<?php

namespace SmartCms\Core\Services;

use App\Actions\ModuleDescriptionSchema;
use App\Actions\ModuleTitleSchema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Models\Form;

class Helper
{
    public static function getComponents(): array
    {
        $directoryPath = base_path('template/modules/');
        if (! File::exists($directoryPath)) {
            return [];
        }
        $files = File::files($directoryPath);
        $files = glob(base_path('template/modules/*.blade.php'));
        $reference = [];
        foreach ($files as $file) {
            $reference[basename($file, '.blade.php')] = basename($file, '.blade.php');
        }

        return $reference;
    }

    public static function getTemplates(): array
    {
        $templates = [];
        $templatesPath = base_path('templates/');
        $files = File::directories($templatesPath);
        foreach ($files as $file) {
            $templateName = basename($file);
            $templates[$templateName] = $templateName;
        }

        return $templates;
    }

    public static function getTemplateComponentTypes(string $template): array
    {
        $types = [];
        $templatePath = base_path('templates/' . $template . '/Modules/');
        if (! File::exists($templatePath)) {
            return $types;
        }
        $files = File::directories($templatePath);
        foreach ($files as $file) {
            $typeName = basename($file);
            $types[$typeName] = $typeName;
        }

        return $types;
    }

    public static function getComponentClass(string $component): array
    {
        if (!File::exists(base_path('template/modules/' . $component . '.blade.php'))) {
            return [];
        }
        $file = File::get(base_path('template/modules/' . $component . '.blade.php'));
        if (! $file) {
            return [];
        }
        $variables = self::extractVariables($file);
        $schema = [];
        foreach ($variables as $key => $variable) {
            if ($variable == 'form') {
                $schema[] = Select::make('value.' . $variable)->options(Form::query()->pluck('name', 'id')->toArray());
                continue;
            }
            if (is_array($variable)) {
                $fields = [];
                foreach ($variable as $field) {
                    if (str_contains($field, 'image')) {
                        $fields[] = Schema::getImage($field);
                    } else if (str_contains($field, 'description')) {
                        $fields[] = Textarea::make(main_lang() . '_' . $field)->required();
                        if (is_multi_lang()) {
                            foreach (get_active_languages() as $lang) {
                                if ($lang->id == main_lang_id()) {
                                    continue;
                                }
                                $fields[] = Textarea::make($lang->slug . '_' . $field)->label(ucfirst($field) . ' ' . $lang->name);
                            }
                        }
                    } else {
                        $fields[] = TextInput::make(main_lang() . '_' . $field)->required();
                        if (is_multi_lang()) {
                            foreach (get_active_languages() as $lang) {
                                if ($lang->id == main_lang_id()) {
                                    continue;
                                }
                                $fields[] = TextInput::make($lang->slug . '_' . $field)->label(ucfirst($field) . ' ' . $lang->name);
                            }
                        }
                    }
                }
                $schema[] = Repeater::make('value.' . $key)->schema($fields);
            } else if ($variable == 'heading') {
                $schema = array_merge($schema, ModuleTitleSchema::run());
            } else if ($variable == 'description') {
                $schema = array_merge($schema, ModuleDescriptionSchema::run());
            } else if (str_contains($variable, 'description')) {
                $schema[] = Textarea::make('value.' . main_lang() . '_' . $variable)->required()->label(self::getLabelFromField($variable));
                if (is_multi_lang()) {
                    foreach (get_active_languages() as $lang) {
                        if ($lang->id == main_lang_id()) {
                            continue;
                        }
                        $fields[] = Textarea::make('value.' . $lang->slug . '_' . $variable)->label(ucfirst($variable) . ' ' . $lang->name)->label(self::getLabelFromField($variable));
                    }
                }
            } else if (str_contains($variable, 'image')) {
                $schema[] = Schema::getImage('value.' . $variable);
            } else {
                $schema[] = TextInput::make('value.' . main_lang() . '.' . $variable)->required()->label(self::getLabelFromField($variable));
                if (is_multi_lang()) {
                    foreach (get_active_languages() as $lang) {
                        if ($lang->id == main_lang_id()) {
                            continue;
                        }
                        $fields[] = TextInput::make($lang->slug . '_' . $variable)->label(self::getLabelFromField($variable) . ' ' . $lang->name);
                    }
                }
            }
        }

        return $schema;
    }

    public static function getLabelFromField(string $field): string
    {
        $field = preg_replace('/(?<!^)([A-Z])/', ' $1', $field);
        $field = explode('_', $field);
        $field =  implode(' ', $field);
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

    public static function getPaymentOptions(int $id): array
    {
        $payments = module_path('Order', 'Services/Payment');
        $files = File::files($payments);
        foreach ($files as $file) {
            $className = 'Modules\\Order\\Services\\Payment\\' . $file->getFilenameWithoutExtension();
            if (!class_exists($className)) {
                require_once $file->getPathname();
            }
            if (class_exists($className)) {
                $payment = new $className();
                if ($payment->getId() == $id) {
                    return $payment->getSchema();
                }
            }
        }
        return [];
    }
    public static function getDeliveryOptions(int $id): array
    {
        $deliveries = module_path('Order', 'Services/Delivery');
        $files = File::files($deliveries);
        foreach ($files as $file) {
            $className = 'Modules\\Order\\Services\\Delivery\\' . $file->getFilenameWithoutExtension();
            if (!class_exists($className)) {
                require_once $file->getPathname();
            }
            if (class_exists($className)) {
                $delivery = new $className();
                if ($delivery->getId() == $id) {
                    return $delivery->getSchema();
                }
            }
        }
        return [];
    }

    public static function getLayoutTemplate($isFooter = false): array
    {
        $files = glob(base_path('template/layout/*.blade.php'));
        $reference = [];
        foreach ($files as $file) {
            $name = basename($file, '.blade.php');
            if (str_contains($name, 'footer') && !$isFooter) {
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
            if (!isset($arrayProperties[$variable])) {
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
    // public static function extractVariables($fileContent)
    // {
    //     $variables = [];
    //     $arrayProperties = [];

    //     // Check for components like <x-heading> and <x-description>
    //     if (str_contains($fileContent, '<x-heading')) {
    //         $variables[] = 'heading';
    //     }
    //     if (str_contains($fileContent, '<x-description')) {
    //         $variables[] = 'description';
    //     }

    //     // Match {{ $variable }}, {{ $variable['key'] }}, and {{ $variable['key']['subkey'] }}
    //     preg_match_all('/\{\{\s*\$([a-zA-Z_][a-zA-Z0-9_]*)(?:\[(?:\'|\")(.+?)(?:\'|\")\])*\s*\}\}/', $fileContent, $matches, PREG_SET_ORDER);
    //     foreach ($matches as $match) {
    //         $varName = $match[1];
    //         if (!in_array($varName, $variables)) {
    //             $variables[] = $varName;
    //         }
    //         if (isset($match[2])) {
    //             $keys = explode("']['", $match[2]);
    //             $current = &$arrayProperties[$varName];
    //             foreach ($keys as $key) {
    //                 if (!isset($current[$key])) {
    //                     $current[$key] = [];
    //                 }
    //                 $current = &$current[$key];
    //             }
    //         }
    //     }

    //     // Match component attributes like :options="$options" or :anyVar="$anyVar"
    //     preg_match_all('/:\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*"\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*"/', $fileContent, $matches2);
    //     $variables = array_merge($variables, $matches2[2]);

    //     // Updated regex for @foreach to handle both ($key => $value) and ($value) syntax
    //     preg_match_all('/@foreach\s*\(\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s+as\s*(?:\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=>\s*)?\$([a-zA-Z_][a-zA-Z0-9_]*)\)/', $fileContent, $matches3, PREG_SET_ORDER);
    //     foreach ($matches3 as $match) {
    //         $arrayVar = $match[1];
    //         $singularVar = $match[3];
    //         if (!in_array($arrayVar, $variables)) {
    //             $variables[] = $arrayVar;
    //         }

    //         // Match $singular['key'] and $singular['key']['subkey'] inside the foreach
    //         preg_match_all('/\$\s*' . preg_quote($singularVar) . '(?:\[(?:\'|\")(.+?)(?:\'|\")\])+/', $fileContent, $matches4, PREG_SET_ORDER);
    //         foreach ($matches4 as $propertyMatch) {
    //             $keys = explode("']['", $propertyMatch[1]);
    //             $current = &$arrayProperties[$arrayVar];
    //             foreach ($keys as $key) {
    //                 if (!isset($current[$key])) {
    //                     $current[$key] = [];
    //                 }
    //                 $current = &$current[$key];
    //             }
    //         }
    //     }

    //     // Remove duplicates from the variables
    //     $variables = array_unique($variables);

    //     // Combine variables and array properties
    //     $result = [];
    //     foreach ($variables as $variable) {
    //         if (!isset($arrayProperties[$variable])) {
    //             $result[] = $variable;
    //         } else {
    //             $result[$variable] = $arrayProperties[$variable];
    //         }
    //     }

    //     // Clean up unnecessary variables
    //     $unnecessaryVars = ['options', 'title', 'key', 'breadcrumbs'];
    //     foreach ($unnecessaryVars as $var) {
    //         if (isset($result[$var])) {
    //             unset($result[$var]);
    //         }
    //     }

    //     return $result;
    // }

    public static function getFormTemplates()
    {
        $dirs = File::directories(base_path('template/forms'));
        $data = [];
        foreach ($dirs as $dir) {
            $data[basename($dir)] = ucfirst(basename($dir));
        }

        return $data;
    }
}
