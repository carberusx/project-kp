<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function mahasiswaIndex()
    {
        $user  = Auth::user();
        $tugas = Tugas::where('is_aktif', true)
            ->orderBy('deadline')
            ->get()
            ->map(function ($t) use ($user) {
                $t->pengumpulan = $t->pengumpulanByUser($user->id);
                return $t;
            });

        return view('mahasiswa.tugas', compact('tugas'));
    }

    public function showUpload(Tugas $tugas)
    {
        $user        = Auth::user();
        $pengumpulan = $tugas->pengumpulanByUser($user->id);

        return view('mahasiswa.tugas-upload', compact('tugas', 'pengumpulan'));
    }

    public function submitTugas(Request $request, Tugas $tugas)
    {
        $user = Auth::user();

        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf,doc,docx,zip|max:10240',
            'catatan'    => 'nullable|string|max:500',
        ], [
            'file_tugas.required' => 'File tugas wajib diupload.',
            'file_tugas.mimes'    => 'Format file harus PDF, DOC, DOCX, atau ZIP.',
            'file_tugas.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $filePath = $request->file('file_tugas')
            ->store('tugas/pengumpulan', 'public');

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'user_id' => $user->id],
            [
                'file_path'       => $filePath,
                'catatan'         => $request->catatan,
                'status'          => 'dikumpulkan',
                'dikumpulkan_at'  => now(),
            ]
        );

        return redirect()->route('mahasiswa.tugas.index')
            ->with('success', "Tugas \"{$tugas->judul}\" berhasil dikumpulkan!");
    }
}
