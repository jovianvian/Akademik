<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\AcademicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    public function login(Request $request)
    {
        $maintenanceMode = AcademicSetting::maintenanceMode();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $failedAttempts = DB::table('login_attempts')
            ->where('email', $credentials['email'])
            ->where('ip_address', $request->ip())
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes(5))
            ->count();

        if ($failedAttempts >= 5) {
            $this->logAttempt($credentials['email'], false, $request);
            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan login gagal. Coba lagi dalam 5 menit.',
            ])->onlyInput('email');
        }

        if ($maintenanceMode) {
            $candidate = DB::table('users as u')
                ->join('roles as r', 'r.id', '=', 'u.role_id')
                ->where('u.email', $credentials['email'])
                ->select('r.role_name')
                ->first();

            if (! $candidate || $candidate->role_name !== 'super_admin') {
                $this->logAttempt($credentials['email'], false, $request);
                return back()->withErrors([
                    'email' => 'Sistem sedang maintenance. Hanya super admin yang bisa login.',
                ])->onlyInput('email');
            }
        }

        if (! Auth::attempt($credentials, false)) {
            $this->logAttempt($credentials['email'], false, $request);
            return back()->withErrors([
                'email' => 'Email atau password tidak valid.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();
        if (! $user || $user->status !== 'aktif') {
            $this->logAttempt($credentials['email'], false, $request);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors([
                'email' => 'Akun tidak aktif. Hubungi administrator.',
            ])->onlyInput('email');
        }

        $request->session()->put('auth_user_id', $user->id);
        $request->session()->put('auth_role_id', $user->role_id);
        $request->session()->put('auth_login_at', now()->toDateTimeString());
        $request->session()->put('auth_last_activity', now()->timestamp);

        $target = '/dashboard';

        if ($user?->hasRole('mahasiswa')) {
            $mhs = DB::table('mahasiswa')->where('user_id', $user->id)->select('status_akademik', 'enrollment_status_current')->first();
            $statusAkademik = (string) ($mhs->status_akademik ?? 'aktif');
            $enrollment = (string) ($mhs->enrollment_status_current ?? 'aktif');
            if ($enrollment === 'do') {
                $this->logAttempt($credentials['email'], false, $request);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Akun mahasiswa dengan status DO dinonaktifkan untuk login.',
                ])->onlyInput('email');
            }
            $target = in_array($enrollment, ['lulus', 'do'], true) || in_array($statusAkademik, ['do', 'alumni'], true) ? '/khs' : '/krs';
        } elseif ($user?->hasRole('admin_akademik')) {
            $target = '/master/fakultas';
        } elseif ($user?->hasRole('admin_keuangan')) {
            $target = '/keuangan/tagihan';
        } elseif ($user?->hasRole('dosen')) {
            $target = '/dosen/jadwal';
        }

        AuditLogger::log(
            aksi: 'Login sukses',
            modul: 'auth',
            entityType: 'users',
            entityId: $user?->id,
            konteks: ['target' => $target],
            request: $request
        );
        $this->logAttempt($credentials['email'], true, $request);

        return redirect()->intended($target);
    }

    public function logout(Request $request)
    {
        $userId = auth()->id();
        Auth::logout();

        $request->session()->forget(['auth_user_id', 'auth_role_id', 'auth_login_at', 'auth_last_activity']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        AuditLogger::log(
            aksi: 'Logout',
            modul: 'auth',
            entityType: 'users',
            entityId: $userId,
            konteks: [],
            request: $request
        );

        return redirect('/login');
    }

    private function logAttempt(string $email, bool $success, Request $request): void
    {
        DB::table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => $request->ip(),
            'success' => $success,
            'user_agent' => $request->userAgent(),
            'attempted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
