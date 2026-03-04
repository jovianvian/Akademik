<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("
                UPDATE tagihan_ukt
                SET status = CASE status
                    WHEN 'menunggu' THEN 'open'
                    WHEN 'lunas' THEN 'paid'
                    WHEN 'ditolak' THEN 'void'
                    ELSE status
                END
            ");

            return;
        }

        DB::statement("
            ALTER TABLE tagihan_ukt
            MODIFY COLUMN status ENUM('menunggu','lunas','ditolak','open','partial','paid','disputed','void')
            NOT NULL DEFAULT 'menunggu'
        ");

        DB::statement("
            UPDATE tagihan_ukt
            SET status = CASE status
                WHEN 'menunggu' THEN 'open'
                WHEN 'lunas' THEN 'paid'
                WHEN 'ditolak' THEN 'void'
                ELSE status
            END
        ");

        DB::statement("
            ALTER TABLE tagihan_ukt
            MODIFY COLUMN status ENUM('open','partial','paid','disputed','void')
            NOT NULL DEFAULT 'open'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("
                UPDATE tagihan_ukt
                SET status = CASE status
                    WHEN 'open' THEN 'menunggu'
                    WHEN 'partial' THEN 'menunggu'
                    WHEN 'paid' THEN 'lunas'
                    WHEN 'disputed' THEN 'ditolak'
                    WHEN 'void' THEN 'ditolak'
                    ELSE status
                END
            ");

            return;
        }

        DB::statement("
            ALTER TABLE tagihan_ukt
            MODIFY COLUMN status ENUM('menunggu','lunas','ditolak','open','partial','paid','disputed','void')
            NOT NULL DEFAULT 'open'
        ");

        DB::statement("
            UPDATE tagihan_ukt
            SET status = CASE status
                WHEN 'open' THEN 'menunggu'
                WHEN 'partial' THEN 'menunggu'
                WHEN 'paid' THEN 'lunas'
                WHEN 'disputed' THEN 'ditolak'
                WHEN 'void' THEN 'ditolak'
                ELSE status
            END
        ");

        DB::statement("
            ALTER TABLE tagihan_ukt
            MODIFY COLUMN status ENUM('menunggu','lunas','ditolak')
            NOT NULL DEFAULT 'menunggu'
        ");
    }
};
