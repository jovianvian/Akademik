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
                    <button type="button" class="top-user-dropdown-link" data-theme-toggle>
                        <span data-theme-icon-sun class="hidden">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3V5M12 19V21M4.93 4.93L6.34 6.34M17.66 17.66L19.07 19.07M3 12H5M19 12H21M4.93 19.07L6.34 17.66M17.66 6.34L19.07 4.93M16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span data-theme-icon-moon>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M21 12.79C20.6842 16.2992 17.7278 19 14 19C9.58172 19 6 15.4183 6 11C6 7.27223 8.70076 4.3158 12.21 4C11.4032 5.09316 10.9263 6.44252 10.9263 7.90307C10.9263 11.5301 13.8699 14.4737 17.4969 14.4737C18.9575 14.4737 20.3068 13.9968 21.4 13.19C21.1414 13.0048 21.0073 12.8967 21 12.79Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span data-theme-label>Mode Gelap</span>
                    </button>
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
