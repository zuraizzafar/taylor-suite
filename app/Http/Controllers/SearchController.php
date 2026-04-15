<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Suit;
use App\Traits\HasBranchScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $q = trim($request->input('q', ''));
        $customers = collect();
        $suits     = collect();

        if (strlen($q) >= 2) {
            $customerQuery = Customer::where(function ($cq) use ($q) {
                $cq->where('name', 'like', "%{$q}%")
                   ->orWhere('mobile', 'like', "%{$q}%")
                   ->orWhere('file_number', 'like', "%{$q}%");
            })->withCount('suits');
            $this->branchQuery($customerQuery);
            $customers = $customerQuery->take(20)->get();

            $suitQuery = Suit::with(['customer', 'worker'])
                ->where('suit_code', 'like', "%{$q}%");
            $this->branchQuery($suitQuery);
            $suits = $suitQuery->take(20)->get();
        }

        return view('search.results', compact('q', 'customers', 'suits'));
    }
}
