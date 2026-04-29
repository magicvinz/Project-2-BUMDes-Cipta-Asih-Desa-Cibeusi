<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles)) {
            // User sudah login tapi role tidak sesuai → redirect ke dashboard-nya sendiri
            $role = $request->user()->role;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'pengelola_bumdes') {
                return redirect()->route('pengelola.dashboard');
            } elseif ($role === 'pengunjung') {
                return redirect()->route('pengunjung.dashboard');
            }
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
