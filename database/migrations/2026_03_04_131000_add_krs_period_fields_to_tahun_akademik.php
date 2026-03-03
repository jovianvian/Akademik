<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->boolean('krs_dibuka')->default(false)->after('status_aktif');
            $table->dateTime('krs_mulai')->nullable()->after('krs_dibuka');
            $table->dateTime('krs_selesai')->nullable()->after('krs_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->dropColumn(['krs_dibuka', 'krs_mulai', 'krs_selesai']);
        });
    }
};
