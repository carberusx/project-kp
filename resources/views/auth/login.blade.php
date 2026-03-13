@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex flex-col bg-gray-50">

    {{-- Header --}}
    <header class="flex items-center justify-between px-6 md:px-10 py-4 bg-white border-b border-slate-200">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 text-primary">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-lg">account_balance</span>
            </div>
            <h2 class="text-slate-900 text-xl font-bold">SiMagang</h2>
        </a>
        <div class="flex items-center gap-6">
            <a href="#" class="text-slate-500 text-sm hover:text-primary transition-colors">Pusat Bantuan</a>
            <a href="#" class="text-slate-500 text-sm hover:text-primary transition-colors">Dukungan Sistem</a>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl shadow-slate-200/80 border border-slate-200/80">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">lock_person</span>
                    </div>
                    <h1 class="text-slate-900 text-3xl font-black tracking-tight">Selamat Datang</h1>
                    <p class="text-slate-500 mt-1.5">Portal aman untuk Mahasiswa & Administrator</p>
                </div>

                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-red-600 text-lg">error</span>
                        <span class="font-semibold">Login gagal</span>
                    </div>
                    {{ $errors->first('email') }}
                </div>
                @endif

                @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-slate-700 text-sm font-semibold">Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">person</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   placeholder="Masukkan email Anda"
                                   class="w-full h-13 pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary text-sm transition-all @error('email') border-red-400 @enderror"
                                   autofocus/>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-slate-700 text-sm font-semibold">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                            <input type="password" id="password" name="password"
                                   placeholder="Masukkan password"
                                   class="w-full h-13 pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary text-sm transition-all"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary focus:ring-primary"/>
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                        <a href="#" class="text-primary text-sm font-semibold hover:underline">Lupa Password?</a>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/25">
                        <span class="material-symbols-outlined text-xl">login</span>
                        Masuk ke Dashboard
                    </button>
                </form>

                {{-- Security note --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="flex items-start gap-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">security</span>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Ini adalah sistem pemerintah yang aman. Akses tidak sah dilarang keras dan dapat dikenakan tindakan hukum.
                        </p>
                    </div>
                    <p class="text-center text-xs text-slate-400 mt-4">
                        Belum punya akun? <a href="{{ route('beranda') }}#daftar" class="text-primary font-semibold hover:underline">Daftar di sini</a>
                    </p>
                </div>
            </div>

        </div>
    </main>

    <footer class="p-6 text-center">
        <p class="text-slate-400 text-xs">© {{ date('Y') }} SiMagang — Sistem Manajemen Magang Pusat. Hak cipta dilindungi.</p>
    </footer>
</div>
@endsection
