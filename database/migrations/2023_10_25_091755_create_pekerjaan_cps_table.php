<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Kerjasama;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pekerjaan_cps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Divisi::class);
            $table->foreignIdFor(PekerjaanCp::class);
            $table->foreignIdFor(Kerjasama::class);
            $table->string('name');
            $table->string('type_check');
            $table->string('img');
            $table->string('deskripsi');
            $table->string('approve_status');
            $table->string('latitude_longitude');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_cps');
    }
};
