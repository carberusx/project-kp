<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Cek apakah pendaftaran ditutup manual
            $statusPendaftaran = Pengaturan::getNilai('status_pendaftaran', 'buka');
            if ($statusPendaftaran === 'tutup') {
                return redirect()->route('beranda')
                    ->with('error', 'Maaf, form pendaftaran saat ini sedang ditutup oleh Admin.');
            }

            // Batas maksimal pendaftar
            $batasPendaftar = (int) Pengaturan::getNilai('batas_max_pendaftar', 10);

            // Cek apakah sudah mencapai batas (hanya hitung yang BUKAN ditolak, sama seperti di BerandaController)
            $jumlahPendaftar = Pendaftaran::where('status', '!=', 'ditolak')->count();

            if ($jumlahPendaftar >= $batasPendaftar) {
                return redirect()->route('beranda')
                    ->with('error', 'Maaf, pendaftaran telah ditutup karena kuota ' . $batasPendaftar . ' pendaftar sudah terpenuhi.');
            }

            $validated = $request->validate([
                'nama_lengkap'  => 'required|string|max:255',
                'email' => [
                        'required',
                        'email',
                        function ($attribute, $value, $fail) {
                            // Boleh daftar lagi kalau status sebelumnya ditolak
                            $existing = \App\Models\Pendaftaran::where('email', $value)->latest()->first();
                            if ($existing && $existing->status !== 'ditolak') {
                                $fail('Email ini sudah pernah mendaftar.');
                            }
                        },
                    ],
                'nim'           => 'required|string|max:50',
                'no_telpon'     => 'required|string|max:20',
                'universitas'   => 'required|string|max:255',
                'jurusan'       => 'required|string|max:255',
                'motivasi'      => 'required|string|min:20|max:2000',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai'=> 'required|date|after:tanggal_mulai',
                'file_dokumen'  => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
                'setuju'        => 'required|accepted',
            ], [
                'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
                'email.required'         => 'Email wajib diisi.',
                'email.unique'           => 'Email ini sudah pernah mendaftar.',
                'nim.required'           => 'NIM wajib diisi.',
                'no_telpon.required'     => 'Nomor telepon wajib diisi.',
                'universitas.required'   => 'Nama institusi wajib diisi.',
                'jurusan.required'       => 'Jurusan wajib diisi.',
                'motivasi.required'      => 'Motivasi wajib diisi.',
                'motivasi.min'           => 'Motivasi minimal 20 karakter.',
                'tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
                'tanggal_mulai.date'     => 'Format tanggal mulai tidak valid.',
                'tanggal_selesai.required'=> 'Tanggal selesai magang wajib diisi.',
                'tanggal_selesai.date'   => 'Format tanggal selesai tidak valid.',
                'tanggal_selesai.after'  => 'Tanggal selesai harus setelah tanggal mulai.',
                'file_dokumen.mimes'     => 'Format file harus PDF, DOC, DOCX, atau ZIP.',
                'file_dokumen.max'       => 'Ukuran file maksimal 10 MB.',
                'setuju.required'        => 'Anda harus menyetujui pernyataan di atas.',
            ]);



            $filePath = null;
            if ($request->hasFile('file_dokumen')) {
                $filePath = $request->file('file_dokumen')
                    ->store('pendaftaran/dokumen', 'public');
            }

            $pendaftaran = Pendaftaran::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email'        => $validated['email'],
                'nim'          => $validated['nim'],
                'no_telpon'    => $validated['no_telpon'],
                'universitas'  => $validated['universitas'],
                'jurusan'      => $validated['jurusan'],
                'motivasi'     => $validated['motivasi'],
                'tanggal_mulai'=> $validated['tanggal_mulai'],
                'tanggal_selesai'=> $validated['tanggal_selesai'],
                'file_cv'      => $filePath,
                'status'       => 'menunggu',
            ]);



            return redirect()->route('beranda')
                ->with('success', 'Pendaftaran berhasil dikirim! Kami akan menghubungi Anda melalui email dalam 3-5 hari kerja.');

        } catch (\Exception $e) {
            \Log::error('PENDAFTARAN ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('beranda')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}