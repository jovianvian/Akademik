<header class="app-header">
    <div class="flex items-center justify-between w-full gap-3 px-4 py-3 border-b border-gray-200 lg:px-6 lg:py-4">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebarToggle" class="toggle-btn xl:hidden" aria-label="Toggle Sidebar">
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
            <div class="top-user-card">
                <div class="top-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="top-user-meta">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ config('permissions.roles.'.auth()->user()->role?->role_name.'.label', auth()->user()->role?->role_name ?? '-') }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="top-logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M15 17L20 12L15 7M20 12H9M9 21H5V3H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
