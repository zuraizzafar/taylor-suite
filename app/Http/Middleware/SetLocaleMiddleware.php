<?php

namespace App\Http\Middleware;

use App\Models\TranslationOverride;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'en'));

        if (!in_array($locale, ['en', 'ur'])) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        // Load DB overrides into the translator (cached per locale)
        $this->loadOverrides($locale);

        return $next($request);
    }

    private function loadOverrides(string $locale): void
    {
        try {
            if (!Schema::hasTable('translation_overrides')) {
                return;
            }

            $overrides = Cache::remember("trans_overrides_{$locale}", 3600, function () use ($locale) {
                return TranslationOverride::where('locale', $locale)
                    ->pluck('value', 'key')
                    ->toArray();
            });

            if (!empty($overrides)) {
                app('translator')->addLines($overrides, $locale);
            }
        } catch (\Throwable $e) {
            // Silently fail during migrations / fresh installs
        }
    }
}
