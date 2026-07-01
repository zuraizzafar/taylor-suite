<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires both sides of a foreign key to be InnoDB. Force it here so a
        // `suits` table that drifted to MyISAM (e.g. from a manual import on shared
        // hosting) doesn't throw errno 150 when the constraint below is added.
        DB::statement('ALTER TABLE `suits` ENGINE=InnoDB');
        DB::statement('ALTER TABLE `fabrics` ENGINE=InnoDB');

        // Idempotent: a previous run may have added the columns but failed on the
        // constraint step (Laravel issues those as separate ALTER statements), so
        // don't blindly re-add columns that already exist.
        if (! Schema::hasColumn('suits', 'fabric_id')) {
            Schema::table('suits', function (Blueprint $table) {
                $table->foreignId('fabric_id')->nullable()->after('measurement_id');
            });
        }

        if (! Schema::hasColumn('suits', 'fabric_meter_deducted')) {
            Schema::table('suits', function (Blueprint $table) {
                $table->decimal('fabric_meter_deducted', 8, 2)->nullable()->after('fabric_meter');
            });
        }

        $constraintExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'suits')
            ->where('CONSTRAINT_NAME', 'suits_fabric_id_foreign')
            ->exists();

        if (! $constraintExists) {
            Schema::table('suits', function (Blueprint $table) {
                $table->foreign('fabric_id')->references('id')->on('fabrics')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('suits', function (Blueprint $table) {
            if (Schema::hasColumn('suits', 'fabric_id')) {
                $table->dropForeign(['fabric_id']);
                $table->dropColumn('fabric_id');
            }
            if (Schema::hasColumn('suits', 'fabric_meter_deducted')) {
                $table->dropColumn('fabric_meter_deducted');
            }
        });
    }
};
