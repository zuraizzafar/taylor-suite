<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Default'); // name/label for this measurement set
            // Qameez measurements (decimal for half-inch precision)
            $table->decimal('q_length', 5, 1)->nullable();
            $table->decimal('q_shoulder', 5, 1)->nullable();
            $table->decimal('q_chest', 5, 1)->nullable();
            $table->decimal('q_waist', 5, 1)->nullable();
            $table->decimal('q_seat', 5, 1)->nullable();
            $table->decimal('q_sleeve', 5, 1)->nullable();
            $table->decimal('q_sleeve_width', 5, 1)->nullable();
            $table->decimal('q_collar', 5, 1)->nullable();
            $table->decimal('q_front', 5, 1)->nullable();
            $table->decimal('q_back', 5, 1)->nullable();
            $table->decimal('q_armhole', 5, 1)->nullable();
            $table->decimal('q_cuff', 5, 1)->nullable();
            // Shalwar measurements
            $table->decimal('s_length', 5, 1)->nullable();
            $table->decimal('s_waist', 5, 1)->nullable();
            $table->decimal('s_seat', 5, 1)->nullable();
            $table->decimal('s_thigh', 5, 1)->nullable();
            $table->decimal('s_knee', 5, 1)->nullable();
            $table->decimal('s_bottom', 5, 1)->nullable();
            $table->decimal('s_crotch', 5, 1)->nullable();
            $table->decimal('s_ankle', 5, 1)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
