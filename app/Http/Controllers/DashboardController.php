<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Tugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function mahasiswaDashboard()
    {
        $user = Auth::user();

        $absensiHariIni = $user->absensiHariIni();
        $totalHadirBulanIni = $user->totalHadirBulanIni();

        // Tugas aktif dengan status pengumpulan
        $tugasAktif = Tugas::where('is_aktif', true)
   		 ->whereHas('mahasiswas', fn($q) => $q->where('users.id', $user->id))
   		 ->orderBy('deadline')
   		 ->get()
            ->map(function ($t) use ($user) {
                $t->pengumpulan = $t->pengumpulanByUser($user->id);
                return $t;
            });

        $tugasDikumpulkan  = $tugasAktif->filter(fn($t) => $t->pengumpulan)->count();
        $tugasBelumKumpul  = $tugasAktif->filter(fn($t) => !$t->pengumpulan)->count();

        // Riwayat absensi 5 terakhir
        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        // Deadline terdekat
        $deadlineDekat = $tugasAktif
            ->filter(fn($t) => !$t->pengumpulan)
            ->take(3);

        return view('mahasiswa.dashboard', compact(
            'user',
            'absensiHariIni',
            'totalHadirBulanIni',
            'tugasAktif',
            'tugasDikumpulkan',
            'tugasBelumKumpul',
            'riwayatAbsensi',
            'deadlineDekat',
        ));
    }

    public function profil()
    {
        $user        = Auth::user();
        $pendaftaran = $user->pendaftaran;
        return view('mahasiswa.profil', compact('user', 'pendaftaran'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password Anda berhasil diperbarui!');
    }
}
