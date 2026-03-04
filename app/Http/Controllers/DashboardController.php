<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleName = $user?->role?->role_name;
        $abilities = $user?->abilities() ?? [];
        $abilityLabels = config('permissions.ability_labels', []);
        $abilityItems = collect($abilities)
            ->map(fn (string $ability) => $abilityLabels[$ability] ?? $ability)
            ->values()
            ->all();

        $mahasiswaAktif = DB::table('mahasiswa')->where('status_mahasiswa', 'aktif')->count();
        $dosenAktif = DB::table('dosen')->count();
        $mkDitawarkan = DB::table('jadwal')->whereNull('deleted_at')->distinct('mata_kuliah_id')->count('mata_kuliah_id');
        $totalTagihan = DB::table('tagihan_ukt')->count();
        $tagihanLunas = DB::table('tagihan_ukt')->where('status', 'paid')->count();
        $persenLunas = $totalTagihan > 0 ? round(($tagihanLunas / $totalTagihan) * 100) : 0;
        $tahunAktif = DB::table('tahun_akademik')->where('status_aktif', true)->first();
        $krsStatus = DB::table('krs')
            ->select('status_krs', DB::raw('COUNT(*) as total'))
            ->groupBy('status_krs')
            ->pluck('total', 'status_krs');

        $months = collect(range(5, 0))->map(fn (int $offset) => now()->subMonths($offset)->format('Y-m'))
            ->push(now()->format('Y-m'))
            ->values();

        $paymentRaw = DB::table('pembayaran')
            ->whereDate('tanggal_bayar', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->select('tanggal_bayar', 'jumlah_bayar')
            ->orderBy('tanggal_bayar')
            ->get();

        $paymentMap = [];
        foreach ($months as $month) {
            $paymentMap[$month] = 0;
        }
        foreach ($paymentRaw as $payment) {
            $key = substr((string) $payment->tanggal_bayar, 0, 7);
            if (isset($paymentMap[$key])) {
                $paymentMap[$key] += (float) $payment->jumlah_bayar;
            }
        }

        $paymentSeries = collect($paymentMap)->map(fn ($total, $month) => [
            'month' => $month,
            'label' => date('M y', strtotime($month.'-01')),
            'total' => (float) $total,
        ])->values()->all();

        $roleCharts = [];

        if ($roleName === 'super_admin') {
            $roleCharts['userByRole'] = DB::table('roles as r')
                ->leftJoin('users as u', 'u.role_id', '=', 'r.id')
                ->groupBy('r.role_name')
                ->orderBy('r.role_name')
                ->select('r.role_name as label', DB::raw('COUNT(u.id) as total'))
                ->get();

            $roleCharts['auditByModule'] = DB::table('audit_logs')
                ->select('modul as label', DB::raw('COUNT(*) as total'))
                ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
                ->groupBy('modul')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        }

        if ($roleName === 'admin_akademik') {
            $roleCharts['statusMahasiswa'] = DB::table('mahasiswa')
                ->select('status_akademik as label', DB::raw('COUNT(*) as total'))
                ->groupBy('status_akademik')
                ->orderBy('status_akademik')
                ->get();

            $roleCharts['jabatanDosen'] = DB::table('jabatan_dosen')
                ->select('jabatan as label', DB::raw('COUNT(*) as total'))
                ->where('status_aktif', true)
                ->whereNull('deleted_at')
                ->groupBy('jabatan')
                ->orderByDesc('total')
                ->get();

            $roleCharts['krsFinalPerProdi'] = DB::table('krs as k')
                ->join('mahasiswa as m', 'm.id', '=', 'k.mahasiswa_id')
                ->join('program_studi as p', 'p.id', '=', 'm.prodi_id')
                ->select('p.nama_prodi as label', DB::raw('SUM(CASE WHEN k.status_krs = "final" THEN 1 ELSE 0 END) as final_total'))
                ->groupBy('p.nama_prodi')
                ->orderByDesc('final_total')
                ->get();
        }

        if ($roleName === 'dosen') {
            $dosenId = DB::table('dosen')->where('user_id', $user?->id)->value('id');
            $roleCharts['kelasByHari'] = collect();
            $roleCharts['nilaiCompleteness'] = ['terisi' => 0, 'belum' => 0];

            if ($dosenId) {
                $roleCharts['kelasByHari'] = DB::table('jadwal')
                    ->select('hari as label', DB::raw('COUNT(*) as total'))
                    ->where('dosen_id', $dosenId)
                    ->groupBy('hari')
                    ->get();

                $nilaiStats = DB::table('krs_detail as kd')
                    ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
                    ->leftJoin('nilai as n', 'n.krs_detail_id', '=', 'kd.id')
                    ->where('j.dosen_id', $dosenId)
                    ->select(
                        DB::raw('SUM(CASE WHEN n.id IS NOT NULL THEN 1 ELSE 0 END) as terisi'),
                        DB::raw('SUM(CASE WHEN n.id IS NULL THEN 1 ELSE 0 END) as belum')
                    )
                    ->first();

                $roleCharts['nilaiCompleteness'] = [
                    'terisi' => (int) ($nilaiStats->terisi ?? 0),
                    'belum' => (int) ($nilaiStats->belum ?? 0),
                ];
            }
        }

        if ($roleName === 'admin_keuangan') {
            if ($tahunAktif) {
                $roleCharts['statusTagihanAktif'] = DB::table('tagihan_ukt')
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->select('status as label', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->orderBy('status')
                    ->get();
            } else {
                $roleCharts['statusTagihanAktif'] = collect();
            }
        }

        $operationalCards = [];
        $cards = [
            ['label' => 'Mahasiswa Aktif', 'value' => number_format($mahasiswaAktif), 'hint' => 'Data mahasiswa status aktif'],
            ['label' => 'Dosen Aktif', 'value' => number_format($dosenAktif), 'hint' => 'Data dosen terdaftar'],
            ['label' => 'Mata Kuliah Ditawarkan', 'value' => number_format($mkDitawarkan), 'hint' => 'Dari jadwal semester aktif'],
            ['label' => 'Tagihan UKT Lunas', 'value' => $persenLunas.'%', 'hint' => $tagihanLunas.' dari '.$totalTagihan.' tagihan'],
        ];

        if ($roleName === 'mahasiswa') {
            $mahasiswa = DB::table('mahasiswa')->where('user_id', $user?->id)->first();
            $krsAktif = null;
            $uktStatus = 'Belum ada';
            $notifikasi = [];

            if ($mahasiswa && $tahunAktif) {
                $krsAktif = DB::table('krs')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->first();
                $uktStatus = (string) DB::table('tagihan_ukt')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->value('status');
                if ($uktStatus === '') {
                    $uktStatus = 'Belum ada';
                }

                if (($krsAktif?->status_krs ?? null) !== 'final') {
                    $notifikasi[] = 'KRS semester aktif belum final.';
                }
                if ($uktStatus !== 'paid') {
                    $notifikasi[] = 'Tagihan UKT semester aktif belum lunas.';
                }
                if ($mahasiswa->status_akademik !== 'aktif') {
                    $notifikasi[] = 'Status akademik Anda saat ini '.strtoupper($mahasiswa->status_akademik).'.';
                }
            }

            $cards = [
                ['label' => 'Status KRS', 'value' => strtoupper((string) ($krsAktif?->status_krs ?? 'belum ada')), 'hint' => 'Semester aktif'],
                ['label' => 'Total SKS', 'value' => number_format((int) ($krsAktif?->total_sks ?? 0)), 'hint' => 'KRS semester aktif'],
                ['label' => 'Status UKT', 'value' => strtoupper($uktStatus), 'hint' => 'Tagihan semester aktif'],
                ['label' => 'Notifikasi', 'value' => (string) count($notifikasi), 'hint' => 'Informasi penting akademik'],
            ];

        }

        if ($roleName === 'dosen') {
            $dosenId = DB::table('dosen')->where('user_id', $user?->id)->value('id');
            $kelasAktif = 0;
            $kelasHariIni = 0;
            $totalMahasiswa = 0;

            if ($dosenId && $tahunAktif) {
                $kelasAktif = (int) DB::table('jadwal')
                    ->where('dosen_id', $dosenId)
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->count();
                $kelasHariIni = (int) DB::table('jadwal')
                    ->where('dosen_id', $dosenId)
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->where('hari', $this->hariIni())
                    ->count();
                $totalMahasiswa = (int) DB::table('krs_detail as kd')
                    ->join('krs as k', 'k.id', '=', 'kd.krs_id')
                    ->join('jadwal as j', 'j.id', '=', 'kd.jadwal_id')
                    ->where('j.dosen_id', $dosenId)
                    ->where('k.tahun_akademik_id', $tahunAktif->id)
                    ->distinct('k.mahasiswa_id')
                    ->count('k.mahasiswa_id');
            }

            $cards = [
                ['label' => 'Total Kelas Aktif', 'value' => number_format($kelasAktif), 'hint' => 'Semester aktif'],
                ['label' => 'Total Mahasiswa', 'value' => number_format($totalMahasiswa), 'hint' => 'Mahasiswa pada kelas Anda'],
                ['label' => 'Kelas Hari Ini', 'value' => number_format($kelasHariIni), 'hint' => 'Berdasarkan hari server'],
                ['label' => 'Tahun Akademik', 'value' => $tahunAktif ? ($tahunAktif->tahun.' '.ucfirst($tahunAktif->semester)) : '-', 'hint' => 'Periode aktif'],
            ];
        }

        if ($roleName === 'admin_akademik') {
            $nilaiTotal = DB::table('krs_detail')->count();
            $nilaiTerisi = DB::table('nilai')->count();
            $operationalCards = [
                [
                    'title' => 'KRS Draft Menunggu Finalisasi',
                    'value' => DB::table('krs')->where('status_krs', 'draft')->count(),
                    'link' => route('akademik.generate-khs.index'),
                ],
                [
                    'title' => 'Mahasiswa Nonaktif',
                    'value' => DB::table('mahasiswa')->where('status_akademik', 'nonaktif')->count(),
                    'link' => route('akademik.mahasiswa-status.index'),
                ],
                [
                    'title' => 'Jabatan Berakhir <= 30 Hari',
                    'value' => DB::table('jabatan_dosen')
                        ->where('status_aktif', true)
                        ->whereNotNull('periode_selesai')
                        ->whereBetween('periode_selesai', [now()->toDateString(), now()->addDays(30)->toDateString()])
                        ->count(),
                    'link' => route('master.jabatan-dosen.index'),
                ],
            ];

            $cards = [
                ['label' => 'Tahun Akademik Aktif', 'value' => $tahunAktif ? ($tahunAktif->tahun.' '.ucfirst($tahunAktif->semester)) : '-', 'hint' => 'Periode aktif'],
                ['label' => 'Mahasiswa Aktif', 'value' => number_format(DB::table('mahasiswa')->where('status_akademik', 'aktif')->count()), 'hint' => 'Status akademik aktif'],
                ['label' => 'KRS Final', 'value' => number_format(DB::table('krs')->where('status_krs', 'final')->count()), 'hint' => 'Total KRS final'],
                ['label' => 'Kelengkapan Nilai', 'value' => $nilaiTotal > 0 ? round(($nilaiTerisi / $nilaiTotal) * 100).'%' : '0%', 'hint' => $nilaiTerisi.' dari '.$nilaiTotal.' detail KRS'],
            ];
        }

        if ($roleName === 'admin_keuangan') {
            $totalTagihanAktif = 0;
            $totalLunasAktif = 0;
            $totalBelumAktif = 0;
            if ($tahunAktif) {
                $totalTagihanAktif = (int) DB::table('tagihan_ukt')->where('tahun_akademik_id', $tahunAktif->id)->count();
                $totalLunasAktif = (int) DB::table('tagihan_ukt')->where('tahun_akademik_id', $tahunAktif->id)->where('status', 'paid')->count();
                $totalBelumAktif = (int) DB::table('tagihan_ukt')->where('tahun_akademik_id', $tahunAktif->id)->whereIn('status', ['open', 'partial', 'disputed', 'void'])->count();
            }

            $cards = [
                ['label' => 'Tagihan Semester Ini', 'value' => number_format($totalTagihanAktif), 'hint' => 'Berdasarkan tahun akademik aktif'],
                ['label' => 'Tagihan Paid', 'value' => number_format($totalLunasAktif), 'hint' => 'Status paid'],
                ['label' => 'Belum Clear', 'value' => number_format($totalBelumAktif), 'hint' => 'Open/partial/disputed/void'],
                ['label' => 'Pembayaran Hari Ini', 'value' => 'Rp'.number_format((float) DB::table('pembayaran')->whereDate('tanggal_bayar', now()->toDateString())->sum('jumlah_bayar'), 0, ',', '.'), 'hint' => 'Total transaksi hari ini'],
            ];

            $operationalCards = [
                [
                    'title' => 'Tagihan Menunggu',
                    'value' => DB::table('tagihan_ukt')->whereIn('status', ['open', 'partial'])->count(),
                    'link' => route('keuangan.tagihan.index'),
                ],
                [
                    'title' => 'Pembayaran Hari Ini',
                    'value' => 'Rp'.number_format((float) DB::table('pembayaran')->whereDate('tanggal_bayar', now()->toDateString())->sum('jumlah_bayar'), 0, ',', '.'),
                    'link' => route('keuangan.pembayaran.index'),
                ],
            ];
        }

        if ($roleName === 'super_admin') {
            $lastLog = DB::table('audit_logs')->orderByDesc('id')->value('created_at');
            $cards = [
                ['label' => 'Total User', 'value' => number_format(DB::table('users')->count()), 'hint' => 'Seluruh akun terdaftar'],
                ['label' => 'Total Role', 'value' => number_format(DB::table('roles')->count()), 'hint' => 'Role aktif sistem'],
                ['label' => 'Log Terakhir', 'value' => $lastLog ? (string) $lastLog : '-', 'hint' => 'Aktivitas terakhir audit'],
                ['label' => 'Server Status', 'value' => 'ONLINE', 'hint' => 'Aplikasi berjalan normal'],
            ];
        }

        return view('dashboard', [
            'title' => 'Dashboard Akademik',
            'cards' => $cards,
            'roleLabel' => config('permissions.roles.'.$user?->role?->role_name.'.label', $user?->role?->role_name ?? '-'),
            'abilityItems' => $abilityItems,
            'tahunAktifLabel' => $tahunAktif ? ($tahunAktif->tahun.' '.ucfirst($tahunAktif->semester)) : 'Belum diatur',
            'krsStatus' => [
                'draft' => (int) ($krsStatus['draft'] ?? 0),
                'final' => (int) ($krsStatus['final'] ?? 0),
            ],
            'paymentSeries' => $paymentSeries,
            'roleName' => $roleName,
            'roleCharts' => $roleCharts,
            'operationalCards' => $operationalCards,
        ]);
    }

    private function hariIni(): string
    {
        return match ((int) now()->dayOfWeekIso) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
    }
}
