<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SystemSetting
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = DB::table('system_settings')->where('key', $key)->value('value');
        return $value !== null ? $value : $default;
    }

    public static function int(string $key, int $default): int
    {
        return (int) self::get($key, $default);
    }

    public static function bool(string $key, bool $default): bool
    {
        $value = self::get($key, $default ? '1' : '0');
        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    public static function json(string $key, array $default = []): array
    {
        $raw = self::get($key);
        if (! is_string($raw) || $raw === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $default;
    }

    public static function set(string $key, mixed $value, ?int $updatedBy = null): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'updated_by' => $updatedBy,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public static function gradeRanges(): array
    {
        return self::json('grade_ranges', [
            ['min' => 85, 'grade' => 'A'],
            ['min' => 80, 'grade' => 'A-'],
            ['min' => 75, 'grade' => 'B+'],
            ['min' => 70, 'grade' => 'B'],
            ['min' => 65, 'grade' => 'B-'],
            ['min' => 60, 'grade' => 'C+'],
            ['min' => 55, 'grade' => 'C'],
            ['min' => 45, 'grade' => 'D'],
            ['min' => 0, 'grade' => 'E'],
        ]);
    }
}
