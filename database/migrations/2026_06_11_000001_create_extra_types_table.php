<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('default_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed with common Pakistani tailoring extras
        DB::table('extra_types')->insert(array_map(fn($row) => array_merge($row, [
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]), [
            ['name' => 'Embroidery',        'default_price' => 500],
            ['name' => 'Lining',            'default_price' => 300],
            ['name' => 'Dry Cleaning',      'default_price' => 200],
            ['name' => 'Urgent Charges',    'default_price' => 500],
            ['name' => 'Handmade Buttons',  'default_price' => 150],
            ['name' => 'Piping Work',       'default_price' => 250],
            ['name' => 'Mirror Work',       'default_price' => 400],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_types');
    }
};
