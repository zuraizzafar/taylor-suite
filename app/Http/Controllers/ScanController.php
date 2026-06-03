<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Suit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function show(string $code): View|RedirectResponse
    {
        $suit = Suit::with(['customer', 'worker', 'order'])
            ->where('suit_code', strtoupper($code))
            ->firstOrFail();

        // Logged-in admin or branch_manager → redirect to full backend view
        if (Auth::check() && in_array(Auth::user()?->role, ['admin', 'branch_manager'])) {
            return redirect()->route('suits.show', $suit);
        }

        return view('scan.show', compact('suit'));
    }

    public function tracking(Request $request): View|RedirectResponse
    {
        $query = trim((string) $request->input('q', ''));

        if ($query !== '') {
            return redirect()->route('tracking.show', ['tracking' => strtoupper($query)]);
        }

        return view('scan.tracking', [
            'order' => null,
            'tracking' => null,
            'searchError' => null,
        ]);
    }

    public function trackingShow(string $tracking): View|RedirectResponse
    {
        $tracking = strtoupper(trim($tracking));
        [$order, $resolvedTracking] = $this->findTrackedOrder($tracking);

        return view('scan.tracking', [
            'order' => $order,
            'tracking' => $resolvedTracking ?: $tracking,
            'searchError' => $order ? null : 'We could not find an order for that tracking number.',
        ]);
    }

    private function findTrackedOrder(string $tracking): array
    {
        $order = Order::with(['customer', 'suits.worker'])
            ->where('order_number', $tracking)
            ->first();

        if ($order) {
            return [$order, $order->order_number];
        }

        $suit = Suit::with(['order.customer', 'order.suits.worker'])
            ->where('suit_code', $tracking)
            ->first();

        if ($suit?->order) {
            return [$suit->order, $suit->suit_code];
        }

        return [null, null];
    }
}
