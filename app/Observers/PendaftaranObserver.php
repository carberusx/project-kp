<?php

namespace App\Observers;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PendaftaranObserver
{
    public function updated(Pendaftaran $pendaftaran): void
    {
        // Cek apakah status baru saja diubah menjadi 'diterima'
        if ($pendaftaran->wasChanged('status') && $pendaftaran->status === 'diterima') {

            // Cek apakah akun sudah ada
            $sudahAda = User::where('email', $pendaftaran->email)->exists();
            if ($sudahAda) return;

            // Buat password default dari NIM atau random
            $passwordDefault = Str::random(8);

            // Buat akun otomatis
            $user = User::create([
                'name'        => $pendaftaran->nama_lengkap,
                'email'       => $pendaftaran->email,
                'password'    => Hash::make($passwordDefault),
                'role'        => 'mahasiswa',
                'universitas' => $pendaftaran->universitas,
                'jurusan'     => $pendaftaran->jurusan,
            ]);

            // Hubungkan pendaftaran dengan user
            $pendaftaran->update(['user_id' => $user->id]);

            // Simpan password sementara ke catatan admin
            $pendaftaran->update([
                'catatan_admin' => ($pendaftaran->catatan_admin ?? '') .
                    "\n\n[SISTEM] Akun otomatis dibuat.\nEmail: {$user->email}\nPassword sementara: {$passwordDefault}"
            ]);
        }
    }
}
