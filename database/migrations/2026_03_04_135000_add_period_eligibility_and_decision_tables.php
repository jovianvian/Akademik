<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->enum('enrollment_status_current', ['aktif', 'cuti', 'lulus', 'do'])->default('aktif')->after('status_akademik');
            $table->boolean('global_hold')->default(false)->after('enrollment_status_current');
            $table->string('global_hold_reason', 255)->nullable()->after('global_hold');
        });

        DB::statement("
            UPDATE mahasiswa
            SET enrollment_status_current = CASE status_mahasiswa
                WHEN 'aktif' THEN 'aktif'
                WHEN 'cuti' THEN 'cuti'
                WHEN 'lulus' THEN 'lulus'
                ELSE 'do'
            END
        ");

        Schema::create('mahasiswa_period_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->enum('eligibility_status', ['eligible', 'suspended', 'suspended_pending_decision'])->default('eligible');
            $table->string('reason', 255)->nullable();
            $table->dateTime('effective_from');
            $table->dateTime('effective_until')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['mahasiswa_id', 'tahun_akademik_id', 'effective_from'], 'mps_lookup_idx');
            $table->index(['tahun_akademik_id', 'eligibility_status'], 'mps_period_status_idx');
        });

        Schema::create('mahasiswa_enrollment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'cuti', 'lulus', 'do']);
            $table->string('reason', 255)->nullable();
            $table->dateTime('effective_from');
            $table->dateTime('effective_until')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['mahasiswa_id', 'effective_from'], 'meh_lookup_idx');
        });

        Schema::create('academic_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->string('context', 120);
            $table->enum('decision', ['allow_suspend', 'allow_restore', 'override']);
            $table->text('notes')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at');
            $table->timestamps();
            $table->index(['tahun_akademik_id', 'context', 'decided_at'], 'ad_context_idx');
        });

        $mahasiswa = DB::table('mahasiswa')->select('id', 'status_akademik', 'enrollment_status_current', 'created_at')->get();
        $tahunUsed = DB::table('tahun_akademik as ta')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('krs as k')
                    ->whereColumn('k.tahun_akademik_id', 'ta.id');
            })
            ->orWhereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('tagihan_ukt as t')
                    ->whereColumn('t.tahun_akademik_id', 'ta.id');
            })
            ->orWhere('status_aktif', true)
            ->pluck('id')
            ->all();

        $now = now();
        $historyRows = [];
        $periodRows = [];

        foreach ($mahasiswa as $mhs) {
            $historyRows[] = [
                'mahasiswa_id' => $mhs->id,
                'status' => $mhs->enrollment_status_current,
                'reason' => 'Migrasi awal enrollment status.',
                'effective_from' => $mhs->created_at ?? $now,
                'effective_until' => null,
                'set_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $eligibility = match ($mhs->status_akademik) {
                'aktif' => 'eligible',
                'suspended_pending_decision' => 'suspended_pending_decision',
                default => 'suspended',
            };

            foreach ($tahunUsed as $tahunId) {
                $periodRows[] = [
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunId,
                    'eligibility_status' => $eligibility,
                    'reason' => 'Migrasi awal eligibility status dari status_akademik.',
                    'effective_from' => $now,
                    'effective_until' => null,
                    'set_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($historyRows)) {
            DB::table('mahasiswa_enrollment_history')->insert($historyRows);
        }
        if (! empty($periodRows)) {
            DB::table('mahasiswa_period_status')->insert($periodRows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_decisions');
        Schema::dropIfExists('mahasiswa_enrollment_history');
        Schema::dropIfExists('mahasiswa_period_status');

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['enrollment_status_current', 'global_hold', 'global_hold_reason']);
        });
    }
};

