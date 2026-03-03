<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\AcademicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KrsController extends Controller
{
    public function index()
    {
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();
        $mahasiswa = $this->resolveMahasiswa();
        $maxSks = $this->resolveMaxSks($mahasiswa?->id);

        $jadwal = collect();
        $selectedJadwalIds = [];
        $krsStatus = null;
        if ($tahunAktif && $mahasiswa) {
            $jadwal = DB::table('jadwal as j')
                ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
                ->where('j.tahun_akademik_id', $tahunAktif->id)
                ->where('mk.prodi_id', $mahasiswa->prodi_id)
                ->whereNull('j.deleted_at')
                ->whereNull('mk.deleted_at')
                ->select('j.id', 'mk.kode_mk as kode', 'mk.nama_mk as nama', 'mk.sks', 'j.hari', 'j.jam_mulai', 'j.jam_selesai', 'j.ruangan as ruang')
                ->orderBy('j.hari')
                ->orderBy('j.jam_mulai')
                ->get()
                ->map(function ($item) {
                    $item->jam = substr($item->jam_mulai, 0, 5).' - '.substr($item->jam_selesai, 0, 5);
                    return $item;
                });

            $krs = DB::table('krs')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->first();

            if ($krs) {
                $selectedJadwalIds = DB::table('krs_detail')
                    ->where('krs_id', $krs->id)
                    ->pluck('jadwal_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
                $krsStatus = $krs->status_krs;
            }
        }

        $statusUkt = false;
        if ($tahunAktif && $mahasiswa) {
            $statusUkt = DB::table('tagihan_ukt')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->where('status', 'lunas')
                ->exists();
        }

        return view('krs.index', [
            'title' => 'Isi KRS',
            'maxSks' => $maxSks,
            'jadwal' => $jadwal,
            'selectedJadwalIds' => $selectedJadwalIds,
            'krsStatus' => $krsStatus,
            'statusAkademik' => [
                'mahasiswa_aktif' => (bool) ($mahasiswa && $this->isEligibleForAcademicWrite($mahasiswa)),
                'periode_krs' => (bool) $tahunAktif,
                'ukt_lunas' => $statusUkt,
                'krs_window' => $this->isKrsWindowOpen($tahunAktif),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => ['required', 'array', 'min:1'],
            'jadwal_id.*' => ['integer', 'distinct'],
            'total_sks' => ['required', 'integer'],
        ]);

        $mahasiswa = $this->resolveMahasiswa();
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();

        if (! $mahasiswa || ! $tahunAktif) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Data mahasiswa atau tahun akademik aktif belum tersedia.',
            ]);
        }

        if (! $this->isEligibleForAcademicWrite($mahasiswa)) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Mahasiswa tidak memenuhi eligibility akademik untuk write KRS.',
            ]);
        }
        if (! $this->isKrsWindowOpen($tahunAktif)) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Periode KRS saat ini ditutup oleh admin akademik.',
            ]);
        }

        $isUktLunas = DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAktif->id)
            ->where('status', 'lunas')
            ->exists();

        if (! $isUktLunas) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'KRS ditolak. Tagihan UKT belum lunas.',
            ]);
        }

        $selectedSchedules = DB::table('jadwal as j')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->whereIn('j.id', $validated['jadwal_id'])
            ->where('j.tahun_akademik_id', $tahunAktif->id)
            ->where('mk.prodi_id', $mahasiswa->prodi_id)
            ->whereNull('j.deleted_at')
            ->whereNull('mk.deleted_at')
            ->select('j.id', 'j.hari', 'j.jam_mulai', 'j.jam_selesai', 'mk.sks')
            ->get();

        if ($selectedSchedules->count() !== count($validated['jadwal_id'])) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Ada jadwal yang tidak valid untuk mahasiswa atau semester aktif.',
            ]);
        }

        $totalSks = (int) $selectedSchedules->sum('sks');
        $maxSks = $this->resolveMaxSks($mahasiswa?->id);
        if ($totalSks > $maxSks) {
            throw ValidationException::withMessages([
                'total_sks' => "Total SKS melebihi batas {$maxSks} SKS.",
            ]);
        }

        if ($this->hasScheduleConflict($selectedSchedules)) {
            throw ValidationException::withMessages([
                'jadwal_id' => 'Ada bentrok jadwal pada mata kuliah yang dipilih.',
            ]);
        }

        DB::transaction(function () use ($mahasiswa, $tahunAktif, $selectedSchedules, $totalSks) {
            $krs = DB::table('krs')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->first();

            if (! $krs) {
                $krsId = DB::table('krs')->insertGetId([
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahunAktif->id,
                    'total_sks' => $totalSks,
                    'status_krs' => 'draft',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                if ($krs->status_krs === 'final') {
                    throw ValidationException::withMessages([
                        'jadwal_id' => 'KRS sudah final dan tidak dapat diubah.',
                    ]);
                }
                $krsId = $krs->id;
                DB::table('krs')->where('id', $krsId)->update([
                    'total_sks' => $totalSks,
                    'status_krs' => 'draft',
                    'updated_at' => now(),
                ]);
                DB::table('krs_detail')->where('krs_id', $krsId)->delete();
            }

            $rows = $selectedSchedules->map(fn ($item) => [
                'krs_id' => $krsId,
                'jadwal_id' => $item->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            DB::table('krs_detail')->insert($rows);
        });

        AuditLogger::log(
            aksi: 'Simpan draft KRS',
            modul: 'krs',
            entityType: 'krs',
            entityId: DB::table('krs')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->value('id'),
            konteks: [
                'mahasiswa_id' => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAktif->id,
                'total_sks' => $totalSks,
                'jadwal_id' => $selectedSchedules->pluck('id')->all(),
            ],
            request: $request
        );

        return back()->with('success', 'KRS tersimpan sebagai draft. Total SKS: '.$totalSks);
    }

    public function finalize(Request $request)
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();

        if (! $mahasiswa || ! $tahunAktif) {
            return back()->withErrors(['krs' => 'Data mahasiswa atau tahun akademik aktif tidak ditemukan.']);
        }

        if (! $this->isEligibleForAcademicWrite($mahasiswa)) {
            return back()->withErrors(['krs' => 'Mahasiswa tidak memenuhi eligibility akademik untuk finalisasi KRS.']);
        }
        if (! $this->isKrsWindowOpen($tahunAktif)) {
            return back()->withErrors(['krs' => 'Finalisasi KRS ditolak. Periode KRS sedang ditutup.']);
        }

        $isUktLunas = DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAktif->id)
            ->where('status', 'lunas')
            ->exists();

        if (! $isUktLunas) {
            return back()->withErrors(['krs' => 'Finalisasi KRS gagal. Tagihan UKT semester aktif belum lunas.']);
        }

        $krs = DB::table('krs')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAktif->id)
            ->first();

        if (! $krs) {
            return back()->withErrors(['krs' => 'Belum ada draft KRS untuk difinalisasi.']);
        }

        if ($krs->status_krs === 'final') {
            return back()->with('success', 'KRS sudah berstatus final.');
        }

        $detailCount = DB::table('krs_detail')->where('krs_id', $krs->id)->count();
        if ($detailCount === 0) {
            return back()->withErrors(['krs' => 'Finalisasi gagal. KRS belum memiliki mata kuliah.']);
        }

        DB::table('krs')->where('id', $krs->id)->update([
            'status_krs' => 'final',
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Finalisasi KRS mahasiswa',
            modul: 'krs',
            entityType: 'krs',
            entityId: $krs->id,
            konteks: ['mahasiswa_id' => $mahasiswa->id, 'tahun_akademik_id' => $tahunAktif->id],
            request: $request
        );

        return back()->with('success', 'KRS berhasil difinalisasi.');
    }

    public function generateAuto(Request $request)
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();
        if (! $mahasiswa || ! $tahunAktif) {
            return back()->withErrors(['krs' => 'Data mahasiswa atau tahun akademik aktif tidak ditemukan.']);
        }

        if (! $this->isKrsWindowOpen($tahunAktif)) {
            return back()->withErrors(['krs' => 'Generate otomatis ditolak. Periode KRS tidak aktif.']);
        }
        if (! $this->isEligibleForAcademicWrite($mahasiswa)) {
            return back()->withErrors(['krs' => 'Generate otomatis ditolak. Mahasiswa tidak eligible untuk write akademik.']);
        }

        $isUktLunas = DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAktif->id)
            ->where('status', 'lunas')
            ->exists();
        if (! $isUktLunas) {
            return back()->withErrors(['krs' => 'Generate otomatis ditolak. UKT belum lunas.']);
        }

        $jadwal = DB::table('jadwal as j')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->where('j.tahun_akademik_id', $tahunAktif->id)
            ->where('mk.prodi_id', $mahasiswa->prodi_id)
            ->whereNull('j.deleted_at')
            ->whereNull('mk.deleted_at')
            ->select('j.id', 'j.hari', 'j.jam_mulai', 'j.jam_selesai', 'mk.id as mk_id', 'mk.sks')
            ->orderBy('mk.semester')
            ->orderBy('j.hari')
            ->orderBy('j.jam_mulai')
            ->get();

        $passedMkIds = DB::table('krs as k')
            ->join('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->where('k.mahasiswa_id', $mahasiswa->id)
            ->whereIn('n.nilai_huruf', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])
            ->pluck('j.mata_kuliah_id')
            ->unique()
            ->all();

        $maxSks = $this->resolveMaxSks($mahasiswa->id);
        $selected = collect();
        foreach ($jadwal as $item) {
            if (in_array($item->mk_id, $passedMkIds, true)) {
                continue;
            }
            $candidate = $selected->push($item);
            if ($this->hasScheduleConflict($candidate)) {
                $selected->pop();
                continue;
            }
            $sum = (int) $selected->sum('sks');
            if ($sum > $maxSks) {
                $selected->pop();
                continue;
            }
        }

        if ($selected->isEmpty()) {
            return back()->withErrors(['krs' => 'Generate otomatis gagal. Tidak ada jadwal yang memenuhi syarat.']);
        }

        DB::transaction(function () use ($mahasiswa, $tahunAktif, $selected) {
            $totalSks = (int) $selected->sum('sks');
            $krs = DB::table('krs')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->first();

            if (! $krs) {
                $krsId = DB::table('krs')->insertGetId([
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahunAktif->id,
                    'total_sks' => $totalSks,
                    'status_krs' => 'draft',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                if ($krs->status_krs === 'final') {
                    throw ValidationException::withMessages([
                        'krs' => 'KRS sudah final, generate otomatis tidak bisa dijalankan.',
                    ]);
                }
                $krsId = $krs->id;
                DB::table('krs')->where('id', $krsId)->update([
                    'total_sks' => $totalSks,
                    'status_krs' => 'draft',
                    'updated_at' => now(),
                ]);
                DB::table('krs_detail')->where('krs_id', $krsId)->delete();
            }

            $rows = $selected->map(fn ($item) => [
                'krs_id' => $krsId,
                'jadwal_id' => $item->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();
            DB::table('krs_detail')->insert($rows);
        });

        AuditLogger::log(
            aksi: 'Generate otomatis KRS',
            modul: 'krs',
            entityType: 'mahasiswa',
            entityId: $mahasiswa->id,
            konteks: ['tahun_akademik_id' => $tahunAktif->id, 'max_sks' => $maxSks],
            request: $request
        );

        return back()->with('success', 'KRS draft otomatis berhasil dibuat.');
    }

    private function hasScheduleConflict(Collection $schedules): bool
    {
        $groupedByDay = $schedules->groupBy('hari');

        foreach ($groupedByDay as $sameDaySchedules) {
            $sorted = $sameDaySchedules->sortBy('jam_mulai')->values();
            for ($i = 0; $i < $sorted->count() - 1; $i++) {
                $current = $sorted[$i];
                $next = $sorted[$i + 1];
                if ($current->jam_selesai > $next->jam_mulai) {
                    return true;
                }
            }
        }

        return false;
    }

    private function resolveMahasiswa()
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->hasRole('mahasiswa')) {
            return DB::table('mahasiswa')->where('user_id', $user->id)->first();
        }

        return DB::table('mahasiswa')->orderBy('id')->first();
    }

    private function isKrsWindowOpen($tahunAktif): bool
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

    private function isEligibleForAcademicWrite(object $mahasiswa): bool
    {
        return $mahasiswa->status_akademik === 'aktif';
    }

    private function resolveMaxSks(?int $mahasiswaId): int
    {
        if (! $mahasiswaId) {
            return AcademicSetting::maxSksDefault();
        }

        $latest = DB::table('krs as k')
            ->join('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->join('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->where('k.mahasiswa_id', $mahasiswaId)
            ->where('k.status_krs', 'final')
            ->select('mk.sks', 'n.nilai_huruf')
            ->get();

        if ($latest->isEmpty()) {
            return AcademicSetting::maxSksDefault();
        }

        $totalSks = (int) $latest->sum('sks');
        $totalBobot = 0.0;
        foreach ($latest as $row) {
            $totalBobot += ((int) $row->sks) * $this->toBobot($row->nilai_huruf);
        }
        $ips = $totalSks > 0 ? $totalBobot / $totalSks : 0;

        return $ips >= 3.0 ? AcademicSetting::maxSksIps3() : AcademicSetting::maxSksDefault();
    }

    private function toBobot(?string $huruf): float
    {
        return match ($huruf) {
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }
}
