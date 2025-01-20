<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login.
    public function showLoginForm()
    {
        // Jika pengguna sudah login, arahkan langsung ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Menampilkan form login
        return view('login');
    }

    // Proses login.
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Coba autentikasi
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Regenerasi sesi untuk keamanan
            $request->session()->regenerate();

            // Redirect ke dashboard jika berhasil login
            return redirect()->route('dashboard');
        }

        // Jika login gagal, kirimkan pesan kesalahan dan input kembali
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        // Proses logout
        Auth::logout();

        // Invalidasi sesi pengguna
        $request->session()->invalidate();

        // Regenerasi token CSRF
        $request->session()->regenerateToken();

        // Redirect ke halaman login setelah logout
        return redirect()->route('login');
    }
}
