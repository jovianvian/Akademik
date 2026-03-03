<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class AcademicSetting
{
    private static ?object $cached = null;

    public static function row(): object
    {
        if (self::$cached) {
            return self::$cached;
        }

        $row = DB::table('academic_settings')->orderBy('id')->first();
        if (! $row) {
            $id = DB::table('academic_settings')->insertGetId([
                'max_sks_default' => 24,
                'max_sks_ips_3' => 24,
                'grade_a_min' => 85,
                'grade_a_minus_min' => 80,
                'grade_b_plus_min' => 75,
                'grade_b_min' => 70,
                'grade_b_minus_min' => 65,
                'grade_c_plus_min' => 60,
                'grade_c_min' => 55,
                'grade_d_min' => 45,
                'maintenance_mode' => false,
                'evaluasi_enabled' => true,
                'auto_nonaktif_if_ukt_unpaid' => true,
                'bobot_tugas' => 30,
                'bobot_uts' => 25,
                'bobot_uas' => 35,
                'bobot_kehadiran' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $row = DB::table('academic_settings')->where('id', $id)->first();
        }

        self::$cached = $row;
        return $row;
    }

    public static function refresh(): void
    {
        self::$cached = null;
    }

    public static function maxSksDefault(): int
    {
        return (int) self::row()->max_sks_default;
    }

    public static function maxSksIps3(): int
    {
        return (int) self::row()->max_sks_ips_3;
    }

    public static function maintenanceMode(): bool
    {
        return (bool) self::row()->maintenance_mode;
    }

    public static function evaluasiEnabled(): bool
    {
        return (bool) self::row()->evaluasi_enabled;
    }

    public static function autoNonaktifIfUktUnpaid(): bool
    {
        return (bool) self::row()->auto_nonaktif_if_ukt_unpaid;
    }

    public static function nilaiInputCloseAt(): ?string
    {
        return self::row()->nilai_input_close_at;
    }

    public static function krsOpenAt(): ?string
    {
        return self::row()->krs_open_at;
    }

    public static function krsCloseAt(): ?string
    {
        return self::row()->krs_close_at;
    }

    public static function gradeRanges(): array
    {
        $row = self::row();
        return [
            ['min' => (float) $row->grade_a_min, 'grade' => 'A'],
            ['min' => (float) $row->grade_a_minus_min, 'grade' => 'A-'],
            ['min' => (float) $row->grade_b_plus_min, 'grade' => 'B+'],
            ['min' => (float) $row->grade_b_min, 'grade' => 'B'],
            ['min' => (float) $row->grade_b_minus_min, 'grade' => 'B-'],
            ['min' => (float) $row->grade_c_plus_min, 'grade' => 'C+'],
            ['min' => (float) $row->grade_c_min, 'grade' => 'C'],
            ['min' => (float) $row->grade_d_min, 'grade' => 'D'],
            ['min' => 0.0, 'grade' => 'E'],
        ];
    }

    public static function bobotNilai(): array
    {
        $row = self::row();
        return [
            'tugas' => (float) $row->bobot_tugas,
            'uts' => (float) $row->bobot_uts,
            'uas' => (float) $row->bobot_uas,
            'kehadiran' => (float) $row->bobot_kehadiran,
        ];
    }
}
