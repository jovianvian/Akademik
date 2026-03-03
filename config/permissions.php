<?php

return [
    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'abilities' => [
                'dashboard.view',
                'krs.view',
                'krs.manage',
                'master.view',
                'master.manage',
                'keuangan.view',
                'keuangan.manage',
                'nilai.manage',
                'jadwal.view',
                'mahasiswa.monitor',
                'users.manage',
                'roles.manage',
                'audit.view',
            ],
        ],
        'admin_akademik' => [
            'label' => 'Admin Akademik',
            'abilities' => [
                'dashboard.view',
                'master.view',
                'master.manage',
                'khs.generate',
            ],
        ],
        'admin_keuangan' => [
            'label' => 'Admin Keuangan',
            'abilities' => [
                'dashboard.view',
                'keuangan.view',
                'keuangan.manage',
            ],
        ],
        'dosen' => [
            'label' => 'Dosen',
            'abilities' => [
                'dashboard.view',
                'nilai.manage',
                'jadwal.view',
                'mahasiswa.monitor',
            ],
        ],
        'mahasiswa' => [
            'label' => 'Mahasiswa',
            'abilities' => [
                'dashboard.view',
                'krs.view',
                'krs.manage',
                'nilai.view',
                'ukt.view',
            ],
        ],
    ],
    'ability_labels' => [
        'dashboard.view' => 'Lihat dashboard',
        'krs.view' => 'Lihat menu KRS',
        'krs.manage' => 'Isi dan simpan KRS',
        'master.view' => 'Lihat data master akademik',
        'master.manage' => 'Kelola data master akademik',
        'khs.generate' => 'Generate/finalisasi KHS',
        'keuangan.view' => 'Lihat data tagihan dan pembayaran',
        'keuangan.manage' => 'Kelola tagihan dan validasi pembayaran',
        'users.manage' => 'Kelola user',
        'roles.manage' => 'Kelola role',
        'nilai.manage' => 'Input nilai mahasiswa',
        'jadwal.view' => 'Lihat jadwal mengajar',
        'nilai.view' => 'Lihat nilai / KHS',
        'ukt.view' => 'Lihat status pembayaran UKT',
        'mahasiswa.monitor' => 'Monitoring mahasiswa bimbingan/perkuliahan',
        'audit.view' => 'Lihat audit log sistem',
    ],
];
