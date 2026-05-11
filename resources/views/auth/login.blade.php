@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex flex-col bg-gray-50">

    {{-- Header --}}
    <header class="flex items-center justify-between px-6 md:px-10 py-4 bg-white border-b border-slate-200">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 text-primary">
          <!--  <div class="h-6 w-auto">
            <img src="{{ asset('images/ptsp.png') }}" alt="PTSP Jateng" class="h-full w-auto object-contain">
            </div> -->
            <h2 class="text-slate-900 text-xl font-bold">Magang DPMPTSP</h2>
        </a>
        <!--<div class="flex items-center gap-6">
            <a href="#" class="text-slate-500 text-sm hover:text-primary transition-colors">Pusat Bantuan</a>
            <a href="#" class="text-slate-500 text-sm hover:text-primary transition-colors">Dukungan Sistem</a>
        </div>-->
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl shadow-slate-200/80 border border-slate-200/80">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4">
                        <img src="{{ asset('images/ptsp.png') }}" alt="PTSP Jateng" class="h-full w-auto object-contain">
                    </div>
                    <h1 class="text-slate-900 text-3xl font-black tracking-tight">Selamat Datang</h1>
                    <p class="text-slate-500 mt-1.5">Portal aman untuk Mahasiswa</p>
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
                        <button type="button" onclick="toggleResetModal()" class="text-primary text-sm font-semibold hover:underline">Lupa Password?</button>
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
                            <!--Ini adalah sistem pemerintah yang aman.--> Akses tidak sah dilarang keras dan dapat dikenakan tindakan hukum.
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
        <p class="text-slate-400 text-xs">© {{ date('Y') }} Magang DPMPTSP. Hak cipta dilindungi.</p>
    </footer>
</div>

{{-- Modal Lupa Password --}}
<div id="resetModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="toggleResetModal()"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <span class="material-symbols-outlined text-2xl">lock_reset</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Lupa Password Anda?</h3>
                <p class="text-sm text-slate-500 text-center leading-relaxed mb-6">
                    Karena alasan keamanan data, reset password hanya dapat dilakukan oleh Admin Pembimbing Lapangan.
                </p>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                    <p class="text-xs text-slate-600 text-center">
                        Silakan kirim pesan WhatsApp dengan format:<br>
                        <span class="font-bold text-slate-800">"Nama Lengkap - NIM - Reset Password"</span>
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="https://wa.me/{{ \App\Models\Pengaturan::getNilai('nomor_wa_admin', '6282328280963') }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3 rounded-xl transition-all shadow-md shadow-green-500/20">
                        <span class="material-symbols-outlined text-xl">chat</span>
                        Hubungi via WhatsApp
                    </a>
                    <button type="button" onclick="toggleResetModal()" class="w-full font-bold text-slate-500 hover:text-slate-700 py-3 rounded-xl transition-all">
                        Kembali ke Login
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleResetModal() {
        const modal = document.getElementById('resetModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
