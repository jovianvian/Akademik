<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\AcademicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DosenController extends Controller
{
    public function jadwal()
    {
        $dosenId = $this->resolveDosenId();

        $query = DB::table('jadwal as j')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 'j.tahun_akademik_id')
            ->select('j.*', 'mk.kode_mk', 'mk.nama_mk', 'mk.sks', 'ta.tahun', 'ta.semester')
            ->orderBy('j.hari')
            ->orderBy('j.jam_mulai');

        if ($dosenId) {
            $query->where('j.dosen_id', $dosenId);
        }

        return view('dosen.jadwal', [
            'title' => 'Jadwal Mengajar',
            'items' => $query->get(),
        ]);
    }

    public function nilai()
    {
        $dosenId = $this->resolveDosenId();

        $query = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->select(
                'kd.id as krs_detail_id',
                'm.nim',
                'm.nama as nama_mahasiswa',
                'mk.kode_mk',
                'mk.nama_mk',
                'mk.sks',
                'k.status_krs',
                'k.nilai_terkunci',
                'n.nilai_tugas',
                'n.nilai_uts',
                'n.nilai_uas',
                'n.nilai_kehadiran',
                'n.nilai_angka',
                'n.nilai_huruf'
            )
            ->orderBy('m.nim');

        if ($dosenId) {
            $query->where('j.dosen_id', $dosenId);
        }

        return view('dosen.nilai', [
            'title' => 'Input Nilai Mahasiswa',
            'items' => $query->get(),
        ]);
    }

    public function monitoringMahasiswa()
    {
        $dosenId = $this->resolveDosenId();

        $query = DB::table('krs as k')
            ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
            ->join('program_studi as p', 'p.id', '=', 'm.prodi_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 'k.tahun_akademik_id')
            ->leftJoin('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->select(
                'm.id as mahasiswa_id',
                'm.nim',
                'm.nama',
                'm.angkatan',
                'p.nama_prodi',
                'ta.tahun',
                'ta.semester',
                'k.status_krs',
                DB::raw('COUNT(kd.id) as jumlah_mk'),
                DB::raw('SUM(CASE WHEN n.id IS NOT NULL THEN 1 ELSE 0 END) as jumlah_nilai')
            )
            ->groupBy('m.id', 'm.nim', 'm.nama', 'm.angkatan', 'p.nama_prodi', 'ta.tahun', 'ta.semester', 'k.status_krs')
            ->orderBy('m.nim');

        if ($dosenId) {
            $query->whereExists(function ($sub) use ($dosenId) {
                $sub->select(DB::raw(1))
                    ->from('krs_detail as kd2')
                    ->join('jadwal as j2', 'j2.id', '=', 'kd2.jadwal_id')
                    ->whereColumn('kd2.krs_id', 'k.id')
                    ->where('j2.dosen_id', $dosenId);
            });
        }

        return view('dosen.monitoring-mahasiswa', [
            'title' => 'Monitoring Mahasiswa',
            'items' => $query->get(),
        ]);
    }

    public function storeNilai(Request $request)
    {
        $validatedBase = $request->validate([
            'krs_detail_id' => ['required', 'integer', 'exists:krs_detail,id'],
            'nilai_angka' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_tugas' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_uts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_uas' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_kehadiran' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $validated = $validatedBase;

        $hasBreakdown = $request->filled('nilai_tugas') || $request->filled('nilai_uts') || $request->filled('nilai_uas') || $request->filled('nilai_kehadiran');
        if ($hasBreakdown && (! $request->filled('nilai_tugas') || ! $request->filled('nilai_uts') || ! $request->filled('nilai_uas') || ! $request->filled('nilai_kehadiran'))) {
            throw ValidationException::withMessages([
                'nilai_tugas' => 'Jika menggunakan breakdown nilai, semua komponen (tugas, UTS, UAS, kehadiran) wajib diisi.',
            ]);
        }

        $dosenId = $this->resolveDosenId();
        $nilaiInputCloseAt = AcademicSetting::nilaiInputCloseAt();
        if ($nilaiInputCloseAt && now()->gt($nilaiInputCloseAt)) {
            throw ValidationException::withMessages([
                'nilai_angka' => 'Periode input nilai sudah ditutup oleh admin sistem.',
            ]);
        }

        $krsLock = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->where('kd.id', $validated['krs_detail_id'])
            ->select('k.id', 'k.nilai_terkunci')
            ->first();

        if ($krsLock && (bool) $krsLock->nilai_terkunci) {
            throw ValidationException::withMessages([
                'nilai_angka' => 'Nilai sudah dikunci karena KHS telah digenerate admin akademik.',
            ]);
        }

        if ($dosenId) {
            $isOwned = DB::table('krs_detail as kd')
                ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
                ->where('kd.id', $validated['krs_detail_id'])
                ->where('j.dosen_id', $dosenId)
                ->exists();

            if (! $isOwned) {
                throw ValidationException::withMessages([
                    'nilai_angka' => 'Anda tidak berhak menginput nilai untuk kelas ini.',
                ]);
            }
        }

        $statusAkademikMhs = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
            ->where('kd.id', $validated['krs_detail_id'])
            ->value('m.status_akademik');

        if (in_array($statusAkademikMhs, ['suspended', 'suspended_pending_decision'], true)) {
            throw ValidationException::withMessages([
                'nilai_angka' => 'Input nilai diblokir karena status akademik mahasiswa sedang suspended.',
            ]);
        }

        if ($hasBreakdown) {
            $bobot = AcademicSetting::bobotNilai();
            $angka = (
                ((float) $validated['nilai_tugas'] * (float) ($bobot['tugas'] ?? 30)) +
                ((float) $validated['nilai_uts'] * (float) ($bobot['uts'] ?? 25)) +
                ((float) $validated['nilai_uas'] * (float) ($bobot['uas'] ?? 35)) +
                ((float) $validated['nilai_kehadiran'] * (float) ($bobot['kehadiran'] ?? 10))
            ) / 100;
        } else {
            if (! isset($validated['nilai_angka'])) {
                throw ValidationException::withMessages([
                    'nilai_angka' => 'Isi nilai angka atau komponen breakdown nilai.',
                ]);
            }
            $angka = (float) $validated['nilai_angka'];
        }

        $huruf = $this->toHuruf($angka);

        DB::table('nilai')->updateOrInsert(
            ['krs_detail_id' => $validated['krs_detail_id']],
            [
                'nilai_tugas' => $hasBreakdown ? (float) $validated['nilai_tugas'] : null,
                'nilai_uts' => $hasBreakdown ? (float) $validated['nilai_uts'] : null,
                'nilai_uas' => $hasBreakdown ? (float) $validated['nilai_uas'] : null,
                'nilai_kehadiran' => $hasBreakdown ? (float) $validated['nilai_kehadiran'] : null,
                'nilai_angka' => $angka,
                'nilai_huruf' => $huruf,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        AuditLogger::log(
            aksi: 'Input nilai mahasiswa',
            modul: 'dosen',
            entityType: 'krs_detail',
            entityId: $validated['krs_detail_id'],
            konteks: [
                'nilai_tugas' => $hasBreakdown ? (float) $validated['nilai_tugas'] : null,
                'nilai_uts' => $hasBreakdown ? (float) $validated['nilai_uts'] : null,
                'nilai_uas' => $hasBreakdown ? (float) $validated['nilai_uas'] : null,
                'nilai_kehadiran' => $hasBreakdown ? (float) $validated['nilai_kehadiran'] : null,
                'nilai_angka' => round($angka, 2),
                'nilai_huruf' => $huruf,
            ],
            request: $request
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function exportNilaiCsv()
    {
        $dosenId = $this->resolveDosenId();
        $query = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->whereNull('j.deleted_at')
            ->whereNull('mk.deleted_at')
            ->select(
                'm.nim',
                'm.nama as nama_mahasiswa',
                'mk.kode_mk',
                'mk.nama_mk',
                'n.nilai_tugas',
                'n.nilai_uts',
                'n.nilai_uas',
                'n.nilai_kehadiran',
                'n.nilai_angka',
                'n.nilai_huruf'
            )
            ->orderBy('m.nim');

        if ($dosenId) {
            $query->where('j.dosen_id', $dosenId);
        }
        $rows = $query->get();

        $filename = 'nilai-dosen-'.now()->format('YmdHis').'.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['NIM', 'Nama', 'Kode MK', 'Mata Kuliah', 'Tugas', 'UTS', 'UAS', 'Kehadiran', 'Nilai Akhir', 'Huruf']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->nim,
                    $row->nama_mahasiswa,
                    $row->kode_mk,
                    $row->nama_mk,
                    $row->nilai_tugas,
                    $row->nilai_uts,
                    $row->nilai_uas,
                    $row->nilai_kehadiran,
                    $row->nilai_angka,
                    $row->nilai_huruf,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function resolveDosenId(): ?int
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('dosen')) {
            return null;
        }

        return (int) DB::table('dosen')->where('user_id', $user->id)->value('id');
    }

    private function toHuruf(float $nilai): string
    {
        $ranges = AcademicSetting::gradeRanges();
        usort($ranges, fn ($a, $b) => (float) $b['min'] <=> (float) $a['min']);

        foreach ($ranges as $range) {
            if ($nilai >= (float) ($range['min'] ?? 0)) {
                return strtoupper((string) ($range['grade'] ?? 'E'));
            }
        }

        return 'E';
    }
}
