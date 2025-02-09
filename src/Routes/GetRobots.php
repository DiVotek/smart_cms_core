<?php

namespace SmartCms\Core\Routes;

use Lorisleiva\Actions\Concerns\AsAction;

class GetRobots
{
    use AsAction;

    public function handle()
    {
        $robots = _settings('indexation', false);
        if (! $robots) {
            $robots = "User-agent: *\nDisallow: /";
        } else {
            $robots = "User-agent: *\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nDisallow: /search\nDisallow: /register\nDisallow: /reset-password\nDisallow: /*page*\nSitemap: ".route('sitemap')."\nHost: ".request()->getHost();
        }

        return response($robots)->header('Content-Type', 'text/plain');
    }
}
