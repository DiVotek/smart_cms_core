<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class Layout extends Component
{
    public string $style;

    public array $scripts;

    public array $meta_tags;

    public string $script;

    public string $favicon;

    public array $theme;

    public string $og_type;

    public function __construct()
    {
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
        $this->theme = $theme;
        $this->style = Cache::remember('template_styles', 60 * 60 * 24, function () {
            if (File::exists(scms_template_path(template()) . '/assets/css/app.css')) {
                return 'scms/templates/' . template() . '/assets/css/app.css';
            } else {
                return '';
            }
        });
        $this->script = Cache::remember('template_scripts', 60 * 60 * 24, function () {
            if (File::exists(scms_template_path(template()) . '/assets/js/app.js')) {
                return 'scms/templates/' . template() . '/assets/js/app.js';
            } else {
                return '';
            }
        });
        $fav = _settings('branding.favicon', '/favicon.ico');
        if (str_starts_with($fav, '/')) {
            $fav = substr($fav, 1);
        }
        $this->favicon = asset('/storage/' . $fav);
        $this->og_type = _settings('og_type', 'website');
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
                            <!DOCTYPE html>
                    <html lang="{{current_lang()}}">
                    <head>
                        <meta charset="UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                        <link rel="canonical" href="{{ url()->current() }}" />
                        <link rel="icon" type="image/x-icon" href="{{$favicon}}">
                        <link rel="preload" href="{{asset('/storage'.logo())}}" as="image" type="image/webp">
                        <title>@yield('title')</title>
                        <meta name="description" content="@yield('description')" />
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <meta name="robots" content="index, follow">
                        <link rel="robots" href="{{route('robots')}}">
                        <link rel="sitemap" type="application/xml" title="Sitemap" href="{{route('sitemap')}}">
                        @foreach($meta_tags as $tag)
                        <meta name="{{$tag['name']}}" content="{{$tag['meta_tags']}}">
                        @endforeach
                        <x-s::microdata.organization />
                        <meta property="og:type" content="{{$og_type}}" />
                        <meta property="og:title" content="@yield('title')" />
                        <meta property="og:description" content="@yield('description')" />
                        <meta property="og:url" content="{{ url()->current() }}" />
                        <meta property="og:image" content="@yield('meta-image')" />
                        <meta property="og:site_name" content="{{company_name()}}">
                        @yield('microdata')
                        <style>
                            :root {@foreach($theme as $key => $value)--{{$key}}: {{$value ?? '#000'}};@endforeach}
                        </style>
                        @vite(['assets/js/app.js', 'assets/css/app.css'], 'templates/' . template())
                    </head>
                    <body class="antialiased">
                      <x-s::layout.main-layout>
                                @yield('content')
                      </x-s::layout.main-layout>
                            <div x-data="dialog" x-show="isShow" x-cloak
                                @open-modal.window="openModal($event.detail.element, $event.detail.label, $event.detail.description, $event.detail.time, $event.detail.backdrop)"
                                @keyup.escape="closeModal()" @click.self="closeModal()" x-trap.inert.noscroll="isShow" :data-open="isShow"
                                id="dialog" role="dialog" :aria-label="label" :aria-description="description" class="dialog":style="`position: fixed; inset: 0; z-index: 50; ${isBackdrop ? 'background-color: #00000080; backdrop-filter: blur(4px);' : ''}`"
                                x-transition:enter="ease-out duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-out duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                            @foreach($scripts as $script)
                                {!! $script['scripts'] !!}
                            @endforeach
                            <x-s::misc.gtm />
                    </body>
                    </html>
        blade;
    }
}
