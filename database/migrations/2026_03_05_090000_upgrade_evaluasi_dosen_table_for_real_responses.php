<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi_dosen', function (Blueprint $table) {
            $table->foreignId('mahasiswa_id')->nullable()->after('id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->after('mahasiswa_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->nullable()->after('dosen_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->nullable()->after('mata_kuliah_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_1')->nullable()->after('status_selesai');
            $table->unsignedTinyInteger('nilai_2')->nullable()->after('nilai_1');
            $table->unsignedTinyInteger('nilai_3')->nullable()->after('nilai_2');
            $table->text('komentar')->nullable()->after('nilai_3');
            $table->timestamp('submitted_at')->nullable()->after('komentar');
            $table->index(['mahasiswa_id', 'tahun_akademik_id'], 'evaluasi_mhs_ta_idx');
            $table->index(['dosen_id', 'mata_kuliah_id', 'tahun_akademik_id'], 'evaluasi_dosen_mk_ta_idx');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_dosen', function (Blueprint $table) {
            $table->dropIndex('evaluasi_mhs_ta_idx');
            $table->dropIndex('evaluasi_dosen_mk_ta_idx');
            $table->dropConstrainedForeignId('tahun_akademik_id');
            $table->dropConstrainedForeignId('mata_kuliah_id');
            $table->dropConstrainedForeignId('dosen_id');
            $table->dropConstrainedForeignId('mahasiswa_id');
            $table->dropColumn(['nilai_1', 'nilai_2', 'nilai_3', 'komentar', 'submitted_at']);
        });
    }
};

