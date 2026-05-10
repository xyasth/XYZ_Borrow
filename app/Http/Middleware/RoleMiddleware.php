<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next, ...$roles)
    {
        if (!Auth::check() || !in_array(Auth::user()->jenis_akun, $roles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }
        return $next($request);
    }
}
