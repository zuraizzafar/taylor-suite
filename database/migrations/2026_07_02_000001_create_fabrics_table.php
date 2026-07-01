<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fabric_type', 100);
            $table->string('brand', 100)->nullable();
            $table->string('color', 50);
            $table->string('design_code', 50)->nullable();
            $table->string('roll_number', 50)->unique();
            $table->decimal('total_meter', 8, 2);
            $table->decimal('available_meter', 8, 2);
            $table->decimal('cost_price', 10, 2); // per meter
            $table->decimal('sale_price', 10, 2); // per meter
            $table->string('supplier', 150)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('status', 20)->default('in_stock'); // in_stock|low_stock|out_of_stock
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
