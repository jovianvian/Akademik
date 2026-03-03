@extends('layouts.app')

@section('content')
    @php
        $evaluasiEnabled = \App\Support\AcademicSetting::evaluasiEnabled();
    @endphp
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        @foreach($cards as $card)
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm md:col-span-6 xl:col-span-3">
                <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                <p class="mt-2 text-xs text-brand-600">{{ $card['hint'] }}</p>
            </article>
        @endforeach

        <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Ringkasan Aktivitas Akademik</h2>
            <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                <div class="p-4 border border-gray-100 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500">Tahun Akademik Aktif</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ $tahunAktifLabel }}</p>
                </div>
                <div class="p-4 border border-gray-100 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500">Panel Operasional</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ count($operationalCards) }} Widget</p>
                </div>
            </div>
        </article>

        <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Aksi Cepat</h2>
            <div class="grid grid-cols-1 gap-3 mt-4">
                @if(auth()->user()->hasAbility('krs.view'))
                    <a href="{{ route('krs.index') }}" class="quick-link">Isi KRS Semester Aktif</a>
                    <a href="{{ route('mahasiswa.jadwal.index') }}" class="quick-link">Lihat Jadwal Kuliah</a>
                    <a href="{{ route('mahasiswa.profil.index') }}" class="quick-link">Profil Mahasiswa</a>
                @endif
                @if(auth()->user()->hasAbility('master.view'))
                    <a href="{{ route('master.fakultas.index') }}" class="quick-link">Kelola Master Akademik</a>
                    <a href="{{ route('master.jabatan-dosen.index') }}" class="quick-link">Kelola Jabatan Dosen</a>
                @endif
                @if(auth()->user()->hasAbility('keuangan.view'))
                    <a href="{{ route('keuangan.tagihan.index') }}" class="quick-link">Kelola Tagihan UKT</a>
                @endif
                @if(auth()->user()->hasAbility('jadwal.view'))
                    <a href="{{ route('dosen.jadwal.index') }}" class="quick-link">Lihat Jadwal Mengajar</a>
                @endif
                @if(auth()->user()->hasAbility('nilai.manage'))
                    <a href="{{ route('dosen.nilai.index') }}" class="quick-link">Input Nilai Mahasiswa</a>
                @endif
                @if(auth()->user()->hasAbility('nilai.view'))
                    <a href="{{ route('khs.index') }}" class="quick-link">Lihat Nilai / KHS</a>
                @endif
                @if(auth()->user()->hasAbility('ukt.view'))
                    <a href="{{ route('ukt.index') }}" class="quick-link">Status Pembayaran UKT</a>
                @endif
                @if(auth()->user()->hasAbility('mahasiswa.monitor'))
                    <a href="{{ route('dosen.monitoring-mahasiswa.index') }}" class="quick-link">Monitoring Mahasiswa</a>
                @endif
                @if(auth()->user()->hasAbility('krs.manage') && $evaluasiEnabled)
                    <a href="{{ route('mahasiswa.evaluasi.index') }}" class="quick-link">Isi Evaluasi Dosen</a>
                @endif
                @if(auth()->user()->hasAbility('khs.generate'))
                    <a href="{{ route('akademik.generate-khs.index') }}" class="quick-link">Generate KHS</a>
                    <a href="{{ route('akademik.periode-krs.index') }}" class="quick-link">Kontrol Periode KRS</a>
                    <a href="{{ route('akademik.mahasiswa-status.index') }}" class="quick-link">Kelola Status Mahasiswa</a>
                @endif
                @if(auth()->user()->hasAbility('users.manage'))
                    <a href="{{ route('super-admin.users.index') }}" class="quick-link">Kelola User & Role</a>
                    <a href="{{ route('super-admin.role-permissions.index') }}" class="quick-link">Kelola Hak Akses Role</a>
                    <a href="{{ route('super-admin.system-settings.index') }}" class="quick-link">System Configuration</a>
                    <a href="{{ route('super-admin.master-recovery.index') }}" class="quick-link">Recovery Master Data</a>
                @endif
                @if(auth()->user()->hasAbility('audit.view'))
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="quick-link">Lihat Audit Log</a>
                @endif
            </div>
        </article>

        @if(!empty($operationalCards))
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
                <h2 class="text-base font-semibold text-gray-900">Panel Operasional</h2>
                <div class="grid grid-cols-1 gap-3 mt-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($operationalCards as $card)
                        <a href="{{ $card['link'] }}" class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                            <p class="text-xs text-gray-500">{{ $card['title'] }}</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ $card['value'] }}</p>
                        </a>
                    @endforeach
                </div>
            </article>
        @endif

        <article class="col-span-12 p-3 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
            <div class="flex items-center gap-2">
                <button type="button" class="px-3 py-1 text-xs text-white rounded bg-brand-500" data-chart-toggle="payment">Tren Pembayaran</button>
                <button type="button" class="px-3 py-1 text-xs border border-gray-200 rounded" data-chart-toggle="krs">Status KRS</button>
            </div>
        </article>

        <article id="chartPayment" class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
            <h2 class="text-base font-semibold text-gray-900">Tren Pembayaran 6 Bulan</h2>
            <div class="mt-4 h-72"><canvas id="paymentChart"></canvas></div>
        </article>

        <article id="chartKrs" class="hidden col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
            <h2 class="text-base font-semibold text-gray-900">Status KRS</h2>
            <div class="mt-4 h-72"><canvas id="krsChart"></canvas></div>
        </article>

        @if($roleName === 'super_admin')
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Diagram User per Role</h2>
                <div class="mt-4 h-72"><canvas id="superUserRoleChart"></canvas></div>
            </article>
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Diagram Audit per Modul (30 Hari)</h2>
                <div class="mt-4 h-72"><canvas id="superAuditChart"></canvas></div>
            </article>
        @endif

        @if($roleName === 'admin_akademik')
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Diagram Status Mahasiswa</h2>
                <div class="mt-4 h-72"><canvas id="akademikStatusMhsChart"></canvas></div>
            </article>
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">KRS Final per Prodi</h2>
                <div class="mt-4 h-72"><canvas id="akademikKrsProdiChart"></canvas></div>
            </article>
        @endif

        @if($roleName === 'dosen')
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Diagram Kelas per Hari</h2>
                <div class="mt-4 h-72"><canvas id="dosenKelasHariChart"></canvas></div>
            </article>
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Diagram Kelengkapan Nilai</h2>
                <div class="mt-4 h-72"><canvas id="dosenNilaiChart"></canvas></div>
            </article>
        @endif

        @if($roleName === 'admin_keuangan')
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-6">
                <h2 class="text-base font-semibold text-gray-900">Status Tagihan Semester Aktif</h2>
                <div class="mt-4 h-72"><canvas id="keuanganStatusTagihanChart"></canvas></div>
            </article>
        @endif

    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const paymentPanel = document.getElementById('chartPayment');
    const krsPanel = document.getElementById('chartKrs');
    const buttons = document.querySelectorAll('[data-chart-toggle]');

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.chartToggle;
            const showPayment = target === 'payment';
            paymentPanel.classList.toggle('hidden', !showPayment);
            krsPanel.classList.toggle('hidden', showPayment);

            buttons.forEach((b) => {
                b.classList.remove('bg-brand-500', 'text-white');
                b.classList.add('border', 'border-gray-200');
            });
            btn.classList.add('bg-brand-500', 'text-white');
            btn.classList.remove('border', 'border-gray-200');
        });
    });

    const chartSource = {
        roleName: @json($roleName),
        paymentSeries: @json($paymentSeries),
        krsStatus: @json($krsStatus),
        roleCharts: @json($roleCharts),
    };

    if (typeof window.Chart === 'undefined') {
        return;
    }

    const commonGrid = {
        color: 'rgba(148, 163, 184, 0.18)',
        drawBorder: false,
    };

    const initChart = (id, config) => {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        new window.Chart(ctx, config);
    };

    initChart('paymentChart', {
        type: 'line',
        data: {
            labels: chartSource.paymentSeries.map((r) => r.label),
            datasets: [{
                label: 'Pembayaran (Rp)',
                data: chartSource.paymentSeries.map((r) => r.total),
                borderColor: '#465fff',
                backgroundColor: 'rgba(70,95,255,0.2)',
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { grid: commonGrid }, y: { grid: commonGrid } }
        }
    });

    initChart('krsChart', {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Final'],
            datasets: [{
                data: [chartSource.krsStatus.draft, chartSource.krsStatus.final],
                backgroundColor: ['#fb923c', '#16a34a'],
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    if (chartSource.roleName === 'super_admin') {
        const u = chartSource.roleCharts.userByRole || [];
        const a = chartSource.roleCharts.auditByModule || [];
        initChart('superUserRoleChart', {
            type: 'bar',
            data: { labels: u.map((r) => r.label), datasets: [{ label: 'User', data: u.map((r) => r.total), backgroundColor: '#465fff' }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: commonGrid }, y: { grid: commonGrid } } }
        });
        initChart('superAuditChart', {
            type: 'bar',
            data: { labels: a.map((r) => r.label), datasets: [{ label: 'Aktivitas', data: a.map((r) => r.total), backgroundColor: '#16a34a' }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: commonGrid }, y: { grid: commonGrid } } }
        });
    }

    if (chartSource.roleName === 'admin_akademik') {
        const s = chartSource.roleCharts.statusMahasiswa || [];
        const p = chartSource.roleCharts.krsFinalPerProdi || [];
        initChart('akademikStatusMhsChart', {
            type: 'pie',
            data: {
                labels: s.map((r) => String(r.label).toUpperCase()),
                datasets: [{ data: s.map((r) => r.total), backgroundColor: ['#465fff', '#f59e0b', '#ef4444', '#22c55e'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
        initChart('akademikKrsProdiChart', {
            type: 'bar',
            data: { labels: p.map((r) => r.label), datasets: [{ label: 'KRS Final', data: p.map((r) => r.final_total), backgroundColor: '#0ea5e9' }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: commonGrid }, y: { grid: commonGrid } } }
        });
    }

    if (chartSource.roleName === 'dosen') {
        const h = chartSource.roleCharts.kelasByHari || [];
        const n = chartSource.roleCharts.nilaiCompleteness || { terisi: 0, belum: 0 };
        initChart('dosenKelasHariChart', {
            type: 'bar',
            data: { labels: h.map((r) => r.label), datasets: [{ label: 'Kelas', data: h.map((r) => r.total), backgroundColor: '#465fff' }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: commonGrid }, y: { grid: commonGrid } } }
        });
        initChart('dosenNilaiChart', {
            type: 'doughnut',
            data: { labels: ['Terisi', 'Belum'], datasets: [{ data: [n.terisi, n.belum], backgroundColor: ['#16a34a', '#fb923c'] }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (chartSource.roleName === 'admin_keuangan') {
        const t = chartSource.roleCharts.statusTagihanAktif || [];
        initChart('keuanganStatusTagihanChart', {
            type: 'doughnut',
            data: {
                labels: t.map((r) => String(r.label).toUpperCase()),
                datasets: [{ data: t.map((r) => r.total), backgroundColor: ['#f59e0b', '#16a34a', '#ef4444'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

});
</script>
@endpush
