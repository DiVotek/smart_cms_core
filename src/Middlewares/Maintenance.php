<?php

namespace SmartCms\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Maintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (_settings('system.maintenance') && ! request()->cookie('maintenance_bypass')) {
            if (view()->exists('errors.maintenance')) {
                return response()->view('errors.maintenance');
            }
            abort(503);
        }

        return $next($request);
    }
}
