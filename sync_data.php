<?php
// Script untuk sinkronisasi data NIM & telepon dari pendaftaran ke akun user
// Jalankan: php artisan tinker sync_data.php
// Atau: php sync_data.php (dari folder project)

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pendaftaran;

$pendaftarans = Pendaftaran::whereNotNull('user_id')->get();

echo "=== Sinkronisasi Data Pendaftaran ke Akun User ===" . PHP_EOL;
echo "Ditemukan " . $pendaftarans->count() . " pendaftaran dengan akun terkait." . PHP_EOL . PHP_EOL;

foreach ($pendaftarans as $p) {
    if ($p->user) {
        $p->user->update([
            'nim'         => $p->nim,
            'universitas' => $p->universitas,
            'jurusan'     => $p->jurusan,
            'telepon'     => $p->no_telpon,
        ]);
        echo "✓ Synced: {$p->nama_lengkap} | NIM: {$p->nim} | Telpon: {$p->no_telpon}" . PHP_EOL;
    }
}

echo PHP_EOL . "Selesai!" . PHP_EOL;
