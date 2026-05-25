<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->default('ur');
            $table->string('key', 500);
            $table->text('value');
            $table->timestamps();

            $table->unique(['locale', 'key'], 'trans_locale_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_overrides');
    }
};
