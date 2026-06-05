<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed with common Pakistani suit types
        DB::table('suit_types')->insert(array_map(fn($name) => [
            'name'       => $name,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], [
            'Kameez Shalwar',
            'Sherwani',
            'Coat Pant',
            'Waistcoat',
            'Kurta',
            'Kameez Only',
            'Shalwar Only',
            'Suit (3-Piece)',
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('suit_types');
    }
};
