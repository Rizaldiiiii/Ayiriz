<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
                ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return match ($user->role) {
            UserRole::Pusat => redirect()->intended(route('pusat.dashboard'))
                ->with('toast_success', 'Selamat datang kembali, Pusat Ayiriz.'),
            UserRole::Outlet => redirect()->intended(route('outlet.dashboard'))
                ->with('toast_success', 'Selamat datang di panel kasir & pengelolaan outlet.'),
            UserRole::Pelanggan => redirect()->intended(route('catalog.index'))
                ->with('toast_success', 'Berhasil masuk. Silakan pilih kantin dan menu favoritmu!'),
        };
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Pelanggan,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('catalog.index')
            ->with('toast_success', 'Akun pelanggan berhasil dibuat. Selamat memesan di Ayiriz!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog.index')
            ->with('toast_info', 'Anda telah keluar dari sesi.');
    }
}
