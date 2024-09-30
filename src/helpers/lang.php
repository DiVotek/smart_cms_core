<?php

use Illuminate\Database\Eloquent\Collection;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Translation;

if (! function_exists('_actions')) {
    function _actions(string $key): string
    {
        return __('smart_cms::actions.'.$key);
    }
}

if (! function_exists('strans')) {
    function strans(string $key): string
    {
        return __('smart_cms::'.$key);
    }
}

if (! function_exists('_columns')) {
    function _columns(string $key): string
    {
        return __('smart_cms::columns.'.$key);
    }
}

if (! function_exists('_fields')) {
    function _fields(string $key): string
    {
        return __('smart_cms::fields.'.$key);
    }
}

if (! function_exists('_hints')) {
    function _hints(string $key): string
    {
        return __('smart_cms::hints.'.$key);
    }
}

if (! function_exists('_nav')) {
    function _nav(string $key): string
    {
        return __('smart_cms::navigation.'.$key);
    }
}

if (! function_exists('_t')) {
    function _t(string $key): string
    {
        $translation = app('translations')->where('key', $key)->where('language_id', current_lang_id())->first()?->value ?? $key;
        if (setting(config('settings.add_translations'))) {
            if ($translation == $key && ! Translation::query()->where('key', $key)->where('language_id', current_lang_id())->exists()) {
                Translation::query()->create([
                    'key' => $key,
                    'language_id' => current_lang_id(),
                    'value' => $key,
                ]);
            }
        }

        return $translation;
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
        return Language::query()->find(setting(config('settings.main_language'), 1))->slug ?? 'en';

        return setting(config('settings.main_language'), 'en');
    }
}
if (! function_exists('current_lang')) {
    function current_lang(): string
    {
        $currentRoute = Route::currentRouteName();
        if (str_contains($currentRoute, '.')) {
            $currentRoute = explode('.', $currentRoute);

            return $currentRoute[0];
        }

        return Language::query()->find(setting(config('settings.main_language'), 1))->slug ?? 'en';
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
        try {
            return setting(config('settings.is_multi_lang'), false);
        } catch (Exception $exception) {
            return false;
        }
    }
}
