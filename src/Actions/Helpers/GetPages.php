<?php

namespace SmartCms\Core\Actions\Helpers;

use Illuminate\Support\Facades\Cache;

class GetPages
{
    public static function run()
    {
        return Cache::rememberForever('pages', function () {
            return \SmartCms\Core\Models\Page::where('status', true)->get();
        });
    }
}
