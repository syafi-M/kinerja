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
        // Add created_by_user_id to person_ins table
        Schema::table('person_ins', function (Blueprint $table) {
            if (!Schema::hasColumn('person_ins', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('status');
            }
        });

        // Add created_by_user_id to person_outs table
        Schema::table('person_outs', function (Blueprint $table) {
            if (!Schema::hasColumn('person_outs', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('status');
            }
        });

        // Add created_by_user_id to cuttings table
        Schema::table('cuttings', function (Blueprint $table) {
        });

        // Add created_by_user_id to overtimes table
        Schema::table('overtimes', function (Blueprint $table) {
            if (!Schema::hasColumn('overtimes', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('status');
            }
        });

        // Add created_by_user_id to finished_trainings table
        Schema::table('finished_trainings', function (Blueprint $table) {
            if (!Schema::hasColumn('finished_trainings', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('person_ins', function (Blueprint $table) {
        });

        Schema::table('person_outs', function (Blueprint $table) {
        });

        Schema::table('cuttings', function (Blueprint $table) {
        });

        Schema::table('overtimes', function (Blueprint $table) {
            if (Schema::hasColumn('overtimes', 'created_by_user_id')) {
                $table->dropColumn('created_by_user_id');
            }
        });

        Schema::table('finished_trainings', function (Blueprint $table) {
            if (Schema::hasColumn('finished_trainings', 'created_by_user_id')) {
                $table->dropColumn('created_by_user_id');
            }
        });
    }
};
