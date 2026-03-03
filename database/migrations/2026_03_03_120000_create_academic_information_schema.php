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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('password');
        });

        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fakultas', 100)->unique();
            $table->timestamps();
        });

        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_prodi', 100);
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['nama_prodi', 'fakultas_id']);
        });

        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->string('nidn', 30)->unique();
            $table->string('nama', 100);
            $table->foreignId('prodi_id')->constrained('program_studi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 30)->unique();
            $table->string('nama', 100);
            $table->foreignId('prodi_id')->constrained('program_studi')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('angkatan');
            $table->enum('status_mahasiswa', ['aktif', 'cuti', 'lulus', 'dropout'])->default('aktif');
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 9);
            $table->enum('semester', ['ganjil', 'genap', 'pendek']);
            $table->boolean('status_aktif')->default(false);
            $table->timestamps();
            $table->unique(['tahun', 'semester']);
        });

        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk', 20);
            $table->string('nama_mk', 120);
            $table->unsignedTinyInteger('sks');
            $table->unsignedTinyInteger('semester');
            $table->foreignId('prodi_id')->constrained('program_studi')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['kode_mk', 'prodi_id']);
        });

        Schema::create('prasyarat_mk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_prasyarat_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mata_kuliah_id', 'mata_kuliah_prasyarat_id'], 'mk_prasyarat_unique');
        });

        Schema::create('jabatan_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->string('jabatan', 80);
            $table->date('periode_mulai');
            $table->date('periode_selesai')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('dosen_pa_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->date('periode_mulai');
            $table->date('periode_selesai')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('tagihan_ukt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->enum('status', ['menunggu', 'lunas', 'ditolak'])->default('menunggu');
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'tahun_akademik_id'], 'ukt_mahasiswa_tahun_unique');
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan_ukt')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->timestamps();
        });

        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 40);
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->unsignedTinyInteger('total_sks')->default(0);
            $table->enum('status_krs', ['draft', 'final'])->default('draft');
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'tahun_akademik_id'], 'krs_mahasiswa_tahun_unique');
        });

        Schema::create('krs_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['krs_id', 'jadwal_id']);
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->unique()->constrained('krs_detail')->cascadeOnDelete();
            $table->decimal('nilai_angka', 5, 2);
            $table->char('nilai_huruf', 2);
            $table->timestamps();
        });

        Schema::create('evaluasi_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->unique()->constrained('krs_detail')->cascadeOnDelete();
            $table->boolean('status_selesai')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_dosen');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('krs_detail');
        Schema::dropIfExists('krs');
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('tagihan_ukt');
        Schema::dropIfExists('dosen_pa_mahasiswa');
        Schema::dropIfExists('jabatan_dosen');
        Schema::dropIfExists('prasyarat_mk');
        Schema::dropIfExists('mata_kuliah');
        Schema::dropIfExists('tahun_akademik');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('dosen');
        Schema::dropIfExists('program_studi');
        Schema::dropIfExists('fakultas');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('status');
        });

        Schema::dropIfExists('roles');
    }
};

