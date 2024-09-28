<?php

namespace SmartCms\Core\Routes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use SmartCms\Core\Components\Pages\StaticPage;
use SmartCms\Core\Models\Page;
use Symfony\Component\HttpKernel\Attribute\Cache;

class GetSlug
{
    #[Cache(public: true, maxage: 31536000, mustRevalidate: true)]
    public function __invoke(Request $request): string
    {
        $segments = $request->segments();
        if (count($segments) >= 1 && strlen($segments[0]) == 2) {
            $segments = array_slice($segments, 1);
        }
        $slug = $segments[0] ?? null;
        if ($slug == null) {
            $slug = '';
        }
        if (count($segments) <= 1) {
            return $this->resolveOneSlug($slug);
        }
        abort(404);
    }

    private function resolveOneSlug(string $slug): mixed
    {
        $page = Page::query()->slug($slug)->first();
        if ($page) {
            return Blade::renderComponent(new StaticPage($page));
            // return response(Blade::renderComponent(new StaticPage($page)))->header('X-SMART-CMS', 'true');
        }
        return abort(404);
    }
}
