@extends('layouts.mahasiswa')

@section('title', 'Profil')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Informasi akun dan data magang')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Peringatan Wajib Ganti Password --}}
    @if(session('warning_ganti_password') || auth()->user()->force_password_change)
        <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded-xl flex items-start gap-3" id="password-alert">
            <span class="material-symbols-outlined mt-0.5 text-amber-500">warning</span>
            <div>
                <p class="font-bold">Wajib Ganti Password</p>
                <p class="text-sm mt-0.5">Demi keamanan akun, Anda wajib mengganti password sementara Anda sebelum bisa mengakses menu lainnya. Silakan isi form di bawah ini.</p>
            </div>
        </div>
    @endif



    {{-- Kartu Profil --}}
    <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center gap-5 mb-6">
            {{-- Avatar dengan tombol upload --}}
            <form action="{{ route('mahasiswa.profil.foto.update') }}" method="POST" enctype="multipart/form-data" id="foto-form">
                @csrf
                @method('PUT')
                <div class="relative w-20 h-20 cursor-pointer" onclick="document.getElementById('foto-input').click()">
                    @if($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil"
                             class="w-20 h-20 rounded-md object-cover border-2 border-slate-200" id="foto-preview">
                    @else
                        <div class="w-20 h-20 rounded-md bg-primary/10 text-primary flex items-center justify-center font-black text-3xl" id="foto-placeholder">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <img src="" alt="" class="w-20 h-20 rounded-md object-cover border-2 border-slate-200 hidden" id="foto-preview">
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-primary rounded-full flex items-center justify-center shadow">
                        <span class="material-symbols-outlined text-white" style="font-size:14px">photo_camera</span>
                    </div>
                </div>
                <input type="file" id="foto-input" name="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">
            </form>
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
    <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm">
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
    <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm mt-6" id="ubah-password-section">
        <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock_reset</span>
            Ubah Password
        </h3>


        <form action="{{ route('mahasiswa.profil.password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3 @error('current_password') border-red-500 @enderror">
                </div>
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3 @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary text-sm p-3">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="show_password" onchange="toggleAllPasswords(this)"
                       class="w-4 h-4 rounded accent-primary cursor-pointer">
                <label for="show_password" class="text-sm text-slate-600 cursor-pointer select-none">Tampilkan password</label>
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

@section('scripts')
<script>
    function toggleAllPasswords(checkbox) {
        ['current_password', 'password', 'password_confirmation'].forEach(function(id) {
            const input = document.getElementById(id);
            if (input) input.type = checkbox.checked ? 'text' : 'password';
        });
    }

    function previewFoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('foto-preview');
            const placeholder = document.getElementById('foto-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
        // Auto-submit setelah file dipilih
        document.getElementById('foto-form').submit();
    }
</script>
@if(session('warning_ganti_password') || auth()->user()->force_password_change)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('ubah-password-section')?.scrollIntoView({ behavior: 'smooth' });
    });
</script>
@endif
@endsection
