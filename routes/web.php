<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Beranda & Pendaftaran (Publik) ────────────────────────────────────────
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::post('/daftar', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

// ── Autentikasi ───────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Area Mahasiswa (Terautentikasi) ───────────────────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
    Route::post('/absensi/izin-sakit', [AbsensiController::class, 'izinSakit'])->name('absensi.izinsakit');
    // Tugas
    Route::get('/tugas', [TugasController::class, 'mahasiswaIndex'])->name('tugas.index');
    Route::get('/tugas/{tugas}/kumpul', [TugasController::class, 'showUpload'])->name('tugas.upload');
    Route::post('/tugas/{tugas}/kumpul', [TugasController::class, 'submitTugas'])->name('tugas.submit');

    // Profil
    Route::get('/profil', [DashboardController::class, 'profil'])->name('profil');
});

// Redirect /dashboard ke role yang sesuai
Route::get('/dashboard', function () {
    if (auth()->user()?->role === 'admin') {
        return redirect('/admin');
    }
    return redirect()->route('mahasiswa.dashboard');
})->middleware('auth')->name('dashboard');
