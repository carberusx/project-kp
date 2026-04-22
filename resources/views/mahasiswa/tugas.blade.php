@extends('layouts.mahasiswa')

@section('title', 'Tugas')
@section('page-title', 'Tugas')
@section('page-subtitle', 'Daftar tugas yang harus dikumpulkan')

@section('content')
<div class="space-y-4">

    @forelse($tugas as $t)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $t->pengumpulan ? 'bg-green-100 text-green-600' : ($t->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                    <span class="material-symbols-outlined text-2xl">
                        {{ $t->pengumpulan ? 'task_alt' : ($t->isOverdue() ? 'warning' : 'assignment') }}
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="font-bold text-slate-900">{{ $t->judul }}</h3>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full
                            {{ $t->tipe === 'laporan' ? 'bg-blue-100 text-blue-700' :
                               ($t->tipe === 'proyek' ? 'bg-violet-100 text-violet-700' :
                               ($t->tipe === 'evaluasi' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600')) }}">
                            {{ ucfirst($t->tipe) }}
                        </span>
                    </div>
                    @if($t->deskripsi)
                    <p class="text-sm text-slate-500 mb-2">{{ Str::limit($t->deskripsi, 120) }}</p>
                    @endif

		{{-- Tombol download file dari admin --}}
			@if($t->file_tugas)
            <div class="mt-2 mb-3">
                <a href="{{ asset('storage/' . $t->file_tugas) }}" 
                    target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 px-3 py-1.5 rounded-lg transition-colors w-fit">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Download File Lampiran
                </a>
            </div>
			@endif

                    <div class="flex items-center gap-1 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        Deadline: <strong class="{{ $t->isOverdue() && !$t->pengumpulan ? 'text-red-600' : '' }}">
                            {{ $t->deadline->isoFormat('D MMMM Y, HH:mm') }}
                        </strong>
                        @if(!$t->pengumpulan && !$t->isOverdue())
                            <span class="ml-1 {{ $t->days_left <= 2 ? 'text-red-600 font-bold' : 'text-slate-400' }}">
                                ({{ $t->days_left }} hari lagi)
                            </span>
                        @endif
                    </div>

                    @if($t->pengumpulan)
                    <div class="mt-2 text-xs text-slate-500">
                        Dikumpulkan: {{ $t->pengumpulan->dikumpulkan_at->isoFormat('D MMM Y, HH:mm') }}
                        @if($t->pengumpulan->nilai)
                            · Nilai: <strong class="text-primary">{{ $t->pengumpulan->nilai }}</strong>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex-shrink-0 w-full md:w-auto mt-4 pt-4 md:mt-0 md:pt-0 border-t border-slate-100 md:border-none flex justify-start md:justify-end">
                @if($t->pengumpulan)
                    @if($t->pengumpulan->status === 'revisi')
                        {{-- Status revisi: tampilkan badge + tombol kirim ulang --}}
                        <div class="flex flex-row items-center justify-between w-full md:w-auto md:flex-col md:items-end gap-3 md:gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700">
                                <span class="material-symbols-outlined text-sm">rate_review</span>
                                Perlu Revisi
                            </span>
                            <a href="{{ route('mahasiswa.tugas.upload', $t) }}"
                            class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-xl text-xs transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">upload_file</span>
                                Kirim Ulang
                            </a>
                        </div>
                    @elseif($t->pengumpulan->status === 'dinilai')
                        {{-- Sudah dinilai --}}
                        <div class="flex flex-row items-center justify-between w-full md:w-auto md:flex-col md:items-end gap-3 md:gap-1">
                            <a href="{{ route('mahasiswa.tugas.upload', $t) }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition-colors cursor-pointer" title="Klik untuk mengecek tugas">
                                <span class="material-symbols-outlined text-sm">grade</span>
                                Dinilai
                            </a>
                            @if($t->pengumpulan->nilai)
                            <span class="text-xl md:text-lg font-black text-primary">{{ $t->pengumpulan->nilai }}</span>
                            @endif
                        </div>
                    @else
                        {{-- Sudah dikumpulkan, menunggu penilaian --}}
                        <a href="{{ route('mahasiswa.tugas.upload', $t) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-bold rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors cursor-pointer w-full md:w-auto" title="Klik untuk mengecek tugas">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            Dikumpulkan
                        </a>
                    @endif
                @elseif(!$t->isOverdue())
                    <a href="{{ route('mahasiswa.tugas.upload', $t) }}"
                       class="inline-flex items-center justify-center gap-1.5 bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition-all shadow-md shadow-primary/20 w-full md:w-auto">
                        <span class="material-symbols-outlined text-base">upload_file</span>
                        Kumpulkan
                    </a>
                @else
                    <a href="{{ route('mahasiswa.tugas.upload', $t) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-bold rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition-colors cursor-pointer w-full md:w-auto" title="Kumpulkan tugas (terlambat)">
                        <span class="material-symbols-outlined text-base">warning</span>
                        Kumpulkan (Terlambat)
                    </a>
                @endif
            </div>
        </div>
    </div>

    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">assignment</span>
        <h3 class="font-bold text-slate-500 mb-1">Tidak ada tugas aktif</h3>
        <p class="text-sm text-slate-400">Tugas baru akan muncul di sini saat admin menambahkannya.</p>
    </div>
    @endforelse

</div>
@endsection
