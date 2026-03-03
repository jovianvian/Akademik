<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogger
{
    public static function log(
        string $aksi,
        string $modul,
        ?string $entityType = null,
        int|string|null $entityId = null,
        array $konteks = [],
        ?array $beforeData = null,
        ?array $afterData = null,
        ?Request $request = null
    ): void {
        $request ??= request();
        $user = auth()->user();

        $extractedBefore = $beforeData ?? (is_array($konteks['before'] ?? null) ? $konteks['before'] : null);
        $extractedAfter = $afterData ?? (is_array($konteks['after'] ?? null) ? $konteks['after'] : null);

        DB::table('audit_logs')->insert([
            'user_id' => $user?->id,
            'aksi' => $aksi,
            'modul' => $modul,
            'entity_type' => $entityType,
            'entity_id' => $entityId !== null ? (int) $entityId : null,
            'konteks' => ! empty($konteks) ? json_encode($konteks) : null,
            'before_data' => $extractedBefore ? json_encode($extractedBefore) : null,
            'after_data' => $extractedAfter ? json_encode($extractedAfter) : null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
