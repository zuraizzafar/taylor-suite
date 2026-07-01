<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabric_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // added|suit_used|fabric_sale|return|damage|adjustment
            $table->decimal('meter', 8, 2);
            $table->string('reference_type', 30)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_movements');
    }
};
