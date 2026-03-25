<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'user_id'    => $user->id,
            'tanggal'    => today()->toDateString(),
            'jam_masuk'  => now()->format('H:i:s'),
            'status'     => 'hadir',
            'keterangan' => $request->input('keterangan'),
        ]);

        return back()->with('success', 'Absensi masuk berhasil dicatat pada ' . now()->format('H:i') . '.');
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

        $absensi->update([
            'jam_keluar' => now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absensi pulang berhasil dicatat pada ' . now()->format('H:i') . '.');
    }

    public function izinSakit(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'status'     => 'required|in:izin,sakit',
            'keterangan' => 'required|string|min:5|max:500',
        ], [
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.min'      => 'Keterangan minimal 5 karakter.',
        ]);

        $existing = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'user_id'    => $user->id,
            'tanggal'    => today()->toDateString(),
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        $label = $request->status === 'izin' ? 'Izin' : 'Sakit';
        return back()->with('success', $label . ' berhasil dicatat.');
    }
}
