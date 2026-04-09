<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Suit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->input('q', ''));
        $customers = collect();
        $suits     = collect();

        if (strlen($q) >= 2) {
            $customers = Customer::where('name', 'like', "%{$q}%")
                ->orWhere('mobile', 'like', "%{$q}%")
                ->orWhere('file_number', 'like', "%{$q}%")
                ->withCount('suits')
                ->take(20)
                ->get();

            $suits = Suit::with(['customer', 'worker'])
                ->where('suit_code', 'like', "%{$q}%")
                ->take(20)
                ->get();
        }

        return view('search.results', compact('q', 'customers', 'suits'));
    }
}
