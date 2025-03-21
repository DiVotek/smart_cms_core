<?php

if (! function_exists('sconfig')) {
    function sconfig(string $key): string
    {
        return config('smart_cms.' . $key);
    }
}

if (! function_exists('_settings')) {
    function _settings(string $key, mixed $default = ''): mixed
    {
        return app('_settings')->get(sconfig($key)) ?? $default;
    }
}
