<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class FinancialImpactService
{
    public static function applyTagihanStatusImpact(int $tagihanId, string $newStatus, ?int $actorId = null, ?string $notes = null): void
    {
        $tagihan = DB::table('tagihan_ukt')->where('id', $tagihanId)->first();
        if (! $tagihan) {
            return;
        }

        $hasFinalKrs = DB::table('krs')
            ->where('mahasiswa_id', $tagihan->mahasiswa_id)
            ->where('tahun_akademik_id', $tagihan->tahun_akademik_id)
            ->where('status_krs', 'final')
            ->exists();

        if (! $hasFinalKrs) {
            return;
        }

        $mahasiswaId = (int) $tagihan->mahasiswa_id;
        $tahunAkademikId = (int) $tagihan->tahun_akademik_id;

        if (in_array($newStatus, ['disputed', 'void'], true)) {
            AcademicStatusMutationService::mutatePeriodEligibility(
                $mahasiswaId,
                $tahunAkademikId,
                'suspended_pending_decision',
                'financial_guard',
                'Dampak finansial: status UKT '.$newStatus.' setelah KRS final.',
                $actorId
            );

            DB::table('academic_decisions')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'context' => 'ukt_'.$newStatus.'_after_krs_final',
                'decision' => 'allow_suspend',
                'notes' => $notes ?? 'Auto-flag untuk review akademik setelah perubahan status UKT.',
                'decided_by' => $actorId,
                'decided_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($newStatus === 'paid') {
            $current = AcademicEligibilityService::currentPeriodStatus($mahasiswaId, $tahunAkademikId);
            $isActiveSemester = (bool) DB::table('tahun_akademik')->where('id', $tahunAkademikId)->value('status_aktif');

            if (($current?->eligibility_status === 'suspended_pending_decision') && $isActiveSemester) {
                AcademicStatusMutationService::mutatePeriodEligibility(
                    $mahasiswaId,
                    $tahunAkademikId,
                    'eligible',
                    'financial_restore',
                    'Status UKT kembali paid setelah review.',
                    $actorId
                );
            }

            DB::table('academic_decisions')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => $tahunAkademikId,
                'context' => 'payment_recovered_after_exception',
                'decision' => $isActiveSemester ? 'allow_restore' : 'override',
                'notes' => $notes ?? ($isActiveSemester
                    ? 'Pembayaran kembali paid, eligibility dipulihkan pada semester aktif.'
                    : 'Pembayaran kembali paid, namun restore eligibility ditunda karena semester sudah ditutup.'),
                'decided_by' => $actorId,
                'decided_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
