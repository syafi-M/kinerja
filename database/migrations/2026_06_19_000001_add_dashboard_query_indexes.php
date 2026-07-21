<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MyISAM + utf8mb3: full varchar(255) composite keys exceed 1000-byte limit.
     * Use short prefixes — type values are short enums, tanggal is date-like.
     */
    public function up(): void
    {
        $this->addIndexIfMissing(
            'absensis',
            'absensis_dashboard_pulang_created_idx',
            'ADD INDEX `absensis_dashboard_pulang_created_idx` (`user_id`, `absensi_type_pulang`(20), `created_at`)'
        );
        $this->addIndexIfMissing(
            'absensis',
            'absensis_dashboard_pulang_tanggal_idx',
            'ADD INDEX `absensis_dashboard_pulang_tanggal_idx` (`user_id`, `absensi_type_pulang`(20), `tanggal_absen`(32))'
        );
        $this->addIndexIfMissing(
            'absensis',
            'absensis_created_at_idx',
            'ADD INDEX `absensis_created_at_idx` (`created_at`)'
        );
    }

    public function down(): void
    {
        $this->dropIndexIfExists('absensis', 'absensis_dashboard_pulang_created_idx');
        $this->dropIndexIfExists('absensis', 'absensis_dashboard_pulang_tanggal_idx');
        $this->dropIndexIfExists('absensis', 'absensis_created_at_idx');
    }

    private function addIndexIfMissing(string $table, string $name, string $addSql): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` {$addSql}");
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$name}`");
    }

    private function indexExists(string $table, string $name): bool
    {
        $db = Schema::getConnection()->getDatabaseName();
        $row = DB::selectOne(
            'SELECT 1 AS ok FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, $table, $name]
        );

        return $row !== null;
    }
};
