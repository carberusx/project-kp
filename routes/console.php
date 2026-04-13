<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler untuk mengatur Alpha bagi mahasiswa yang belum absensi
Schedule::call(function () {
    $today = Carbon::today();
    
    // 1. Filter Akhir Pekan (Sabtu & Minggu)
    if ($today->isWeekend()) {
        return;
    }

    // 2. Filter Tanggal Merah / Cuti Bersama
    $isLibur = \App\Models\HariLibur::whereDate('tanggal', $today)->exists();
    if ($isLibur) {
        return;
    }
    
    // Ambil semua user dengan role mahasiswa
    $mahasiswas = User::where('role', 'mahasiswa')->get();
    
    foreach ($mahasiswas as $mahasiswa) {
        // Cek apakah mahasiswa ini sudah ada record absensi hari ini (apapun statusnya: hadir, izin, sakit)
        $hasAbsensi = Absensi::where('user_id', $mahasiswa->id)
                             ->whereDate('tanggal', $today)
                             ->exists();
                             
        // Jika belum ada record sama sekali hari ini, insert data Alpha
        if (!$hasAbsensi) {
            Absensi::create([
                'user_id'    => $mahasiswa->id,
                'tanggal'    => $today,
                'status'     => 'alpha',
                'keterangan' => 'Sistem otomatis: Tidak melakukan absensi hingga pukul 17:00',
            ]);
        }
    }
})->dailyAt('23:59');
