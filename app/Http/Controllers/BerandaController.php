<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Pengaturan;

class BerandaController extends Controller
{
    public function index()
    {
        $batasPendaftar = (int) Pengaturan::getNilai('batas_max_pendaftar', 10);
        $statusPendaftaran = Pengaturan::getNilai('status_pendaftaran', 'buka');

        // Hanya hitung pendaftar yang BUKAN ditolak
        $jumlahPendaftar  = Pendaftaran::where('status', '!=', 'ditolak')->count();
        $sisaKuota        = max(0, $batasPendaftar - $jumlahPendaftar);
        
        $pendaftaranTutup = ($jumlahPendaftar >= $batasPendaftar) || ($statusPendaftaran === 'tutup');

        // Data real dari database
        $mahasiswaAktif = User::where('role', 'mahasiswa')->count();
        $reviewPending  = Pendaftaran::where('status', 'menunggu')->count();
        
        $magangAktif = Pendaftaran::where('status', 'diterima')->count();
        $persentase = $batasPendaftar > 0 ? ($magangAktif / $batasPendaftar) * 100 : 0;
        $persenDiterima = min(100, round($persentase));

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
