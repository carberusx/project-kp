<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Evaluasi Magang - {{ $user->name }}</title>
    <!-- Gunakan tailwind via CDN untuk mempermudah styling (print version tidak butuh bundle yang berat) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4;
                margin: 0; /* Margin 0 akan menyembunyikan header/footer bawaan browser */
            }
        }
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            padding: 30px 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        @media print {
            .a4-container {
                width: 100%;
                min-height: 100%;
                margin: 0;
                padding: 20mm; /* Tambahkan padding 20mm agar teks tidak menempel di ujung kertas */
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="text-gray-800">

    <!-- Floating Action Button for Print -->
    <div class="fixed top-5 right-5 no-print flex gap-2">
        <a href="{{ App\Filament\Resources\UserResource::getUrl('index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow-md transition">
            Kembali
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-md flex items-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
            </svg>
            Cetak / PDF
        </button>
    </div>

    <div class="a4-container">
        <!-- Header / Kop -->
        <div class="text-center border-b-4 border-gray-800 pb-4 mb-6">
            <h1 class="text-2xl font-bold uppercase tracking-widest">Laporan Evaluasi Magang</h1>
            <p class="text-gray-600 mt-1">Sistem Informasi Mahasiswa Magang</p>
            <p class="text-sm text-gray-500">Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <!-- Biodata -->
        <div class="mb-8">
            <h2 class="text-lg font-bold border-b-2 border-gray-300 mb-3 pb-1">Biodata Peserta</h2>
            <table class="w-full text-sm">
                <tr>
                    <td class="w-1/4 py-1 font-semibold text-gray-600">Nama Lengkap</td>
                    <td class="w-3/4 py-1">: <span class="font-bold text-gray-900">{{ $user->name }}</span></td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold text-gray-600">NIM</td>
                    <td class="py-1">: {{ $user->nim ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold text-gray-600">Universitas</td>
                    <td class="py-1">: {{ $user->universitas ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold text-gray-600">Jurusan</td>
                    <td class="py-1">: {{ $user->jurusan ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- Rekap Absensi -->
            <div>
                <h2 class="text-lg font-bold border-b-2 border-gray-300 mb-3 pb-1">Rekapitulasi Kehadiran</h2>
                <table class="w-full text-sm border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 text-left">Status</th>
                            <th class="border border-gray-300 px-3 py-2 text-center w-24">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Hadir</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-green-600">{{ $totalHadir }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Izin</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-yellow-600">{{ $totalIzin }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Sakit</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-blue-600">{{ $totalSakit }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Alpha</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-red-600">{{ $totalAlpha }}</td>
                        </tr>
                        <tr class="bg-gray-50 border-t-2 border-gray-300">
                            <td class="border border-gray-300 px-3 py-2 font-bold text-right">Nilai Kehadiran:</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-lg text-indigo-600">{{ number_format($nilaiAbsensi, 1) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rekap Tugas (Summary) -->
            <div>
                <h2 class="text-lg font-bold border-b-2 border-gray-300 mb-3 pb-1">Ringkasan Tugas</h2>
                <table class="w-full text-sm border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 text-left">Keterangan</th>
                            <th class="border border-gray-300 px-3 py-2 text-center w-24">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Tugas Diberikan</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold">{{ $totalTugasSistem }}</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">Tugas Dikerjakan</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold">{{ $totalTugasDikerjakan }}</td>
                        </tr>
                        <tr class="bg-gray-50 border-t-2 border-gray-300">
                            <td class="border border-gray-300 px-3 py-2 font-bold text-right">Rata-rata Tugas:</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold text-indigo-600 text-lg">{{ number_format($rataRataNilai, 1) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Detail Tugas -->
        <div class="mb-8">
            <h2 class="text-lg font-bold border-b-2 border-gray-300 mb-3 pb-1">Rincian Tugas Dikerjakan</h2>
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Judul Tugas</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Tanggal Kumpul</th>
                        <th class="border border-gray-300 px-3 py-2 text-center">Status Waktu</th>
                        <th class="border border-gray-300 px-3 py-2 text-center w-24">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugasList as $index => $pengumpulan)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $pengumpulan->tugas->judul ?? '-' }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $pengumpulan->dikumpulkan_at ? $pengumpulan->dikumpulkan_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-xs">
                                @if($pengumpulan->isLate())
                                    <span class="text-red-600">Terlambat ({{ $pengumpulan->terlambat_text }})</span>
                                @else
                                    <span class="text-green-600">Tepat Waktu</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-bold">
                                {{ $pengumpulan->nilai ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 px-3 py-4 text-center text-gray-500 italic">Belum ada tugas yang dikumpulkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan 
        <div class="mt-16 flex justify-end">
            <div class="text-center w-64">
                <p class="text-sm mb-16">Mengetahui,<br>Pembimbing Magang</p>
                <div class="border-b border-gray-800 w-full mb-1"></div>
                <p class="text-sm font-semibold">Admin / Penanggung jawab</p>
            </div>
        </div>-->

    </div>
</body>
</html>
