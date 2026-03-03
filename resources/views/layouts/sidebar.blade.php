<aside id="sidebar" class="app-sidebar">
    @php
        $evaluasiEnabled = \App\Support\AcademicSetting::evaluasiEnabled();
    @endphp
    <div class="pt-8 pb-7">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="grid w-8 h-8 font-bold text-white rounded-lg place-items-center bg-brand-500">A</span>
            <div>
                <p class="text-sm font-semibold text-gray-900">Akademik</p>
                <p class="text-xs text-gray-500">Sistem Informasi</p>
            </div>
        </a>
    </div>

    <nav class="flex flex-col gap-6 overflow-y-auto no-scrollbar">
        <div>
            <h2 class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Menu Utama</h2>
            <ul class="flex flex-col gap-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="menu-item {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    @if(auth()->user()->hasAbility('krs.view'))
                        <a href="{{ route('krs.index') }}"
                           class="menu-item {{ request()->routeIs('krs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Isi KRS</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->user()->hasAbility('nilai.view'))
                        <a href="{{ route('khs.index') }}"
                           class="menu-item {{ request()->routeIs('khs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Lihat KHS</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->user()->hasAbility('krs.view'))
                        <a href="{{ route('mahasiswa.jadwal.index') }}"
                           class="menu-item {{ request()->routeIs('mahasiswa.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Jadwal Kuliah</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->user()->hasAbility('ukt.view'))
                        <a href="{{ route('ukt.index') }}"
                           class="menu-item {{ request()->routeIs('ukt.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Status UKT</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->user()->hasAbility('krs.manage') && $evaluasiEnabled)
                        <a href="{{ route('mahasiswa.evaluasi.index') }}"
                           class="menu-item {{ request()->routeIs('mahasiswa.evaluasi.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Evaluasi Dosen</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->user()->hasAbility('krs.view'))
                        <a href="{{ route('mahasiswa.profil.index') }}"
                           class="menu-item {{ request()->routeIs('mahasiswa.profil.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Profil Mahasiswa</span>
                        </a>
                    @endif
                </li>
            </ul>
        </div>

        @if(auth()->user()->hasAbility('master.view'))
            <div>
                <h2 class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Admin Akademik</h2>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('master.fakultas.index') }}"
                           class="menu-item {{ request()->routeIs('master.fakultas.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Fakultas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('master.prodi.index') }}"
                           class="menu-item {{ request()->routeIs('master.prodi.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Prodi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('master.mata-kuliah.index') }}"
                           class="menu-item {{ request()->routeIs('master.mata-kuliah.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Mata Kuliah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('master.jadwal.index') }}"
                           class="menu-item {{ request()->routeIs('master.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Jadwal</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('master.jabatan-dosen.index') }}"
                           class="menu-item {{ request()->routeIs('master.jabatan-dosen.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Jabatan Dosen</span>
                        </a>
                    </li>
                    @if(auth()->user()->hasAbility('khs.generate'))
                        <li>
                            <a href="{{ route('akademik.generate-khs.index') }}"
                               class="menu-item {{ request()->routeIs('akademik.generate-khs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Generate KHS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('akademik.periode-krs.index') }}"
                               class="menu-item {{ request()->routeIs('akademik.periode-krs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Kontrol Periode KRS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('akademik.mahasiswa-status.index') }}"
                               class="menu-item {{ request()->routeIs('akademik.mahasiswa-status.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Status Mahasiswa</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        @if(auth()->user()->hasAbility('keuangan.view'))
            <div>
                <h2 class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Admin Keuangan</h2>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('keuangan.tagihan.index') }}"
                           class="menu-item {{ request()->routeIs('keuangan.tagihan.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Kelola Tagihan UKT</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('keuangan.pembayaran.index') }}"
                           class="menu-item {{ request()->routeIs('keuangan.pembayaran.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Validasi Pembayaran</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        @if(auth()->user()->hasAbility('jadwal.view') || auth()->user()->hasAbility('nilai.manage'))
            <div>
                <h2 class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Dosen</h2>
                <ul class="flex flex-col gap-1">
                    @if(auth()->user()->hasAbility('jadwal.view'))
                        <li>
                            <a href="{{ route('dosen.jadwal.index') }}"
                               class="menu-item {{ request()->routeIs('dosen.jadwal.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Lihat Jadwal Mengajar</span>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasAbility('nilai.manage'))
                        <li>
                            <a href="{{ route('dosen.nilai.index') }}"
                               class="menu-item {{ request()->routeIs('dosen.nilai.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Input Nilai</span>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasAbility('mahasiswa.monitor'))
                        <li>
                            <a href="{{ route('dosen.monitoring-mahasiswa.index') }}"
                               class="menu-item {{ request()->routeIs('dosen.monitoring-mahasiswa.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Monitoring Mahasiswa</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        @if(auth()->user()->hasAbility('users.manage'))
            <div>
                <h2 class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Super Admin</h2>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('super-admin.users.index') }}"
                           class="menu-item {{ request()->routeIs('super-admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Kelola User</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.role-permissions.index') }}"
                           class="menu-item {{ request()->routeIs('super-admin.role-permissions.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Hak Akses Role</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.system-settings.index') }}"
                           class="menu-item {{ request()->routeIs('super-admin.system-settings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>System Config</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.master-recovery.index') }}"
                           class="menu-item {{ request()->routeIs('super-admin.master-recovery.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <span>Master Recovery</span>
                        </a>
                    </li>
                    @if(auth()->user()->hasAbility('audit.view'))
                        <li>
                            <a href="{{ route('super-admin.audit-logs.index') }}"
                               class="menu-item {{ request()->routeIs('super-admin.audit-logs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <span>Audit Log</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </nav>
</aside>
