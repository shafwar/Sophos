<?php
namespace App\Http\Controllers;

use App\Models\User; // Tambahkan import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahkan import ini
use Illuminate\Support\Facades\DB;

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
            // Cek status user
            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda belum di-approve admin.'
                ])->withInput();
            }
            // Regenerasi sesi untuk keamanan
            $request->session()->regenerate();

            // Tambahkan log aktivitas login
            \DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'activity' => 'Login',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
            'status' => 'pending',
        ]);

        // Log ke activity_logs
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity' => 'Register (pending approval)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect ke login dengan pesan
        return redirect()->route('login')
            ->with('success', 'Akun Anda menunggu persetujuan admin.');
    }
}
