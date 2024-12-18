<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use SmartCms\Core\Models\Language;

if (! function_exists('_actions')) {
    function _actions(string $key): string
    {
        return strans('actions.'.$key);
    }
}

if (! function_exists('strans')) {
    function strans(string $key): string
    {
        $translation = trans('smart_cms_store::'.$key);
        if ($translation === 'smart_cms_store::'.$key) {
            $translation = trans('smart_cms::'.$key);
        }

        return $translation;
        // return __('smart_cms::' . $key);
    }
}

if (! function_exists('_columns')) {
    function _columns(string $key): string
    {
        return strans('columns.'.$key);
    }
}

if (! function_exists('_fields')) {
    function _fields(string $key): string
    {
        return strans('fields.'.$key);
    }
}

if (! function_exists('_hints')) {
    function _hints(string $key): string
    {
        return strans('hints.'.$key);
    }
}

if (! function_exists('_nav')) {
    function _nav(string $key): string
    {
        return strans('navigation.'.$key);
    }
}

if (! function_exists('_t')) {
    function _t(string $key): string
    {
        return $key;

        return app('translations')->where('key', $key)->where('language_id', current_lang_id())->first()?->value ?? $key;
    }
}
if (! function_exists('_lang_routes')) {

    function _lang_routes(): array
    {
        $routes = [];
        $route = Route::getCurrentRoute();
        $name = $route->getName();
        if (! str_contains($name, 'lang')) {
            $name = $name.'.lang';
        }
        if ($route->hasParameter('lang')) {
            foreach (get_active_languages() as $lang) {
                $parameters = $route->parameters();
                if ($lang->id == main_lang_id()) {
                    $parameters = array_diff_key($parameters, ['lang' => '']);
                } else {
                    $parameters['lang'] = $lang->slug;
                }
                $routes[] = [
                    'name' => $lang->name,
                    'code' => $lang->slug,
                    'route' => route($name, $parameters),
                ];
            }
        } else {
            foreach (get_active_languages() as $lang) {
                $parameters = $route->parameters();
                if ($lang->id == main_lang_id()) {
                    $parameters = array_diff_key($parameters, ['lang' => '']);
                } else {
                    $parameters['lang'] = $lang->slug;
                }
                $routes[] = [
                    'name' => $lang->name,
                    'code' => $lang->slug,
                    'route' => route($name, $parameters),
                ];
            }
        }

        return $routes;
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
        return app('_lang')->getDefault()->slug;
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
        $ids = [main_lang_id()];
        if (is_multi_lang()) {
            $ids = array_merge($ids, _settings('additional_languages', []));
        }

        return app('_lang')->getMulti($ids);
        // return Language::query()->whereIn('id', $ids)->get();
    }
}
if (! function_exists('is_multi_lang')) {
    function is_multi_lang(): bool
    {
        try {
            return _settings('is_multi_lang', false);
        } catch (Exception $exception) {
            return false;
        }
    }
}
