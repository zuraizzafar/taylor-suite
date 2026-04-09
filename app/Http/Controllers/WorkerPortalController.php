<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerPortalController extends Controller
{
    public function suits(Request $request): View|RedirectResponse
    {
        $user   = auth()->user();
        $worker = $user->worker;

        if (! $worker) {
            return redirect()->route('dashboard')
                ->with('error', 'No worker profile linked to your account. Please contact admin.');
        }

        $suits = $worker->suits()
            ->with(['customer', 'order'])
            ->latest()
            ->get();

        return view('worker.suits', compact('suits', 'worker'));
    }
}
