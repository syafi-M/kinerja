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
            $table->string('subuh')->nullable()->after('point_id');
            $table->string('fotoSubuh')->nullable()->after('subuh');
            $table->string('dzuhur')->nullable()->after('fotoSubuh');
            $table->string('fotoDzuhur')->nullable()->after('dzuhur');
            $table->string('asar')->nullable()->after('fotoDzuhur');
            $table->string('fotoAsar')->nullable()->after('asar');
            $table->string('maghrib')->nullable()->after('fotoAsar');
            $table->string('fotoMaghrib')->nullable()->after('maghrib');
            $table->string('isya')->nullable()->after('fotoMaghrib');
            $table->string('fotoIsya')->nullable()->after('isya');
            $table->string('subuh_lat')->nullable()->after('subuh');
            $table->string('subuh_long')->nullable()->after('subuh_lat');
            $table->string('dzuhur_lat')->nullable()->after('subuh_long');
            $table->string('dzuhur_long')->nullable()->after('dzuhur_lat');
            $table->string('asar_lat')->nullable()->after('dzuhur_long');
            $table->string('asar_long')->nullable()->after('asar_lat');
            $table->string('maghrib_lat')->nullable()->after('asar_long');
            $table->string('maghrib_long')->nullable()->after('maghrib_lat');
            $table->string('isya_lat')->nullable()->after('maghrib_long');
            $table->string('isya_long')->nullable()->after('isya_lat');
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
