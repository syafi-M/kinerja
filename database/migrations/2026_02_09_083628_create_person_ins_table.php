<?php

use App\Models\Client;
use App\Models\Jabatan;
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
        Schema::create('person_ins', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(Jabatan::class);
            $table->date('date_in');
            $table->string('method_salary');
            $table->string('method_salary_manual')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_ins');
    }
};
