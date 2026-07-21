<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep day+time only semantics; store as datetime so time is available.
        Schema::table('rekap_due_date_settings', function (Blueprint $table) {
            $table->dateTime('due_date')->change();
        });

        // Existing date-only rows → midnight of that day.
        DB::table('rekap_due_date_settings')
            ->whereNotNull('due_date')
            ->orderBy('id')
            ->get(['id', 'due_date'])
            ->each(function ($row) {
                $value = (string) $row->due_date;
                if (strlen($value) === 10) {
                    DB::table('rekap_due_date_settings')
                        ->where('id', $row->id)
                        ->update(['due_date' => $value.' 00:00:00']);
                }
            });
    }

    public function down(): void
    {
        Schema::table('rekap_due_date_settings', function (Blueprint $table) {
            $table->date('due_date')->change();
        });
    }
};
