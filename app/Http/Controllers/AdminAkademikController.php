<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\AcademicSetting;
use App\Support\AcademicStatusMutationService;
use App\Support\AcademicRuleSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAkademikController extends Controller
{
    public function periodeKrs()
    {
        return view('akademik.periode-krs', [
            'title' => 'Kontrol Periode KRS',
            'items' => DB::table('tahun_akademik')->orderByDesc('status_aktif')->orderByDesc('id')->get(),
        ]);
    }

    public function updatePeriodeKrs(Request $request, int $id)
    {
        $validated = $request->validate([
            'krs_dibuka' => ['nullable', 'boolean'],
            'krs_mulai' => ['nullable', 'date'],
            'krs_selesai' => ['nullable', 'date', 'after:krs_mulai'],
        ]);

        $row = DB::table('tahun_akademik')->where('id', $id)->first();
        if (! $row) {
            return back()->withErrors(['periode_krs' => 'Tahun akademik tidak ditemukan.']);
        }

        $payload = [
            'krs_dibuka' => (bool) ($validated['krs_dibuka'] ?? false),
            'krs_mulai' => $validated['krs_mulai'] ?? null,
            'krs_selesai' => $validated['krs_selesai'] ?? null,
            'updated_at' => now(),
        ];

        DB::table('tahun_akademik')->where('id', $id)->update($payload);
        AcademicRuleSnapshotService::getOrCreate($id, auth()->id());

        AuditLogger::log(
            aksi: 'Update periode KRS',
            modul: 'akademik',
            entityType: 'tahun_akademik',
            entityId: $id,
            konteks: [
                'before' => [
                    'krs_dibuka' => (bool) $row->krs_dibuka,
                    'krs_mulai' => $row->krs_mulai,
                    'krs_selesai' => $row->krs_selesai,
                ],
                'after' => $payload,
            ],
            request: $request
        );

        return back()->with('success', 'Periode KRS berhasil diperbarui.');
    }

    public function generateKhs()
    {
        $items = DB::table('krs as k')
            ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 'k.tahun_akademik_id')
            ->leftJoin('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->select(
                'k.id',
                'm.nim',
                'm.nama',
                'ta.tahun',
                'ta.semester',
                'k.status_krs',
                'k.nilai_terkunci',
                DB::raw('COUNT(kd.id) as total_mk'),
                DB::raw('SUM(CASE WHEN n.id IS NOT NULL THEN 1 ELSE 0 END) as total_nilai')
            )
            ->groupBy('k.id', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 'k.status_krs', 'k.nilai_terkunci')
            ->orderByDesc('k.id')
            ->get();

        return view('akademik.generate-khs', [
            'title' => 'Generate KHS',
            'items' => $items,
        ]);
    }

    public function finalizeKhs(Request $request, int $krsId)
    {
        $isLocked = DB::table('krs')->where('id', $krsId)->value('nilai_terkunci');
        if ($isLocked) {
            return back()->with('success', 'KHS sudah pernah digenerate dan nilai sudah terkunci.');
        }

        $row = DB::table('krs as k')
            ->leftJoin('krs_detail as kd', 'kd.krs_id', '=', 'k.id')
            ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
            ->where('k.id', $krsId)
            ->groupBy('k.id')
            ->select(
                'k.id',
                DB::raw('COUNT(kd.id) as total_mk'),
                DB::raw('SUM(CASE WHEN n.id IS NOT NULL THEN 1 ELSE 0 END) as total_nilai')
            )
            ->first();

        if (! $row || (int) $row->total_mk === 0) {
            return back()->withErrors(['khs' => 'KRS belum memiliki mata kuliah.']);
        }

        if ((int) $row->total_mk !== (int) $row->total_nilai) {
            return back()->withErrors(['khs' => 'Generate KHS gagal. Masih ada nilai yang belum diinput dosen.']);
        }

        $krsRow = DB::table('krs')->where('id', $krsId)->select('tahun_akademik_id')->first();

        DB::table('krs')->where('id', $krsId)->update([
            'status_krs' => 'final',
            'nilai_terkunci' => true,
            'updated_at' => now(),
        ]);
        if ($krsRow) {
            AcademicRuleSnapshotService::getOrCreate((int) $krsRow->tahun_akademik_id, auth()->id());
            AcademicRuleSnapshotService::lock((int) $krsRow->tahun_akademik_id);
        }

        AuditLogger::log(
            aksi: 'Generate/finalisasi KHS',
            modul: 'akademik',
            entityType: 'krs',
            entityId: $krsId,
            konteks: ['status_krs' => 'final', 'nilai_terkunci' => true],
            request: $request
        );

        return back()->with('success', 'KHS berhasil digenerate/finalisasi.');
    }

    public function unlockNilai(Request $request, int $krsId)
    {
        $krs = DB::table('krs')->where('id', $krsId)->first();
        if (! $krs) {
            return back()->withErrors(['khs' => 'Data KRS tidak ditemukan.']);
        }

        DB::table('krs')->where('id', $krsId)->update([
            'nilai_terkunci' => false,
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Override unlock nilai',
            modul: 'akademik',
            entityType: 'krs',
            entityId: $krsId,
            konteks: ['nilai_terkunci' => false],
            request: $request
        );

        return back()->with('success', 'Nilai berhasil di-unlock. Dosen dapat input/edit nilai lagi.');
    }

    public function mahasiswaStatus()
    {
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();

        $items = DB::table('mahasiswa as m')
            ->join('program_studi as p', 'p.id', '=', 'm.prodi_id')
            ->leftJoin('mahasiswa_period_status as mps', function ($join) use ($tahunAktif) {
                $join->on('mps.mahasiswa_id', '=', 'm.id');
                if ($tahunAktif) {
                    $join->where('mps.tahun_akademik_id', '=', $tahunAktif->id)
                        ->whereNull('mps.effective_until');
                }
            })
            ->leftJoin('tagihan_ukt as t', function ($join) use ($tahunAktif) {
                $join->on('t.mahasiswa_id', '=', 'm.id');
                if ($tahunAktif) {
                    $join->where('t.tahun_akademik_id', '=', $tahunAktif->id);
                }
            })
            ->select(
                'm.*',
                'p.nama_prodi',
                't.status as status_tagihan_aktif',
                'mps.eligibility_status',
                'mps.reason as eligibility_reason'
            )
            ->orderBy('m.nim')
            ->get();

        return view('akademik.mahasiswa-status', [
            'title' => 'Status Mahasiswa',
            'items' => $items,
            'tahunAktif' => $tahunAktif,
            'statusOptions' => ['eligible', 'suspended_pending_decision', 'suspended'],
            'statusLogs' => DB::table('mahasiswa_status_logs as l')
                ->join('mahasiswa as m', 'm.id', '=', 'l.mahasiswa_id')
                ->leftJoin('users as u', 'u.id', '=', 'l.changed_by')
                ->select('l.*', 'm.nim', 'm.nama', 'u.name as changed_by_name')
                ->orderByDesc('l.id')
                ->limit(60)
                ->get(),
        ]);
    }

    public function updateMahasiswaStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'eligibility_status' => ['required', 'in:eligible,suspended_pending_decision,suspended'],
            'catatan_status' => ['nullable', 'max:255'],
        ]);

        $mahasiswa = DB::table('mahasiswa')->where('id', $id)->first();
        if (! $mahasiswa) {
            return back()->withErrors(['status' => 'Mahasiswa tidak ditemukan.']);
        }

        $tahunAktifId = (int) DB::table('tahun_akademik')->where('status_aktif', true)->value('id');
        if (! $tahunAktifId) {
            return back()->withErrors(['status' => 'Tahun akademik aktif tidak ditemukan.']);
        }

        try {
            $changed = AcademicStatusMutationService::mutatePeriodEligibility(
                $id,
                $tahunAktifId,
                $validated['eligibility_status'],
                'manual',
                $validated['catatan_status'] ?? null,
                auth()->id(),
                $request
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()])->withInput();
        }

        return back()->with('success', $changed ? 'Status mahasiswa berhasil diperbarui.' : 'Status mahasiswa tidak berubah.');
    }

    public function syncStatusByUkt(Request $request)
    {
        if (! AcademicSetting::autoNonaktifIfUktUnpaid()) {
            return back()->withErrors(['sync' => 'Sinkron UKT otomatis dinonaktifkan pada academic settings.']);
        }

        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();
        if (! $tahunAktif) {
            return back()->withErrors(['sync' => 'Tahun akademik aktif belum diset.']);
        }

        $rows = DB::table('mahasiswa as m')
            ->leftJoin('tagihan_ukt as t', function ($join) use ($tahunAktif) {
                $join->on('t.mahasiswa_id', '=', 'm.id')
                    ->where('t.tahun_akademik_id', '=', $tahunAktif->id);
            })
            ->whereNotIn('m.enrollment_status_current', ['do', 'lulus'])
            ->select('m.id', 't.status as status_tagihan')
            ->get();

        $updated = 0;
        foreach ($rows as $row) {
            $newStatus = match ($row->status_tagihan) {
                'paid' => 'eligible',
                'disputed', 'void' => 'suspended_pending_decision',
                default => 'suspended',
            };

            $catatan = $newStatus !== 'eligible'
                ? 'Update otomatis eligibility: status UKT periode aktif tidak paid.'
                : null;

            try {
                $changed = AcademicStatusMutationService::mutatePeriodEligibility(
                    $row->id,
                    $tahunAktif->id,
                    $newStatus,
                    'sinkron_ukt',
                    $catatan,
                    auth()->id(),
                    $request
                );
            } catch (\RuntimeException) {
                continue;
            }
            if ($changed) {
                $updated++;
            }
        }

        AuditLogger::log(
            aksi: 'Sinkron status mahasiswa dari UKT',
            modul: 'akademik',
            entityType: 'tahun_akademik',
            entityId: $tahunAktif->id,
            konteks: ['updated_rows' => $updated],
            request: $request
        );

        return back()->with('success', "Sinkron status mahasiswa selesai. {$updated} data diperbarui.");
    }
}
