<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->jenis_akun, $roles)) {
            abort(403, 'Anda tidak memiliki otoritas untuk mengakses halaman ini.');
        }
        return $next($request);
    }
}
