<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $validated['email'];
        $customer = DB::table('customers')->where('email', $email)->first();
        $adminAccount = DB::table('admins')->where('email', $email)->first();

        if (!$customer) {
            if ($adminAccount) {
                return back()
                    ->withErrors([
                        'email' => 'Email ini untuk akun admin. Buka halaman Login Admin (bukan login pelanggan).',
                    ])
                    ->withInput();
            }

            return back()
                ->withErrors(['email' => 'Email atau password tidak valid. Belum punya akun? Daftar dulu.'])
                ->withInput();
        }

        if (!Hash::check($validated['password'], $customer->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak valid. Belum punya akun? Daftar dulu.'])
                ->withInput();
        }

        if ($adminAccount) {
            return back()
                ->withErrors([
                    'email' => 'Email ini terdaftar sebagai admin. Jangan pakai login pelanggan — buka halaman Login Admin untuk masuk ke panel admin.',
                ])
                ->withInput();
        }

        $account = $customer;

        $request->session()->put('auth', [
            'id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'role' => 'user',
        ]);

        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('status', 'Berhasil masuk. Selamat datang, ' . $account->name . '!');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:customers,email',
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'terms.accepted' => 'Kamu perlu menyetujui syarat & ketentuan.',
        ]);

        // Check if email is already used in admins table
        if (DB::table('admins')->where('email', $validated['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'Email ini dipakai untuk akun admin. Gunakan email lain atau masuk lewat Login Admin.'])
                ->withInput();
        }

        DB::table('customers')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Akun berhasil dibuat. Silakan login dengan email dan password kamu.');
    }

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $account = DB::table('admins')->where('email', $validated['email'])->first();

        if (!$account || !Hash::check($validated['password'], $account->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password admin tidak valid.'])
                ->withInput();
        }

        $request->session()->put('auth', [
            'id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'role' => 'admin',
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Selamat datang di panel admin, ' . $account->name . '.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Berhasil logout.');
    }
}
