<?php

namespace SmartCms\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Language;

/**
 * Class Lang
 *
 * Prepends the language to the request.
 */
class Lang
{
    /**
     * Handles the request.
     *
     * @param  Request  $request  The request to handle.
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $referer = $request->header('referer');
        if ($referer) {
            $url = parse_url($referer);
            if (isset($url['path'])) {
                $segments = explode('/', trim($url['path'], '/'));
                if (! empty($segments)) {
                    $potentialLang = $segments[0];
                    if (strlen($potentialLang) === 2) {
                        $lang = Language::query()->where('slug', $potentialLang)->first();
                        if ($lang) {
                            app()->setLocale($lang->slug);
                            Context::add('current_lang', $lang->slug);
                            app('_lang')->setCurrentLanguage($lang);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
