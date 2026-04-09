<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('measurement_id')->nullable()->constrained('measurements')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->unsignedInteger('suit_number'); // sequential per customer: 1, 2, 3…
            $table->string('suit_code', 30)->unique(); // ST-001-1
            $table->string('suit_type'); // e.g. Shalwar Kameez, Pant Coat
            $table->decimal('fabric_meter', 5, 2);
            $table->string('fabric_description')->nullable();
            $table->string('status', 20)->default('pending'); // pending|cutting|stitching|ready|delivered
            $table->text('notes')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suits');
    }
};
