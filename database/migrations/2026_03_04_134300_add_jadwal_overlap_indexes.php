<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->index(['ruangan', 'hari', 'jam_mulai', 'jam_selesai'], 'jadwal_ruangan_hari_jam_idx');
            $table->index(['dosen_id', 'hari', 'jam_mulai', 'jam_selesai'], 'jadwal_dosen_hari_jam_idx');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropIndex('jadwal_ruangan_hari_jam_idx');
            $table->dropIndex('jadwal_dosen_hari_jam_idx');
        });
    }
};
