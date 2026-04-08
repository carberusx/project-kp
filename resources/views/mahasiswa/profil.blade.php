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

    {{-- Ubah Password --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mt-6">
        <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock_reset</span>
            Ubah Password
        </h3>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('mahasiswa.profil.password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3 @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-xl transition-all text-sm w-full md:w-auto shadow-sm shadow-primary/20">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
