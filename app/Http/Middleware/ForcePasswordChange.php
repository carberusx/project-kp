<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->isMahasiswa() &&
            $user->force_password_change &&
            !$request->routeIs('mahasiswa.profil') &&
            !$request->routeIs('mahasiswa.profil.password.update')
        ) {
            return redirect()->route('mahasiswa.profil')
                ->with('warning_ganti_password', true);
        }

        return $next($request);
    }
}
