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
        Schema::connection('mysql2')->create('p_g_j__kontraks', function (Blueprint $table) {
            $table->id();
            $table->string('no_srt')->nullable();
            $table->longText('ttd')->nullable();
            $table->longText('ttd_atasan')->nullable();
            $table->integer('send_to_operator')->default(0);
            $table->integer('send_to_atasan')->default(0);

            // Pihak Kedua columns
            $table->string('nama_pk_kda');
            $table->string('tempat_lahir_pk_kda')->nullable();
            $table->date('tgl_lahir_pk_kda')->nullable();
            $table->string('nik_pk_kda')->nullable();
            $table->text('alamat_pk_kda')->nullable();
            $table->string('jabatan_pk_kda')->nullable();
            $table->string('unit_pk_kda')->nullable();
            $table->string('status_pk_kda')->nullable();

            // Contract dates
            $table->date('tgl_mulai_kontrak')->nullable();
            $table->date('tgl_selesai_kontrak')->nullable();

            // Additional columns
            $table->date('tgl_dibuat')->nullable();

            // Pihak Pertama columns
            $table->string('nama_pk_ptm')->nullable();
            $table->text('alamat_pk_ptm')->nullable();
            $table->string('jabatan_pk_ptm')->nullable();

            // Salary/allowance columns
            $table->decimal('g_pok', 15, 2)->default(0);
            $table->decimal('tj_hadir', 15, 2)->default(0);
            $table->decimal('kinerja', 15, 2)->default(0);
            $table->decimal('lain_lain', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql2')->dropIfExists('p_g_j__kontraks');
    }
};
