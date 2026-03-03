<?php

use App\Support\AuditLogger;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('akademik:nonaktifkan-jabatan-kadaluarsa {--dry-run}', function () {
    $today = now()->toDateString();
    $expired = DB::table('jabatan_dosen')
        ->where('status_aktif', true)
        ->whereNotNull('periode_selesai')
        ->whereDate('periode_selesai', '<', $today)
        ->select('id', 'dosen_id', 'jabatan', 'periode_selesai')
        ->get();

    $this->info("Tanggal acuan: {$today}");
    $this->info('Jabatan kadaluarsa terdeteksi: '.$expired->count());

    if ($expired->isEmpty()) {
        return;
    }

    if ($this->option('dry-run')) {
        foreach ($expired as $row) {
            $this->line("#{$row->id} | dosen_id={$row->dosen_id} | {$row->jabatan} | selesai={$row->periode_selesai}");
        }
        $this->comment('Dry run aktif: tidak ada perubahan data.');
        return;
    }

    $ids = $expired->pluck('id')->all();
    DB::table('jabatan_dosen')
        ->whereIn('id', $ids)
        ->update([
            'status_aktif' => false,
            'updated_at' => now(),
        ]);

    AuditLogger::log(
        aksi: 'Auto nonaktif jabatan dosen kadaluarsa',
        modul: 'akademik',
        entityType: 'jabatan_dosen',
        entityId: null,
        konteks: [
            'tanggal_acuan' => $today,
            'jumlah_nonaktif' => count($ids),
            'ids' => $ids,
        ],
        request: null
    );

    $this->info('Sukses menonaktifkan '.count($ids).' jabatan dosen kadaluarsa.');
})->purpose('Nonaktifkan otomatis jabatan dosen yang melewati periode_selesai');

Schedule::command('akademik:nonaktifkan-jabatan-kadaluarsa')
    ->dailyAt('00:10')
    ->withoutOverlapping()
    ->description('Auto nonaktifkan jabatan dosen kadaluarsa');
