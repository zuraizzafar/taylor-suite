<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            $table->foreignId('fabric_id')->nullable()->after('measurement_id')->constrained()->nullOnDelete();
            $table->decimal('fabric_meter_deducted', 8, 2)->nullable()->after('fabric_meter');
        });
    }

    public function down(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fabric_id');
            $table->dropColumn('fabric_meter_deducted');
        });
    }
};
