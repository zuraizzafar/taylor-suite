<?php

namespace App\Http\Controllers;

use App\Models\TranslationOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TranslationController extends Controller
{
    /**
     * Show the translation management page.
     */
    public function index(Request $request): View
    {
        $locales = ['ur', 'en'];
        $rows = [];
        $overrides = [];

        foreach ($locales as $loc) {
            $langFile = base_path("lang/{$loc}.json");
            $defaults = [];
            if (file_exists($langFile)) {
                $decoded = json_decode(file_get_contents($langFile), true);
                if (is_array($decoded)) {
                    $defaults = $decoded;
                }
            }

            $dbOverrides = TranslationOverride::where('locale', $loc)
                ->pluck('value', 'key')
                ->toArray();

            $overrides[$loc] = $dbOverrides;

            // Build rows array for JS
            $locRows = [];
            // Use ur.json keys as the master key list
            $masterFile = base_path('lang/ur.json');
            $masterKeys = [];
            if (file_exists($masterFile)) {
                $decoded = json_decode(file_get_contents($masterFile), true);
                if (is_array($decoded)) {
                    $masterKeys = array_keys($decoded);
                }
            }

            foreach ($masterKeys as $key) {
                $default = $defaults[$key] ?? $key;
                $value   = $dbOverrides[$key] ?? $default;
                $locRows[] = [
                    'key'        => $key,
                    'default'    => $default,
                    'value'      => $value,
                    'overridden' => isset($dbOverrides[$key]),
                    'dirty'      => false,
                ];
            }
            $rows[$loc] = $locRows;
        }

        return view('translations.index', compact('rows', 'overrides'));
    }

    /**
     * Save (upsert) a single translation override.
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:en,ur'],
            'key'    => ['required', 'string', 'max:500'],
            'value'  => ['required', 'string'],
        ]);

        TranslationOverride::updateOrCreate(
            ['locale' => $data['locale'], 'key' => $data['key']],
            ['value'  => $data['value']]
        );

        Cache::forget("trans_overrides_{$data['locale']}");

        return response()->json(['success' => true]);
    }

    /**
     * Delete a translation override (revert to file default).
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:en,ur'],
            'key'    => ['required', 'string', 'max:500'],
        ]);

        TranslationOverride::where('locale', $data['locale'])
            ->where('key', $data['key'])
            ->delete();

        Cache::forget("trans_overrides_{$data['locale']}");

        return response()->json(['success' => true]);
    }
}
