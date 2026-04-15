<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add branch_id to users, customers, orders, suits
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('role')
                ->constrained('branches')->nullOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('notes')
                ->constrained('branches')->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('customer_id')
                ->constrained('branches')->nullOnDelete();
        });

        Schema::table('suits', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('customer_id')
                ->constrained('branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Branch::class);
            $table->dropColumn('branch_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Branch::class);
            $table->dropColumn('branch_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Branch::class);
            $table->dropColumn('branch_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Branch::class);
            $table->dropColumn('branch_id');
        });
    }
};
