<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KillSwitchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $switchPath = env('KILL_SWITCH_PATH');
        $flagFile   = storage_path('app/private/.kill');

        // Handle toggle request — runs before CSRF so no token needed
        if ($switchPath && $request->is($switchPath) && $request->isMethod('POST')) {
            $configuredKey = env('KILL_SWITCH_KEY', '');

            // Wrong or missing key → 404, reveal nothing
            if (!$configuredKey || !hash_equals($configuredKey, (string) $request->input('key', ''))) {
                abort(404);
            }

            $action = $request->input('action');

            if ($action === 'off') {
                file_put_contents($flagFile, now()->toIso8601String());
                $status = 'off';
            } elseif ($action === 'on') {
                if (file_exists($flagFile)) {
                    unlink($flagFile);
                }
                $status = 'on';
            } else {
                // Toggle
                if (file_exists($flagFile)) {
                    unlink($flagFile);
                    $status = 'on';
                } else {
                    file_put_contents($flagFile, now()->toIso8601String());
                    $status = 'off';
                }
            }

            return response()->json(['status' => $status]);
        }

        // Block all other requests when kill flag is active
        if (file_exists($flagFile)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Service unavailable.'], 503);
            }

            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
