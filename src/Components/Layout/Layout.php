<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public array $scripts;

    public array $meta_tags;

    public string $favicon;

    public array $theme;

    public string $og_type;

    public $titleMod;

    public $descriptionMod;

    public string $stylePath;

    public function __construct()
    {

        $stylePath = file_exists(resource_path('scss/app.scss')) ? 'resources/scss/app.scss' : 'resources/css/app.css';
        $this->stylePath = $stylePath;

        $scripts = _settings('custom_scripts', []);
        if (! is_array($scripts)) {
            $scripts = [];
        }
        $meta_tags = _settings('custom_meta', []);
        if (! is_array($meta_tags)) {
            $meta_tags = [];
        }
        $this->scripts = $scripts;
        $this->meta_tags = $meta_tags;
        $theme = _settings('theme', []);
        if (! is_array($theme)) {
            $theme = [];
        }
        $this->theme = array_merge(config('theme', []), $theme);
        $fav = _settings('branding.favicon', '/favicon.ico');
        if (str_starts_with($fav, '/')) {
            $fav = substr($fav, 1);
        }
        $this->favicon = asset('/storage/' . $fav);
        $this->og_type = _settings('og_type', 'website');
        $this->titleMod = [
            'prefix' => _settings('title.prefix', ''),
            'suffix' => _settings('title.suffix', ''),
        ];
        $this->descriptionMod = [
            'prefix' => _settings('description.prefix', ''),
            'suffix' => _settings('description.suffix', ''),
        ];
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
                            <!DOCTYPE html>
                    <html lang="{{current_lang()}}">
                    <head>
                        <meta charset="UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <link rel="canonical" href="{{ url()->current() }}" />
                        <link rel="icon" type="image/x-icon" href="{{$favicon}}">
                        <link rel="preload" href="{{validateImage(logo())}}" as="image" type="image/webp">
                        <title>{{$titleMod['prefix'] ?? ''}}@yield('title'){{$titleMod['suffix'] ?? ''}}</title>
                        <meta name="description" content="{{$descriptionMod['prefix'] ?? ''}}@yield('description'){{$descriptionMod['suffix'] ?? ''}}" />
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <meta name="robots" content="index, follow">
                        <link rel="robots" href="{{route('robots')}}">
                        <link rel="sitemap" type="application/xml" title="Sitemap" href="{{route('sitemap')}}">
                        @foreach($meta_tags as $tag)
                        <meta name="{{$tag['name']}}" content="{{$tag['meta_tags']}}">
                        @endforeach
                        <meta property="og:type" content="{{$og_type}}" />
                        <meta property="og:title" content="{{$titleMod['prefix'] ?? ''}}@yield('title'){{$titleMod['suffix'] ?? ''}}" />
                        <meta property="og:description" content="{{$descriptionMod['prefix'] ?? ''}}@yield('description'){{$descriptionMod['suffix'] ?? ''}}" />
                        <meta property="og:url" content="{{ url()->current() }}" />
                        <meta property="og:image" content="@yield('meta-image')" />
                        <meta property="og:site_name" content="{{ company_name() }}">
                        <meta name="twitter:card" content="summary">
                        <meta name="twitter:site" content="{{ '@' . company_name() }}">
                        <meta name="twitter:description" content="{{$descriptionMod['prefix'] ?? ''}}@yield('description'){{$descriptionMod['suffix'] ?? ''}}">
                        <meta name="twitter:title" content="{{$titleMod['prefix'] ?? ''}}@yield('title'){{$titleMod['suffix'] ?? ''}}">
                        <meta name="twitter:image" content="@yield('meta-image')">
                        <x-s::microdata.organization />
                        <x-s::microdata.website />
                        @yield('microdata')
                        <style>
                            :root {@foreach($theme as $key => $value)--{{$key}}: {{$value ?? '#000'}};@endforeach}
                        </style>
                        @vite([$stylePath, 'resources/js/app.js'])
                        @stack('styles')
                    </head>
                    <body class="antialiased">
                        <x-s::layout.header />
                        <main>
                            @yield('content')
                        </main>
                        <x-s::layout.footer />
                        @foreach($scripts as $script)
                            {!! $script['scripts'] !!}
                        @endforeach
                        <x-s::misc.gtm />
                    </body>
                    </html>
        blade;
    }
}
