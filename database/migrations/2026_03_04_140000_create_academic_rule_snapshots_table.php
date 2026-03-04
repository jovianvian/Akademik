<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_rule_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->json('rules_json');
            $table->dateTime('snapshotted_at');
            $table->dateTime('locked_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('tahun_akademik_id', 'ars_tahun_unique');
            $table->index(['locked_at', 'snapshotted_at'], 'ars_lock_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_rule_snapshots');
    }
};

