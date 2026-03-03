<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('max_sks_default')->default(24);
            $table->unsignedTinyInteger('max_sks_ips_3')->default(24);
            $table->decimal('grade_a_min', 5, 2)->default(85);
            $table->decimal('grade_a_minus_min', 5, 2)->default(80);
            $table->decimal('grade_b_plus_min', 5, 2)->default(75);
            $table->decimal('grade_b_min', 5, 2)->default(70);
            $table->decimal('grade_b_minus_min', 5, 2)->default(65);
            $table->decimal('grade_c_plus_min', 5, 2)->default(60);
            $table->decimal('grade_c_min', 5, 2)->default(55);
            $table->decimal('grade_d_min', 5, 2)->default(45);
            $table->dateTime('krs_open_at')->nullable();
            $table->dateTime('krs_close_at')->nullable();
            $table->dateTime('nilai_input_close_at')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->boolean('evaluasi_enabled')->default(true);
            $table->boolean('auto_nonaktif_if_ukt_unpaid')->default(true);
            $table->decimal('bobot_tugas', 5, 2)->default(30);
            $table->decimal('bobot_uts', 5, 2)->default(25);
            $table->decimal('bobot_uas', 5, 2)->default(35);
            $table->decimal('bobot_kehadiran', 5, 2)->default(10);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_settings');
    }
};
