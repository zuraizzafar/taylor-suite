<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabric_sales', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('fabric_id')->constrained()->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_mobile', 30)->nullable();
            $table->decimal('meter', 8, 2);
            $table->decimal('rate', 10, 2); // sale_price snapshot at time of sale
            $table->decimal('total_amount', 10, 2);
            $table->string('sale_code', 30)->unique();
            $table->foreignId('sold_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_sales');
    }
};
