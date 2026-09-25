<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Fabric;
use App\Models\FabricSale;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Setting;
use App\Models\User;
use App\Services\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxAndDiscountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'a@example.com', 'password' => 'secret123', 'role' => 'admin',
        ]);
        $this->customer = Customer::create([
            'file_sequence' => 1, 'file_number' => 'ST-001', 'name' => 'Test Client',
            'company_name' => 'Akhuwat', 'ntn' => '3048949-7', 'mobile' => '03001234567',
        ]);
        $this->actingAs($this->admin);

        Setting::set('tax_default_mode', 'inclusive');
        Setting::set('tax_default_rate', '18');
    }

    private function quotationPayload(array $over = []): array
    {
        return array_merge([
            'customer_id'        => $this->customer->id,
            'quotation_date'     => '2026-10-01',
            'validity_days'      => 15,
            'advance_percentage' => 50,
            'description'        => ['Suit stitching', 'Embroidery'],
            'qty'                => [2, 2],
            'rate'               => [3000, 2000],
            'discount_type'      => 'percent',
            'discount_value'     => 10,
            'tax_mode'           => 'inclusive',
            'tax_rate'           => 18,
        ], $over);
    }

    public function test_quotation_applies_discount_then_inclusive_tax(): void
    {
        $this->post(route('quotations.store'), $this->quotationPayload())->assertRedirect();

        $q = Quotation::firstOrFail();
        $this->assertEquals(10000, $q->subtotal);
        $this->assertEquals(1000, $q->discount_amount);
        $this->assertEquals(1620, $q->tax_amount);
        $this->assertEquals(10620, $q->total_amount);
        $this->assertEquals(5310, $q->advance_amount);
        $this->assertEquals(5310, $q->balance_amount);
    }

    public function test_quotation_exclusive_tax_is_not_added_to_total(): void
    {
        $this->post(route('quotations.store'), $this->quotationPayload(['tax_mode' => 'exclusive', 'discount_type' => '']))->assertRedirect();

        $q = Quotation::firstOrFail();
        $this->assertEquals(1800, $q->tax_amount);
        $this->assertEquals(10000, $q->total_amount);
    }

    public function test_quotation_edit_recalculates(): void
    {
        $this->post(route('quotations.store'), $this->quotationPayload());
        $q = Quotation::firstOrFail();

        $this->put(route('quotations.update', $q), $this->quotationPayload([
            'rate' => [1000, 1000], 'discount_type' => 'fixed', 'discount_value' => 500, 'tax_mode' => 'none',
        ]))->assertRedirect();

        $q->refresh();
        $this->assertEquals(4000, $q->subtotal);
        $this->assertEquals(500, $q->discount_amount);
        $this->assertEquals(0, $q->tax_amount);
        $this->assertEquals(3500, $q->total_amount);
    }

    public function test_quotation_pages_and_pdf_render(): void
    {
        Setting::set('tax_registration_no', '12-34-5678-901-23');
        $this->post(route('quotations.store'), $this->quotationPayload());
        $q = Quotation::firstOrFail();

        $this->get(route('quotations.create'))->assertOk()->assertSee('Discount');
        $this->get(route('quotations.edit', $q))->assertOk();
        $this->get(route('quotations.show', $q))->assertOk()->assertSee('1,620');
        $this->get(route('quotations.pdf', $q))->assertOk()->assertHeader('content-type', 'application/pdf');

        // Urdu locale renders the browser-print page, whose HTML we can inspect
        $this->withSession(['locale' => 'ur'])->get(route('quotations.pdf', $q))
            ->assertOk()->assertSee('1,620')->assertSee('12-34-5678-901-23')->assertSee('3048949-7');
    }

    public function test_converting_quotation_carries_discount_and_tax_to_the_order(): void
    {
        $this->post(route('quotations.store'), $this->quotationPayload());
        $q = Quotation::firstOrFail();

        $resp = $this->post(route('quotations.convert', $q));
        $o = Order::firstOrFail();
        $resp->assertRedirect(route('measurements.create', [
            'customer' => $this->customer->id, 'redirect_to' => route('orders.suits-prompt', $o),
        ]));

        $this->assertEquals(10000, $o->subtotal);
        $this->assertEquals(1000, $o->discount_amount);
        $this->assertEquals('inclusive', $o->tax_mode);
        $this->assertEquals(1620, $o->tax_amount);
        $this->assertEquals(10620, $o->total_amount);
        // 50% advance of the payable total is recorded as a payment; the rest is the balance
        $this->assertEquals(5310, $o->fresh()->balance_amount);
    }

    public function test_order_store_computes_totals_server_side(): void
    {
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-01',
            'subtotal' => 20000, 'advance_amount' => 5000,
            'discount_type' => 'fixed', 'discount_value' => 2000,
            'tax_mode' => 'exclusive', 'tax_rate' => 16,
            'total_amount' => 1, // a tampered client total must be ignored
        ])->assertRedirect();

        $o = Order::firstOrFail();
        $this->assertEquals(20000, $o->subtotal);
        $this->assertEquals(2000, $o->discount_amount);
        $this->assertEquals(2880, $o->tax_amount);
        $this->assertEquals(18000, $o->total_amount);
        $this->assertEquals(13000, $o->balance_amount);

        $this->get(route('orders.show', $o))->assertOk();
        $this->get(route('orders.edit', $o))->assertOk();
        $this->get(route('orders.invoice', $o))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->withSession(['locale' => 'ur'])->get(route('orders.invoice', $o))->assertOk()->assertSee('Akhuwat');
    }

    public function test_disabled_module_forces_no_tax(): void
    {
        Setting::set('tax_order_enabled', '0');

        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-01',
            'subtotal' => 1000, 'tax_mode' => 'inclusive', 'tax_rate' => 18,
        ])->assertRedirect();

        $o = Order::firstOrFail();
        $this->assertSame('none', $o->tax_mode);
        $this->assertEquals(1000, $o->total_amount);
    }

    public function test_branch_overrides_shop_defaults(): void
    {
        $branch = Branch::create(['name' => 'DHA', 'is_active' => true, 'tax_mode' => 'exclusive', 'tax_rate' => 5, 'tax_registration_no' => 'DHA-1']);

        $shop = TaxService::resolve('order', null);
        $this->assertSame('inclusive', $shop['mode']);
        $this->assertEquals(18, $shop['rate']);

        $b = TaxService::resolve('order', $branch->id);
        $this->assertSame('exclusive', $b['mode']);
        $this->assertEquals(5, $b['rate']);
        $this->assertSame('DHA-1', $b['registration_no']);

        Setting::set('tax_rate_order', '16');
        $this->assertEquals(16, TaxService::resolve('order', null)['rate']);          // per-module rate
        $this->assertEquals(18, TaxService::resolve('fabric_sale', null)['rate']);    // falls back to default
    }

    public function test_pos_order_uses_tax_and_discount(): void
    {
        $this->post(route('pos.store'), [
            'customer_name' => 'Walk In', 'customer_mobile' => '0300', 'order_date' => '2026-10-01',
            'subtotal' => 10000, 'advance_amount' => 1000,
            'discount_type' => 'percent', 'discount_value' => 10,
            'tax_mode' => 'inclusive', 'tax_rate' => 18,
            'suits' => [['suit_type' => 'Shalwar Kameez', 'fabric_meter' => 4]],
        ])->assertRedirect();

        $o = Order::firstOrFail();
        $this->assertEquals(9000, $o->subtotal - $o->discount_amount);
        $this->assertEquals(10620, $o->total_amount);
        $this->assertEquals(9620, $o->fresh()->balance_amount);
    }

    public function test_pos_rejects_advance_larger_than_payable_total(): void
    {
        $this->post(route('pos.store'), [
            'customer_name' => 'Walk In', 'customer_mobile' => '0300', 'order_date' => '2026-10-01',
            'subtotal' => 1000, 'advance_amount' => 5000, 'tax_mode' => 'none',
            'suits' => [['suit_type' => 'Shalwar Kameez', 'fabric_meter' => 4]],
        ])->assertSessionHasErrors('advance_amount');
        $this->assertSame(0, Order::count());
    }

    public function test_fabric_sale_tax(): void
    {
        $fabric = Fabric::create([
            'fabric_type' => 'Wash & Wear', 'color' => 'Blue', 'roll_number' => 'R-1',
            'total_meter' => 50, 'available_meter' => 50, 'cost_price' => 800, 'sale_price' => 1000,
        ]);

        $this->post(route('fabric-sales.store'), [
            'fabric_id' => $fabric->id, 'customer_name' => 'Buyer', 'meter' => 5,
            'tax_mode' => 'inclusive', 'tax_rate' => 18,
        ])->assertRedirect();

        $s = FabricSale::firstOrFail();
        $this->assertEquals(5000, $s->subtotal);
        $this->assertEquals(900, $s->tax_amount);
        $this->assertEquals(5900, $s->total_amount);
        $this->get(route('fabric-sales.invoice', $s))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get(route('fabric-sales.create'))->assertOk();
    }

    public function test_expense_input_tax_and_validation(): void
    {
        $this->post(route('expenses.store'), [
            'category' => 'Fabric Purchase', 'amount' => 1180, 'tax_amount' => 180, 'date' => '2026-10-02',
            'supplier_name' => 'Mills', 'supplier_ntn' => '111-2', 'supplier_invoice_no' => 'S-9',
        ])->assertRedirect();
        $this->assertEquals(180, Expense::firstOrFail()->tax_amount);

        $this->post(route('expenses.store'), [
            'category' => 'Rent', 'amount' => 100, 'tax_amount' => 500, 'date' => '2026-10-02',
        ])->assertSessionHasErrors('tax_amount');

        $this->get(route('expenses.index'))->assertOk();
        $this->get(route('expenses.create'))->assertOk();
    }

    public function test_tax_report_summarises_output_input_and_net(): void
    {
        // Invoice: 10,000 taxable, exclusive 18% => 1,800 output tax (borne by us)
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-03',
            'subtotal' => 10000, 'tax_mode' => 'exclusive', 'tax_rate' => 18,
        ]);
        // Invoice: 5,000 taxable, inclusive 18% => 900 output tax (charged)
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-04',
            'subtotal' => 5000, 'tax_mode' => 'inclusive', 'tax_rate' => 18,
        ]);
        // An untaxed invoice must not appear
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-05',
            'subtotal' => 700, 'tax_mode' => 'none',
        ]);
        Expense::create(['category' => 'Fabric Purchase', 'amount' => 590, 'tax_amount' => 90, 'date' => '2026-10-06']);

        $params = ['from' => '2026-10-01', 'to' => '2026-10-31'];

        $r = $this->get(route('reports.tax', $params))->assertOk();
        $summary = $r->viewData('summary');
        $this->assertEquals(15000, $summary['taxable']);
        $this->assertEquals(2700, $summary['output_tax']);
        $this->assertEquals(900, $summary['collected']);
        $this->assertEquals(1800, $summary['borne']);
        $this->assertEquals(90, $summary['input_tax']);
        $this->assertEquals(2610, $summary['net_payable']);
        $this->assertCount(2, $r->viewData('rows'));
        $this->assertSame('3048949-7', $r->viewData('rows')[0]['buyer_ntn']);

        // outside the period => nothing
        $empty = $this->get(route('reports.tax', ['from' => '2025-01-01', 'to' => '2025-01-31']));
        $this->assertEquals(0, $empty->viewData('summary')['output_tax']);

        $this->get(route('reports.tax-pdf', $params))->assertOk()->assertHeader('content-type', 'application/pdf');
        $csv = $this->get(route('reports.tax-csv', $params))->assertOk();
        $this->assertStringContainsString('Net tax payable', $csv->streamedContent());
        $this->assertStringContainsString('ORD-', $csv->streamedContent() ?: '');
    }

    public function test_discounts_report(): void
    {
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-03',
            'subtotal' => 10000, 'discount_type' => 'percent', 'discount_value' => 10, 'tax_mode' => 'none',
        ]);
        $this->post(route('quotations.store'), $this->quotationPayload(['quotation_date' => '2026-10-04']));

        $params = ['from' => '2026-10-01', 'to' => '2026-10-31'];
        $r = $this->get(route('reports.discounts', $params))->assertOk();
        $s = $r->viewData('summary');
        $this->assertEquals(1000, $s['invoice_discount']);
        $this->assertEquals(1, $s['invoice_count']);
        $this->assertEquals(1000, $s['quotation_discount']);
        $this->assertEquals(10, $s['pct_of_sales']);

        $this->get(route('reports.discounts-pdf', $params))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get(route('reports.discounts-csv', $params))->assertOk();
        $this->get(route('reports.index'))->assertOk()->assertSee('Sales Tax Report');
    }

    public function test_dashboard_shows_tax_tiles_and_adjusts_profit(): void
    {
        $this->get(route('dashboard'))->assertOk()->assertDontSee('Net Tax Payable');

        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-03',
            'subtotal' => 10000, 'advance_amount' => 10000, 'tax_mode' => 'exclusive', 'tax_rate' => 18,
        ]);

        $r = $this->get(route('dashboard'))->assertOk()->assertSee('Net Tax Payable');
        $finance = $r->viewData('finance');
        $this->assertEquals(1800, $finance['outputTax']);
        // collected 10,000 - output tax 1,800 (we bore it)
        $this->assertEquals(8200, $finance['netProfit']);
    }

    public function test_settings_and_branch_tax_fields_persist(): void
    {
        $this->put(route('settings.update'), [
            'tax_label' => 'Sales Tax', 'tax_registration_no' => 'STRN-1',
            'tax_default_mode' => 'exclusive', 'tax_default_rate' => '18',
            'tax_quotation_enabled' => '1', 'tax_order_enabled' => '0', 'tax_fabric_sale_enabled' => '1',
            'tax_rate_fabric_sale' => '17',
        ])->assertSessionHasNoErrors();

        $this->assertSame('exclusive', Setting::get('tax_default_mode'));
        $this->assertSame('0', Setting::get('tax_order_enabled'));
        $this->assertFalse(TaxService::resolve('order')['enabled']);
        $this->assertTrue(TaxService::resolve('quotation')['enabled']);
        $this->assertEquals(17, TaxService::resolve('fabric_sale')['rate']);
        $this->get(route('settings.index'))->assertOk()->assertSee('STRN-1');

        $this->post(route('branches.store'), [
            'name' => 'Gulberg', 'tax_mode' => 'inclusive', 'tax_rate' => '5', 'tax_registration_no' => 'B-7', 'is_active' => 1,
        ])->assertRedirect();
        $b = Branch::where('name', 'Gulberg')->firstOrFail();
        $this->assertSame('inclusive', $b->tax_mode);
        $this->get(route('branches.edit', $b))->assertOk();
    }

    public function test_customer_quick_create_accepts_company_ntn_email(): void
    {
        $this->postJson(route('customers.quick-create'), [
            'name' => 'Ali', 'mobile' => '0301', 'company_name' => 'Acme', 'ntn' => '123-4', 'email' => 'a@acme.pk',
        ])->assertOk()->assertJsonPath('name', 'Ali');

        $c = Customer::where('name', 'Ali')->firstOrFail();
        $this->assertSame('Acme', $c->company_name);
        $this->assertSame('123-4', $c->ntn);
    }

    public function test_suits_prompt_offers_add_or_skip_and_skips_when_suits_exist(): void
    {
        $this->post(route('orders.store'), [
            'customer_id' => $this->customer->id, 'order_date' => '2026-10-01', 'subtotal' => 1000, 'tax_mode' => 'none',
        ]);
        $o = Order::firstOrFail();

        $this->get(route('orders.suits-prompt', $o))->assertOk()
            ->assertSee(route('suits.create', ['order_id' => $o->id, 'customer_id' => $this->customer->id]))
            ->assertSee(route('orders.show', $o))
            ->assertSee('Skip');

        \App\Models\Suit::create([
            'customer_id' => $this->customer->id, 'order_id' => $o->id, 'suit_number' => 1, 'suit_code' => 'S00001',
            'suit_type' => 'Shalwar Kameez', 'fabric_meter' => 4, 'status' => 'pending',
        ]);
        $this->get(route('orders.suits-prompt', $o))->assertRedirect(route('orders.show', $o));
    }
}
