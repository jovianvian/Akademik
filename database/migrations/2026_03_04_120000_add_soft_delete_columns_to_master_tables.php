<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['fakultas', 'program_studi', 'mata_kuliah', 'jadwal', 'jabatan_dosen'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->index();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = ['fakultas', 'program_studi', 'mata_kuliah', 'jadwal', 'jabatan_dosen'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('deleted_by');
                $table->dropColumn('deleted_at');
            });
        }
    }
};
