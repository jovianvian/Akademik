<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class ScheduleService
{
    public function hasConflict(array $payload, ?int $excludeId = null): bool
    {
        return $this->hasConflictWithMode($payload, $excludeId, false);
    }

    public function assertNoConflictForWrite(array $payload, ?int $excludeId = null): void
    {
        // Lock semua jadwal dalam scope hari + tahun untuk mencegah race condition create/update paralel.
        DB::table('jadwal')
            ->whereNull('deleted_at')
            ->where('tahun_akademik_id', $payload['tahun_akademik_id'])
            ->where('hari', $payload['hari'])
            ->lockForUpdate()
            ->select('id')
            ->get();

        if ($this->hasConflictWithMode($payload, $excludeId, true)) {
            throw new RuntimeException('Bentrok jadwal terdeteksi (ruangan atau dosen).');
        }
    }

    private function hasConflictWithMode(array $payload, ?int $excludeId, bool $forUpdate): bool
    {
        $ruanganConflict = DB::table('jadwal')
            ->whereNull('deleted_at')
            ->where('tahun_akademik_id', $payload['tahun_akademik_id'])
            ->where('hari', $payload['hari'])
            ->where('ruangan', $payload['ruangan'])
            ->where(function ($q) use ($payload) {
                $q->where('jam_mulai', '<', $payload['jam_selesai'])
                    ->where('jam_selesai', '>', $payload['jam_mulai']);
            });

        if ($excludeId) {
            $ruanganConflict->where('id', '!=', $excludeId);
        }
        if ($forUpdate) {
            $ruanganConflict->lockForUpdate();
        }
        if ($ruanganConflict->exists()) {
            return true;
        }

        $dosenConflict = DB::table('jadwal')
            ->whereNull('deleted_at')
            ->where('tahun_akademik_id', $payload['tahun_akademik_id'])
            ->where('hari', $payload['hari'])
            ->where('dosen_id', $payload['dosen_id'])
            ->where(function ($q) use ($payload) {
                $q->where('jam_mulai', '<', $payload['jam_selesai'])
                    ->where('jam_selesai', '>', $payload['jam_mulai']);
            });

        if ($excludeId) {
            $dosenConflict->where('id', '!=', $excludeId);
        }
        if ($forUpdate) {
            $dosenConflict->lockForUpdate();
        }

        return $dosenConflict->exists();
    }
}
