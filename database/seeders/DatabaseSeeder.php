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
        $now = now();

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

        $roleMap = DB::table('roles')->pluck('id', 'role_name');
        $permissionMap = DB::table('permissions')->pluck('id', 'kode');
        $rolePermissions = [];
        foreach (config('permissions.roles', []) as $roleName => $conf) {
            $roleId = $roleMap[$roleName] ?? null;
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

        DB::table('fakultas')->insert([
            'nama_fakultas' => 'Fakultas Ilmu Komputer',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('program_studi')->insert([
            'nama_prodi' => 'Informatika',
            'fakultas_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('tahun_akademik')->insert([
            'tahun' => '2025/2026',
            'semester' => 'genap',
            'status_aktif' => true,
            'krs_dibuka' => true,
            'krs_mulai' => now()->subDays(5),
            'krs_selesai' => now()->addDays(20),
            'created_at' => $now,
            'updated_at' => $now,
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
            'krs_open_at' => now()->subDays(7),
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

        DB::table('users')->insert([
            [
                'role_id' => 1,
                'name' => 'Super Admin',
                'email' => 'superadmin@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 4,
                'name' => 'Dosen Satu',
                'email' => 'dosen1@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 5,
                'name' => 'Mahasiswa Satu',
                'email' => 'mhs1@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 2,
                'name' => 'Admin Akademik',
                'email' => 'adminakademik@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 3,
                'name' => 'Admin Keuangan',
                'email' => 'adminkeuangan@kampus.ac.id',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('dosen')->insert([
            'nidn' => '0123456789',
            'nama' => 'Dosen Satu',
            'prodi_id' => 1,
            'user_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('mahasiswa')->insert([
            'nim' => '23010001',
            'nama' => 'Mahasiswa Satu',
            'prodi_id' => 1,
            'angkatan' => 2023,
            'status_mahasiswa' => 'aktif',
            'status_akademik' => 'aktif',
            'user_id' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('mata_kuliah')->insert([
            ['kode_mk' => 'IF301', 'nama_mk' => 'Pemrograman Web Lanjut', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_mk' => 'IF305', 'nama_mk' => 'Basis Data Lanjut', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_mk' => 'IF307', 'nama_mk' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 6, 'prodi_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_mk' => 'IF311', 'nama_mk' => 'Kecerdasan Buatan', 'sks' => 2, 'semester' => 6, 'prodi_id' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('jadwal')->insert([
            ['mata_kuliah_id' => 1, 'dosen_id' => 1, 'hari' => 'Senin', 'jam_mulai' => '08:00:00', 'jam_selesai' => '10:30:00', 'ruangan' => 'Lab-3', 'tahun_akademik_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['mata_kuliah_id' => 2, 'dosen_id' => 1, 'hari' => 'Selasa', 'jam_mulai' => '10:30:00', 'jam_selesai' => '13:00:00', 'ruangan' => 'R-204', 'tahun_akademik_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['mata_kuliah_id' => 3, 'dosen_id' => 1, 'hari' => 'Rabu', 'jam_mulai' => '08:00:00', 'jam_selesai' => '10:30:00', 'ruangan' => 'R-202', 'tahun_akademik_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['mata_kuliah_id' => 4, 'dosen_id' => 1, 'hari' => 'Kamis', 'jam_mulai' => '13:00:00', 'jam_selesai' => '14:40:00', 'ruangan' => 'R-203', 'tahun_akademik_id' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tagihan_ukt')->insert([
            'mahasiswa_id' => 1,
            'tahun_akademik_id' => 1,
            'jumlah' => 4500000,
            'status' => 'paid',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('krs')->insert([
            'mahasiswa_id' => 1,
            'tahun_akademik_id' => 1,
            'total_sks' => 11,
            'status_krs' => 'final',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('krs_detail')->insert([
            ['krs_id' => 1, 'jadwal_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['krs_id' => 1, 'jadwal_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['krs_id' => 1, 'jadwal_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['krs_id' => 1, 'jadwal_id' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('nilai')->insert([
            ['krs_detail_id' => 1, 'nilai_tugas' => 85, 'nilai_uts' => 84, 'nilai_uas' => 90, 'nilai_kehadiran' => 95, 'nilai_angka' => 88.05, 'nilai_huruf' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['krs_detail_id' => 2, 'nilai_tugas' => 76, 'nilai_uts' => 78, 'nilai_uas' => 79, 'nilai_kehadiran' => 84, 'nilai_angka' => 78.65, 'nilai_huruf' => 'B+', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
