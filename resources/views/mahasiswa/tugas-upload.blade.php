@extends('layouts.mahasiswa')

@section('title', 'Kumpulkan Tugas')
@section('page-title', 'Kumpulkan Tugas')
@section('page-subtitle', $tugas->judul)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-primary to-blue-600 p-6 text-white">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">upload_file</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $tugas->judul }}</h2>
                    <p class="text-blue-100 text-sm mt-0.5">
                        Deadline: {{ $tugas->deadline->isoFormat('D MMMM Y, HH:mm') }}
                        @if(!$tugas->isOverdue())
                            · {{ $tugas->days_left }} hari lagi
                        @else
                            · <span class="text-red-300 font-bold">Sudah lewat deadline</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Tugas --}}
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 space-y-4">

            {{-- Tipe & Deadline --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-xl p-3 border border-slate-200">
                    <p class="text-xs text-slate-400 font-medium mb-1">Tipe Tugas</p>
                    <p class="text-sm font-bold capitalize text-slate-800">{{ $tugas->tipe }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-slate-200">
                    <p class="text-xs text-slate-400 font-medium mb-1">Deadline</p>
                    <p class="text-sm font-bold text-slate-800">{{ $tugas->deadline->isoFormat('D MMM Y, HH:mm') }}</p>
                </div>
            </div>

            {{-- Deskripsi --}}
            @if($tugas->deskripsi)
            <div class="bg-white rounded-xl p-4 border border-slate-200">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">description</span>
                    Instruksi Tugas
                </p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $tugas->deskripsi }}</p>
            </div>
            @endif

            {{-- File Lampiran dari Admin --}}
            @if($tugas->file_tugas)
            <div class="bg-primary/5 rounded-xl p-4 border border-primary/20">
                <p class="text-xs text-primary font-bold uppercase tracking-wider mb-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">attach_file</span>
                    File Lampiran dari Admin
                </p>
                <a href="{{ asset('storage/' . $tugas->file_tugas) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary/20">
                    <span class="material-symbols-outlined text-lg">download</span>
                    Download File Lampiran
                </a>
            </div>
            @endif
        </div>

        @php
            $isDinilai = $pengumpulan && $pengumpulan->status === 'dinilai';
        @endphp

        @if(!$isDinilai)
        {{-- Form Upload --}}
        <form action="{{ route('mahasiswa.tugas.submit', $tugas) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">error</span>{{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($tugas->isOverdue())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
                <div class="flex items-center gap-2 font-semibold mb-1">
                    <span class="material-symbols-outlined text-lg">warning</span>
                    Peringatan: Lewat Batas Waktu
                </div>
                Batas waktu pengumpulan telah lewat <strong>{{ $tugas->terlambat_text }}</strong> yang lalu. 
                @if($pengumpulan)
                    Jika Anda mengupload ulang sekarang, pengumpulan ini akan dihitung sebagai terlambat.
                @else
                    Tugas yang Anda kumpulkan akan ditandai sebagai terlambat.
                @endif
            </div>
            @endif

            @if($pengumpulan)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                <div class="flex items-center gap-2 font-semibold mb-1">
                    <span class="material-symbols-outlined text-lg">info</span>
                    Tugas sudah pernah dikumpulkan
                </div>
                Upload ulang akan menggantikan file sebelumnya.
                Dikumpulkan pada: {{ $pengumpulan->dikumpulkan_at->isoFormat('D MMM Y, HH:mm') }}
                @if($pengumpulan->nilai)
                    · Nilai: <strong class="text-primary">{{ $pengumpulan->nilai }}</strong>
                @endif
                @if($pengumpulan->feedback)
                <div class="mt-2 p-3 bg-white rounded-lg border border-amber-200">
                    <p class="text-xs font-bold text-amber-700 mb-1">Feedback Admin:</p>
                    <p class="text-sm text-slate-700">{{ $pengumpulan->feedback }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Upload --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    File Tugas <span class="text-xs font-normal text-slate-400">(opsional jika Anda mengisi catatan)</span>
                </label>
                <div class="relative border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:border-primary hover:bg-primary/5 transition-all cursor-pointer" id="dropzone">
                    <div class="flex flex-col items-center justify-center py-10" id="dropzone-content">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-3">cloud_upload</span>
                        <p class="text-sm font-semibold text-slate-700">Klik untuk upload atau drag & drop</p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOCX, ZIP — Maks 10MB</p>
                    </div>
                    <input type="file" name="file_tugas" accept=".pdf,.doc,.docx,.zip"
                           class="absolute inset-0 opacity-0 cursor-pointer" id="file-input"/>
                </div>
                <p class="text-xs text-slate-400 mt-2" id="file-name"></p>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan <span class="text-xs font-normal text-slate-400">(opsional jika Anda mengupload file)</span></label>
                <textarea name="catatan" rows="3"
                          placeholder="Tambahkan catatan untuk mentor Anda..."
                          class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary p-4 text-sm resize-none">{{ old('catatan', $pengumpulan?->catatan) }}</textarea>
            </div>

            <div class="flex flex-col md:flex-row gap-3 pt-2">
                <button type="submit"
                    class="flex items-center w-full justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-7 rounded-xl transition-all shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined">send</span>
                    {{ $pengumpulan ? 'Upload Ulang' : 'Kumpulkan Tugas' }}
                </button>
                <a href="{{ route('mahasiswa.tugas.index') }}"
                   class="flex items-center justify-center gap-2 border border-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl hover:bg-slate-50 transition-all text-sm w-full md:w-auto">
                    Batal
                </a>
            </div>
        </form>
        @elseif($isDinilai)
        {{-- Detail Pengumpulan (Read-only) --}}
        <div class="p-6 space-y-5">
            <div class="p-5 bg-green-50 border-green-200 text-green-800 border rounded-2xl">
                <div class="flex items-center gap-2 text-lg font-bold mb-2">
                    <span class="material-symbols-outlined text-2xl">grade</span>
                    Tugas Dinilai
                </div>
                <p class="text-sm opacity-80 mb-1">Dikumpulkan pada: {{ $pengumpulan->dikumpulkan_at->isoFormat('D MMMM Y, HH:mm') }}</p>
                
                @if($pengumpulan->nilai)
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-sm font-bold opacity-80">Nilai Akhir:</span>
                    <span class="bg-white text-green-700 px-4 py-1 rounded-lg text-xl font-black shadow-sm border border-green-100">{{ $pengumpulan->nilai }} / 100</span>
                </div>
                @endif
                
                @if($pengumpulan->feedback)
                <div class="mt-4 p-4 bg-white/70 rounded-xl border border-white space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider opacity-60">Feedback dari Admin:</p>
                    <p class="text-sm font-medium leading-relaxed">{{ $pengumpulan->feedback }}</p>
                </div>
                @endif
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                @if($pengumpulan->file_path)
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">File yang Telah Dikumpulkan</p>
                <a href="{{ asset('storage/' . $pengumpulan->file_path) }}" target="_blank" class="inline-flex items-center gap-3 text-primary bg-white border border-slate-200 shadow-sm hover:border-primary hover:shadow px-5 py-3 rounded-xl transition-all font-semibold">
                    <span class="material-symbols-outlined text-2xl">description</span>
                    Dokumen Tugas Anda
                </a>
                @endif

                @if($pengumpulan->catatan)
                <div class="mt-5 pt-4 border-t border-slate-200">
                    <p class="text-xs font-bold text-slate-500 mb-2">Pesan yang Disertakan:</p>
                    <p class="text-sm text-slate-800 font-medium italic opacity-80 leading-relaxed">"{{ $pengumpulan->catatan }}"</p>
                </div>
                @endif
            </div>

            <div class="pt-4">
                <a href="{{ route('mahasiswa.tugas.index') }}" class="flex items-center justify-center gap-2 border-2 border-slate-200 text-slate-600 font-bold py-3.5 px-6 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all text-sm w-full">
                    
                    Kembali ke Daftar Tugas
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const input = document.getElementById('file-input');
    const nameEl = document.getElementById('file-name');
    const content = document.getElementById('dropzone-content');
    input.addEventListener('change', () => {
        const file = input.files[0];
        if (file) {
            nameEl.textContent = '📎 ' + file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)';
            content.innerHTML = `<span class="material-symbols-outlined text-4xl text-primary mb-3">description</span><p class="text-sm font-semibold text-primary">${file.name}</p>`;
        }
    });
</script>
@endsection