<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function login()
    {
        // Jika sudah login, lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        
        return view('be.auth.login');
    }

    /**
     * Proses otentikasi
     */
    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Redirect sesuai role (kalau mau dibedakan, bisa ditambahkan disini)
            // Saat ini semua dilempar ke dashboard
            return redirect()->route('dashboard.index')->with('simpan', 'Berhasil Login!');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('simpan', 'Berhasil Logout!');
    }
}
