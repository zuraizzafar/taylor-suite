<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_stitch_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stitch_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['worker_id', 'stitch_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_stitch_rates');
    }
};
