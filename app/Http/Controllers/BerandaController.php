<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;

class BerandaController extends Controller
{
    public function index()
    {
        $batasPendaftar = 10;

        // Hanya hitung pendaftar yang BUKAN ditolak
        $jumlahPendaftar  = Pendaftaran::where('status', '!=', 'ditolak')->count();
        $pendaftaranTutup = $jumlahPendaftar >= $batasPendaftar;
        $sisaKuota        = max(0, $batasPendaftar - $jumlahPendaftar);

        // Data real dari database
        $mahasiswaAktif = User::where('role', 'mahasiswa')->count();
        $reviewPending  = Pendaftaran::where('status', 'menunggu')->count();
        $totalDiterima  = Pendaftaran::where('status', 'diterima')->count();
        $totalSemuaPendaftar = Pendaftaran::count();
        $persenDiterima = $totalSemuaPendaftar > 0
                          ? round(($totalDiterima / $totalSemuaPendaftar) * 100)
                          : 0;

        return view('beranda.index', compact(
            'jumlahPendaftar',
            'batasPendaftar',
            'pendaftaranTutup',
            'sisaKuota',
            'mahasiswaAktif',
            'reviewPending',
            'persenDiterima',
        ));
    }
}
