<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Check2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Jika user login, memiliki 2FA aktif, dan belum verifikasi di sesi ini
        if ($user && !empty($user->google2fa_secret) && !$request->session()->has('2fa_passed')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
