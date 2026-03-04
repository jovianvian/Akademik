@extends('layouts.app')

@section('content')
    @php
        $evaluasiEnabled = \App\Support\AcademicSetting::evaluasiEnabled();
    @endphp
    <section class="grid grid-cols-12 items-start gap-4 md:gap-6">
        @foreach($cards as $card)
            <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm md:col-span-6 xl:col-span-3">
                <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                <p class="mt-2 text-xs text-brand-600">{{ $card['hint'] }}</p>
            </article>
        @endforeach

        <div class="col-span-12 space-y-4 xl:col-span-8">
            <article class="p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
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
                <div class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-gray-900">Analitik Cepat</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-compact" data-chart-toggle="payment">Tren Pembayaran</button>
                            <button type="button" class="btn-secondary" data-chart-toggle="krs">Status KRS</button>
                        </div>
                    </div>
                    <article id="chartPayment" class="mt-4">
                        <div class="h-56"><canvas id="paymentChart"></canvas></div>
                    </article>
                    <article id="chartKrs" class="hidden mt-4">
                        <div class="h-56"><canvas id="krsChart"></canvas></div>
                    </article>
                </div>
            </article>

            @if($roleName === 'super_admin')
                <article class="p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-base font-semibold text-gray-900">Analitik Super Admin</h2>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-compact" data-super-toggle="user">User per Role</button>
                            <button type="button" class="btn-secondary" data-super-toggle="audit">Audit per Modul</button>
                        </div>
                    </div>
                    <article id="superUserPanel" class="mt-4">
                        <div class="h-64"><canvas id="superUserRoleChart"></canvas></div>
                    </article>
                    <article id="superAuditPanel" class="hidden mt-4">
                        <div class="h-64"><canvas id="superAuditChart"></canvas></div>
                    </article>
                </article>
            @endif
        </div>

        <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Aksi Cepat</h2>
            <div class="grid grid-cols-1 gap-3 mt-4">
                @if(auth()->user()->hasAbility('krs.view'))
                    <a href="{{ route('krs.index') }}" class="quick-link">Isi KRS Semester Aktif</a>
                    <a href="{{ route('mahasiswa.jadwal.index') }}" class="quick-link">Lihat Jadwal Kuliah</a>
                    <a href="{{ route('transkrip.index') }}" class="quick-link">Lihat Transkrip</a>
                    <a href="{{ route('mahasiswa.profil.index') }}" class="quick-link">Profil Mahasiswa</a>
                @endif
                @if(auth()->user()->hasAbility('master.view'))
                    <a href="{{ route('master.fakultas.index') }}" class="quick-link">Kelola Master Akademik</a>
                    <a href="{{ route('master.jabatan-dosen.index') }}" class="quick-link">Kelola Jabatan Dosen</a>
                @endif
                @if(auth()->user()->hasAbility('keuangan.view'))
                    <a href="{{ route('keuangan.tagihan.index') }}" class="quick-link">Kelola Tagihan UKT</a>
                    <a href="{{ route('keuangan.monitoring-pembayaran.index') }}" class="quick-link">Monitoring Pembayaran</a>
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
                    <a href="{{ route('akademik.monitoring-krs.index') }}" class="quick-link">Monitoring KRS</a>
                    <a href="{{ route('akademik.nilai-mahasiswa.index') }}" class="quick-link">Rekap Nilai Mahasiswa</a>
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

    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const paymentPanel = document.getElementById('chartPayment');
    const krsPanel = document.getElementById('chartKrs');
    const buttons = document.querySelectorAll('[data-chart-toggle]');
    const superUserPanel = document.getElementById('superUserPanel');
    const superAuditPanel = document.getElementById('superAuditPanel');
    const superButtons = document.querySelectorAll('[data-super-toggle]');

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.chartToggle;
            const showPayment = target === 'payment';
            paymentPanel.classList.toggle('hidden', !showPayment);
            krsPanel.classList.toggle('hidden', showPayment);

            buttons.forEach((b) => {
                b.classList.remove('btn-compact');
                b.classList.add('btn-secondary');
            });
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-compact');
        });
    });

    superButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.superToggle;
            const showUser = target === 'user';
            if (superUserPanel && superAuditPanel) {
                superUserPanel.classList.toggle('hidden', !showUser);
                superAuditPanel.classList.toggle('hidden', showUser);
            }

            superButtons.forEach((b) => {
                b.classList.remove('btn-compact');
                b.classList.add('btn-secondary');
            });
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-compact');
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
            data: {
                labels: u.map((r) => r.label),
                datasets: [{
                    label: 'User',
                    data: u.map((r) => r.total),
                    backgroundColor: '#465fff',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { grid: commonGrid }, y: { grid: commonGrid } },
            },
        });

        initChart('superAuditChart', {
            type: 'bar',
            data: {
                labels: a.map((r) => r.label),
                datasets: [{
                    label: 'Aktivitas',
                    data: a.map((r) => r.total),
                    backgroundColor: '#16a34a',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { grid: commonGrid }, y: { grid: commonGrid } },
            },
        });
    }

});
</script>
@endpush



