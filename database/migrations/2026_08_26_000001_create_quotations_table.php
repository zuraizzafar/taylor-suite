<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('quotation_number', 20)->unique(); // QT-2026-001
            $table->date('quotation_date');
            $table->unsignedInteger('validity_days')->default(15);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('advance_percentage', 5, 2)->default(50);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->text('design_reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft'); // draft|converted
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
