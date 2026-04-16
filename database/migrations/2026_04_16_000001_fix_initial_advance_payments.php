<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * For every order that has advance_amount > 0 and no INITIAL_ADVANCE payment record yet,
     * create that Payment row so recalculateBalance() has a complete picture.
     *
     * Only safe for orders with NO regular payments — those have a reliable advance_amount.
     * Orders that already went through the buggy recalculateBalance() have lost their
     * original advance; we cannot recover it, so we leave them alone.
     */
    public function up(): void
    {
        $orders = DB::table('orders')
            ->where('advance_amount', '>', 0)
            ->whereNotIn('id', function ($q) {
                $q->select('order_id')
                  ->from('payments')
                  ->where('reference', 'INITIAL_ADVANCE');
            })
            ->get();

        $now = now();

        foreach ($orders as $order) {
            $paymentSum = DB::table('payments')
                ->where('order_id', $order->id)
                ->sum('amount');

            if ((float) $paymentSum > 0) {
                // Payments already exist — advance_amount was overwritten by the bug.
                // We can't recover the original advance; skip this order.
                continue;
            }

            // No payments yet: advance_amount is exactly the original initial advance.
            DB::table('payments')->insert([
                'order_id'     => $order->id,
                'branch_id'    => $order->branch_id,
                'received_by'  => null,
                'amount'       => $order->advance_amount,
                'method'       => 'cash',
                'payment_date' => $order->order_date,
                'reference'    => 'INITIAL_ADVANCE',
                'note'         => 'Initial advance (migrated)',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Remove only the migrated records (not ones created by the app going forward)
        DB::table('payments')
            ->where('reference', 'INITIAL_ADVANCE')
            ->where('note', 'Initial advance (migrated)')
            ->delete();
    }
};
