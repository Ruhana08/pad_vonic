<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MasterAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isMasterAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Akses ditolak. Halaman ini khusus Master Admin.');
        }

        return $next($request);
    }
}
