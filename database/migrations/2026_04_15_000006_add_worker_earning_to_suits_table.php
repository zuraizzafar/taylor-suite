<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            $table->decimal('worker_earning', 8, 2)->nullable()->after('delivered_at');
            $table->timestamp('stitching_started_at')->nullable()->after('worker_earning');
        });
    }

    public function down(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            $table->dropColumn(['worker_earning', 'stitching_started_at']);
        });
    }
};
