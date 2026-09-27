<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Order/quotation numbers are generated from a row count (ORD-YYYY-NNN), so two requests
 * arriving at the same instant can compute the same "next" number and collide on the
 * column's unique constraint. HasUniqueNumber::createWithUniqueNumber() is the safety net:
 * catch that specific collision and retry with a fresh number instead of a 500 in production.
 */
class UniqueNumberRetryTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::create([
            'file_sequence' => 1, 'file_number' => 'ST-001', 'name' => 'Test Client', 'mobile' => '0300',
        ]);
    }

    private function orderAttributes(array $over = []): array
    {
        return array_merge([
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-01',
            'subtotal' => 500, 'total_amount' => 500, 'advance_amount' => 0, 'balance_amount' => 500,
        ], $over);
    }

    public function test_order_number_collision_is_caught_and_retried_with_a_new_number(): void
    {
        // Simulates another request having already taken the number ours would compute first.
        Order::create($this->orderAttributes(['order_number' => 'ORD-DUP-1']));

        $attempts = [];
        $order = Order::createWithUniqueNumber('order_number', function (int $offset) use (&$attempts) {
            $attempts[] = $offset;
            return $offset === 0 ? 'ORD-DUP-1' : 'ORD-DUP-2';
        }, $this->orderAttributes());

        $this->assertSame([0, 1], $attempts, 'first attempt collided, second attempt used a fresh number');
        $this->assertSame('ORD-DUP-2', $order->order_number);
        $this->assertSame(2, Order::count());
    }

    public function test_quotation_number_collision_is_caught_and_retried_with_a_new_number(): void
    {
        Quotation::create([
            'customer_id' => $this->customer->id, 'quotation_number' => 'QT-DUP-1',
            'quotation_date' => '2026-10-01', 'validity_days' => 15, 'advance_percentage' => 50, 'status' => 'draft',
        ]);

        $q = Quotation::createWithUniqueNumber('quotation_number', fn (int $offset) => $offset === 0 ? 'QT-DUP-1' : 'QT-DUP-2', [
            'customer_id' => $this->customer->id, 'quotation_date' => '2026-10-01',
            'validity_days' => 15, 'advance_percentage' => 50, 'status' => 'draft',
        ]);

        $this->assertSame('QT-DUP-2', $q->quotation_number);
        $this->assertSame(2, Quotation::count());
    }

    public function test_real_number_generators_recover_from_a_forced_collision(): void
    {
        // nextOrderNumber(0) will compute 'ORD-<year>-001' for a fresh table; occupy it directly
        // (bypassing the generator) the way a genuinely concurrent request would.
        $year = date('Y');
        Order::create($this->orderAttributes(['order_number' => "ORD-{$year}-001"]));

        // The natural next guess based on current count is now ORD-<year>-002, i.e. no real
        // collision remains — this just proves the production code path (Order::nextOrderNumber
        // wired through createWithUniqueNumber) still works end to end, not only the mocked path above.
        $order = Order::createWithUniqueNumber('order_number', fn (int $offset) => Order::nextOrderNumber($offset), $this->orderAttributes());
        $this->assertSame("ORD-{$year}-002", $order->order_number);
    }

    public function test_gives_up_after_max_attempts_without_leaving_partial_rows(): void
    {
        Order::create($this->orderAttributes(['order_number' => 'ORD-STUCK']));

        $threw = false;
        try {
            Order::createWithUniqueNumber('order_number', fn () => 'ORD-STUCK', $this->orderAttributes(), 3);
        } catch (UniqueConstraintViolationException) {
            $threw = true;
        }

        $this->assertTrue($threw, 'a generator that can never produce a free number must eventually surface, not loop forever');
        $this->assertSame(1, Order::count(), 'no partially-created rows were left behind by the failed attempts');
    }
}
