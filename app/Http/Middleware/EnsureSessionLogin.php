<?php

namespace App\Http\Middleware;

use App\Support\AcademicSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $session = $request->session();
        $sessionUserId = (int) $session->get('auth_user_id', 0);
        $sessionRoleId = (int) $session->get('auth_role_id', 0);
        $authUserId = (int) Auth::id();
        $authRoleId = (int) (Auth::user()?->role_id ?? 0);

        if ($sessionUserId !== $authUserId) {
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Sesi login tidak valid. Silakan login ulang.']);
        }

        if ($sessionRoleId !== $authRoleId) {
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Sesi dihentikan karena role akun berubah.']);
        }

        $lastActivityTs = (int) $session->get('auth_last_activity', now()->timestamp);
        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $maxIdleSeconds = $lifetimeMinutes * 60;

        if ((now()->timestamp - $lastActivityTs) > $maxIdleSeconds) {
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Sesi berakhir karena tidak ada aktivitas.']);
        }

        $session->put('auth_last_activity', now()->timestamp);

        if (AcademicSetting::maintenanceMode() && ! Auth::user()?->hasRole('super_admin')) {
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Sistem sedang maintenance.']);
        }

        return $next($request);
    }
}
