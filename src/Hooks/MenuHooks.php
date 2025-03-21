<?php

namespace SmartCms\Core\Hooks;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SmartCms\Core\Models\Menu;

class MenuHooks
{
    public static function beforeUpdate(Menu $menu)
    {
        // Because we need to clear menu, all layouts and all sections
        Cache::flush();
    }
}
