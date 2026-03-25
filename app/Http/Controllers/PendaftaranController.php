<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    // Batas maksimal pendaftar
    const BATAS_PENDAFTAR = 10;

    public function store(Request $request)
    {
        // Cek apakah sudah mencapai batas
        $jumlahPendaftar = Pendaftaran::count();
        if ($jumlahPendaftar >= self::BATAS_PENDAFTAR) {
            return redirect()->route('beranda')
                ->with('error', 'Maaf, pendaftaran telah ditutup karena kuota ' . self::BATAS_PENDAFTAR . ' pendaftar sudah terpenuhi.');
        }

        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'email'         => 'required|email|unique:pendaftarans,email',
            'universitas'   => 'required|string|max:255',
            'jurusan'       => 'required|string|max:255',
            'motivasi'      => 'required|string|min:20|max:2000',
            'file_dokumen'  => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'setuju'        => 'required|accepted',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email ini sudah pernah mendaftar.',
            'universitas.required'   => 'Nama institusi wajib diisi.',
            'jurusan.required'       => 'Jurusan wajib diisi.',
            'motivasi.required'      => 'Motivasi wajib diisi.',
            'motivasi.min'           => 'Motivasi minimal 20 karakter.',
            'file_dokumen.mimes'     => 'Format file harus PDF, DOC, DOCX, atau ZIP.',
            'file_dokumen.max'       => 'Ukuran file maksimal 10 MB.',
            'setuju.required'        => 'Anda harus menyetujui pernyataan di atas.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_dokumen')) {
            $filePath = $request->file('file_dokumen')
                ->store('pendaftaran/dokumen', 'public');
        }

        Pendaftaran::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'universitas'  => $validated['universitas'],
            'jurusan'      => $validated['jurusan'],
            'motivasi'     => $validated['motivasi'],
            'file_cv'      => $filePath,
            'status'       => 'menunggu',
        ]);

        return redirect()->route('beranda')
            ->with('success', 'Pendaftaran berhasil dikirim! Kami akan menghubungi Anda melalui email dalam 3-5 hari kerja.');
    }
}