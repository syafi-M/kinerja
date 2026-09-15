<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_points', function (Blueprint $table): void {
            $table->text('input_manual')->nullable()->after('pekerjaan_cp_id');
        });
    }

    public function down(): void
    {
        Schema::table('check_points', function (Blueprint $table): void {
            $table->dropColumn('input_manual');
        });
    }
};
