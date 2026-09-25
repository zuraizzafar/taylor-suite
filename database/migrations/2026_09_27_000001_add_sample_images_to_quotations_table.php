<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('sample_image_1')->nullable()->after('delivery_note');
            $table->string('sample_image_2')->nullable()->after('sample_image_1');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['sample_image_1', 'sample_image_2']);
        });
    }
};
