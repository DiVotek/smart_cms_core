<?php

use Illuminate\Support\Facades\Cache;


if (! function_exists('logo')) {
    /**
     * @param  bool  $isHeader
     */
    function logo($isHeader = true): string
    {
        $key = $isHeader ? 'settings.header_logo' : 'settings.footer_logo';

        return setting(config($key), '');
    }
}
if (! function_exists('phones')) {
    function phones(): array
    {
        $setting = setting(config('settings.company_info'), []);
        $setting = $setting[0] ?? [];
        $phones = $setting['phones'] ?? [];
        $newPhones = [];
        if (is_array($phones)) {
            foreach ($phones as $phone) {
                $newPhones[] = $phone['value'];
            }
        }

        return $newPhones;
    }
}
if (! function_exists('email')) {
    function email(): string
    {
        $setting = setting(config('settings.company_info'), []);
        $setting = $setting[0] ?? [];

        return $setting['email'] ?? '';
    }
}
if (! function_exists('socials')) {
    function socials(): array
    {
        return setting(config('settings.socials'), []);
    }
}
if (! function_exists('template')) {
    function template(): string
    {
        return setting(config('settings.template'), 'Base');
    }
}
if (! function_exists('ceilPrice')) {
    function ceilPrice(int|float $price): string
    {
        $roundFor = setting(config('settings.price_round'), 0);

        return round($price, $roundFor);
    }
}

if (! function_exists('slogan')) {
    function slogan(): string
    {
        return setting(config('settings.company_slogan'), '');
    }
}
if (! function_exists('company_name')) {
    function company_name(): string
    {
        return setting(config('settings.company_name'), '');
    }
}
if (! function_exists('home')) {
    function home(): string
    {
        return Cache::remember('home', 3600, function () {
            $systemPage = SystemPage::query()->where('name', 'Home')->first()->page_id ?? 0;
            $page = StaticPage::query()->find($systemPage);
            if ($page) {
                return tRoute('slug', [
                    'slug' => $page->slug,
                ]);
            }

            return '/';
        });
    }
}
if (! function_exists('home_name')) {
    function home_name(): string
    {
        $page = StaticPage::query()->where('slug', home())->first();

        return $page->translate_name ?? $page->name ?? 'Home';
    }
}
if (! function_exists('home_slug')) {
    function home_slug()
    {
        return Cache::remember('home_slug', 3600, function () {
            $systemPage = SystemPage::query()->where('name', 'Home')->first()->page_id ?? 0;
            $page = StaticPage::query()->find($systemPage);
            if ($page) {
                return $page->slug;
            }

            return '/';
        });
    }
}
if (! function_exists('address')) {
    function address(): string
    {
        $setting = setting(config('settings.company_info'), []);
        $setting = $setting[0] ?? [];

        return $setting['address'] ?? '';
    }
}
if (! function_exists('addresses')) {
    function addresses(): array
    {
        $setting = setting(config('settings.company_info'), []);

        return array_map(function ($item) {
            return [
                'address' => $item['address'],
                'coordinates' => $item['coordinates'] ?? '',
            ];
        }, $setting);
    }
}
if (! function_exists('schedule')) {
    function schedule(): string
    {
        $setting = setting(config('settings.company_info'), []);
        $setting = $setting[0] ?? [];
        $schedule = $setting['schedule'] ?? '';

        return $schedule;
    }
}
if (! function_exists('company_info')) {
    function company_info(): array
    {
        return setting(config('settings.company_info'), []);
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
