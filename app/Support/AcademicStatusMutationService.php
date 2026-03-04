<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicStatusMutationService
{
    public static function mutatePeriodEligibility(
        int $mahasiswaId,
        int $tahunAkademikId,
        string $newStatus,
        string $source,
        ?string $note = null,
        ?int $actorId = null,
        ?Request $request = null
    ): bool {
        $current = AcademicEligibilityService::ensurePeriodStatus($mahasiswaId, $tahunAkademikId, $actorId);
        if ((string) $current->eligibility_status === $newStatus) {
            return false;
        }

        DB::transaction(function () use ($mahasiswaId, $tahunAkademikId, $newStatus, $source, $note, $actorId, $request, $current) {
            AcademicEligibilityService::setPeriodStatus(
                $mahasiswaId,
                $tahunAkademikId,
                $newStatus,
                $note,
                $actorId
            );

            DB::table('mahasiswa')->where('id', $mahasiswaId)->update([
                'status_akademik' => self::toLegacyAcademicStatus($newStatus),
                'catatan_status' => $note,
                'updated_at' => now(),
            ]);

            DB::table('mahasiswa_status_logs')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'changed_by' => $actorId,
                'status_lama' => self::toLegacyAcademicStatus((string) $current->eligibility_status),
                'status_baru' => self::toLegacyAcademicStatus($newStatus),
                'sumber' => $source,
                'catatan' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            AuditLogger::log(
                aksi: 'Mutasi status akademik via service',
                modul: 'akademik',
                entityType: 'mahasiswa',
                entityId: $mahasiswaId,
                konteks: [
                    'tahun_akademik_id' => $tahunAkademikId,
                    'from' => $current->eligibility_status,
                    'to' => $newStatus,
                    'source' => $source,
                    'note' => $note,
                ],
                request: $request
            );
        });

        return true;
    }

    public static function toLegacyAcademicStatus(string $eligibility): string
    {
        return match ($eligibility) {
            'eligible' => 'aktif',
            'suspended_pending_decision' => 'suspended_pending_decision',
            default => 'suspended',
        };
    }
}

