<?php

namespace App\Actions;

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
        return response()->view('sitemap', [
            'links' => $links,
            'is_sitemap' => false,
        ])->header('Content-Type', 'text/xml');
    }
}
