@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '!')

@section('content')
@php
    $isWeekend = today()->isWeekend();
    $liburNasional = \App\Models\HariLibur::whereDate('tanggal', today())->first();
@endphp
<div class="space-y-5">

    {{-- ── STAT CARDS ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">
        <div class="bg-white rounded-md border border-slate-200 p-4 md:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-md bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">fingerprint</span>
                </div>
                @if($absensiHariIni)
                    @if($absensiHariIni->status === 'alpha')
                        <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">Alpha</span>
                    @else
                        <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">{{ ucfirst($absensiHariIni->status) }}</span>
                    @endif
                @elseif($isWeekend || $liburNasional)
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">Libur</span>
                @else
                    <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">Belum</span>
                @endif
            </div>
            <p class="text-slate-500 text-xs font-medium">Absensi Hari Ini</p>
            <p class="text-xl md:text-2xl font-black mt-1">
                @if($absensiHariIni && $absensiHariIni->jam_masuk)
                    {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                @elseif($isWeekend || $liburNasional)
                    -
                @else
                    Belum
                @endif
            </p>
        </div>

        <div class="bg-white rounded-md border border-slate-200 p-4 md:p-5 shadow-sm">
            <div class="w-10 h-10 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
            </div>
            <p class="text-slate-500 text-xs font-medium">Hadir Bulan Ini</p>
            <p class="text-xl md:text-2xl font-black mt-1">{{ $totalHadirBulanIni }} <span class="text-sm font-normal text-slate-400">hari</span></p>
        </div>

        <div class="bg-white rounded-md border border-slate-200 p-4 md:p-5 shadow-sm">
            <div class="w-10 h-10 rounded-md bg-violet-100 text-violet-600 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-xl">task_alt</span>
            </div>
            <p class="text-slate-500 text-xs font-medium">Tugas Dikumpulkan</p>
            <p class="text-xl md:text-2xl font-black mt-1">{{ $tugasDikumpulkan }} <span class="text-sm font-normal text-slate-400">/ {{ $tugasAktif->count() }}</span></p>
        </div>

        <div class="bg-white rounded-md border border-slate-200 p-4 md:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-md bg-amber-100 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">pending_actions</span>
                </div>
                @if($tugasBelumKumpul > 0)
                    <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">Aksi</span>
                @endif
            </div>
            <p class="text-slate-500 text-xs font-medium">Belum Dikumpulkan</p>
            <p class="text-xl md:text-2xl font-black mt-1">{{ $tugasBelumKumpul }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── KIRI ────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Absensi Harian --}}
            <div class="bg-white rounded-md border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                    Absensi Harian
                </h3>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-slate-50 rounded-md p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 mb-1">Waktu Sekarang</p>
                        <p class="text-xl font-bold" id="clock">{{ now()->format('H:i') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-md p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 mb-1">Tanggal</p>
                        <p class="text-sm font-bold">{{ now()->isoFormat('D MMM Y') }}</p>
                    </div>
                </div>

                @if($isWeekend || $liburNasional)
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-md p-4 flex items-start gap-3">
                        <div>
                            <h4 class="font-bold text-sm">Hari Libur</h4>
                            <p class="text-sm">Hari ini adalah hari libur ({{ $isWeekend ? 'Akhir Pekan' : $liburNasional->keterangan }}). Anda tidak perlu Absen.</p>
                        </div>
                    </div>
                @elseif(!$absensiHariIni)
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('mahasiswa.absensi.index') }}?action=masuk" class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-md transition-all shadow-md shadow-primary/20">
                            <span class="material-symbols-outlined">fingerprint</span>
                            Tandai Hadir
                        </a>
                        <a href="{{ route('mahasiswa.absensi.index') }}?action=izin_sakit" class="flex-1 flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-md transition-all shadow-md shadow-amber-500/20">
                            <span class="material-symbols-outlined">event_busy</span>
                            Izin / Sakit
                        </a>
                    </div>
                @elseif($absensiHariIni->status === 'alpha')
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-md p-4 flex items-center gap-3">
                        <span class="material-symbols-outlined text-red-600 text-3xl flex-shrink-0">cancel</span>
                        <div>
                            <h4 class="font-bold text-sm">Alpha</h4>
                            <p class="text-sm">Anda tercatat Alpha hari ini.</p>
                        </div>
                    </div>
                @elseif($absensiHariIni->status === 'izin' || $absensiHariIni->status === 'sakit')
                    <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-md p-4 flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-3xl flex-shrink-0">
                            {{ $absensiHariIni->status === 'izin' ? 'event_busy' : 'medical_services' }}
                        </span>
                        <div>
                            <h4 class="font-bold text-sm">{{ ucfirst($absensiHariIni->status) }}</h4>
                            <p class="text-sm">Anda telah tercatat {{ $absensiHariIni->status }} hari ini.</p>
                        </div>
                    </div>
                @elseif($absensiHariIni->jam_masuk && !$absensiHariIni->jam_keluar)
                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <div class="w-full sm:w-auto flex items-center justify-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md text-sm font-semibold">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            Masuk {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                        </div>
                        <a href="{{ route('mahasiswa.absensi.index') }}?action=keluar" class="w-full sm:w-auto flex items-center justify-center gap-2 border border-slate-300 text-slate-700 font-bold py-2 px-5 rounded-md hover:bg-slate-50 transition-all text-sm">
                            <span class="material-symbols-outlined text-lg">logout</span>
                            Tandai Pulang
                        </a>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <div class="w-full sm:w-auto flex items-center justify-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md text-sm font-semibold">
                            <span class="material-symbols-outlined">login</span>
                            Masuk: {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                        </div>
                        @if($absensiHariIni->jam_keluar)
                        <div class="w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-md text-sm font-semibold">
                            <span class="material-symbols-outlined">logout</span>
                            Pulang: {{ \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') }}
                        </div>
                        @endif
                    </div>
                @endif

                @if($absensiHariIni?->keterangan)
    <div class="mt-3 text-xs text-amber-600 bg-amber-50 rounded-md p-3 border border-amber-100">
        <span class="font-bold">Catatan:</span> {{ $absensiHariIni->keterangan }}
    </div>
                @endif
            </div>

            {{-- Tugas Terkini --}}
            <div class="bg-white rounded-md border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">assignment</span>
                        Tugas Terkini
                    </h3>
                    <a href="{{ route('mahasiswa.tugas.index') }}" class="text-primary text-xs font-semibold hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($tugasAktif->take(4) as $tugas)
                    <div class="flex items-center justify-between p-3 md:p-4 rounded-md border border-slate-100 hover:bg-slate-50 transition-colors gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($tugas->pengumpulan)
                                <div class="w-9 h-9 rounded-md bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                </div>
                            @elseif($tugas->isOverdue())
                                <div class="w-9 h-9 rounded-md bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-lg">warning</span>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-md bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-lg">pending_actions</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $tugas->judul }}</p>
                                <p class="text-xs text-slate-500">{{ $tugas->deadline->isoFormat('D MMM Y, HH:mm') }}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            @if($tugas->pengumpulan)
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">Dikumpulkan</span>
                            @elseif($tugas->isOverdue())
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">Terlambat</span>
                            @else
                                <a href="{{ route('mahasiswa.tugas.upload', $tugas) }}"
                                   class="text-xs font-bold px-3 py-1.5 rounded-full bg-primary text-white hover:bg-primary/90 transition-all">
                                    Kumpulkan
                                </a>
                            @endif
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 block">task_alt</span>
                            <p class="text-sm">Tidak ada tugas aktif.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── KANAN ───────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Riwayat Absensi --}}
            <div class="bg-white rounded-md border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary text-xl">history</span>
                    Riwayat Absensi
                </h3>
                <div class="space-y-2">
                    @forelse($riwayatAbsensi as $abs)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-sm font-semibold">{{ $abs->tanggal->isoFormat('ddd, D MMM') }}</p>
                            <p class="text-xs text-slate-400">
                                @if($abs->jam_masuk){{ \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') }}@endif
                                @if($abs->jam_keluar) — {{ \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') }}@endif
                            </p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $abs->status === 'hadir' ? 'bg-green-100 text-green-700' :
                               ($abs->status === 'izin' ? 'bg-amber-100 text-amber-700' :
                               ($abs->status === 'sakit' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst($abs->status) }}
                        </span>
                    </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada riwayat.</p>
                    @endforelse
                </div>
                <a href="{{ route('mahasiswa.absensi.index') }}" class="mt-3 block text-center text-xs text-primary font-semibold hover:underline">
                    Lihat semua 
                </a>
            </div>

            {{-- Deadline Terdekat --}}
            @if($deadlineDekat->count() > 0)
            <div class="bg-white rounded-md border border-slate-200 p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Deadline Terdekat</h3>
                <div class="space-y-3">
                    @foreach($deadlineDekat as $t)
                    <div class="flex items-start gap-3">
                        <div class="min-w-[44px] text-center bg-primary/5 rounded-md p-2">
                            <p class="text-xs font-bold text-slate-400 uppercase">{{ $t->deadline->format('M') }}</p>
                            <p class="text-lg font-black text-primary leading-none">{{ $t->deadline->format('d') }}</p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $t->judul }}</p>
                            <p class="text-xs text-slate-500">{{ $t->deadline->format('H:i') }}</p>
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
            <div class="bg-gradient-to-br from-primary to-blue-700 rounded-md p-5 text-white shadow-lg shadow-primary/20">
                <h4 class="font-bold mb-2">Butuh Bantuan?</h4>
                <p class="text-sm text-blue-100 mb-4 leading-relaxed">Hubungi Pembimbing jika ada kendala teknis.</p>
                <button type="button" onclick="toggleHelpModal()" class="inline-flex items-center gap-2 text-sm font-bold bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-md transition-colors">
                    <span class="material-symbols-outlined text-base">support_agent</span>
                    Hubungi Pembimbing
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hubungi Pembimbing --}}
<div id="helpModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="toggleHelpModal()"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <span class="material-symbols-outlined text-2xl">support_agent</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Hubungi Pembimbing</h3>
                <p class="text-sm text-slate-500 text-center leading-relaxed mb-6">
                    Punya kendala teknis atau pertanyaan seputar tugas magang? Silakan hubungi Pembimbing Lapangan.
                </p>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                    <p class="text-xs text-slate-600 text-center">
                        Silakan kirim pesan WhatsApp dengan menyertakan:<br>
                        <span class="font-bold text-slate-800">Nama Lengkap & Kendala Anda</span>
                    </p>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="https://wa.me/{{ \App\Models\Pengaturan::getNilai('nomor_wa_admin', '6282328280963') }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3 rounded-xl transition-all shadow-md shadow-green-500/20">
                        <span class="material-symbols-outlined text-xl">chat</span>
                        Hubungi via WhatsApp
                    </a>
                    <button type="button" onclick="toggleHelpModal()" class="w-full font-bold text-slate-500 hover:text-slate-700 py-3 rounded-xl transition-all">
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleHelpModal() {
        const modal = document.getElementById('helpModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

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
