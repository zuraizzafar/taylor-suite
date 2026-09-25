<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // total_amount stays the amount the customer actually pays. subtotal is the
        // pre-discount, pre-tax value.
        foreach (['orders', 'quotations'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->string('discount_type', 10)->nullable();
                $table->decimal('discount_value', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('tax_mode', 15)->default('none');
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
            });
            DB::table($tableName)->update(['subtotal' => DB::raw('total_amount')]);
        }

        Schema::table('fabric_sales', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->string('tax_mode', 15)->default('none');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
        });
        DB::table('fabric_sales')->update(['subtotal' => DB::raw('total_amount')]);

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->string('supplier_name', 150)->nullable();
            $table->string('supplier_ntn', 50)->nullable();
            $table->string('supplier_invoice_no', 50)->nullable();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->string('tax_mode', 15)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('tax_registration_no', 50)->nullable();
        });
    }

    public function down(): void
    {
        foreach (['orders', 'quotations'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['subtotal', 'discount_type', 'discount_value', 'discount_amount', 'tax_mode', 'tax_rate', 'tax_amount']);
            });
        }
        Schema::table('fabric_sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_mode', 'tax_rate', 'tax_amount']);
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'supplier_name', 'supplier_ntn', 'supplier_invoice_no']);
        });
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['tax_mode', 'tax_rate', 'tax_registration_no']);
        });
    }
};
