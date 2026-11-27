<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Akses ditolak. Halaman ini khusus Administrator.');
        }

        return $next($request);
    }
}
