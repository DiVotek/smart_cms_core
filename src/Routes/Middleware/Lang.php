<?php

namespace SmartCms\Core\Routes\Middleware;

use Illuminate\Http\Request;

class Lang
{
    public function handle(Request $request, $next)
    {
        $lang = $request->segment(1);
        if (strlen($lang) !== 2) {
            $lang = main_lang();
        } else {
            $lang = strtolower($lang);
        }
        $this->setLang($lang);

        return $next($request);
    }

    public function setLang($lang)
    {
        // to do
        // app()->setLocale($language->slug);
    }
}
