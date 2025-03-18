<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Support\Facades\Event;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Traits\HasHooks;

class RobotsHandler
{
    use AsAction;
    use HasHooks;

    public function handle()
    {
        $robots = _settings('indexation', false);
        if (! $robots) {
            $robots = "User-agent: *\nDisallow: /";
        } else {
            $robots = "User-agent: *\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nDisallow: /search\nDisallow: /register\nDisallow: /reset-password\nDisallow: /*page*\nSitemap: " . route('sitemap') . "\nHost: " . request()->getHost();
        }
        $robots = self::applyHook('robots.generate', $robots);

        return response($robots)->header('Content-Type', 'text/plain');
    }
}
