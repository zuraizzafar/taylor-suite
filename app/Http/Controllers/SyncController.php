<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StitchType;
use App\Models\Suit;
use App\Models\Worker;
use App\Traits\HasBranchScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    use HasBranchScope;

    /**
     * GET /sync/pull
     * Returns all data for the current user's scope.
     * Cached locally in IndexedDB by the service worker.
     */
    public function pull(Request $request): JsonResponse
    {
        $user = auth()->user();

        $customers = $this->branchQuery(Customer::query())
            ->with('measurements')
            ->orderBy('file_number')
            ->get();

        $orders = $this->branchQuery(Order::query())
            ->with(['suits.stitchType', 'payments', 'customer:id,name,file_number'])
            ->orderByDesc('order_date')
            ->get();

        $suits = $this->branchQuery(Suit::query())
            ->with(['stitchType:id,name', 'worker:id,name', 'customer:id,name,file_number'])
            ->orderByDesc('updated_at')
            ->get();

        $workers = $this->branchQuery(Worker::query())
            ->orderBy('name')
            ->get();

        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        $stitchTypes = StitchType::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'customers'    => $customers,
            'orders'       => $orders,
            'suits'        => $suits,
            'workers'      => $workers,
            'branches'     => $branches,
            'stitch_types' => $stitchTypes,
            'synced_at'    => now()->toIso8601String(),
            'user_role'    => $user->role,
            'branch_id'    => $user->branch_id,
        ]);
    }

    /**
     * POST /sync/push
     * Accepts a single offline mutation and applies it.
     * The client sends: { type, payload, client_id, created_at }
     */
    public function push(Request $request): JsonResponse
    {
        $request->validate([
            'type'      => 'required|string',
            'payload'   => 'required|array',
            'client_id' => 'required|string',
        ]);

        $type      = $request->input('type');
        $payload   = $request->input('payload');
        $clientId  = $request->input('client_id');

        try {
            $result = DB::transaction(function () use ($type, $payload, $clientId) {
                return match ($type) {
                    'create_customer'    => $this->handleCreateCustomer($payload),
                    'create_order'       => $this->handleCreateOrder($payload),
                    'create_measurement' => $this->handleCreateMeasurement($payload),
                    'update_suit_status' => $this->handleUpdateSuitStatus($payload),
                    'create_payment'     => $this->handleCreatePayment($payload),
                    default              => throw new \InvalidArgumentException("Unknown mutation type: {$type}"),
                };
            });

            return response()->json([
                'ok'        => true,
                'client_id' => $clientId,
                'type'      => $type,
                'data'      => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'        => false,
                'client_id' => $clientId,
                'type'      => $type,
                'error'     => $e->getMessage(),
            ], 422);
        }
    }

    // ── Mutation handlers ─────────────────────────────────────────────────────

    private function handleCreateCustomer(array $p): array
    {
        $fileData = Customer::nextFileNumber();

        $customer = Customer::create([
            'file_sequence' => $fileData['file_sequence'],
            'file_number'   => $fileData['file_number'],
            'name'          => $p['name'] ?? 'Unknown',
            'mobile'        => $p['mobile'] ?? null,
            'address'       => $p['address'] ?? null,
            'notes'         => $p['notes'] ?? null,
            'branch_id'     => $p['branch_id'] ?? $this->currentBranchId(),
        ]);

        return ['id' => $customer->id, 'file_number' => $customer->file_number];
    }

    private function handleCreateOrder(array $p): array
    {
        $customer = Customer::findOrFail((int) ($p['customer_id'] ?? 0));
        $advance  = (float) ($p['advance_amount'] ?? 0);
        $total    = (float) ($p['total_amount']   ?? 0);

        // Generate order number
        $last = Order::max('id') ?? 0;
        $orderNumber = 'ORD-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);

        $order = Order::create([
            'customer_id'    => $customer->id,
            'branch_id'      => $p['branch_id'] ?? $customer->branch_id ?? $this->currentBranchId(),
            'order_number'   => $orderNumber,
            'order_date'     => $p['order_date'] ?? today()->toDateString(),
            'delivery_date'  => $p['delivery_date'] ?? null,
            'total_amount'   => $total,
            'advance_amount' => $advance,
            'balance_amount' => max(0, $total - $advance),
            'notes'          => $p['notes'] ?? null,
        ]);

        // Record advance as a payment
        if ($advance > 0) {
            Payment::create([
                'order_id'     => $order->id,
                'amount'       => $advance,
                'method'       => $p['payment_method'] ?? 'cash',
                'payment_date' => $p['order_date']     ?? today()->toDateString(),
                'note'         => 'Advance (offline sync)',
                'type'         => 'INITIAL_ADVANCE',
            ]);
        }

        return ['id' => $order->id, 'order_number' => $order->order_number];
    }

    private function handleCreateMeasurement(array $p): array
    {
        $customer = Customer::findOrFail((int) ($p['customer_id'] ?? 0));

        $measurement = Measurement::updateOrCreate(
            ['customer_id' => $customer->id],
            array_filter([
                'shirt_length'      => $p['shirt_length']      ?? null,
                'chest'             => $p['chest']             ?? null,
                'waist'             => $p['waist']             ?? null,
                'hips'              => $p['hips']              ?? null,
                'shoulder'          => $p['shoulder']          ?? null,
                'sleeve_length'     => $p['sleeve_length']     ?? null,
                'collar'            => $p['collar']            ?? null,
                'trouser_length'    => $p['trouser_length']    ?? null,
                'thigh'             => $p['thigh']             ?? null,
                'knee'              => $p['knee']              ?? null,
                'ankle'             => $p['ankle']             ?? null,
                'kameez_length'     => $p['kameez_length']     ?? null,
                'shalwar_length'    => $p['shalwar_length']    ?? null,
                'notes'             => $p['notes']             ?? null,
            ], fn($v) => $v !== null)
        );

        return ['id' => $measurement->id, 'customer_id' => $customer->id];
    }

    private function handleUpdateSuitStatus(array $p): array
    {
        $suit = Suit::findOrFail((int) ($p['suit_id'] ?? 0));

        $allowed = ['pending','assigned','stitching','ready','delivered'];
        $status  = in_array($p['status'] ?? '', $allowed) ? $p['status'] : null;

        if (!$status) {
            throw new \InvalidArgumentException('Invalid suit status: ' . ($p['status'] ?? ''));
        }

        $suit->update(['status' => $status]);

        return ['id' => $suit->id, 'status' => $suit->status];
    }

    private function handleCreatePayment(array $p): array
    {
        $order = Order::findOrFail((int) ($p['order_id'] ?? 0));

        $payment = Payment::create([
            'order_id'     => $order->id,
            'amount'       => (float) ($p['amount']       ?? 0),
            'method'       => $p['method']       ?? 'cash',
            'payment_date' => $p['payment_date'] ?? today()->toDateString(),
            'reference'    => $p['reference']    ?? null,
            'note'         => ($p['note'] ?? '') . ' (offline sync)',
        ]);

        $order->recalculateBalance();
        $order->save();

        return ['id' => $payment->id, 'order_id' => $order->id];
    }
}
