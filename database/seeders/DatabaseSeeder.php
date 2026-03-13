<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────
        $admin = User::create([
            'name'        => 'Administrator',
            'email'       => 'admin@simagang.go.id',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'universitas' => null,
        ]);

        // ── Mahasiswa Demo ─────────────────────────────────────────────────
        $mahasiswa = User::create([
            'name'        => 'Alex Johnson',
            'email'       => 'mahasiswa@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'mahasiswa',
            'nim'         => '2021001',
            'universitas' => 'Universitas Indonesia',
            'jurusan'     => 'Ilmu Pemerintahan',
            'telepon'     => '081234567890',
        ]);

        Pendaftaran::create([
            'user_id'         => $mahasiswa->id,
            'nama_lengkap'    => 'Alex Johnson',
            'email'           => 'mahasiswa@test.com',
            'universitas'     => 'Universitas Indonesia',
            'jurusan'         => 'Ilmu Pemerintahan',
            'motivasi'        => 'Saya ingin berkontribusi pada pelayanan publik dan mengembangkan kompetensi di bidang pemerintahan.',
            'status'          => 'diterima',
            'tanggal_mulai'   => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->addMonths(3)->endOfMonth(),
        ]);

        // ── Absensi Demo (7 hari terakhir) ────────────────────────────────
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            if ($tanggal->isWeekend()) continue;

            Absensi::create([
                'user_id'    => $mahasiswa->id,
                'tanggal'    => $tanggal->toDateString(),
                'jam_masuk'  => '08:00:00',
                'jam_keluar' => $i === 0 ? null : '17:00:00',
                'status'     => 'hadir',
            ]);
        }

        // ── Tugas Demo ─────────────────────────────────────────────────────
        $tugas1 = Tugas::create([
            'judul'      => 'Laporan Mingguan - Minggu ke-3',
            'deskripsi'  => 'Buat laporan kegiatan magang selama seminggu terakhir mencakup kegiatan, pembelajaran, dan kendala yang dihadapi.',
            'deadline'   => Carbon::now()->subDays(4)->setTime(23, 59),
            'tipe'       => 'laporan',
            'is_aktif'   => true,
        ]);

        $tugas2 = Tugas::create([
            'judul'      => 'Proposal Proyek Akhir',
            'deskripsi'  => 'Susun proposal proyek akhir magang dengan format yang telah ditentukan. Meliputi latar belakang, tujuan, metodologi, dan rencana kerja.',
            'deadline'   => Carbon::now()->addDays(2)->setTime(23, 59),
            'tipe'       => 'proyek',
            'is_aktif'   => true,
        ]);

        $tugas3 = Tugas::create([
            'judul'      => 'Formulir Evaluasi Tengah Program',
            'deskripsi'  => 'Isi formulir evaluasi diri dan evaluasi pembimbing lapangan untuk penilaian tengah program magang.',
            'deadline'   => Carbon::now()->addDays(8)->setTime(23, 59),
            'tipe'       => 'evaluasi',
            'is_aktif'   => true,
        ]);

        // Tugas 1 sudah dikumpulkan
        PengumpulanTugas::create([
            'tugas_id'        => $tugas1->id,
            'user_id'         => $mahasiswa->id,
            'file_path'       => 'tugas/sample-laporan.pdf',
            'catatan'         => 'Laporan minggu ke-3 sudah saya selesaikan.',
            'status'          => 'dinilai',
            'nilai'           => 88.5,
            'feedback'        => 'Laporan sangat baik dan terstruktur. Pertahankan!',
            'dikumpulkan_at'  => Carbon::now()->subDays(4)->setTime(20, 30),
        ]);

        // ── Pendaftar tambahan (untuk tampil di tabel admin) ───────────────
        $extraApplicants = [
            ['nama_lengkap' => 'Budi Santoso',    'email' => 'budi@ui.ac.id',      'universitas' => 'Universitas Indonesia',   'jurusan' => 'Administrasi Negara', 'status' => 'menunggu'],
            ['nama_lengkap' => 'Citra Dewi',      'email' => 'citra@ugm.ac.id',    'universitas' => 'Universitas Gadjah Mada', 'jurusan' => 'Hukum',               'status' => 'wawancara'],
            ['nama_lengkap' => 'Dimas Pratama',   'email' => 'dimas@its.ac.id',    'universitas' => 'Institut Teknologi Sepuluh Nopember', 'jurusan' => 'Teknik Informatika', 'status' => 'menunggu'],
            ['nama_lengkap' => 'Eka Fitriani',    'email' => 'eka@unpad.ac.id',    'universitas' => 'Universitas Padjadjaran', 'jurusan' => 'Komunikasi',          'status' => 'diterima'],
            ['nama_lengkap' => 'Fajar Nugroho',   'email' => 'fajar@undip.ac.id',  'universitas' => 'Universitas Diponegoro',  'jurusan' => 'Ekonomi',             'status' => 'menunggu'],
        ];

        foreach ($extraApplicants as $data) {
            Pendaftaran::create(array_merge($data, [
                'motivasi' => 'Saya sangat tertarik untuk berkontribusi dalam program magang pemerintah ini.',
            ]));
        }
    }
}
