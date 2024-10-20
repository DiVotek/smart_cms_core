<?php

namespace SmartCms\Core\Routes;

use Illuminate\Support\Facades\Blade;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Page;
use Symfony\Component\HttpKernel\Attribute\Cache;

class GetSitemap
{
    use AsAction;

    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function handle()
    {
        $links = [];
        foreach (Page::query()->get() as $page) {
            $links[] = $page->route();
        }
        $content = $this->getBladeContent();
        $content = Blade::render($content, [
            'links' => $links,
            'is_sitemap' => true,
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
                @if ($is_sitemap)
                    @foreach ($links as $link)
                        <sitemap>
                            <loc>{{ $link }}</loc>
                            <lastmod>
                                {{ Carbon::parse(Carbon::now())->tz("UTC")->toAtomString() }}
                            </lastmod>
                        </sitemap>
                    @endforeach
                @else
                    @foreach ($links as $link)
                        <url>
                            <loc>{{ url($link) }}</loc>
                            <lastmod>
                                {{ Carbon::parse(Carbon::now())->tz("UTC")->toAtomString() }}
                            </lastmod>
                            <changefreq>weekly</changefreq>
                            <priority>0.7</priority>
                        </url>
                    @endforeach
                @endif
            </urlset>
        blade;
    }
}
