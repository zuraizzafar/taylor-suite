<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KillSwitchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Never block the toggle endpoint itself
        $switchPath = env('KILL_SWITCH_PATH');
        if ($switchPath && $request->is($switchPath)) {
            return $next($request);
        }

        if (file_exists(storage_path('app/private/.kill'))) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Service unavailable.'], 503);
            }

            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
