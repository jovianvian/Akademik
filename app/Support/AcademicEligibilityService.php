<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class AcademicEligibilityService
{
    /**
     * Minimal transition matrix for period eligibility.
     */
    private const TRANSITIONS = [
        'eligible' => ['suspended', 'suspended_pending_decision'],
        'suspended' => ['eligible', 'suspended_pending_decision'],
        'suspended_pending_decision' => ['eligible', 'suspended'],
    ];

    public static function resolve(int $mahasiswaId, int $tahunAkademikId): array
    {
        $mahasiswa = DB::table('mahasiswa')
            ->where('id', $mahasiswaId)
            ->select('enrollment_status_current', 'global_hold', 'global_hold_reason')
            ->first();

        if (! $mahasiswa) {
            return [
                'enrollment_status' => null,
                'global_hold' => false,
                'period_eligibility' => null,
                'effective_eligibility' => false,
                'reason' => 'Mahasiswa tidak ditemukan.',
            ];
        }

        $period = self::currentPeriodStatus($mahasiswaId, $tahunAkademikId);
        $periodStatus = $period?->eligibility_status;
        if (! $periodStatus) {
            $periodStatus = ($mahasiswa->enrollment_status_current === 'aktif' && ! (bool) $mahasiswa->global_hold)
                ? 'eligible'
                : 'suspended';
        }

        $isEligible = $mahasiswa->enrollment_status_current === 'aktif'
            && ! (bool) $mahasiswa->global_hold
            && ($periodStatus === 'eligible');

        return [
            'enrollment_status' => $mahasiswa->enrollment_status_current,
            'global_hold' => (bool) $mahasiswa->global_hold,
            'global_hold_reason' => $mahasiswa->global_hold_reason,
            'period_eligibility' => $periodStatus,
            'effective_eligibility' => $isEligible,
            'reason' => self::resolveReason($mahasiswa->enrollment_status_current, (bool) $mahasiswa->global_hold, $periodStatus),
        ];
    }

    public static function isEligibleForAcademicWrite(int $mahasiswaId, int $tahunAkademikId): bool
    {
        return (bool) (self::resolve($mahasiswaId, $tahunAkademikId)['effective_eligibility'] ?? false);
    }

    public static function resolveWriteContext(int $mahasiswaId, int $tahunAkademikId): array
    {
        $base = self::resolve($mahasiswaId, $tahunAkademikId);
        $uktPaid = AcademicFinanceService::isUktPaid($mahasiswaId, $tahunAkademikId);
        $tahunAktif = DB::table('tahun_akademik')->where('id', $tahunAkademikId)->first();
        $krsWindowOpen = AcademicScheduleWindowService::isKrsWindowOpen($tahunAktif);

        return [
            ...$base,
            'ukt_paid' => $uktPaid,
            'krs_window_open' => $krsWindowOpen,
            'allowed_for_krs_write' => (bool) ($base['effective_eligibility'] ?? false) && $uktPaid && $krsWindowOpen,
        ];
    }

    public static function currentPeriodStatus(int $mahasiswaId, int $tahunAkademikId): ?object
    {
        return DB::table('mahasiswa_period_status')
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where(function ($q) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>', now());
            })
            ->where('effective_from', '<=', now())
            ->orderByDesc('effective_from')
            ->first();
    }

    public static function ensurePeriodStatus(int $mahasiswaId, int $tahunAkademikId, ?int $setBy = null): object
    {
        $current = self::currentPeriodStatus($mahasiswaId, $tahunAkademikId);
        if ($current) {
            return $current;
        }

        $mahasiswa = DB::table('mahasiswa')->where('id', $mahasiswaId)->first();
        $status = ($mahasiswa && $mahasiswa->enrollment_status_current === 'aktif' && ! (bool) $mahasiswa->global_hold)
            ? 'eligible'
            : 'suspended';

        $id = DB::table('mahasiswa_period_status')->insertGetId([
            'mahasiswa_id' => $mahasiswaId,
            'tahun_akademik_id' => $tahunAkademikId,
            'eligibility_status' => $status,
            'reason' => 'Inisialisasi default period eligibility.',
            'effective_from' => now(),
            'effective_until' => null,
            'set_by' => $setBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('mahasiswa_period_status')->where('id', $id)->first();
    }

    public static function setPeriodStatus(
        int $mahasiswaId,
        int $tahunAkademikId,
        string $newStatus,
        ?string $reason = null,
        ?int $setBy = null
    ): int {
        if (! in_array($newStatus, ['eligible', 'suspended', 'suspended_pending_decision'], true)) {
            throw new \InvalidArgumentException('Eligibility status tidak valid.');
        }

        $current = self::ensurePeriodStatus($mahasiswaId, $tahunAkademikId, $setBy);
        $from = (string) $current->eligibility_status;
        if ($from === $newStatus) {
            return (int) $current->id;
        }

        $allowed = self::TRANSITIONS[$from] ?? [];
        if (! in_array($newStatus, $allowed, true)) {
            throw new \RuntimeException("Transisi eligibility {$from} -> {$newStatus} tidak diizinkan.");
        }
        self::assertTransitionPolicy($tahunAkademikId, $from, $newStatus);

        return DB::transaction(function () use ($current, $mahasiswaId, $tahunAkademikId, $newStatus, $reason, $setBy) {
            DB::table('mahasiswa_period_status')
                ->where('id', $current->id)
                ->update([
                    'effective_until' => now(),
                    'updated_at' => now(),
                ]);

            return (int) DB::table('mahasiswa_period_status')->insertGetId([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'eligibility_status' => $newStatus,
                'reason' => $reason,
                'effective_from' => now(),
                'effective_until' => null,
                'set_by' => $setBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private static function assertTransitionPolicy(int $tahunAkademikId, string $from, string $to): void
    {
        // Guardrail semester close: restore ke eligible harus diputuskan pada semester aktif.
        if ($to === 'eligible' && in_array($from, ['suspended', 'suspended_pending_decision'], true)) {
            $isActiveSemester = (bool) DB::table('tahun_akademik')
                ->where('id', $tahunAkademikId)
                ->value('status_aktif');

            if (! $isActiveSemester) {
                throw new \RuntimeException('Restore eligibility hanya diizinkan saat semester aktif.');
            }
        }
    }

    private static function resolveReason(?string $enrollment, bool $globalHold, ?string $periodStatus): string
    {
        if ($enrollment !== 'aktif') {
            return 'Enrollment mahasiswa tidak aktif.';
        }
        if ($globalHold) {
            return 'Mahasiswa dalam global hold.';
        }
        if ($periodStatus !== 'eligible') {
            return 'Eligibility periode belum eligible.';
        }

        return 'Eligible.';
    }
}
