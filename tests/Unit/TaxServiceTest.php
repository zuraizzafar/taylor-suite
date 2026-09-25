<?php

namespace Tests\Unit;

use App\Services\TaxService;
use PHPUnit\Framework\TestCase;

class TaxServiceTest extends TestCase
{
    public function test_none_mode_leaves_total_untouched(): void
    {
        $r = TaxService::calculate(10000, null, 0, 'none', 18);

        $this->assertSame(10000.0, $r['total_amount']);
        $this->assertSame(0.0, $r['tax_amount']);
        $this->assertSame(0.0, $r['tax_rate']);
    }

    public function test_exclusive_shows_tax_but_does_not_charge_it(): void
    {
        $r = TaxService::calculate(10000, null, 0, 'exclusive', 18);

        $this->assertSame(1800.0, $r['tax_amount']);
        $this->assertSame(10000.0, $r['total_amount']);
        $this->assertFalse($r['tax_collected']);
    }

    public function test_inclusive_adds_tax_to_what_client_pays(): void
    {
        $r = TaxService::calculate(10000, null, 0, 'inclusive', 18);

        $this->assertSame(1800.0, $r['tax_amount']);
        $this->assertSame(11800.0, $r['total_amount']);
        $this->assertTrue($r['tax_collected']);
    }

    public function test_percent_discount_is_applied_before_tax(): void
    {
        $r = TaxService::calculate(10000, 'percent', 10, 'inclusive', 18);

        $this->assertSame(1000.0, $r['discount_amount']);
        $this->assertSame(9000.0, $r['taxable']);
        $this->assertSame(1620.0, $r['tax_amount']);
        $this->assertSame(10620.0, $r['total_amount']);
    }

    public function test_fixed_discount_cannot_exceed_subtotal(): void
    {
        $r = TaxService::calculate(500, 'fixed', 900, 'inclusive', 18);

        $this->assertSame(500.0, $r['discount_amount']);
        $this->assertSame(0.0, $r['total_amount']);
    }

    public function test_percent_discount_is_capped_at_100(): void
    {
        $r = TaxService::calculate(500, 'percent', 250, 'none', 0);

        $this->assertSame(500.0, $r['discount_amount']);
        $this->assertSame(0.0, $r['total_amount']);
    }

    public function test_unknown_mode_falls_back_to_none(): void
    {
        $r = TaxService::calculate(1000, null, 0, 'bogus', 18);

        $this->assertSame('none', $r['tax_mode']);
        $this->assertSame(1000.0, $r['total_amount']);
    }

    public function test_tax_is_rounded_to_whole_rupees(): void
    {
        $r = TaxService::calculate(1234, null, 0, 'inclusive', 18); // 222.12

        $this->assertSame(222.0, $r['tax_amount']);
        $this->assertSame(1456.0, $r['total_amount']);
    }
}
