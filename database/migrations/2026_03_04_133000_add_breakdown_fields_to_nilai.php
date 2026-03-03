<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->decimal('nilai_tugas', 5, 2)->nullable()->after('krs_detail_id');
            $table->decimal('nilai_uts', 5, 2)->nullable()->after('nilai_tugas');
            $table->decimal('nilai_uas', 5, 2)->nullable()->after('nilai_uts');
            $table->decimal('nilai_kehadiran', 5, 2)->nullable()->after('nilai_uas');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn(['nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_kehadiran']);
        });
    }
};
