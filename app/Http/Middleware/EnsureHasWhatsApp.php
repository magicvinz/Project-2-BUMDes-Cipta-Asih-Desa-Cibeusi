<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasWhatsApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === 'pengunjung' && empty($user->no_hp)) {
            return redirect()->route('pengunjung.profil.index')
                ->with('warning', 'Silakan lengkapi Nomor WhatsApp Anda terlebih dahulu sebelum dapat memesan tiket.');
        }

        return $next($request);
    }
}
