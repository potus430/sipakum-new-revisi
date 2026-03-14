<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user login tapi is_active adalah false
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda belum diaktifkan oleh Superadmin.']);
        }

        return $next($request);
    }
}
