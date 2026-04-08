<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Cek kadaluarsa khusus untuk mahasiswa
            if ($user->role === 'mahasiswa') {
                $pendaftaran = $user->pendaftaran;
                if ($pendaftaran && $pendaftaran->tanggal_selesai) {
                    $tanggalBerakhir = \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->endOfDay();
                    if (now()->gt($tanggalBerakhir)) {
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return back()->withErrors(['email' => 'Masa magang Anda telah berakhir. Akun tidak dapat digunakan lagi.'])->onlyInput('email');
                    }
                }
            }

            $request->session()->regenerate();
            return $this->redirectAfterLogin($user);
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectAfterLogin($user)
    {
        if ($user->role === 'admin') {
            return redirect('/admin');
        }
        return redirect()->route('mahasiswa.dashboard');
    }
}
