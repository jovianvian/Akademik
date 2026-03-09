<header class="app-header">
    <div class="flex items-center justify-between w-full gap-3 px-4 py-3 border-b border-gray-200 lg:px-6 lg:py-4">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebarToggle" class="toggle-btn" aria-label="Toggle Sidebar">
                <svg width="18" height="14" viewBox="0 0 18 14" fill="none">
                    <path d="M1 1H17M1 7H17M1 13H10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
            <div>
                <p class="text-sm text-gray-500">Semester 2025/2026 Genap</p>
                <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button
                type="button"
                data-theme-toggle
                class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                aria-label="Ubah tema"
            >
                <span data-theme-icon-sun class="hidden">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"></circle>
                        <path d="M10 2.5v2M10 15.5v2M4.7 4.7l1.4 1.4M13.9 13.9l1.4 1.4M2.5 10h2M15.5 10h2M4.7 15.3l1.4-1.4M13.9 6.1l1.4-1.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    </svg>
                </span>
                <span data-theme-icon-moon>
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M15.5 12.2a6.6 6.6 0 1 1-7.7-7.7 6 6 0 0 0 7.7 7.7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            </button>

            <details class="top-user-dropdown" id="userDropdown">
                <summary class="top-user-card cursor-pointer">
                    <div class="top-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="top-user-meta">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ config('permissions.roles.'.auth()->user()->role?->role_name.'.label', auth()->user()->role?->role_name ?? '-') }}</p>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 20 20" fill="none" class="text-gray-500">
                        <path d="M5 8L10 13L15 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </summary>
                <div class="top-user-dropdown-menu">
                    <a href="{{ route('profile.index') }}" class="top-user-dropdown-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                            <path d="M20 21C20 17.6863 16.4183 15 12 15C7.58172 15 4 17.6863 4 21M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="top-user-dropdown-link text-red-600">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M15 17L20 12L15 7M20 12H9M9 21H5V3H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>
