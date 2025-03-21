<?php

namespace SmartCms\Core\Hooks;

use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Models\Layout;

class LayoutHooks
{
    public static function beforeUpdate(Layout $layout)
    {
        foreach (get_active_languages() as $lang) {
            Cache::forget('layout_variables_'.$layout->id.'_'.$lang->id);
        }
    }
}
