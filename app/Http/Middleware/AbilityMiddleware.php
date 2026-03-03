<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbilityMiddleware
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        foreach ($abilities as $ability) {
            if ($user->hasAbility($ability)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki permission untuk aksi ini.');
    }
}

