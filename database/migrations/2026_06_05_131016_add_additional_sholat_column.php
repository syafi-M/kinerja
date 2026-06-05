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
            $table->string('fotoSubuh')->nullable()->after('subuh');
            $table->string('fotoAsar')->nullable()->after('asar');
            $table->string('fotoIsya')->nullable()->after('isya');
            $table->string('dzuhur_lat')->nullable()->after('subuh_long');
            $table->string('dzuhur_long')->nullable()->after('dzuhur_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            //
        });
    }
};
