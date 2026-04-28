<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KillSwitchController extends Controller
{
    public function toggle(Request $request)
    {
        $configuredKey = env('KILL_SWITCH_KEY', '');

        // Wrong or missing key → look like a normal 404, reveal nothing
        if (!$configuredKey || !hash_equals($configuredKey, (string) $request->input('key', ''))) {
            abort(404);
        }

        $flagFile = storage_path('app/private/.kill');
        $action   = $request->input('action'); // 'off' = disable system, 'on' = re-enable, omit = toggle

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
}
