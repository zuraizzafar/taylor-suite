<?php

namespace App\Http\Controllers;

use App\Models\Suit;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function show(string $code): View
    {
        $suit = Suit::with(['customer', 'worker'])
            ->where('suit_code', strtoupper($code))
            ->firstOrFail();

        return view('scan.show', compact('suit'));
    }
}
