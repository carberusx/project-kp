@extends('layouts.mahasiswa')

@section('title', 'Absensi')
@section('page-title', 'Absensi')
@section('page-subtitle', 'Rekap kehadiran Anda')

@section('content')
<div class="space-y-6">

    {{-- ── Aksi Absensi ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-primary">fingerprint</span>
            Absensi Hari Ini — {{ now()->isoFormat('dddd, D MMMM Y') }}
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Jam Sekarang</p>
                <p class="text-xl font-bold" id="clock">{{ now()->format('H:i:s') }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Status</p>
                <p class="text-sm font-bold {{ $absensiHariIni ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $absensiHariIni ? 'Sudah Absen' : 'Belum Absen' }}
                </p>
            </div>
            @if($absensiHariIni)
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Jam Masuk</p>
                <p class="text-xl font-bold text-green-700">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-500 mb-1">Jam Pulang</p>
                <p class="text-xl font-bold text-blue-700">
                    {{ $absensiHariIni->jam_keluar ? \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') : '—' }}
                </p>
            </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-3">
            @if(!$absensiHariIni)
                <form action="{{ route('mahasiswa.absensi.checkin') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-7 rounded-xl transition-all shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined">login</span>
                        Tandai Hadir Sekarang
                    </button>
                </form>
            @elseif(!$absensiHariIni->jam_keluar)
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl font-semibold text-sm">
                    <span class="material-symbols-outlined">check_circle</span>
                    Sudah absen masuk
                </div>
                <form action="{{ route('mahasiswa.absensi.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 border border-slate-300 text-slate-700 font-bold py-3 px-7 rounded-xl hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined">logout</span>
                        Tandai Pulang
                    </button>
                </form>
            @else
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl font-semibold text-sm">
                    <span class="material-symbols-outlined">task_alt</span>
                    Absensi hari ini lengkap — Durasi: {{ $absensiHariIni->durasi ?? '—' }}
                </div>
            @endif
        </div>
    </div>

    {{-- ── Ringkasan Bulan Ini ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm text-center">
            <p class="text-3xl font-black text-primary">{{ $totalHadir }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Hari Hadir</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm text-center">
            <p class="text-3xl font-black text-amber-500">{{ $riwayat->where('status','izin')->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Hari Izin</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm text-center">
            <p class="text-3xl font-black text-blue-500">{{ $riwayat->where('status','sakit')->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Hari Sakit</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm text-center">
            <p class="text-3xl font-black text-red-500">{{ $riwayat->where('status','alpha')->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-1">Alpha</p>
        </div>
    </div>

    {{-- ── Riwayat Absensi ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">Riwayat Absensi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Hari</th>
                        <th class="px-6 py-4">Jam Masuk</th>
                        <th class="px-6 py-4">Jam Pulang</th>
                        <th class="px-6 py-4">Durasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $abs)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold">{{ $abs->tanggal->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $abs->tanggal->isoFormat('dddd') }}</td>
                        <td class="px-6 py-4">{{ $abs->jam_masuk ? \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-4">{{ $abs->jam_keluar ? \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $abs->durasi ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full
                                {{ $abs->status === 'hadir' ? 'bg-green-100 text-green-700' :
                                   ($abs->status === 'izin' ? 'bg-amber-100 text-amber-700' :
                                   ($abs->status === 'sakit' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($abs->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 max-w-[200px] truncate">{{ $abs->keterangan ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">calendar_month</span>
                            Belum ada riwayat absensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function updateClock() {
        const el = document.getElementById('clock');
        if (!el) return;
        const now = new Date();
        el.textContent = [
            now.getHours().toString().padStart(2,'0'),
            now.getMinutes().toString().padStart(2,'0'),
            now.getSeconds().toString().padStart(2,'0'),
        ].join(':');
    }
    setInterval(updateClock, 1000);
</script>
@endsection
