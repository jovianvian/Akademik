<aside id="sidebar" class="app-sidebar">
    @php
        $user = auth()->user();
        $evaluasiEnabled = \App\Support\AcademicSetting::evaluasiEnabled();
    @endphp

    <div class="pb-7" style="padding-top: 26px;">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="grid w-10 h-10 text-base font-bold text-white rounded-lg place-items-center bg-brand-500">A</span>
            <div>
                <p class="text-base font-semibold text-gray-900">Akademik</p>
                <p class="text-sm text-gray-500">Sistem Informasi</p>
            </div>
        </a>
    </div>

    <nav class="flex flex-col gap-3 pb-4">
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
            <span>Dashboard</span>
        </a>

        @if($user->hasAbility('krs.view') || $user->hasAbility('nilai.view') || $user->hasAbility('ukt.view'))
            <details class="sidebar-group" data-menu-group="mahasiswa" data-default-open="{{ request()->routeIs('krs.*','khs.*','transkrip.*','nilai.detail','mahasiswa.jadwal.*','mahasiswa.evaluasi.*','ukt.*','mahasiswa.profil.*') ? '1' : '0' }}" {{ request()->routeIs('krs.*','khs.*','transkrip.*','nilai.detail','mahasiswa.jadwal.*','mahasiswa.evaluasi.*','ukt.*','mahasiswa.profil.*') ? 'open' : '' }}>
                <summary class="sidebar-summary"><span>Mahasiswa</span><svg class="sidebar-chevron" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
                <div class="px-2 pb-2">
                    @if($user->hasAbility('krs.view'))
                        <a href="{{ route('krs.index') }}" class="menu-item {{ request()->routeIs('krs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Isi KRS</a>
                        <a href="{{ route('mahasiswa.jadwal.index') }}" class="menu-item {{ request()->routeIs('mahasiswa.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Jadwal Kuliah</a>
                        <a href="{{ route('mahasiswa.profil.index') }}" class="menu-item {{ request()->routeIs('mahasiswa.profil.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Profil Mahasiswa</a>
                    @endif
                    @if($user->hasAbility('nilai.view'))
                        <a href="{{ route('khs.index') }}" class="menu-item {{ request()->routeIs('khs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Nilai / KHS</a>
                        <a href="{{ route('transkrip.index') }}" class="menu-item {{ request()->routeIs('transkrip.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Transkrip</a>
                    @endif
                    @if($user->hasAbility('ukt.view'))
                        <a href="{{ route('ukt.index') }}" class="menu-item {{ request()->routeIs('ukt.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Tagihan & Pembayaran</a>
                    @endif
                    @if($user->hasAbility('krs.manage') && $evaluasiEnabled)
                        <a href="{{ route('mahasiswa.evaluasi.index') }}" class="menu-item {{ request()->routeIs('mahasiswa.evaluasi.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Evaluasi Dosen</a>
                    @endif
                </div>
            </details>
        @endif

        @if($user->hasAbility('master.view') || $user->hasAbility('khs.generate'))
            <details class="sidebar-group" data-menu-group="akademik" data-default-open="{{ request()->routeIs('master.*','akademik.*') ? '1' : '0' }}" {{ request()->routeIs('master.*','akademik.*') ? 'open' : '' }}>
                <summary class="sidebar-summary"><span>Akademik</span><svg class="sidebar-chevron" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
                <div class="px-2 pb-2">
                    @if($user->hasAbility('master.view'))
                        <a href="{{ route('master.fakultas.index') }}" class="menu-item {{ request()->routeIs('master.fakultas.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Fakultas</a>
                        <a href="{{ route('master.prodi.index') }}" class="menu-item {{ request()->routeIs('master.prodi.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Prodi</a>
                        <a href="{{ route('master.mata-kuliah.index') }}" class="menu-item {{ request()->routeIs('master.mata-kuliah.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Mata Kuliah</a>
                        <a href="{{ route('master.jadwal.index') }}" class="menu-item {{ request()->routeIs('master.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Jadwal</a>
                        <a href="{{ route('master.jabatan-dosen.index') }}" class="menu-item {{ request()->routeIs('master.jabatan-dosen.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Jabatan Dosen</a>
                    @endif
                    @if($user->hasAbility('khs.generate'))
                        <a href="{{ route('akademik.monitoring-krs.index') }}" class="menu-item {{ request()->routeIs('akademik.monitoring-krs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Monitoring KRS</a>
                        <a href="{{ route('akademik.nilai-mahasiswa.index') }}" class="menu-item {{ request()->routeIs('akademik.nilai-mahasiswa.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Nilai Mahasiswa</a>
                        @if($evaluasiEnabled)
                            <a href="{{ route('akademik.evaluasi-dosen.index') }}" class="menu-item {{ request()->routeIs('akademik.evaluasi-dosen.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Evaluasi Dosen</a>
                        @endif
                    @endif
                </div>
            </details>
        @endif

        @if($user->hasAbility('keuangan.view'))
            <details class="sidebar-group" data-menu-group="keuangan" data-default-open="{{ request()->routeIs('keuangan.*') ? '1' : '0' }}" {{ request()->routeIs('keuangan.*') ? 'open' : '' }}>
                <summary class="sidebar-summary"><span>Keuangan</span><svg class="sidebar-chevron" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
                <div class="px-2 pb-2">
                    <a href="{{ route('keuangan.tagihan.index') }}" class="menu-item {{ request()->routeIs('keuangan.tagihan.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Tagihan UKT</a>
                    <a href="{{ route('keuangan.pembayaran.index') }}" class="menu-item {{ request()->routeIs('keuangan.pembayaran.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Validasi Pembayaran</a>
                    <a href="{{ route('keuangan.monitoring-pembayaran.index') }}" class="menu-item {{ request()->routeIs('keuangan.monitoring-pembayaran.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Monitoring Pembayaran</a>
                </div>
            </details>
        @endif

        @if($user->hasAbility('jadwal.view') || $user->hasAbility('nilai.manage') || $user->hasAbility('mahasiswa.monitor'))
            <details class="sidebar-group" data-menu-group="dosen" data-default-open="{{ request()->routeIs('dosen.*') ? '1' : '0' }}" {{ request()->routeIs('dosen.*') ? 'open' : '' }}>
                <summary class="sidebar-summary"><span>Dosen</span><svg class="sidebar-chevron" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
                <div class="px-2 pb-2">
                    @if($user->hasAbility('jadwal.view'))
                        <a href="{{ route('dosen.jadwal.index') }}" class="menu-item {{ request()->routeIs('dosen.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Jadwal Mengajar</a>
                    @endif
                    @if($user->hasAbility('nilai.manage'))
                        <a href="{{ route('dosen.nilai.index') }}" class="menu-item {{ request()->routeIs('dosen.nilai.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Input Nilai</a>
                    @endif
                    @if($user->hasAbility('mahasiswa.monitor'))
                        <a href="{{ route('dosen.monitoring-mahasiswa.index') }}" class="menu-item {{ request()->routeIs('dosen.monitoring-mahasiswa.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Monitoring Mahasiswa</a>
                        @if($evaluasiEnabled)
                            <a href="{{ route('dosen.evaluasi-saya.index') }}" class="menu-item {{ request()->routeIs('dosen.evaluasi-saya.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Evaluasi Saya</a>
                        @endif
                    @endif
                </div>
            </details>
        @endif

        @if($user->hasAbility('users.manage') || $user->hasAbility('roles.manage') || $user->hasAbility('audit.view'))
            <details class="sidebar-group" data-menu-group="system" data-default-open="{{ request()->routeIs('super-admin.*') ? '1' : '0' }}" {{ request()->routeIs('super-admin.*') ? 'open' : '' }}>
                <summary class="sidebar-summary"><span>System</span><svg class="sidebar-chevron" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
                <div class="px-2 pb-2">
                    @if($user->hasAbility('users.manage'))
                        <a href="{{ route('super-admin.users.index') }}" class="menu-item {{ request()->routeIs('super-admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Kelola User</a>
                    @endif
                    @if($user->hasAbility('roles.manage'))
                        <a href="{{ route('super-admin.role-permissions.index') }}" class="menu-item {{ request()->routeIs('super-admin.role-permissions.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Role & Permission</a>
                        <a href="{{ route('super-admin.system-settings.index') }}" class="menu-item {{ request()->routeIs('super-admin.system-settings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">System Config</a>
                        <a href="{{ route('super-admin.master-recovery.index') }}" class="menu-item {{ request()->routeIs('super-admin.master-recovery.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Recovery</a>
                    @endif
                    @if($user->hasAbility('audit.view'))
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="menu-item {{ request()->routeIs('super-admin.audit-logs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">Audit Log</a>
                    @endif
                </div>
            </details>
        @endif
    </nav>
</aside>
