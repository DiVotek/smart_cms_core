<?php

use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Models\Page;

if (! function_exists('logo')) {
    function logo(): string
    {
        return '/'._settings('branding.logo', '');
    }
}
if (! function_exists('phones')) {
    function phones(): array
    {
        $setting = _settings('company_info', []);
        $phones = [];
        foreach ($setting as $branch) {
            if (! isset($branch['phones'])) {
                continue;
            }
            foreach ($branch['phones'] as $phone) {
                if (! isset($phone['value'])) {
                    continue;
                }
                $phones[] = $phone['value'];
            }
        }

        return $phones;
    }
}
if (! function_exists('email')) {
    function email(): string
    {
        $setting = _settings('company_info', []);
        $setting = $setting[0] ?? [];

        return $setting['email'] ?? '';
    }
}
if (! function_exists('emails')) {
    function emails(): array
    {
        $setting = _settings('company_info', []);

        return array_map(function ($item) {
            return $item['email'];
        }, $setting);
    }
}
if (! function_exists('socials')) {
    function socials(): array
    {
        return _settings('branding.socials', []);
    }
}
if (! function_exists('social_names')) {
    function social_names(): array
    {
        return array_map(function ($item) {
            return $item['name'];
        }, _settings('branding.socials', []));
    }
}
if (! function_exists('template')) {
    function template(): string
    {
        return _settings('template', 'default');
    }
}

if (! function_exists('slogan')) {
    function slogan(): string
    {
        return _settings('company_slogan', '');
    }
}
if (! function_exists('company_name')) {
    function company_name(): string
    {
        return _settings('branding.company_name', '');
    }
}
if (! function_exists('home')) {
    function home(): string
    {
        return Cache::remember('home', 3600, function () {
            return Page::query()->first()->route() ?? '/';
        });
    }
}
if (! function_exists('home_name')) {
    function home_name(): string
    {
        return Cache::remember('home_name', 3600, function () {
            return Page::query()->first()->name() ?? '/';
        });
    }
}
if (! function_exists('addresses')) {
    function addresses(): array
    {
        $setting = _settings('company_info', []);

        return array_filter(array_map(function ($item) {
            return $item['address'] ?? '';
        }, $setting));
    }
}
if (! function_exists('schedule')) {
    function schedule(): string
    {
        $setting = _settings('company_info', []);
        $setting = $setting[0] ?? [];
        $schedule = $setting['schedule'] ?? '';

        return $schedule;
    }
}
if (! function_exists('schedules')) {
    function schedules(): array
    {
        $setting = _settings('company_info', []);

        return array_filter(array_map(function ($item) {
            return $item['schedule'] ?? '';
        }, $setting));
    }
}
if (! function_exists('company_info')) {
    function company_info(): array
    {
        return _settings('company_info', []);
    }
}
if (! function_exists('format_phone')) {
    function format_phone($phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone)) {
            return preg_replace('/^\+1(\d{3})(\d{3})(\d{4})$/', '+1($1)$2-$3', $phone);
        }
        if (preg_match('/^\+380(\d{2})(\d{3})(\d{2})(\d{2})$/', $phone)) {
            return preg_replace('/^\+380(\d{2})(\d{3})(\d{2})(\d{2})$/', '+380 ($1) $2-$3-$4', $phone);
        }

        return preg_replace('/\B(?=(\d{3})+(?!\d))/', '-', substr($phone, 1));
    }
}

if (! function_exists('no_image')) {
    function no_image(): string
    {
        return '';
    }
}
