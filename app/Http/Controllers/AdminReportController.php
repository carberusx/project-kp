<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tugas;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function rekapEvaluasi(User $user)
    {
        // Validasi, pastikan yang diakses adalah mahasiswa
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Data tidak ditemukan atau user bukan mahasiswa.');
        }

        // Ambil data absensi
        $absensi = $user->absensis;
        $totalHadir = $absensi->where('status', 'hadir')->count();
        $totalIzin = $absensi->where('status', 'izin')->count();
        $totalSakit = $absensi->where('status', 'sakit')->count();
        $totalAlpha = $absensi->where('status', 'alpha')->count();

        // Hitung Nilai Kehadiran (Sakit & Izin tidak mengurangi nilai, Alpha mengurangi)
        $totalWajibHadir = $totalHadir + $totalAlpha;
        $nilaiAbsensi = $totalWajibHadir > 0 ? ($totalHadir / $totalWajibHadir) * 100 : 0;

        // Ambil data tugas
        $tugasList = $user->pengumpulanTugas()->with('tugas')->get();
        $totalTugasDikerjakan = $tugasList->count();
        
        // Total tugas yang ditugaskan secara spesifik ke mahasiswa ini
        $assignedTugas = $user->tugas()->get();
        $totalTugasSistem = $assignedTugas->count();

        // Hitung Rata-rata Nilai Tugas (Pembagi adalah total tugas sistem)
        $totalNilai = $tugasList->sum('nilai');
        $rataRataNilai = $totalTugasSistem > 0 ? ($totalNilai / $totalTugasSistem) : 100;



        return view('admin.rekap-evaluasi', compact(
            'user',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpha',
            'nilaiAbsensi',
            'tugasList',
            'totalTugasDikerjakan',
            'totalTugasSistem',
            'rataRataNilai'
        ));
    }
}
