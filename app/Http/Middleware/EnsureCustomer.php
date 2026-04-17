<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->session()->get('auth');

        if (is_array($auth) && ($auth['role'] ?? null) === 'user') {
            return $next($request);
        }

        if (is_array($auth) && ($auth['role'] ?? null) === 'admin') {
            return redirect()
                ->route('home')
                ->withErrors(['email' => 'Checkout hanya untuk akun pelanggan (user).']);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()
            ->route('login')
            ->with('status', 'Silakan daftar atau login sebagai pelanggan untuk melanjutkan checkout.');
    }
}
