<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Page;
use Symfony\Component\HttpKernel\Attribute\Cache;

class SitemapHandler
{
    use AsAction;

    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function handle()
    {
        $links = [];
        foreach (Page::query()->get() as $page) {
            $links[] = [
                'link' => $page->route(),
                'priority' => 0.7,
                'changefreq' => 'weekly',
                'lastmod' => $page->updated_at,
            ];
        }
        Event::dispatch('cms.sitemap.generate', [&$links]);
        $content = $this->getBladeContent();
        $content = Blade::render($content, [
            'links' => $links,
        ]);

        return response($content)->header('Content-Type', 'text/xml');
    }

    public function getBladeContent(): string
    {
        return <<<'blade'
                    <?php

            header('content-type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
            @php
                use Carbon\Carbon;
            @endphp
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                    @foreach ($links as $link)
                        <url>
                            <loc>{{ url($link['link']) }}</loc>
                            <lastmod>
                                {{ $link['lastmod']->toAtomString() }}
                            </lastmod>
                            <changefreq>{{ $link['changefreq'] }}</changefreq>
                            <priority>{{ $link['priority'] }}</priority>
                        </url>
                    @endforeach
            </urlset>
        blade;
    }
}
