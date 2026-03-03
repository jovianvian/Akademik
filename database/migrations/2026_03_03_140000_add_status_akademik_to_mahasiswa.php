<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->enum('status_akademik', ['aktif', 'nonaktif', 'do', 'alumni'])->default('aktif')->after('status_mahasiswa');
            $table->string('catatan_status', 255)->nullable()->after('status_akademik');
        });

        DB::statement("
            UPDATE mahasiswa
            SET status_akademik = CASE status_mahasiswa
                WHEN 'aktif' THEN 'aktif'
                WHEN 'cuti' THEN 'nonaktif'
                WHEN 'dropout' THEN 'do'
                WHEN 'lulus' THEN 'alumni'
                ELSE 'aktif'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['status_akademik', 'catatan_status']);
        });
    }
};

