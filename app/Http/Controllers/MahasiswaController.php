<?php

namespace App\Http\Controllers;

use App\Support\PdfDocumentMeta;
use App\Support\AcademicSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function jadwal()
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();
        $items = collect();

        if ($mahasiswa && $tahunAktif) {
            $items = DB::table('krs as k')
                ->join('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
                ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
                ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
                ->where('k.mahasiswa_id', $mahasiswa->id)
                ->where('k.tahun_akademik_id', $tahunAktif->id)
                ->select('mk.kode_mk', 'mk.nama_mk', 'mk.sks', 'j.hari', 'j.jam_mulai', 'j.jam_selesai', 'j.ruangan')
                ->orderBy('j.hari')
                ->orderBy('j.jam_mulai')
                ->get();
        }

        return view('mahasiswa.jadwal', [
            'title' => 'Jadwal Kuliah',
            'mahasiswa' => $mahasiswa,
            'tahunAktif' => $tahunAktif,
            'items' => $items,
        ]);
    }

    public function khs(Request $request)
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAkademikId = $request->query('tahun_akademik_id');

        $rows = $this->khsRows($mahasiswa?->id, $tahunAkademikId ? (int) $tahunAkademikId : null);

        $grouped = $rows->groupBy('krs_id')->map(function ($items) {
            $first = $items->first();
            $totalSks = (int) $items->sum('sks');
            $ipsNumerator = 0.0;

            foreach ($items as $item) {
                $ipsNumerator += ((int) $item->sks) * $this->toBobot($item->nilai_huruf);
            }

            $ips = $totalSks > 0 ? round($ipsNumerator / $totalSks, 2) : 0;

            return [
                'tahun' => $first->tahun,
                'semester' => $first->semester,
                'status_krs' => $first->status_krs,
                'total_sks' => $totalSks,
                'ips' => $ips,
                'items' => $items,
            ];
        })->values();

        return view('mahasiswa.khs', [
            'title' => 'Kartu Hasil Studi',
            'mahasiswa' => $mahasiswa,
            'khs' => $grouped,
            'tahunAkademikList' => DB::table('tahun_akademik')->orderByDesc('id')->get(),
            'selectedTahunAkademikId' => $tahunAkademikId,
        ]);
    }

    public function exportKhsCsv(Request $request)
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAkademikId = $request->query('tahun_akademik_id');
        $rows = $this->khsRows($mahasiswa?->id, $tahunAkademikId ? (int) $tahunAkademikId : null);

        $filename = 'khs-'.($mahasiswa?->nim ?? 'mahasiswa').'-'.now()->format('YmdHis').'.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tahun', 'Semester', 'Status KRS', 'Kode MK', 'Mata Kuliah', 'SKS', 'Nilai Angka', 'Nilai Huruf']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->tahun,
                    $row->semester,
                    $row->status_krs,
                    $row->kode_mk,
                    $row->nama_mk,
                    $row->sks,
                    $row->nilai_angka,
                    $row->nilai_huruf,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportKhsPdf(Request $request)
    {
        $mahasiswa = $this->resolveMahasiswa();
        $tahunAkademikId = $request->query('tahun_akademik_id');
        $rows = $this->khsRows($mahasiswa?->id, $tahunAkademikId ? (int) $tahunAkademikId : null);

        $pdf = Pdf::loadView('pdf.khs', [
            'mahasiswa' => $mahasiswa,
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'docMeta' => PdfDocumentMeta::build('khs'),
        ])->setPaper('a4', 'portrait');

        $filename = 'khs-'.($mahasiswa?->nim ?? 'mahasiswa').'-'.now()->format('YmdHis').'.pdf';
        return $pdf->download($filename);
    }

    public function ukt()
    {
        $mahasiswa = $this->resolveMahasiswa();
        $items = collect();
        $transactions = collect();

        if ($mahasiswa) {
            $items = DB::table('tagihan_ukt as t')
                ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
                ->leftJoin('pembayaran as p', 'p.tagihan_id', '=', 't.id')
                ->where('t.mahasiswa_id', $mahasiswa->id)
                ->groupBy('t.id', 'ta.tahun', 'ta.semester', 't.jumlah', 't.status')
                ->select(
                    't.id',
                    'ta.tahun',
                    'ta.semester',
                    't.jumlah',
                    't.status',
                    DB::raw('COALESCE(SUM(p.jumlah_bayar), 0) as total_bayar')
                )
                ->orderByDesc('t.id')
                ->get();

            $transactions = DB::table('pembayaran as p')
                ->join('tagihan_ukt as t', 't.id', '=', 'p.tagihan_id')
                ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
                ->where('t.mahasiswa_id', $mahasiswa->id)
                ->select(
                    'p.id',
                    'p.tagihan_id',
                    'p.tanggal_bayar',
                    'p.jumlah_bayar',
                    'p.metode_bayar',
                    'p.bukti_bayar',
                    'p.is_reconciliation_error',
                    'ta.tahun',
                    'ta.semester'
                )
                ->orderByDesc('p.id')
                ->get()
                ->groupBy('tagihan_id');
        }

        return view('mahasiswa.ukt', [
            'title' => 'Status Pembayaran UKT',
            'mahasiswa' => $mahasiswa,
            'items' => $items,
            'transactions' => $transactions,
        ]);
    }

    public function profil()
    {
        $mahasiswa = $this->resolveMahasiswa();
        $statusLogs = collect();
        $dosenPaAktif = collect();

        if ($mahasiswa) {
            $statusLogs = DB::table('mahasiswa_status_logs as l')
                ->leftJoin('users as u', 'u.id', '=', 'l.changed_by')
                ->where('l.mahasiswa_id', $mahasiswa->id)
                ->select('l.*', 'u.name as changed_by_name')
                ->orderByDesc('l.id')
                ->limit(30)
                ->get();

            $dosenPaAktif = DB::table('dosen_pa_mahasiswa as dpm')
                ->join('dosen as d', 'd.id', '=', 'dpm.dosen_id')
                ->where('dpm.mahasiswa_id', $mahasiswa->id)
                ->where('dpm.status_aktif', true)
                ->select('d.nama', 'd.nidn', 'dpm.periode_mulai', 'dpm.periode_selesai')
                ->orderByDesc('dpm.id')
                ->get();
        }

        return view('mahasiswa.profil', [
            'title' => 'Profil Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'statusLogs' => $statusLogs,
            'dosenPaAktif' => $dosenPaAktif,
        ]);
    }

    public function evaluasi()
    {
        if (! AcademicSetting::evaluasiEnabled()) {
            return back()->withErrors(['evaluasi' => 'Fitur evaluasi dosen sedang dinonaktifkan oleh super admin.']);
        }

        $mahasiswa = $this->resolveMahasiswa();
        $items = collect();

        if ($mahasiswa) {
            $items = DB::table('krs_detail as kd')
                ->join('krs as k', 'k.id', '=', 'kd.krs_id')
                ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
                ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
                ->join('dosen as d', 'd.id', '=', 'j.dosen_id')
                ->leftJoin('evaluasi_dosen as ed', 'ed.krs_detail_id', '=', 'kd.id')
                ->where('k.mahasiswa_id', $mahasiswa->id)
                ->select(
                    'kd.id as krs_detail_id',
                    'mk.kode_mk',
                    'mk.nama_mk',
                    'd.nama as nama_dosen',
                    DB::raw('COALESCE(ed.status_selesai, 0) as status_selesai')
                )
                ->orderByDesc('kd.id')
                ->get();
        }

        return view('mahasiswa.evaluasi', [
            'title' => 'Evaluasi Dosen',
            'items' => $items,
        ]);
    }

    public function storeEvaluasi(Request $request)
    {
        if (! AcademicSetting::evaluasiEnabled()) {
            return back()->withErrors(['evaluasi' => 'Fitur evaluasi dosen sedang dinonaktifkan oleh super admin.']);
        }

        $mahasiswa = $this->resolveMahasiswa();
        if (! $mahasiswa) {
            return back()->withErrors(['evaluasi' => 'Data mahasiswa tidak ditemukan.']);
        }
        if (! $this->isEligibleForAcademicWrite($mahasiswa)) {
            return back()->withErrors(['evaluasi' => 'Mahasiswa tidak memenuhi eligibility akademik untuk write evaluasi.']);
        }

        $validated = $request->validate([
            'krs_detail_id' => ['required', 'integer', 'exists:krs_detail,id'],
            'status_selesai' => ['required', 'in:0,1'],
        ]);

        $owned = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->where('kd.id', $validated['krs_detail_id'])
            ->where('k.mahasiswa_id', $mahasiswa->id)
            ->exists();

        if (! $owned) {
            return back()->withErrors(['evaluasi' => 'Anda tidak berhak mengubah evaluasi ini.']);
        }

        DB::table('evaluasi_dosen')->updateOrInsert(
            ['krs_detail_id' => $validated['krs_detail_id']],
            [
                'status_selesai' => $validated['status_selesai'] === '1',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Status evaluasi dosen berhasil diperbarui.');
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

    private function khsRows(?int $mahasiswaId, ?int $tahunAkademikId = null)
    {
        if (! $mahasiswaId) {
            return collect();
        }

        $query = DB::table('krs as k')
            ->join('tahun_akademik as ta', 'ta.id', '=', 'k.tahun_akademik_id')
            ->join('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->where('k.mahasiswa_id', $mahasiswaId)
            ->select(
                'k.id as krs_id',
                'ta.tahun',
                'ta.semester',
                'k.status_krs',
                'mk.kode_mk',
                'mk.nama_mk',
                'mk.sks',
                'n.nilai_angka',
                'n.nilai_huruf'
            )
            ->orderByDesc('k.id');

        if ($tahunAkademikId) {
            $query->where('k.tahun_akademik_id', $tahunAkademikId);
        }

        return $query->get();
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

    private function isEligibleForAcademicWrite(object $mahasiswa): bool
    {
        return $mahasiswa->status_akademik === 'aktif';
    }
}
