<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DosenJabatanMiddleware
{
    public function handle(Request $request, Closure $next, string ...$allowedJabatan): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect('/login');
        }

        if (! $user->hasRole('dosen', 'super_admin')) {
            abort(403, 'Akses jabatan dosen tidak valid.');
        }

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if (! $user->hasDosenJabatan(...$allowedJabatan)) {
            abort(403, 'Akses modul ini tidak tersedia untuk jabatan Anda.');
        }

        return $next($request);
    }
}
