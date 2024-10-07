<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class Layout extends Component
{
    public string $style;

    public array $scripts;

    public array $meta_tags;

    public string $fonts;

    public ?string $templateCss;

    public string $favicon;

    public function __construct()
    {
        $styles = _settings('styles', []);
        $this->fonts = $styles['fonts'] ?? '/resources/schemas/fonts/roboto.css';
        $this->style = $styles['colors'] ?? '/resources/schemas/colors/yellow.css';
        $scripts = _settings('custom_scripts', []);
        if (! is_array($scripts)) {
            $scripts = [];
        }
        $meta_tags = _settings('custom_meta', []);
        if (! is_array($meta_tags)) {
            $meta_tags = [];
        }
        $this->templateCss = File::exists(base_path('template/css/app.css')) ? 'template/css/app.css' : null;
        $this->scripts = $scripts;
        $this->meta_tags = $meta_tags;
        $this->style = '';
        //  'resources' . explode('/resources', $this->style)[1];
        $this->fonts = '';
        // 'resources' . explode('/resources', $this->fonts)[1];
        $this->favicon = asset('/storage'._settings('favicon', '/favicon.ico'));
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
                            <!DOCTYPE html>
                    <html lang="{{current_lang()}}">
                    <head>
                        <meta charset="UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                        <link rel="canonical" href="{{ url()->current() }}" />
                        <link rel="icon" type="image/x-icon" href="{{$favicon}}">
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
                        <meta property="og:type" content="website" />
                        <meta property="og:title" content="@yield('title')" />
                        <meta property="og:description" content="@yield('description')" />
                        <meta property="og:url" content="{{ url()->current() }}" />
                        <meta property="og:image" content="@yield('meta-image')" />
                        <meta property="og:site_name" content="{{company_name()}}">
                        @yield('microdata')
                    </head>
                    @if($fonts)
                    @vite($fonts)
                    @endif
                    @if($style)
                    @vite($style)
                    @endif
                    @vite('resources/css/app.css')
                    @vite($templateCss)
                    @vite('resources/js/app.js')
                    <body class="antialiased">
                            <x-s::layout.header />
                            <main class="min-h-80">
                                @yield('content')
                            </main>
                            <x-s::layout.footer/>
                            <x-s::misc.gtm />
                            @foreach($scripts as $script)
                                {!! $script['scripts'] !!}
                            @endforeach
                    </body>
                    </html>
        blade;
    }
}
