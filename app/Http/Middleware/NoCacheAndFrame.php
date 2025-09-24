<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCacheAndFrame
{
    public function handle(Request $request, Closure $next)
    {
        $resp = $next($request);

        return $resp
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Frame-Options', 'DENY')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Permissions-Policy', "camera=(), microphone=(), geolocation=()")
            ->header('Content-Security-Policy', "frame-ancestors 'none';");
    }
}
