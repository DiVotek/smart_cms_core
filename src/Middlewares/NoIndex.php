<?php

namespace SmartCms\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;

/**
 * Class NoIndex
 */
class NoIndex
{
    /**
     * Handles the request.
     *
     * @param  Request  $request  The request to handle.
     * @param  Closure  $next  The next middleware to handle.
     * @return \Illuminate\Http\Response The response.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
