<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class AcademicFinanceService
{
    public static function isUktPaid(int $mahasiswaId, int $tahunAkademikId): bool
    {
        return DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('status', 'paid')
            ->exists();
    }

    public static function currentUktStatus(int $mahasiswaId, int $tahunAkademikId): ?string
    {
        return DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->value('status');
    }
}

