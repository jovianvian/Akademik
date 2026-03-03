<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->boolean('nilai_terkunci')->default(false)->after('status_krs');
        });
    }

    public function down(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->dropColumn('nilai_terkunci');
        });
    }
};
