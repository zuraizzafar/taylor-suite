<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_name', 255)->nullable()->after('name');
            $table->string('ntn', 50)->nullable()->after('company_name');
            $table->string('email', 255)->nullable()->after('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'ntn', 'email']);
        });
    }
};
