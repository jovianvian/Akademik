<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Informasi Akademik</title>
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
<body class="min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
<main class="min-h-screen">
    <section class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
        <div class="flex min-h-screen bg-white dark:bg-slate-950">
            <div class="relative mx-auto flex w-full max-w-[520px] flex-col justify-center px-6 py-10 sm:px-10 lg:px-0 lg:py-12">
                <div class="mx-auto mt-8 w-full max-w-[460px] lg:mt-10">
                    <div>
                        <h1 class="text-[38px] leading-tight font-semibold tracking-[-0.04em] text-slate-900 dark:text-slate-100">Sign In</h1>
                        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Enter your email and password to sign in!</p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/30 dark:text-rose-300">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.attempt') }}" method="POST" class="mt-10 space-y-5">
                        @csrf
                        <div class="space-y-2.5">
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Email <span class="text-brand-500">*</span></label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@kampus.ac.id"
                                class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-800 outline-hidden transition focus:border-brand-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-brand-500"
                                required
                            >
                            @error('email')
                                <p class="text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2.5">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Password <span class="text-brand-500">*</span></label>
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 pr-12 text-sm text-slate-800 outline-hidden transition focus:border-brand-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-brand-500"
                                    required
                                >
                                <button
                                    type="button"
                                    data-password-toggle
                                    data-password-target="password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-brand-500 dark:text-slate-500"
                                    aria-label="Toggle password"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M1.667 10s3-5 8.333-5 8.333 5 8.333 5-3 5-8.333 5-8.333-5-8.333-5Z" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember" class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
                                Keep me logged in
                            </label>
                            <a href="mailto:helpdesk@kampus.ac.id?subject=Reset%20Password%20SIAKAD" class="text-sm font-medium text-brand-500 transition hover:text-brand-600">
                                Forgot password?
                            </a>
                        </div>

                        <button class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-brand-500 px-4 text-sm font-semibold text-white transition hover:bg-brand-600" type="submit">
                            Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="relative hidden overflow-hidden bg-[#191D6B] lg:block dark:bg-[#0E1247]">
            <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 60px 60px;"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_45%,transparent_0%,rgba(25,29,107,0.16)_35%,rgba(25,29,107,0.42)_72%,rgba(25,29,107,0.65)_100%)] dark:bg-[radial-gradient(circle_at_50%_45%,transparent_0%,rgba(14,18,71,0.16)_35%,rgba(14,18,71,0.42)_72%,rgba(14,18,71,0.65)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.10)_0%,transparent_26%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.08)_0%,transparent_30%)]"></div>
            <div class="absolute right-20 top-16 grid grid-cols-2 gap-4 opacity-30">
                <span class="h-5 w-5 rounded-sm bg-white/20"></span>
                <span class="h-5 w-5 rounded-sm bg-white/10"></span>
            </div>
            <div class="absolute bottom-16 left-16 grid grid-cols-2 gap-4 opacity-20">
                <span class="h-7 w-7 rounded-sm bg-white/10"></span>
                <span class="h-7 w-7 rounded-sm bg-white/20"></span>
            </div>
            <div class="relative flex h-full items-center justify-center px-10">
                <div class="text-center text-white">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/12 ring-1 ring-white/15">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#4B6BFF] text-lg font-semibold text-white">S</div>
                    </div>
                    <p class="mt-5 text-4xl font-semibold tracking-tight">SIAKAD</p>
                    <p class="mx-auto mt-3 max-w-xs text-sm leading-7 text-white/70">Sistem Informasi Akademik untuk pengelolaan layanan kampus.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<button
    type="button"
    data-theme-toggle
    class="fixed right-6 bottom-6 z-50 inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
>
    <svg data-theme-icon-sun class="hidden h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
        <circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"></circle>
        <path d="M10 2.5v2M10 15.5v2M4.7 4.7l1.4 1.4M13.9 13.9l1.4 1.4M2.5 10h2M15.5 10h2M4.7 15.3l1.4-1.4M13.9 6.1l1.4-1.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
    </svg>
    <svg data-theme-icon-moon class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
        <path d="M15.5 12.2a6.6 6.6 0 1 1-7.7-7.7 6 6 0 0 0 7.7 7.7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>
    <span data-theme-label class="sr-only">Mode Gelap</span>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = document.getElementById(button.getAttribute('data-password-target'));
                if (!target) return;

                var reveal = target.type === 'password';
                target.type = reveal ? 'text' : 'password';
                var icon = button.querySelector('svg');
                if (icon) {
                    icon.innerHTML = reveal
                        ? '<path d="M17.94 17.94 2.06 2.06m6.76 6.76A2.5 2.5 0 0 0 11.18 11.18M8.52 4.53A9.58 9.58 0 0 1 10 4.44c5.33 0 8.33 5 8.33 5a14.3 14.3 0 0 1-3.09 3.67M5.6 5.6C3.12 7 1.67 9.44 1.67 9.44s3 5 8.33 5a9.9 9.9 0 0 0 4.4-.97" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>'
                        : '<path d="M1.667 10s3-5 8.333-5 8.333 5 8.333 5-3 5-8.333 5-8.333-5-8.333-5Z" stroke="currentColor" stroke-width="1.5"></path><circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"></circle>';
                }
            });
        });
    });
</script>
</body>
</html>
