<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard for partially-created table from previous failed migration run.
        Schema::dropIfExists('rekap_due_date_settings');

        Schema::create('rekap_due_date_settings', function (Blueprint $table) {
            $table->id();
            $table->date('due_date');
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_due_date_settings');
    }
};
