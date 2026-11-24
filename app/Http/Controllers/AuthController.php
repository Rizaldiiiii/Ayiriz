<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        };
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
