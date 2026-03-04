<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIAKAD' }} | Sistem Informasi Akademik</title>
    <script>
        (function () {
            try {
                var mode = localStorage.getItem('theme_mode');
                if (mode === 'dark') document.documentElement.classList.add('dark');
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
@stack('scripts')
</body>
</html>
