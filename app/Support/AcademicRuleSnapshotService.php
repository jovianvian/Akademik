<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class AcademicRuleSnapshotService
{
    public static function getOrCreate(int $tahunAkademikId, ?int $actorId = null): object
    {
        $row = DB::table('academic_rule_snapshots')
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();

        if ($row) {
            return $row;
        }

        $rules = self::rulesFromCurrentSettings();
        $id = DB::table('academic_rule_snapshots')->insertGetId([
            'tahun_akademik_id' => $tahunAkademikId,
            'rules_json' => json_encode($rules),
            'snapshotted_at' => now(),
            'locked_at' => null,
            'created_by' => $actorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('academic_rule_snapshots')->where('id', $id)->first();
    }

    public static function lock(int $tahunAkademikId): void
    {
        DB::table('academic_rule_snapshots')
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public static function rulesForYear(int $tahunAkademikId): array
    {
        $row = self::getOrCreate($tahunAkademikId);
        return self::decodeRules($row);
    }

    public static function toHuruf(float $nilaiAngka, int $tahunAkademikId): string
    {
        $rules = self::rulesForYear($tahunAkademikId);
        $ranges = $rules['grade_ranges'] ?? [];
        usort($ranges, fn ($a, $b) => (float) ($b['min'] ?? 0) <=> (float) ($a['min'] ?? 0));

        foreach ($ranges as $range) {
            if ($nilaiAngka >= (float) ($range['min'] ?? 0)) {
                return strtoupper((string) ($range['grade'] ?? 'E'));
            }
        }

        return 'E';
    }

    public static function gradePoint(string $nilaiHuruf, int $tahunAkademikId): float
    {
        $rules = self::rulesForYear($tahunAkademikId);
        $points = $rules['grade_points'] ?? [];

        return (float) ($points[strtoupper($nilaiHuruf)] ?? 0.0);
    }

    public static function maxSksByIps(float $ips, int $tahunAkademikId): int
    {
        $rules = self::rulesForYear($tahunAkademikId);
        $maxDefault = (int) ($rules['max_sks_default'] ?? 24);
        $maxIps3 = (int) ($rules['max_sks_ips_3'] ?? $maxDefault);

        return $ips >= 3.0 ? $maxIps3 : $maxDefault;
    }

    private static function rulesFromCurrentSettings(): array
    {
        return [
            'max_sks_default' => AcademicSetting::maxSksDefault(),
            'max_sks_ips_3' => AcademicSetting::maxSksIps3(),
            'grade_ranges' => AcademicSetting::gradeRanges(),
            'grade_points' => [
                'A' => 4.0,
                'A-' => 3.7,
                'B+' => 3.3,
                'B' => 3.0,
                'B-' => 2.7,
                'C+' => 2.3,
                'C' => 2.0,
                'D' => 1.0,
                'E' => 0.0,
            ],
            'nilai_bobot' => AcademicSetting::bobotNilai(),
            'krs_window' => [
                'open_at' => AcademicSetting::krsOpenAt(),
                'close_at' => AcademicSetting::krsCloseAt(),
            ],
            'nilai_input_close_at' => AcademicSetting::nilaiInputCloseAt(),
        ];
    }

    private static function decodeRules(object $row): array
    {
        $json = is_string($row->rules_json ?? null) ? $row->rules_json : '{}';
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}

