<?php

if (! function_exists('scms_templates_path')) {
    function scms_templates_path(): string
    {
        return base_path('scms/templates/');
    }
}
if (! function_exists('scms_template_path')) {
    function scms_template_path(string $template): string
    {
        return scms_templates_path().$template.'/';
    }
}
if (! function_exists('sconfig')) {
    function sconfig(string $key): string
    {
        return config('smart_cms.'.$key);
    }
}

if (! function_exists('_settings')) {
    function _settings(string $key, mixed $default = ''): mixed
    {
        return setting(sconfig($key), $default);
    }
}
