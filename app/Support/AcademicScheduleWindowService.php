<?php

namespace App\Support;

class AcademicScheduleWindowService
{
    public static function isKrsWindowOpen(?object $tahunAktif): bool
    {
        if (! $tahunAktif || ! $tahunAktif->krs_dibuka) {
            return false;
        }

        $now = now();
        $globalOpen = AcademicSetting::krsOpenAt();
        $globalClose = AcademicSetting::krsCloseAt();
        if ($globalOpen && $now->lt($globalOpen)) {
            return false;
        }
        if ($globalClose && $now->gt($globalClose)) {
            return false;
        }
        if ($tahunAktif->krs_mulai && $now->lt($tahunAktif->krs_mulai)) {
            return false;
        }
        if ($tahunAktif->krs_selesai && $now->gt($tahunAktif->krs_selesai)) {
            return false;
        }

        return true;
    }
}

