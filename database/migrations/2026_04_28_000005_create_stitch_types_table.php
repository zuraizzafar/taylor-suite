<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stitch_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stitch_types');
    }
};
