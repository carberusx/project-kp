@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name . '!')

@section('content')
<div class="space-y-6">

    {{-- ── STAT CARDS ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Absensi Hari Ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">fingerprint</span>
                </div>
                @if($absensiHariIni)
                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2.5 py-1 rounded-full">Hadir</span>
                @else
                    <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2.5 py-1 rounded-full">Belum</span>
                @endif
            </div>
            <p class="text-slate-500 text-xs font-medium">Absensi Hari Ini</p>
            <p class="text-2xl font-black mt-1">
                @if($absensiHariIni && $absensiHariIni->jam_masuk)
                    {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                @else
                    Belum
                @endif
            </p>
        </div>

        {{-- Total Hadir --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">calendar_month</span>
                </div>
            </div>
            <p class="text-slate-500 text-xs font-medium">Hadir Bulan Ini</p>
            <p class="text-2xl font-black mt-1">{{ $totalHadirBulanIni }} <span class="text-sm font-normal text-slate-400">hari</span></p>
        </div>

        {{-- Tugas Dikumpulkan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">task_alt</span>
                </div>
            </div>
            <p class="text-slate-500 text-xs font-medium">Tugas Dikumpulkan</p>
            <p class="text-2xl font-black mt-1">{{ $tugasDikumpulkan }} <span class="text-sm font-normal text-slate-400">/ {{ $tugasAktif->count() }}</span></p>
        </div>

        {{-- Tugas Pending --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">pending_actions</span>
                </div>
                @if($tugasBelumKumpul > 0)
                    <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2.5 py-1 rounded-full">Perlu Aksi</span>
                @endif
            </div>
            <p class="text-slate-500 text-xs font-medium">Tugas Belum Dikumpulkan</p>
            <p class="text-2xl font-black mt-1">{{ $tugasBelumKumpul }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── KIRI: Absensi + Tugas Terbaru ───────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Absensi Harian --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2 mb-5">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                    Absensi Harian
                </h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 mb-1">Waktu Sekarang</p>
                        <p class="text-2xl font-bold" id="clock">{{ now()->format('H:i') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 mb-1">Tanggal</p>
                        <p class="text-lg font-bold">{{ now()->isoFormat('D MMMM Y') }}</p>
                    </div>
                </div>

                @if(!$absensiHariIni)
                    {{-- Belum absen --}}
                    <form action="{{ route('mahasiswa.absensi.checkin') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-7 rounded-xl transition-all shadow-md shadow-primary/20">
                            <span class="material-symbols-outlined text-xl">fingerprint</span>
                            Tandai Hadir
                        </button>
                    </form>
                @elseif($absensiHariIni->jam_masuk && !$absensiHariIni->jam_keluar)
                    {{-- Sudah check-in, belum check-out --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-xl">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            <span class="text-sm font-semibold">Masuk pukul {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</span>
                        </div>
                        <form action="{{ route('mahasiswa.absensi.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 border border-slate-300 text-slate-700 font-bold py-2 px-5 rounded-xl hover:bg-slate-50 transition-all">
                                <span class="material-symbols-outlined text-lg">logout</span>
                                Tandai Pulang
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Sudah lengkap --}}
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-xl">
                            <span class="material-symbols-outlined text-lg">login</span>
                            <span class="text-sm font-semibold">Masuk: {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-xl">
                            <span class="material-symbols-outlined text-lg">logout</span>
                            <span class="text-sm font-semibold">Pulang: {{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tugas Terkini --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">assignment</span>
                        Tugas Terkini
                    </h3>
                    <a href="{{ route('mahasiswa.tugas.index') }}" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($tugasAktif->take(4) as $tugas)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            @if($tugas->pengumpulan)
                                <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            @elseif($tugas->isOverdue())
                                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined">warning</span>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined">pending_actions</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $tugas->judul }}</p>
                                <p class="text-xs text-slate-500">
                                    Deadline: {{ $tugas->deadline->isoFormat('D MMM Y, HH:mm') }}
                                </p>
                            </div>
                        </div>
                        @if($tugas->pengumpulan)
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-green-100 text-green-700">Dikumpulkan</span>
                        @elseif($tugas->isOverdue())
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-red-100 text-red-700">Terlambat</span>
                        @else
                            <a href="{{ route('mahasiswa.tugas.upload', $tugas) }}"
                               class="text-xs font-bold px-3 py-1.5 rounded-full bg-primary text-white hover:bg-primary/90 transition-all">
                                Kumpulkan
                            </a>
                        @endif
                    </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 block">task_alt</span>
                            <p class="text-sm">Tidak ada tugas aktif saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── KANAN: Riwayat Absensi + Deadline ───────────────────────── --}}
        <div class="space-y-6">

            {{-- Riwayat Absensi --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 mb-5">
                    <span class="material-symbols-outlined text-primary text-xl">history</span>
                    Riwayat Absensi
                </h3>
                <div class="space-y-3">
                    @forelse($riwayatAbsensi as $abs)
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-sm font-semibold">{{ $abs->tanggal->isoFormat('ddd, D MMM') }}</p>
                            <p class="text-xs text-slate-400">
                                @if($abs->jam_masuk){{ \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') }}@endif
                                @if($abs->jam_keluar) — {{ \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') }}@endif
                            </p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $abs->status === 'hadir' ? 'bg-green-100 text-green-700' : ($abs->status === 'izin' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($abs->status) }}
                        </span>
                    </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada riwayat absensi.</p>
                    @endforelse
                </div>
                <a href="{{ route('mahasiswa.absensi.index') }}" class="mt-4 block text-center text-sm text-primary font-semibold hover:underline">
                    Lihat semua →
                </a>
            </div>

            {{-- Deadline Terdekat --}}
            @if($deadlineDekat->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Deadline Terdekat</h3>
                <div class="space-y-4">
                    @foreach($deadlineDekat as $t)
                    <div class="flex items-start gap-3">
                        <div class="min-w-[46px] text-center bg-primary/5 rounded-xl p-2">
                            <p class="text-xs font-bold text-slate-400 uppercase">{{ $t->deadline->format('M') }}</p>
                            <p class="text-xl font-black text-primary leading-none">{{ $t->deadline->format('d') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $t->judul }}</p>
                            <p class="text-xs text-slate-500">Pukul {{ $t->deadline->format('H:i') }}</p>
                            @if($t->days_left <= 2)
                            <span class="text-xs font-bold text-red-600">⚠ {{ $t->days_left }} hari lagi</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Butuh Bantuan --}}
            <div class="bg-gradient-to-br from-primary to-blue-700 rounded-2xl p-6 text-white shadow-lg shadow-primary/20">
                <h4 class="font-bold text-lg mb-2">Butuh Bantuan?</h4>
                <p class="text-sm text-blue-100 mb-4 leading-relaxed">Jika ada kendala teknis atau pertanyaan seputar program, hubungi supervisor Anda.</p>
                <a href="mailto:support@simagang.go.id" class="inline-flex items-center gap-2 text-sm font-bold bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-base">support_agent</span>
                    Hubungi Support
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateClock() {
        const el = document.getElementById('clock');
        if (el) {
            const now = new Date();
            el.textContent = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        }
    }
    setInterval(updateClock, 1000);
</script>
@endsection
