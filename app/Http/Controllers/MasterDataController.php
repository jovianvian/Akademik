<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    private const JABATAN_OPTIONS = [
        'Ketua Prodi',
        'Sekretaris Prodi',
        'Kaprodi',
        'Dekan',
        'Wakil Dekan',
        'Koordinator Mata Kuliah',
        'Dosen Pengampu',
        'Dosen Pembimbing Akademik',
        'Rektor',
        'Wakil Rektor',
    ];

    public function __construct(private readonly ScheduleService $scheduleService)
    {
    }

    public function fakultas()
    {
        return view('master.fakultas', [
            'title' => 'Master Fakultas',
            'items' => DB::table('fakultas')->whereNull('deleted_at')->orderBy('nama_fakultas')->paginate(10)->withQueryString(),
        ]);
    }

    public function storeFakultas(Request $request)
    {
        $validated = $request->validate([
            'nama_fakultas' => [
                'required',
                'max:100',
                Rule::unique('fakultas', 'nama_fakultas')->where(fn ($q) => $q->whereNull('deleted_at')),
            ],
        ]);

        $id = DB::table('fakultas')->insertGetId([
            'nama_fakultas' => $validated['nama_fakultas'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Tambah fakultas',
            modul: 'master_fakultas',
            entityType: 'fakultas',
            entityId: $id,
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function updateFakultas(Request $request, int $id)
    {
        $row = DB::table('fakultas')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['fakultas' => 'Data fakultas tidak ditemukan.']);
        }

        $validated = $request->validate([
            'nama_fakultas' => [
                'required',
                'max:100',
                Rule::unique('fakultas', 'nama_fakultas')
                    ->ignore($id)
                    ->where(fn ($q) => $q->whereNull('deleted_at')),
            ],
        ]);

        DB::table('fakultas')->where('id', $id)->update([
            'nama_fakultas' => $validated['nama_fakultas'],
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Edit fakultas',
            modul: 'master_fakultas',
            entityType: 'fakultas',
            entityId: $id,
            konteks: ['before' => ['nama_fakultas' => $row->nama_fakultas], 'after' => $validated],
            request: $request
        );

        return back()->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroyFakultas(Request $request, int $id)
    {
        $row = DB::table('fakultas')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['fakultas' => 'Data fakultas tidak ditemukan.']);
        }

        DB::table('fakultas')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Soft delete fakultas',
            modul: 'master_fakultas',
            entityType: 'fakultas',
            entityId: $id,
            konteks: ['deleted_data' => ['nama_fakultas' => $row->nama_fakultas]],
            request: $request
        );

        return back()->with('success', 'Fakultas berhasil dihapus (soft delete).');
    }

    public function prodi()
    {
        return view('master.prodi', [
            'title' => 'Master Program Studi',
            'items' => DB::table('program_studi as p')
                ->join('fakultas as f', 'f.id', '=', 'p.fakultas_id')
                ->whereNull('p.deleted_at')
                ->whereNull('f.deleted_at')
                ->select('p.*', 'f.nama_fakultas')
                ->orderBy('p.nama_prodi')
                ->paginate(10)
                ->withQueryString(),
            'fakultas' => DB::table('fakultas')->whereNull('deleted_at')->orderBy('nama_fakultas')->get(),
        ]);
    }

    public function storeProdi(Request $request)
    {
        $validated = $request->validate([
            'nama_prodi' => ['required', 'max:100'],
            'fakultas_id' => ['required', 'integer', 'exists:fakultas,id'],
        ]);

        $id = DB::table('program_studi')->insertGetId([
            'nama_prodi' => $validated['nama_prodi'],
            'fakultas_id' => $validated['fakultas_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Tambah program studi',
            modul: 'master_prodi',
            entityType: 'program_studi',
            entityId: $id,
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function updateProdi(Request $request, int $id)
    {
        $row = DB::table('program_studi')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['prodi' => 'Data program studi tidak ditemukan.']);
        }

        $validated = $request->validate([
            'nama_prodi' => ['required', 'max:100'],
            'fakultas_id' => ['required', 'integer', 'exists:fakultas,id'],
        ]);

        DB::table('program_studi')->where('id', $id)->update([
            'nama_prodi' => $validated['nama_prodi'],
            'fakultas_id' => $validated['fakultas_id'],
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Edit program studi',
            modul: 'master_prodi',
            entityType: 'program_studi',
            entityId: $id,
            konteks: [
                'before' => ['nama_prodi' => $row->nama_prodi, 'fakultas_id' => $row->fakultas_id],
                'after' => $validated,
            ],
            request: $request
        );

        return back()->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroyProdi(Request $request, int $id)
    {
        $row = DB::table('program_studi')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['prodi' => 'Data program studi tidak ditemukan.']);
        }

        DB::table('program_studi')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Soft delete program studi',
            modul: 'master_prodi',
            entityType: 'program_studi',
            entityId: $id,
            konteks: ['deleted_data' => ['nama_prodi' => $row->nama_prodi, 'fakultas_id' => $row->fakultas_id]],
            request: $request
        );

        return back()->with('success', 'Program studi berhasil dihapus (soft delete).');
    }

    public function mataKuliah()
    {
        return view('master.mata-kuliah', [
            'title' => 'Master Mata Kuliah',
            'items' => DB::table('mata_kuliah as mk')
                ->join('program_studi as p', 'p.id', '=', 'mk.prodi_id')
                ->whereNull('mk.deleted_at')
                ->whereNull('p.deleted_at')
                ->select('mk.*', 'p.nama_prodi')
                ->orderBy('mk.kode_mk')
                ->paginate(10)
                ->withQueryString(),
            'prodi' => DB::table('program_studi')->whereNull('deleted_at')->orderBy('nama_prodi')->get(),
        ]);
    }

    public function storeMataKuliah(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => ['required', 'max:20'],
            'nama_mk' => ['required', 'max:120'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'prodi_id' => ['required', 'integer', 'exists:program_studi,id'],
        ]);

        $payload = [
            'kode_mk' => strtoupper($validated['kode_mk']),
            'nama_mk' => $validated['nama_mk'],
            'sks' => $validated['sks'],
            'semester' => $validated['semester'],
            'prodi_id' => $validated['prodi_id'],
        ];

        $id = DB::table('mata_kuliah')->insertGetId([
            ...$payload,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Tambah mata kuliah',
            modul: 'master_mata_kuliah',
            entityType: 'mata_kuliah',
            entityId: $id,
            konteks: $payload,
            request: $request
        );

        return back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function updateMataKuliah(Request $request, int $id)
    {
        $row = DB::table('mata_kuliah')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['mata_kuliah' => 'Data mata kuliah tidak ditemukan.']);
        }

        $validated = $request->validate([
            'kode_mk' => ['required', 'max:20'],
            'nama_mk' => ['required', 'max:120'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'prodi_id' => ['required', 'integer', 'exists:program_studi,id'],
        ]);

        $payload = [
            'kode_mk' => strtoupper($validated['kode_mk']),
            'nama_mk' => $validated['nama_mk'],
            'sks' => $validated['sks'],
            'semester' => $validated['semester'],
            'prodi_id' => $validated['prodi_id'],
        ];

        DB::table('mata_kuliah')->where('id', $id)->update([
            ...$payload,
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Edit mata kuliah',
            modul: 'master_mata_kuliah',
            entityType: 'mata_kuliah',
            entityId: $id,
            konteks: [
                'before' => [
                    'kode_mk' => $row->kode_mk,
                    'nama_mk' => $row->nama_mk,
                    'sks' => $row->sks,
                    'semester' => $row->semester,
                    'prodi_id' => $row->prodi_id,
                ],
                'after' => $payload,
            ],
            request: $request
        );

        return back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroyMataKuliah(Request $request, int $id)
    {
        $row = DB::table('mata_kuliah')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['mata_kuliah' => 'Data mata kuliah tidak ditemukan.']);
        }

        DB::table('mata_kuliah')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Soft delete mata kuliah',
            modul: 'master_mata_kuliah',
            entityType: 'mata_kuliah',
            entityId: $id,
            konteks: ['deleted_data' => ['kode_mk' => $row->kode_mk, 'nama_mk' => $row->nama_mk]],
            request: $request
        );

        return back()->with('success', 'Mata kuliah berhasil dihapus (soft delete).');
    }

    public function jadwal()
    {
        return view('master.jadwal', [
            'title' => 'Master Jadwal',
            'items' => DB::table('jadwal as j')
                ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
                ->join('dosen as d', 'd.id', '=', 'j.dosen_id')
                ->join('tahun_akademik as ta', 'ta.id', '=', 'j.tahun_akademik_id')
                ->whereNull('j.deleted_at')
                ->whereNull('mk.deleted_at')
                ->select('j.*', 'mk.kode_mk', 'mk.nama_mk', 'mk.sks', 'd.nama as nama_dosen', 'ta.tahun', 'ta.semester')
                ->orderBy('j.hari')
                ->orderBy('j.jam_mulai')
                ->paginate(10)
                ->withQueryString(),
            'mataKuliah' => DB::table('mata_kuliah')->whereNull('deleted_at')->orderBy('kode_mk')->get(),
            'dosen' => DB::table('dosen')->orderBy('nama')->get(),
            'tahunAkademik' => DB::table('tahun_akademik')->orderByDesc('status_aktif')->orderByDesc('id')->get(),
        ]);
    }

    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'integer', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'integer', 'exists:dosen,id'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['required', 'max:40'],
            'tahun_akademik_id' => ['required', 'integer', 'exists:tahun_akademik,id'],
        ]);

        try {
            $id = DB::transaction(function () use ($validated) {
                $this->scheduleService->assertNoConflictForWrite($validated);

                return DB::table('jadwal')->insertGetId([
                    ...$validated,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['jadwal' => $e->getMessage()])->withInput();
        }

        AuditLogger::log(
            aksi: 'Tambah jadwal',
            modul: 'master_jadwal',
            entityType: 'jadwal',
            entityId: $id,
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, int $id)
    {
        $row = DB::table('jadwal')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['jadwal' => 'Data jadwal tidak ditemukan.']);
        }

        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'integer', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'integer', 'exists:dosen,id'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan' => ['required', 'max:40'],
            'tahun_akademik_id' => ['required', 'integer', 'exists:tahun_akademik,id'],
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $this->scheduleService->assertNoConflictForWrite($validated, $id);

                DB::table('jadwal')->where('id', $id)->update([
                    ...$validated,
                    'updated_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['jadwal' => $e->getMessage()])->withInput();
        }

        AuditLogger::log(
            aksi: 'Edit jadwal',
            modul: 'master_jadwal',
            entityType: 'jadwal',
            entityId: $id,
            konteks: [
                'before' => [
                    'mata_kuliah_id' => $row->mata_kuliah_id,
                    'dosen_id' => $row->dosen_id,
                    'hari' => $row->hari,
                    'jam_mulai' => $row->jam_mulai,
                    'jam_selesai' => $row->jam_selesai,
                    'ruangan' => $row->ruangan,
                    'tahun_akademik_id' => $row->tahun_akademik_id,
                ],
                'after' => $validated,
            ],
            request: $request
        );

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroyJadwal(Request $request, int $id)
    {
        $row = DB::table('jadwal')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['jadwal' => 'Data jadwal tidak ditemukan.']);
        }

        DB::table('jadwal')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Soft delete jadwal',
            modul: 'master_jadwal',
            entityType: 'jadwal',
            entityId: $id,
            konteks: [
                'deleted_data' => [
                    'mata_kuliah_id' => $row->mata_kuliah_id,
                    'dosen_id' => $row->dosen_id,
                    'hari' => $row->hari,
                    'jam_mulai' => $row->jam_mulai,
                    'jam_selesai' => $row->jam_selesai,
                    'ruangan' => $row->ruangan,
                ],
            ],
            request: $request
        );

        return back()->with('success', 'Jadwal berhasil dihapus (soft delete).');
    }

    public function jabatanDosen()
    {
        return view('master.jabatan-dosen', [
            'title' => 'Master Jabatan Dosen',
            'items' => DB::table('jabatan_dosen as jd')
                ->join('dosen as d', 'd.id', '=', 'jd.dosen_id')
                ->whereNull('jd.deleted_at')
                ->select('jd.*', 'd.nidn', 'd.nama as nama_dosen')
                ->orderByDesc('jd.id')
                ->paginate(10)
                ->withQueryString(),
            'dosen' => DB::table('dosen')->orderBy('nama')->get(),
            'jabatanOptions' => self::JABATAN_OPTIONS,
        ]);
    }

    public function storeJabatanDosen(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => ['required', 'integer', 'exists:dosen,id'],
            'jabatan' => ['required', Rule::in(self::JABATAN_OPTIONS)],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $isActive = (bool) ($validated['status_aktif'] ?? true);
        $jabatanStrategis = ['Dekan', 'Kaprodi', 'Rektor', 'Wakil Rektor', 'Ketua Prodi'];

        if ($isActive && in_array($validated['jabatan'], $jabatanStrategis, true)) {
            // Hanya boleh ada satu pejabat aktif per jabatan strategis.
            DB::table('jabatan_dosen')
                ->where('jabatan', $validated['jabatan'])
                ->where('status_aktif', true)
                ->update([
                    'status_aktif' => false,
                    'updated_at' => now(),
                ]);
        }

        $payload = [
            'dosen_id' => $validated['dosen_id'],
            'jabatan' => $validated['jabatan'],
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'status_aktif' => $isActive,
        ];

        $id = DB::table('jabatan_dosen')->insertGetId([
            ...$payload,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Tambah jabatan dosen',
            modul: 'master_jabatan_dosen',
            entityType: 'jabatan_dosen',
            entityId: $id,
            konteks: $payload,
            request: $request
        );

        return back()->with('success', 'Jabatan dosen berhasil ditambahkan.');
    }

    public function updateJabatanDosen(Request $request, int $id)
    {
        $row = DB::table('jabatan_dosen')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['jabatan' => 'Data jabatan dosen tidak ditemukan.']);
        }

        $validated = $request->validate([
            'dosen_id' => ['required', 'integer', 'exists:dosen,id'],
            'jabatan' => ['required', Rule::in(self::JABATAN_OPTIONS)],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $isActive = (bool) ($validated['status_aktif'] ?? false);
        $jabatanStrategis = ['Dekan', 'Kaprodi', 'Rektor', 'Wakil Rektor', 'Ketua Prodi'];
        if ($isActive && in_array($validated['jabatan'], $jabatanStrategis, true)) {
            DB::table('jabatan_dosen')
                ->where('jabatan', $validated['jabatan'])
                ->where('status_aktif', true)
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->update(['status_aktif' => false, 'updated_at' => now()]);
        }

        $payload = [
            'dosen_id' => $validated['dosen_id'],
            'jabatan' => $validated['jabatan'],
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'status_aktif' => $isActive,
        ];

        DB::table('jabatan_dosen')->where('id', $id)->update([
            ...$payload,
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Edit jabatan dosen',
            modul: 'master_jabatan_dosen',
            entityType: 'jabatan_dosen',
            entityId: $id,
            konteks: [
                'before' => [
                    'dosen_id' => $row->dosen_id,
                    'jabatan' => $row->jabatan,
                    'periode_mulai' => $row->periode_mulai,
                    'periode_selesai' => $row->periode_selesai,
                    'status_aktif' => $row->status_aktif,
                ],
                'after' => $payload,
            ],
            request: $request
        );

        return back()->with('success', 'Jabatan dosen berhasil diperbarui.');
    }

    public function destroyJabatanDosen(Request $request, int $id)
    {
        $row = DB::table('jabatan_dosen')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $row) {
            return back()->withErrors(['jabatan' => 'Data jabatan dosen tidak ditemukan.']);
        }

        DB::table('jabatan_dosen')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Soft delete jabatan dosen',
            modul: 'master_jabatan_dosen',
            entityType: 'jabatan_dosen',
            entityId: $id,
            konteks: [
                'deleted_data' => [
                    'dosen_id' => $row->dosen_id,
                    'jabatan' => $row->jabatan,
                    'periode_mulai' => $row->periode_mulai,
                    'periode_selesai' => $row->periode_selesai,
                    'status_aktif' => $row->status_aktif,
                ],
            ],
            request: $request
        );

        return back()->with('success', 'Jabatan dosen berhasil dihapus (soft delete).');
    }
}
