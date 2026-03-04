<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Informasi Akademik</title>
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
<body class="grid min-h-screen place-items-center bg-gray-50 p-4">
<main class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm">
    <h1 class="text-2xl font-semibold text-gray-900">Login SIAKAD</h1>
    <p class="mt-1 text-sm text-gray-500">Masuk dengan akun role Anda.</p>

    <form action="{{ route('login.attempt') }}" method="POST" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" class="input-text" value="{{ old('email') }}" required>
            @error('email') <p class="mt-1 text-sm text-error-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" class="input-text" required>
        </div>
        <button class="btn-primary w-full justify-center" type="submit">Masuk</button>
    </form>

    <div class="mt-4 text-xs text-gray-500">
        Sistem login menggunakan session aktif (tanpa remember-me otomatis).
    </div>

    <div class="mt-5 rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-600">
        <p>Akun demo:</p>
        <p>`superadmin@kampus.ac.id` / `password`</p>
        <p>`adminkeuangan@kampus.ac.id` / `password`</p>
        <p>`adminakademik@kampus.ac.id` / `password`</p>
        <p>`dosen1@kampus.ac.id` / `password`</p>
        <p>`mhs1@kampus.ac.id` / `password`</p>
    </div>
</main>
</body>
</html>
