<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->session()->get('auth');

        if (!is_array($auth) || ($auth['role'] ?? null) !== 'admin') {
            return redirect()->route('login.admin')->withErrors([
                'email' => 'Akses admin hanya untuk akun admin.',
            ]);
        }

        return $next($request);
    }
}

