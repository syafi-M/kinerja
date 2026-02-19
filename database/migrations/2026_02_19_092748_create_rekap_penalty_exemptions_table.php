<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard for partially-created table from previous failed migration run.
        Schema::dropIfExists('rekap_penalty_exemptions');

        Schema::create('rekap_penalty_exemptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('is_active')->default(true);
            $table->string('source')->default('leader_self');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_penalty_exemptions');
    }
};
