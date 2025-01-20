<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan form untuk meminta link reset password.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email'); // Ganti dengan file Blade untuk meminta email reset
    }

    /**
     * Mengirimkan email untuk reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Kirim email reset password
        $response = Password::sendResetLink($request->only('email'));

        // Cek apakah pengiriman berhasil atau tidak
        if ($response == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda.');
        }

        return back()->withErrors(['email' => 'Email tidak terdaftar atau salah.']);
    }

    /**
     * Menampilkan form untuk mereset password dengan token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        ); // Ganti dengan file Blade untuk reset password
    }

    /**
     * Memproses reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        // Validasi input reset password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        // Proses reset password
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        // Jika berhasil reset password
        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil direset, silakan login.');
        }

        return back()->withErrors(['email' => 'Proses reset password gagal.']);
    }
}
