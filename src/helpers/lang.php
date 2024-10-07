<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translation;

if (! function_exists('_actions')) {
    function _actions(string $key): string
    {
        return __('smart_cms::actions.' . $key);
    }
}

if (! function_exists('strans')) {
    function strans(string $key): string
    {
        return __('smart_cms::' . $key);
    }
}

if (! function_exists('_columns')) {
    function _columns(string $key): string
    {
        return __('smart_cms::columns.' . $key);
    }
}

if (! function_exists('_fields')) {
    function _fields(string $key): string
    {
        return __('smart_cms::fields.' . $key);
    }
}

if (! function_exists('_hints')) {
    function _hints(string $key): string
    {
        return __('smart_cms::hints.' . $key);
    }
}

if (! function_exists('_nav')) {
    function _nav(string $key): string
    {
        return __('smart_cms::navigation.' . $key);
    }
}

if (! function_exists('_t')) {
    function _t(string $key): string
    {
        return app('translations')->where('key', $key)->where('language_id', current_lang_id())->first()?->value ?? $key;
    }
}

if (! function_exists('tRoute')) {
    function tRoute(string $name, array $params = []): string
    {
        return route($name, $params);
    }
}

if (! function_exists('main_lang')) {
    function main_lang(): string
    {
        return Language::query()->find(_settings('main_language', 1))->slug ?? 'en';

        return setting(config('settings.main_language'), 'en');
    }
}
if (! function_exists('current_lang')) {
    function current_lang(): string
    {
        return Context::get('current_lang') ?? main_lang();
    }
}
if (! function_exists('current_lang_id')) {
    function current_lang_id(): string
    {
        $slug = current_lang();

        return Language::query()->where('slug', $slug)->first()->id ?? 1;
    }
}
if (! function_exists('main_lang_id')) {
    function main_lang_id(): int
    {
        return Language::query()->where('slug', main_lang())->first()->id ?? 1;
    }
}
if (! function_exists('get_active_languages')) {
    function get_active_languages(): Collection
    {
        return Language::query()->get();
    }
}
if (! function_exists('is_multi_lang')) {
    function is_multi_lang(): bool
    {
        return true;
        try {
            return _settings('is_multi_lang', false);
        } catch (Exception $exception) {
            return false;
        }
    }
}
