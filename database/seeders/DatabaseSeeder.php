<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ctx = [
            'now' => now(),
            'roles' => [],
            'super_admin_id' => null,
            'dosen_ids' => [],
            'mahasiswa_ids' => [],
            'tagihan_by_mahasiswa' => [],
        ];

        $this->seedRolesAndPermissions($ctx);
        $this->seedMaster($ctx);
        $this->seedUsersAndActors($ctx);
        $this->seedAcademicTransactions($ctx);
        $this->seedAcademicStatus($ctx);
    }

    private function seedRolesAndPermissions(array &$ctx): void
    {
        $now = $ctx['now'];
        DB::table('roles')->insert([
            ['role_name' => 'super_admin', 'created_at' => $now, 'updated_at' => $now],
            ['role_name' => 'admin_akademik', 'created_at' => $now, 'updated_at' => $now],
            ['role_name' => 'admin_keuangan', 'created_at' => $now, 'updated_at' => $now],
            ['role_name' => 'dosen', 'created_at' => $now, 'updated_at' => $now],
            ['role_name' => 'mahasiswa', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $abilityLabels = config('permissions.ability_labels', []);
        $permissionRows = collect($abilityLabels)->map(fn ($label, $kode) => [
            'kode' => $kode,
            'nama' => $label,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->all();
        DB::table('permissions')->insert($permissionRows);

        $ctx['roles'] = DB::table('roles')->pluck('id', 'role_name')->all();
        $permissionMap = DB::table('permissions')->pluck('id', 'kode');
        $rolePermissions = [];
        foreach (config('permissions.roles', []) as $roleName => $conf) {
            $roleId = $ctx['roles'][$roleName] ?? null;
            if (! $roleId) {
                continue;
            }
            foreach (($conf['abilities'] ?? []) as $ability) {
                $permissionId = $permissionMap[$ability] ?? null;
                if (! $permissionId) {
                    continue;
                }
                $rolePermissions[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        if (! empty($rolePermissions)) {
            DB::table('role_permissions')->insert($rolePermissions);
        }
    }

    private function seedMaster(array &$ctx): void
    {
        $now = $ctx['now'];
        DB::table('fakultas')->insert([
            ['id' => 1, 'nama_fakultas' => 'Fakultas Ilmu Komputer', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_fakultas' => 'Fakultas Teknik', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('program_studi')->insert([
            ['id' => 1, 'nama_prodi' => 'Informatika', 'fakultas_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_prodi' => 'Sistem Informasi', 'fakultas_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_prodi' => 'Manajemen', 'fakultas_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_prodi' => 'Teknik Sipil', 'fakultas_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tahun_akademik')->insert([
            [
                'id' => 1,
                'tahun' => '2025/2026',
                'semester' => 'ganjil',
                'status_aktif' => false,
                'krs_dibuka' => false,
                'krs_mulai' => now()->subMonths(5),
                'krs_selesai' => now()->subMonths(4),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'tahun' => '2025/2026',
                'semester' => 'genap',
                'status_aktif' => true,
                'krs_dibuka' => true,
                'krs_mulai' => now()->subDays(10),
                'krs_selesai' => now()->addDays(21),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('academic_settings')->insert([
            'max_sks_default' => 24,
            'max_sks_ips_3' => 24,
            'grade_a_min' => 85,
            'grade_a_minus_min' => 80,
            'grade_b_plus_min' => 75,
            'grade_b_min' => 70,
            'grade_b_minus_min' => 65,
            'grade_c_plus_min' => 60,
            'grade_c_min' => 55,
            'grade_d_min' => 45,
            'krs_open_at' => now()->subDays(15),
            'krs_close_at' => now()->addDays(30),
            'nilai_input_close_at' => now()->addDays(45),
            'maintenance_mode' => false,
            'evaluasi_enabled' => true,
            'auto_nonaktif_if_ukt_unpaid' => true,
            'bobot_tugas' => 30,
            'bobot_uts' => 25,
            'bobot_uas' => 35,
            'bobot_kehadiran' => 10,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedUsersAndActors(array &$ctx): void
    {
        $now = $ctx['now'];
        $roles = $ctx['roles'];

        $ctx['super_admin_id'] = DB::table('users')->insertGetId([
            'role_id' => $roles['super_admin'],
            'name' => 'Super Admin',
            'email' => 'superadmin@kampus.ac.id',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users')->insert([
            [
                'role_id' => $roles['admin_akademik'],
                'name' => 'Admin Akademik',
                'email' => 'adminakademik@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => $roles['admin_keuangan'],
                'name' => 'Admin Keuangan',
                'email' => 'adminkeuangan@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $dosenUsers = [];
        for ($i = 1; $i <= 15; $i++) {
            $dosenUsers[] = DB::table('users')->insertGetId([
                'role_id' => $roles['dosen'],
                'name' => 'Dosen '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'email' => 'dosen'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $mahasiswaUsers = [];
        for ($i = 1; $i <= 25; $i++) {
            $mahasiswaUsers[] = DB::table('users')->insertGetId([
                'role_id' => $roles['mahasiswa'],
                'name' => 'Mahasiswa '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'email' => 'mhs'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $dosenIds = [];
        foreach ($dosenUsers as $index => $userId) {
            $num = $index + 1;
            $dosenIds[] = DB::table('dosen')->insertGetId([
                'nidn' => '1987'.str_pad((string) $num, 6, '0', STR_PAD_LEFT),
                'nama' => 'Dosen '.str_pad((string) $num, 2, '0', STR_PAD_LEFT),
                'prodi_id' => ($index % 4) + 1,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $ctx['dosen_ids'] = $dosenIds;

        $jabatanMap = [
            1 => 'Rektor',
            2 => 'Wakil Rektor',
            3 => 'Dekan',
            4 => 'Wakil Dekan',
            5 => 'Kaprodi',
            6 => 'Ketua Prodi',
            7 => 'Sekretaris Prodi',
            8 => 'Dosen Pembimbing Akademik',
            9 => 'Dosen Pembimbing Akademik',
            10 => 'Koordinator Mata Kuliah',
            11 => 'Dosen Pengampu',
            12 => 'Dosen Pengampu',
            13 => 'Dosen Pengampu',
            14 => 'Dosen Pengampu',
            15 => 'Dosen Pengampu',
        ];
        foreach ($dosenIds as $index => $dosenId) {
            $num = $index + 1;
            DB::table('jabatan_dosen')->insert([
                'dosen_id' => $dosenId,
                'jabatan' => $jabatanMap[$num],
                'periode_mulai' => '2025-08-01',
                'periode_selesai' => null,
                'status_aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('jabatan_dosen')->insert([
            'dosen_id' => $dosenIds[4],
            'jabatan' => 'Kaprodi',
            'periode_mulai' => '2024-08-01',
            'periode_selesai' => '2025-07-31',
            'status_aktif' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $mahasiswaIds = [];
        for ($i = 1; $i <= 25; $i++) {
            $statusMahasiswa = match (true) {
                $i % 12 === 0 => 'lulus',
                $i % 10 === 0 => 'dropout',
                $i % 7 === 0 => 'cuti',
                default => 'aktif',
            };
            $statusAkademik = match ($statusMahasiswa) {
                'aktif' => ($i % 8 === 0 ? 'suspended_pending_decision' : 'aktif'),
                'cuti' => 'nonaktif',
                'lulus' => 'alumni',
                default => 'do',
            };
            $enrollment = $statusMahasiswa === 'dropout' ? 'do' : $statusMahasiswa;

            $mahasiswaIds[] = DB::table('mahasiswa')->insertGetId([
                'nim' => '23'.str_pad((string) (10000 + $i), 6, '0', STR_PAD_LEFT),
                'nama' => 'Mahasiswa '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'prodi_id' => (($i - 1) % 4) + 1,
                'angkatan' => 2022 + ($i % 4),
                'status_mahasiswa' => $statusMahasiswa,
                'status_akademik' => $statusAkademik,
                'enrollment_status_current' => $enrollment,
                'global_hold' => $i % 11 === 0,
                'global_hold_reason' => $i % 11 === 0 ? 'Hold finansial menunggu verifikasi.' : null,
                'user_id' => $mahasiswaUsers[$i - 1],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $ctx['mahasiswa_ids'] = $mahasiswaIds;

        $dosenPaIds = [$dosenIds[7], $dosenIds[8]];
        foreach ($mahasiswaIds as $index => $mahasiswaId) {
            DB::table('dosen_pa_mahasiswa')->insert([
                'dosen_id' => $dosenPaIds[$index % 2],
                'mahasiswa_id' => $mahasiswaId,
                'periode_mulai' => '2025-08-01',
                'periode_selesai' => null,
                'status_aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedAcademicTransactions(array &$ctx): void
    {
        $now = $ctx['now'];
        $dosenIds = $ctx['dosen_ids'];
        $mahasiswaIds = $ctx['mahasiswa_ids'];

        $mkRows = [
            ['kode_mk' => 'IF301', 'nama_mk' => 'Pemrograman Web Lanjut', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1],
            ['kode_mk' => 'IF305', 'nama_mk' => 'Basis Data Lanjut', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1],
            ['kode_mk' => 'IF307', 'nama_mk' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1],
            ['kode_mk' => 'IF311', 'nama_mk' => 'Kecerdasan Buatan', 'sks' => 2, 'semester' => 6, 'prodi_id' => 1],
            ['kode_mk' => 'SI301', 'nama_mk' => 'Manajemen Proyek SI', 'sks' => 3, 'semester' => 6, 'prodi_id' => 2],
            ['kode_mk' => 'SI305', 'nama_mk' => 'Audit Sistem Informasi', 'sks' => 3, 'semester' => 6, 'prodi_id' => 2],
            ['kode_mk' => 'SI307', 'nama_mk' => 'Analitik Bisnis', 'sks' => 2, 'semester' => 6, 'prodi_id' => 2],
            ['kode_mk' => 'MN301', 'nama_mk' => 'Manajemen Keuangan', 'sks' => 3, 'semester' => 6, 'prodi_id' => 3],
            ['kode_mk' => 'MN305', 'nama_mk' => 'Manajemen SDM', 'sks' => 3, 'semester' => 6, 'prodi_id' => 3],
            ['kode_mk' => 'MN307', 'nama_mk' => 'Perilaku Organisasi', 'sks' => 2, 'semester' => 6, 'prodi_id' => 3],
            ['kode_mk' => 'TS301', 'nama_mk' => 'Mekanika Tanah', 'sks' => 3, 'semester' => 6, 'prodi_id' => 4],
            ['kode_mk' => 'TS305', 'nama_mk' => 'Manajemen Konstruksi', 'sks' => 3, 'semester' => 6, 'prodi_id' => 4],
        ];
        foreach ($mkRows as $mk) {
            DB::table('mata_kuliah')->insert([...$mk, 'created_at' => $now, 'updated_at' => $now]);
        }

        $mataKuliah = DB::table('mata_kuliah')->orderBy('id')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamList = [['08:00:00', '10:30:00'], ['10:30:00', '13:00:00'], ['13:00:00', '15:30:00'], ['15:30:00', '18:00:00']];
        foreach ($mataKuliah as $index => $mk) {
            $slot = $jamList[$index % count($jamList)];
            DB::table('jadwal')->insert([
                'mata_kuliah_id' => $mk->id,
                'dosen_id' => $dosenIds[($index % 8) + 7],
                'hari' => $hariList[$index % count($hariList)],
                'jam_mulai' => $slot[0],
                'jam_selesai' => $slot[1],
                'ruangan' => 'R-'.str_pad((string) (201 + $index), 3, '0', STR_PAD_LEFT),
                'tahun_akademik_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $statusCycle = ['open', 'partial', 'paid', 'paid', 'open', 'partial', 'disputed', 'void', 'paid', 'open'];
        $metodeList = ['transfer', 'va', 'qris', 'cash'];
        $tagihanByMahasiswa = [];
        foreach ($mahasiswaIds as $index => $mahasiswaId) {
            $status = $statusCycle[$index % count($statusCycle)];
            $jumlah = 3500000 + (($index % 5) * 500000);
            $tagihanId = DB::table('tagihan_ukt')->insertGetId([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => 2,
                'jumlah' => $jumlah,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $tagihanByMahasiswa[$mahasiswaId] = ['tagihan_id' => $tagihanId, 'status' => $status];
            if ($status === 'paid') {
                $first = (int) ($jumlah * 0.6);
                DB::table('pembayaran')->insert([
                    [
                        'tagihan_id' => $tagihanId,
                        'tanggal_bayar' => now()->subDays(20 - ($index % 7))->toDateString(),
                        'jumlah_bayar' => $first,
                        'metode_bayar' => $metodeList[$index % count($metodeList)],
                        'bukti_bayar' => 'TRX-PD1-'.$tagihanId,
                        'is_reconciliation_error' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'tagihan_id' => $tagihanId,
                        'tanggal_bayar' => now()->subDays(8 - ($index % 3))->toDateString(),
                        'jumlah_bayar' => $jumlah - $first,
                        'metode_bayar' => $metodeList[($index + 1) % count($metodeList)],
                        'bukti_bayar' => 'TRX-PD2-'.$tagihanId,
                        'is_reconciliation_error' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ]);
            }
            if ($status === 'partial') {
                DB::table('pembayaran')->insert([
                    'tagihan_id' => $tagihanId,
                    'tanggal_bayar' => now()->subDays(14 - ($index % 5))->toDateString(),
                    'jumlah_bayar' => (int) ($jumlah * 0.5),
                    'metode_bayar' => $metodeList[$index % count($metodeList)],
                    'bukti_bayar' => 'TRX-PRT-'.$tagihanId,
                    'is_reconciliation_error' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            if ($status === 'disputed') {
                DB::table('pembayaran')->insert([
                    'tagihan_id' => $tagihanId,
                    'tanggal_bayar' => now()->subDays(11)->toDateString(),
                    'jumlah_bayar' => (int) ($jumlah * 0.4),
                    'metode_bayar' => 'transfer',
                    'bukti_bayar' => 'TRX-DSP-'.$tagihanId,
                    'is_reconciliation_error' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        $ctx['tagihan_by_mahasiswa'] = $tagihanByMahasiswa;

        $jadwalByProdi = DB::table('jadwal as j')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->select('j.id', 'mk.prodi_id', 'mk.sks')
            ->orderBy('j.id')
            ->get()
            ->groupBy('prodi_id');

        $krsDetailIds = [];
        foreach ($mahasiswaIds as $mahasiswaId) {
            $mhs = DB::table('mahasiswa')->where('id', $mahasiswaId)->first();
            if (! $mhs) {
                continue;
            }
            $paid = ($tagihanByMahasiswa[$mahasiswaId]['status'] ?? 'open') === 'paid';
            $krsStatus = $paid && $mhs->status_akademik === 'aktif' ? 'final' : 'draft';

            $krsId = DB::table('krs')->insertGetId([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => 2,
                'total_sks' => 0,
                'status_krs' => $krsStatus,
                'nilai_terkunci' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $jadwalCandidates = collect($jadwalByProdi[$mhs->prodi_id] ?? [])->take(3);
            $totalSks = 0;
            foreach ($jadwalCandidates as $jadwal) {
                $detailId = DB::table('krs_detail')->insertGetId([
                    'krs_id' => $krsId,
                    'jadwal_id' => $jadwal->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $krsDetailIds[] = ['id' => $detailId, 'final' => $krsStatus === 'final'];
                $totalSks += (int) $jadwal->sks;
            }
            DB::table('krs')->where('id', $krsId)->update(['total_sks' => $totalSks, 'updated_at' => $now]);
        }

        foreach ($krsDetailIds as $index => $row) {
            if (! $row['final'] || $index % 4 === 0) {
                continue;
            }
            $tugas = 68 + ($index % 25);
            $uts = 65 + ($index % 30);
            $uas = 70 + ($index % 25);
            $hadir = 80 + ($index % 20);
            $angka = round(($tugas * 0.3) + ($uts * 0.25) + ($uas * 0.35) + ($hadir * 0.1), 2);
            DB::table('nilai')->insert([
                'krs_detail_id' => $row['id'],
                'nilai_tugas' => $tugas,
                'nilai_uts' => $uts,
                'nilai_uas' => $uas,
                'nilai_kehadiran' => $hadir,
                'nilai_angka' => $angka,
                'nilai_huruf' => $this->toHuruf($angka),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $evaluasi = DB::table('krs_detail as kd')
            ->join('krs as k', 'k.id', '=', 'kd.krs_id')
            ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'j.mata_kuliah_id')
            ->where('k.status_krs', 'final')
            ->select('kd.id as krs_detail_id', 'k.mahasiswa_id', 'j.dosen_id', 'mk.id as mata_kuliah_id', 'k.tahun_akademik_id')
            ->limit(20)
            ->get();
        foreach ($evaluasi as $index => $item) {
            DB::table('evaluasi_dosen')->insert([
                'krs_detail_id' => $item->krs_detail_id,
                'mahasiswa_id' => $item->mahasiswa_id,
                'dosen_id' => $item->dosen_id,
                'mata_kuliah_id' => $item->mata_kuliah_id,
                'tahun_akademik_id' => $item->tahun_akademik_id,
                'status_selesai' => true,
                'nilai_1' => 3 + ($index % 3),
                'nilai_2' => 3 + (($index + 1) % 3),
                'nilai_3' => 3 + (($index + 2) % 3),
                'komentar' => 'Pembelajaran berjalan baik, materi jelas dan terstruktur.',
                'submitted_at' => now()->subDays($index % 10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedAcademicStatus(array &$ctx): void
    {
        $now = $ctx['now'];
        foreach ($ctx['mahasiswa_ids'] as $index => $mahasiswaId) {
            $eligibility = $index % 9 === 0 ? 'suspended_pending_decision' : ($index % 7 === 0 ? 'suspended' : 'eligible');
            DB::table('mahasiswa_period_status')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'tahun_akademik_id' => 2,
                'eligibility_status' => $eligibility,
                'reason' => 'Seeder default eligibility.',
                'effective_from' => now()->subDays(14),
                'effective_until' => null,
                'set_by' => $ctx['super_admin_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            DB::table('mahasiswa_enrollment_history')->insert([
                'mahasiswa_id' => $mahasiswaId,
                'status' => DB::table('mahasiswa')->where('id', $mahasiswaId)->value('enrollment_status_current'),
                'reason' => 'Seeder initial enrollment status.',
                'effective_from' => now()->subMonths(6),
                'effective_until' => null,
                'set_by' => $ctx['super_admin_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function toHuruf(float $angka): string
    {
        return match (true) {
            $angka >= 85 => 'A',
            $angka >= 80 => 'A-',
            $angka >= 75 => 'B+',
            $angka >= 70 => 'B',
            $angka >= 65 => 'B-',
            $angka >= 60 => 'C+',
            $angka >= 55 => 'C',
            $angka >= 45 => 'D',
            default => 'E',
        };
    }
}
