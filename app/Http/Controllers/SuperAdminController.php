<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\AcademicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    private array $recoveryTables = [
        'fakultas' => 'nama_fakultas',
        'program_studi' => 'nama_prodi',
        'mata_kuliah' => 'nama_mk',
        'jadwal' => 'ruangan',
        'jabatan_dosen' => 'jabatan',
    ];

    public function users()
    {
        $sortBy = request()->query('sort_by', 'name');
        $sortDir = strtolower(request()->query('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $q = request()->query('q');
        $sortMap = [
            'name' => 'u.name',
            'email' => 'u.email',
            'role' => 'r.role_name',
            'status' => 'u.status',
        ];
        $sortColumn = $sortMap[$sortBy] ?? $sortMap['name'];

        $query = DB::table('users as u')
            ->leftJoin('roles as r', 'r.id', '=', 'u.role_id')
            ->select('u.id', 'u.name', 'u.email', 'u.status', 'r.role_name');

        if ($q) {
            $query->where(function ($inner) use ($q) {
                $inner->where('u.name', 'like', "%{$q}%")
                    ->orWhere('u.email', 'like', "%{$q}%")
                    ->orWhere('r.role_name', 'like', "%{$q}%");
            });
        }

        return view('super-admin.users', [
            'title' => 'Kelola User',
            'items' => $query->orderBy($sortColumn, $sortDir)->orderBy('u.id')->paginate(15)->withQueryString(),
            'roles' => DB::table('roles')->orderBy('role_name')->get(),
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'q' => $q,
        ]);
    }

    public function updateUser(Request $request, int $id)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        DB::table('users')->where('id', $id)->update([
            'role_id' => $validated['role_id'],
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Update role/status user',
            modul: 'super_admin',
            entityType: 'users',
            entityId: $id,
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function rolePermissions()
    {
        $roles = DB::table('roles')->orderBy('role_name')->get();
        $permissions = DB::table('permissions')->orderBy('kode')->get();
        $matrix = DB::table('role_permissions')
            ->select('role_id', 'permission_id')
            ->get()
            ->groupBy('role_id')
            ->map(fn ($rows) => $rows->pluck('permission_id')->all());

        return view('super-admin.role-permissions', [
            'title' => 'Hak Akses Role',
            'roles' => $roles,
            'permissions' => $permissions,
            'matrix' => $matrix,
        ]);
    }

    public function updateRolePermissions(Request $request, int $roleId)
    {
        $validated = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $selected = collect($validated['permission_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        DB::transaction(function () use ($roleId, $selected) {
            DB::table('role_permissions')->where('role_id', $roleId)->delete();
            if ($selected->isNotEmpty()) {
                $rows = $selected->map(fn ($permissionId) => [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();
                DB::table('role_permissions')->insert($rows);
            }
        });

        AuditLogger::log(
            aksi: 'Update hak akses role',
            modul: 'super_admin',
            entityType: 'roles',
            entityId: $roleId,
            konteks: ['permission_ids' => $selected->all()],
            request: $request
        );

        return back()->with('success', 'Hak akses role berhasil diperbarui.');
    }

    public function systemSettings()
    {
        $settings = AcademicSetting::row();

        return view('super-admin.system-settings', [
            'title' => 'System Configuration',
            'settings' => $settings,
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'max_sks_default' => ['required', 'integer', 'min:1', 'max:30'],
            'max_sks_ips_3' => ['required', 'integer', 'min:1', 'max:30'],
            'evaluasi_enabled' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'auto_nonaktif_if_ukt_unpaid' => ['nullable', 'boolean'],
            'krs_open_at' => ['nullable', 'date'],
            'krs_close_at' => ['nullable', 'date', 'after:krs_open_at'],
            'nilai_input_close_at' => ['nullable', 'date'],
            'bobot_tugas' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_uts' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_uas' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_kehadiran' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_a_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_a_minus_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_b_plus_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_b_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_b_minus_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_c_plus_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_c_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_d_min' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $totalBobot = (float) $validated['bobot_tugas'] + (float) $validated['bobot_uts'] + (float) $validated['bobot_uas'] + (float) $validated['bobot_kehadiran'];
        if (abs($totalBobot - 100) > 0.0001) {
            return back()->withErrors(['settings' => 'Total bobot nilai harus tepat 100%.'])->withInput();
        }

        $settings = AcademicSetting::row();
        DB::table('academic_settings')->where('id', $settings->id)->update([
            'max_sks_default' => (int) $validated['max_sks_default'],
            'max_sks_ips_3' => (int) $validated['max_sks_ips_3'],
            'grade_a_min' => (float) $validated['grade_a_min'],
            'grade_a_minus_min' => (float) $validated['grade_a_minus_min'],
            'grade_b_plus_min' => (float) $validated['grade_b_plus_min'],
            'grade_b_min' => (float) $validated['grade_b_min'],
            'grade_b_minus_min' => (float) $validated['grade_b_minus_min'],
            'grade_c_plus_min' => (float) $validated['grade_c_plus_min'],
            'grade_c_min' => (float) $validated['grade_c_min'],
            'grade_d_min' => (float) $validated['grade_d_min'],
            'krs_open_at' => $validated['krs_open_at'] ?? null,
            'krs_close_at' => $validated['krs_close_at'] ?? null,
            'nilai_input_close_at' => $validated['nilai_input_close_at'] ?? null,
            'maintenance_mode' => isset($validated['maintenance_mode']),
            'evaluasi_enabled' => isset($validated['evaluasi_enabled']),
            'auto_nonaktif_if_ukt_unpaid' => isset($validated['auto_nonaktif_if_ukt_unpaid']),
            'bobot_tugas' => (float) $validated['bobot_tugas'],
            'bobot_uts' => (float) $validated['bobot_uts'],
            'bobot_uas' => (float) $validated['bobot_uas'],
            'bobot_kehadiran' => (float) $validated['bobot_kehadiran'],
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);
        AcademicSetting::refresh();

        AuditLogger::log(
            aksi: 'Update system configuration',
            modul: 'super_admin',
            entityType: 'academic_settings',
            entityId: $settings->id,
            konteks: ['updated' => true],
            request: $request
        );

        return back()->with('success', 'System configuration berhasil diperbarui.');
    }

    public function masterRecovery()
    {
        $deletedRows = collect();
        foreach ($this->recoveryTables as $table => $labelColumn) {
            $query = DB::table("{$table} as t")
                ->leftJoin('users as u', 'u.id', '=', 't.deleted_by')
                ->whereNotNull('t.deleted_at')
                ->select(
                    DB::raw("'{$table}' as table_name"),
                    't.id',
                    DB::raw("COALESCE(CAST(t.{$labelColumn} AS CHAR), '-') as label"),
                    't.deleted_at',
                    'u.name as deleted_by_name'
                );
            $deletedRows = $deletedRows->merge($query->get());
        }
        $deletedRows = $deletedRows->sortByDesc('deleted_at')->values();

        $editDeleteLogs = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->whereIn('a.aksi', [
                'Edit fakultas',
                'Edit program studi',
                'Edit mata kuliah',
                'Edit jadwal',
                'Edit jabatan dosen',
                'Soft delete fakultas',
                'Soft delete program studi',
                'Soft delete mata kuliah',
                'Soft delete jadwal',
                'Soft delete jabatan dosen',
            ])
            ->orderByDesc('a.id')
            ->limit(200)
            ->select('a.*', 'u.name as user_name')
            ->get();

        return view('super-admin.master-recovery', [
            'title' => 'Master Recovery & Change Log',
            'deletedRows' => $deletedRows,
            'editDeleteLogs' => $editDeleteLogs,
        ]);
    }

    public function restoreMasterData(Request $request)
    {
        $validated = $request->validate([
            'table_name' => ['required', 'string'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $table = $validated['table_name'];
        if (! array_key_exists($table, $this->recoveryTables)) {
            return back()->withErrors(['restore' => 'Table recovery tidak diizinkan.']);
        }

        $row = DB::table($table)->where('id', $validated['id'])->first();
        if (! $row) {
            return back()->withErrors(['restore' => 'Data tidak ditemukan.']);
        }

        DB::table($table)->where('id', $validated['id'])->update([
            'deleted_at' => null,
            'deleted_by' => null,
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            aksi: 'Restore master data',
            modul: 'super_admin',
            entityType: $table,
            entityId: $validated['id'],
            konteks: ['table_name' => $table],
            request: $request
        );

        return back()->with('success', 'Data berhasil dikembalikan.');
    }
}
