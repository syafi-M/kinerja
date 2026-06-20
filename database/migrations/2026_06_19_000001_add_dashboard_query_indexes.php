<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->index(['user_id', 'absensi_type_pulang', 'created_at'], 'absensis_dashboard_pulang_created_idx');
            $table->index(['user_id', 'absensi_type_pulang', 'tanggal_absen'], 'absensis_dashboard_pulang_tanggal_idx');
            $table->index(['created_at'], 'absensis_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('absensis_dashboard_pulang_created_idx');
            $table->dropIndex('absensis_dashboard_pulang_tanggal_idx');
            $table->dropIndex('absensis_created_at_idx');
        });
    }
};
