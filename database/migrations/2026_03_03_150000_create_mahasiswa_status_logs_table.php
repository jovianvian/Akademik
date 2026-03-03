<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_lama', ['aktif', 'nonaktif', 'do', 'alumni'])->nullable();
            $table->enum('status_baru', ['aktif', 'nonaktif', 'do', 'alumni']);
            $table->string('sumber', 50)->default('manual');
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
            $table->index(['mahasiswa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_status_logs');
    }
};

