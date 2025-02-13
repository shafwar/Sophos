<?php
namespace App\Http\Controllers;

use App\Models\User; // Tambahkan import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahkan import ini

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

        // Menampilkan halaman register
    public function showRegisterForm()
    {
        // Jika pengguna sudah login, arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('register');
    }

    // Proses registrasi
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Login user yang baru dibuat
        Auth::login($user);

        // Regenerasi sesi untuk keamanan
        $request->session()->regenerate();

        // Redirect ke dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome to Pertagas Sophos.');
    }
}
