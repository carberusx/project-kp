@extends('layouts.mahasiswa')

@section('title', 'Profil')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Informasi akun dan data magang')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Kartu Profil --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center gap-5 mb-6">
            <div class="w-20 h-20 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-3xl">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500 text-sm">{{ $user->email }}</p>
                @if($pendaftaran)
                    <span class="mt-1 inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full
                        {{ $pendaftaran->status === 'diterima' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        <span class="material-symbols-outlined text-xs">circle</span>
                        {{ $pendaftaran->status_label }}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium mb-1">NIM</p>
                <p class="font-semibold">{{ $user->nim ?? '—' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium mb-1">Telepon</p>
                <p class="font-semibold">{{ $user->telepon ?? '—' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium mb-1">Universitas</p>
                <p class="font-semibold">{{ $user->universitas ?? $pendaftaran?->universitas ?? '—' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium mb-1">Jurusan</p>
                <p class="font-semibold">{{ $user->jurusan ?? $pendaftaran?->jurusan ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Info Magang --}}
    @if($pendaftaran && $pendaftaran->status === 'diterima')
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">work</span>
            Informasi Magang
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-primary/5 rounded-xl p-4 border border-primary/10">
                <p class="text-xs text-slate-500 font-medium mb-1">Tanggal Mulai</p>
                <p class="font-bold text-primary">{{ $pendaftaran->tanggal_mulai?->isoFormat('D MMMM Y') ?? '—' }}</p>
            </div>
            <div class="bg-primary/5 rounded-xl p-4 border border-primary/10">
                <p class="text-xs text-slate-500 font-medium mb-1">Tanggal Selesai</p>
                <p class="font-bold text-primary">{{ $pendaftaran->tanggal_selesai?->isoFormat('D MMMM Y') ?? '—' }}</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
