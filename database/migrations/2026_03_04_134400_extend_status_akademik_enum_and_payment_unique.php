<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE mahasiswa
            MODIFY COLUMN status_akademik ENUM('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision')
            NOT NULL DEFAULT 'aktif'
        ");

        DB::statement("
            ALTER TABLE mahasiswa_status_logs
            MODIFY COLUMN status_lama ENUM('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision') NULL
        ");

        DB::statement("
            ALTER TABLE mahasiswa_status_logs
            MODIFY COLUMN status_baru ENUM('aktif','nonaktif','do','alumni','suspended','suspended_pending_decision') NOT NULL
        ");

        Schema::table('pembayaran', function ($table) {
            $table->unique(['tagihan_id', 'tanggal_bayar', 'jumlah_bayar', 'metode_bayar'], 'pembayaran_dup_guard_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function ($table) {
            $table->dropUnique('pembayaran_dup_guard_unique');
        });

        DB::statement("
            ALTER TABLE mahasiswa
            MODIFY COLUMN status_akademik ENUM('aktif','nonaktif','do','alumni')
            NOT NULL DEFAULT 'aktif'
        ");

        DB::statement("
            ALTER TABLE mahasiswa_status_logs
            MODIFY COLUMN status_lama ENUM('aktif','nonaktif','do','alumni') NULL
        ");

        DB::statement("
            ALTER TABLE mahasiswa_status_logs
            MODIFY COLUMN status_baru ENUM('aktif','nonaktif','do','alumni') NOT NULL
        ");
    }
};
