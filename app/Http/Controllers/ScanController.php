<?php

namespace App\Http\Controllers;

use App\Models\Suit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function show(string $code): View|RedirectResponse
    {
        $suit = Suit::with(['customer', 'worker', 'order'])
            ->where('suit_code', strtoupper($code))
            ->firstOrFail();

        // Logged-in admin or branch_manager → redirect to full backend view
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'branch_manager'])) {
            return redirect()->route('suits.show', $suit);
        }

        return view('scan.show', compact('suit'));
    }
}
