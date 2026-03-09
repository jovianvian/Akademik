<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIAKAD' }} | Sistem Informasi Akademik</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.7.2/css/all.min.css">
    <script>
        (function () {
            function getCookieTheme() {
                var match = document.cookie.match(/(?:^|;\s*)theme_mode=(dark|light)(?:;|$)/);
                return match ? match[1] : null;
            }

            try {
                var mode = localStorage.getItem('theme_mode');
                if (mode !== 'dark' && mode !== 'light') {
                    mode = getCookieTheme();
                }
                if (mode === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (mode === 'light') {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen xl:flex">
    @include('layouts.sidebar')
    <div id="overlay" class="sidebar-overlay"></div>

    <div id="mainWrap" class="main-wrap">
        @include('layouts.header')
        <main class="p-4 mx-auto max-w-[1500px] md:p-6">
            @yield('content')
        </main>
    </div>
</div>
<x-admin.toast-notification />
@stack('scripts')
</body>
</html>
