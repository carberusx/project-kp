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
                    File Tugas <span class="text-red-500">*</span>
                </label>
                <div class="relative border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:border-primary hover:bg-primary/5 transition-all cursor-pointer" id="dropzone">
                    <div class="flex flex-col items-center justify-center py-10" id="dropzone-content">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-3">cloud_upload</span>
                        <p class="text-sm font-semibold text-slate-700">Klik untuk upload atau drag & drop</p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOCX, ZIP — Maks 10MB</p>
                    </div>
                    <input type="file" name="file_tugas" accept=".pdf,.doc,.docx,.zip"
                           class="absolute inset-0 opacity-0 cursor-pointer" id="file-input" required/>
                </div>
                <p class="text-xs text-slate-400 mt-2" id="file-name"></p>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan (opsional)</label>
                <textarea name="catatan" rows="3"
                          placeholder="Tambahkan catatan untuk mentor Anda..."
                          class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary p-4 text-sm resize-none">{{ old('catatan', $pengumpulan?->catatan) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-7 rounded-xl transition-all shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined">send</span>
                    {{ $pengumpulan ? 'Upload Ulang' : 'Kumpulkan Tugas' }}
                </button>
                <a href="{{ route('mahasiswa.tugas.index') }}"
                   class="flex items-center gap-2 border border-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl hover:bg-slate-50 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
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