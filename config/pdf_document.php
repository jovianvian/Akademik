<?php

return [
    'institution_name' => env('PDF_INSTITUTION_NAME', 'Universitas Contoh Indonesia'),
    'faculty_name' => env('PDF_FACULTY_NAME', 'Fakultas Ilmu Komputer'),
    'address' => env('PDF_INSTITUTION_ADDRESS', 'Jl. Pendidikan No. 1, Kota Contoh'),
    'city' => env('PDF_CITY', 'Jakarta'),
    'phone' => env('PDF_PHONE', '(021) 12345678'),
    'website' => env('PDF_WEBSITE', 'www.kampuscontoh.ac.id'),
    'logo_path' => env('PDF_LOGO_PATH', 'images/logo/logo-icon.svg'),
    'signatures' => [
        'khs' => [
            ['label' => 'Mengetahui', 'jabatan' => 'Kepala Program Studi'],
            ['label' => 'Diverifikasi', 'jabatan' => 'Admin Akademik'],
        ],
        'pembayaran' => [
            ['label' => 'Mengetahui', 'jabatan' => 'Admin Keuangan'],
            ['label' => 'Disetujui', 'jabatan' => 'Kepala Bagian Keuangan'],
        ],
        'audit_logs' => [
            ['label' => 'Diketahui', 'jabatan' => 'Super Admin'],
            ['label' => 'Pemeriksa', 'jabatan' => 'Auditor Internal'],
        ],
    ],
];

