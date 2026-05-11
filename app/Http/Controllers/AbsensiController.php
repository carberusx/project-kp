<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        $user           = Auth::user();
        $riwayat        = Absensi::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->paginate(20);
        $absensiHariIni = $user->absensiHariIni();
        $totalHadir     = $user->totalHadirBulanIni();

        return view('mahasiswa.absensi', compact('riwayat', 'absensiHariIni', 'totalHadir'));
    }

    public function checkIn(Request $request)
    {
        if ($this->isHariLibur()) {
            return back()->with('error', 'Hari ini adalah hari libur / akhir pekan. Anda tidak dapat melakukan absensi.');
        }

        $user = Auth::user();

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $request->validate([
            'foto'      => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'alamat'    => 'nullable|string',
            'keterangan_mahasiswa' => 'nullable|string|max:255',
        ], [
            'foto.required'      => 'Foto wajib diambil.',
            'latitude.required'  => 'Lokasi GPS wajib diaktifkan.',
            'longitude.required' => 'Lokasi GPS wajib diaktifkan.',
        ]);

        // Validasi Radius 50 Meter
        $kantor_lat = \App\Models\Pengaturan::getNilai('kantor_lat', '-6.9834745188590315');
        $kantor_lon = \App\Models\Pengaturan::getNilai('kantor_lon', '110.40770197890485');
        $max_radius = \App\Models\Pengaturan::getNilai('radius_absensi', 50);

        $jarak = $this->hitungJarak($request->latitude, $request->longitude, $kantor_lat, $kantor_lon);

        if ($jarak > $max_radius) {
            return back()->with('error', 'Gagal absen. Anda berada ' . round($jarak) . ' meter dari kantor. Anda hanya bisa absen dalam radius ' . $max_radius . ' meter.');
        }

        // Simpan foto base64
        $fotoPath = $this->saveFoto($request->foto, 'masuk');

        // Cek keterlambatan
        $jamMasukStandar = \App\Models\Pengaturan::getNilai('jam_masuk_standar', '07:00:00');
        $jamSekarang = now()->format('H:i:s');
        $noteMasuk   = [];
        
        if ($jamSekarang > $jamMasukStandar) {
            $parts = explode(':', $jamMasukStandar);
            $waktuMasukStandar = \Carbon\Carbon::today()->setTime((int)$parts[0], (int)$parts[1], 0);
            $sekarang = now();
            $diffInMinutes = $waktuMasukStandar->diffInMinutes($sekarang);
            
            $jam = floor($diffInMinutes / 60);
            $menit = $diffInMinutes % 60;
            
            $teksTelat = [];
            if ($jam > 0) $teksTelat[] = "$jam jam";
            if ($menit > 0) $teksTelat[] = "$menit menit";
            $teksTelatStr = count($teksTelat) > 0 ? implode(' ', $teksTelat) : 'kurang dari 1 menit';
            
            $noteMasuk[] = "Terlambat masuk " . $teksTelatStr . " (" . $sekarang->format('H:i') . ")";
        }
        if ($request->filled('keterangan_mahasiswa')) {
            $noteMasuk[] = "Catatan: " . $request->keterangan_mahasiswa;
        }
        
        $keterangan = count($noteMasuk) > 0 ? implode(' | ', $noteMasuk) : null;

        Absensi::create([
            'user_id'         => $user->id,
            'tanggal'         => today()->toDateString(),
            'jam_masuk'       => $jamSekarang,
            'status'          => 'hadir',
            'foto_masuk'      => $fotoPath,
            'latitude_masuk'  => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'alamat_masuk'    => $request->alamat,
            'keterangan'      => $keterangan,
        ]);

        return redirect()->route('mahasiswa.absensi.index')->with('success', 'Absensi masuk berhasil pada ' . now()->format('H:i') . '.');
    }

    public function checkOut(Request $request)
    {
        $user    = Auth::user();
        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum melakukan absensi masuk hari ini.');
        }

        if ($absensi->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan absensi pulang hari ini.');
        }

        $request->validate([
            'foto'      => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'alamat'    => 'nullable|string',
            'keterangan_mahasiswa' => 'nullable|string|max:255',
        ], [
            'foto.required'      => 'Foto wajib diambil.',
            'latitude.required'  => 'Lokasi GPS wajib diaktifkan.',
            'longitude.required' => 'Lokasi GPS wajib diaktifkan.',
        ]);

        // Validasi Radius 50 Meter
        $kantor_lat = \App\Models\Pengaturan::getNilai('kantor_lat', '-6.9834745188590315');
        $kantor_lon = \App\Models\Pengaturan::getNilai('kantor_lon', '110.40770197890485');
        $max_radius = \App\Models\Pengaturan::getNilai('radius_absensi', 50);

        $jarak = $this->hitungJarak($request->latitude, $request->longitude, $kantor_lat, $kantor_lon);

        if ($jarak > $max_radius) {
            return back()->with('error', 'Gagal absen. Anda berada ' . round($jarak) . ' meter dari kantor. Anda hanya bisa absen dalam radius ' . $max_radius . ' meter.');
        }

        $fotoPath = $this->saveFoto($request->foto, 'keluar');

        // Cek pulang cepat dengan toleransi 15 menit
        $jamPulangStandar = \App\Models\Pengaturan::getNilai('jam_pulang_standar', '15:30:00');
        $batasToleransi = \Carbon\Carbon::parse($jamPulangStandar)->subMinutes(15)->format('H:i:s');
        $jamSekarang = now()->format('H:i:s');
        $keterangan  = $absensi->keterangan;
        
        $notePulang = [];
        if ($jamSekarang < $batasToleransi) {
            $notePulang[] = "Pulang cepat (" . now()->format('H:i') . ")";
        }
        if ($request->filled('keterangan_mahasiswa')) {
            $notePulang[] = "Catatan: " . $request->keterangan_mahasiswa;
        }

        if (count($notePulang) > 0) {
            $tambahan = implode(' | ', $notePulang);
            $keterangan = $keterangan ? $keterangan . " | " . $tambahan : $tambahan;
        }

        $absensi->update([
            'jam_keluar'       => $jamSekarang,
            'foto_keluar'      => $fotoPath,
            'latitude_keluar'  => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'alamat_keluar'    => $request->alamat,
            'keterangan'       => $keterangan,
        ]);

        return redirect()->route('mahasiswa.absensi.index')->with('success', 'Absensi pulang berhasil pada ' . now()->format('H:i') . '.');
    }

    public function izinSakit(Request $request)
    {
        if ($this->isHariLibur()) {
            return back()->with('error', 'Hari ini adalah hari libur / akhir pekan. Anda tidak dapat mengajukan izin/sakit.');
        }

        $user = Auth::user();

        $request->validate([
            'status'     => 'required|in:izin,sakit',
            'keterangan' => 'required|string|min:5|max:500',
            'bukti_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.min'      => 'Keterangan minimal 5 karakter.',
            'bukti_file.mimes'    => 'File bukti harus berupa PDF, JPG, atau PNG.',
            'bukti_file.max'      => 'Ukuran file bukti maksimal 2MB.',
        ]);

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $filename = 'bukti_' . $request->status . '_' . now()->format('Ymd_His') . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('absensi/bukti', $filename, 'public');
        }

        Absensi::create([
            'user_id'    => $user->id,
            'tanggal'    => today()->toDateString(),
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
            'bukti_file' => $buktiPath,
        ]);

        $label = $request->status === 'izin' ? 'Izin' : 'Sakit';
        return redirect()->route('mahasiswa.absensi.index')->with('success', $label . ' berhasil dicatat.');
    }

    private function saveFoto(string $base64, string $tipe): string
    {
        // Hapus prefix base64
        $image    = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image    = base64_decode($image);
        $filename = 'absensi/' . $tipe . '/' . now()->format('Ymd_His') . '_' . auth()->id() . '.jpg';

        Storage::disk('public')->put($filename, $image);

        return $filename;
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam satuan meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Jarak dalam meter
    }

    private function isHariLibur(): bool
    {
        $today = \Carbon\Carbon::today();

        // Cek apakah hari ini ditetapkan sebagai Hari Kerja Khusus (override weekend/libur)
        $kerjaKhusus = \App\Models\HariLibur::whereDate('tanggal', $today)
            ->where('tipe', 'kerja_khusus')
            ->exists();
        if ($kerjaKhusus) {
            return false; // Meskipun weekend/libur, absensi tetap dibuka
        }

        // Cek weekend
        if ($today->isWeekend()) {
            return true;
        }

        // Cek hari libur nasional (tipe = libur)
        if (\App\Models\HariLibur::whereDate('tanggal', $today)->where('tipe', 'libur')->exists()) {
            return true;
        }

        return false;
    }
}
