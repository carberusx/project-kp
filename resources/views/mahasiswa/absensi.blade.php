@extends('layouts.mahasiswa')

@section('title', 'Absensi')
@section('page-title', 'Absensi')
@section('page-subtitle', 'Rekap kehadiran Anda')

@section('content')
@php
    $isWeekend = today()->isWeekend();
    $liburNasional = \App\Models\HariLibur::whereDate('tanggal', today())->first();
@endphp
<div class="space-y-6">

    {{-- ── Aksi Absensi ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-primary">fingerprint</span>
            Absensi Hari Ini - {{ now()->isoFormat('dddd, D MMMM Y') }}
        </h3>

        {{-- Info Waktu --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Jam Sekarang</p>
                <p class="text-xl font-bold" id="clock">{{ now()->format('H:i:s') }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Status</p>
                <p class="text-sm font-bold {{ $absensiHariIni ? 'text-green-600' : ($isWeekend || $liburNasional ? 'text-slate-600' : 'text-amber-600') }}">
                    @if($absensiHariIni)
                        {{ ucfirst($absensiHariIni->status) }}
                    @elseif($isWeekend || $liburNasional)
                        Hari Libur
                    @else
                        Belum Absen
                    @endif
                </p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                <p class="text-xs text-slate-500 mb-1">Jam Masuk</p>
                <p class="text-xl font-bold text-green-700">
                    {{ $absensiHariIni?->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : '-' }}
                </p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <p class="text-xs text-slate-500 mb-1">Jam Pulang</p>
                <p class="text-xl font-bold text-blue-700">
                    {{ $absensiHariIni?->jam_keluar ? \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') : '-' }}
                </p>
            </div>
        </div>

        @if($isWeekend || $liburNasional)
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 flex items-start gap-3">
                <div>
                    <h4 class="font-bold text-sm">Hari Libur</h4>
                    <p class="text-sm">Hari ini adalah hari libur ({{ $isWeekend ? 'Akhir Pekan' : $liburNasional->keterangan }}). Anda tidak perlu melakukan absensi.</p>
                </div>
            </div>
        @else
            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap gap-3">
                @if(!$absensiHariIni)
                    <button onclick="bukaModalAbsensi('masuk')"
                        class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined">login</span>
                        Tandai Hadir
                    </button>
                    <button onclick="document.getElementById('modal-izin-sakit').classList.remove('hidden')"
                        class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-all">
                        <span class="material-symbols-outlined">event_busy</span>
                        Izin / Sakit
                    </button>

                @elseif($absensiHariIni->status === 'hadir' && !$absensiHariIni->jam_keluar)
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl font-semibold text-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        Masuk pukul {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                    </div>
                    <button onclick="bukaModalAbsensi('keluar')"
                        class="flex items-center gap-2 border border-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined">logout</span>
                        Tandai Pulang
                    </button>

                @else
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl font-semibold text-sm">
                        <span class="material-symbols-outlined">task_alt</span>
                        @if($absensiHariIni->status === 'hadir')
                            Absensi lengkap — Durasi: {{ $absensiHariIni->durasi ?? '-' }}
                        @elseif($absensiHariIni->status === 'izin')
                            Izin hari ini telah tercatat
                        @elseif($absensiHariIni->status === 'sakit')
                            Sakit hari ini telah tercatat
                        @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- Info lokasi absensi masuk --}}
        @if($absensiHariIni?->alamat_masuk)
        <div class="mt-4 flex items-start gap-2 text-xs text-slate-500 bg-slate-50 rounded-xl p-3">
            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
            <div>
                <span class="font-semibold">Lokasi masuk:</span> {{ $absensiHariIni->alamat_masuk }}
                @if($absensiHariIni->alamat_keluar)
                <br><span class="font-semibold">Lokasi pulang:</span> {{ $absensiHariIni->alamat_keluar }}
                @endif
            </div>
        </div>
        @endif

        {{-- Info Keterangan / Terlambat / Pulang Cepat --}}
        @if($absensiHariIni?->keterangan)
    <div class="mt-3 text-xs text-amber-600 bg-amber-50 rounded-xl p-3 border border-amber-100">
        <span class="font-bold">Catatan:</span> {{ $absensiHariIni->keterangan }}
    </div>
        @endif
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
                        <th class="px-4 py-4">Tanggal</th>
                        <th class="px-4 py-4">Masuk</th>
                        <th class="px-4 py-4">Pulang</th>
                        <th class="px-4 py-4">Foto</th>
                        <th class="px-4 py-4">Lokasi</th>
                        <th class="px-4 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $abs)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-4">
                            <p class="font-semibold">{{ $abs->tanggal->format('d M Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $abs->tanggal->isoFormat('dddd') }}</p>
                        </td>
                        <td class="px-4 py-4">{{ $abs->jam_masuk ? \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') : '-' }}</td>
                        <td class="px-4 py-4">{{ $abs->jam_keluar ? \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') : '-' }}</td>
                        <td class="px-4 py-4">
                            @if($abs->foto_masuk)
                            <a href="{{ asset('storage/' . $abs->foto_masuk) }}" target="_blank">
                                <img src="{{ asset('storage/' . $abs->foto_masuk) }}"
                                     class="w-10 h-10 rounded-lg object-cover border border-slate-200 hover:scale-110 transition-transform"
                                     alt="Foto masuk">
                            </a>
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($abs->latitude_masuk)
                            <a href="https://maps.google.com/?q={{ $abs->latitude_masuk }},{{ $abs->longitude_masuk }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 text-xs text-primary hover:underline">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                Lihat Map
                            </a>
                            @else
                            <span class="text-slate-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full
                                {{ $abs->status === 'hadir' ? 'bg-green-100 text-green-700' :
                                   ($abs->status === 'izin' ? 'bg-amber-100 text-amber-700' :
                                   ($abs->status === 'sakit' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($abs->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">calendar_month</span>
                            Belum ada riwayat absensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>

{{-- ── Modal Absensi (Foto + GPS) ──────────────────────────────────────── --}}
<div id="modal-absensi" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm max-h-full flex flex-col">
        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 flex-shrink-0">
            <h3 class="font-bold text-lg" id="modal-absensi-title">Absensi Masuk</h3>
            <button onclick="tutupModalAbsensi()" class="text-slate-400 hover:text-slate-600 p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="p-4 sm:p-5 space-y-3 overflow-y-auto">
            {{-- Kamera --}}
            <div>
                <p class="text-sm font-semibold text-slate-700 mb-2">Ambil Foto Selfie</p>
                <div class="relative bg-slate-900 rounded-xl overflow-hidden aspect-video">
                    <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline></video>
                    <canvas id="camera-canvas" class="hidden w-full h-full object-cover"></canvas>
                    <div id="foto-taken" class="hidden absolute inset-0">
                        <img id="foto-preview" class="w-full h-full object-cover" src="" alt="Foto"/>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="button" onclick="ambilFoto()"
                        id="btn-capture"
                        class="flex-1 flex items-center justify-center gap-2 bg-primary text-white font-bold py-2.5 rounded-xl text-sm hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                        Ambil Foto
                    </button>
                    <button type="button" onclick="ulangi()"
                        id="btn-ulangi"
                        class="hidden flex-1 flex items-center justify-center gap-2 border border-slate-300 text-slate-700 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-lg">refresh</span>
                        Ulangi
                    </button>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-semibold text-slate-700">Lokasi GPS</p>
                    <button type="button" onclick="ambilLokasi()"
                        class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">my_location</span>
                        Deteksi
                    </button>
                </div>
                <p id="lokasi-status" class="text-[10px] text-slate-400">Klik Deteksi untuk mendapatkan lokasi...</p>
                <p id="lokasi-alamat" class="text-xs text-slate-600 font-medium mt-1 hidden"></p>
            </div>

            {{-- Hidden inputs --}}
            <input type="hidden" id="input-foto"/>
            <input type="hidden" id="input-latitude"/>
            <input type="hidden" id="input-longitude"/>
            <input type="hidden" id="input-alamat"/>
            <input type="hidden" id="input-tipe"/>

            {{-- Form Keterangan --}}
            <div>
                <textarea id="input-keterangan-mahasiswa" rows="1" placeholder="Catatan tambahan (Opsional)..." class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary py-2 px-3 text-xs resize-none"></textarea>
            </div>

            {{-- Error --}}
            <div id="absensi-error" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"></div>

            {{-- Submit --}}
            <button type="button" onclick="submitAbsensi()"
                id="btn-submit-absensi"
                class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-xl transition-all shadow-md shadow-primary/20">
                <span class="material-symbols-outlined">send</span>
                <span id="btn-submit-label">Kirim Absensi</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Modal Izin / Sakit ───────────────────────────────────────────────── --}}
<div id="modal-izin-sakit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-slate-900">Form Izin / Sakit</h3>
            <button onclick="document.getElementById('modal-izin-sakit').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('mahasiswa.absensi.izinsakit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Ketidakhadiran</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="izin" class="peer hidden" required/>
                        <div class="flex items-center gap-2 p-3 border-2 border-slate-200 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                            <span class="material-symbols-outlined text-amber-500">event_busy</span>
                            <span class="text-sm font-semibold">Izin</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="sakit" class="peer hidden"/>
                        <div class="flex items-center gap-2 p-3 border-2 border-slate-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <span class="material-symbols-outlined text-blue-500">medical_services</span>
                            <span class="text-sm font-semibold">Sakit</span>
                        </div>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan" rows="3" required
                    placeholder="Jelaskan alasan izin/sakit Anda..."
                    class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary p-3 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-xl transition-all">
                    <span class="material-symbols-outlined">send</span>Kirim
                </button>
                <button type="button" onclick="document.getElementById('modal-izin-sakit').classList.add('hidden')"
                    class="flex-1 border border-slate-300 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-50 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
const KANTOR_LAT = -6.9834745188590315; // sesuaikan latitude dgn lok kantor
const KANTOR_LON = 110.40770197890485; // sesuaikan longitude dgn lok kantor
const MAX_RADIUS = 50;

function hitungJarakJS(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

let stream       = null;
let fotoData     = null;
let latitudeData = null;
let longitudeData= null;
let alamatData   = null;
let tipeAbsensi  = null;

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'masuk') {
        bukaModalAbsensi('masuk');
    } else if (params.get('action') === 'keluar') {
        bukaModalAbsensi('keluar');
    }
});

async function bukaModalAbsensi(tipe) {
    tipeAbsensi = tipe;
    fotoData     = null;
    latitudeData = null;
    longitudeData= null;
    alamatData   = null;

    document.getElementById('modal-absensi-title').textContent = tipe === 'masuk' ? ' Absensi Masuk' : ' Absensi Pulang';
    document.getElementById('btn-submit-label').textContent    = tipe === 'masuk' ? 'Kirim Absensi Masuk' : 'Kirim Absensi Pulang';
    document.getElementById('input-tipe').value = tipe;
    document.getElementById('input-keterangan-mahasiswa').value = '';
    document.getElementById('lokasi-status').textContent = 'Klik Deteksi untuk mendapatkan lokasi...';
    document.getElementById('lokasi-alamat').classList.add('hidden');
    document.getElementById('absensi-error').classList.add('hidden');

    // Reset foto
    ulangi();

    document.getElementById('modal-absensi').classList.remove('hidden');

    // Mulai kamera
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 }
        });
        document.getElementById('camera-preview').srcObject = stream;
    } catch(e) {
        showError('Kamera tidak dapat diakses: ' + e.message);
    }

    // Auto deteksi lokasi
    ambilLokasi();
}

function tutupModalAbsensi() {
    document.getElementById('modal-absensi').classList.add('hidden');
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
}

function ambilFoto() {
    const video  = document.getElementById('camera-preview');
    const canvas = document.getElementById('camera-canvas');
    const ctx    = canvas.getContext('2d');

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    fotoData = canvas.toDataURL('image/jpeg', 0.8);

    document.getElementById('foto-preview').src = fotoData;
    document.getElementById('foto-taken').classList.remove('hidden');
    document.getElementById('camera-preview').classList.add('hidden');
    document.getElementById('btn-capture').classList.add('hidden');
    document.getElementById('btn-ulangi').classList.remove('hidden');
    document.getElementById('input-foto').value = fotoData;
}

function ulangi() {
    fotoData = null;
    document.getElementById('input-foto').value = '';
    document.getElementById('foto-taken').classList.add('hidden');
    document.getElementById('camera-preview').classList.remove('hidden');
    document.getElementById('btn-capture').classList.remove('hidden');
    document.getElementById('btn-ulangi').classList.add('hidden');
}

function ambilLokasi() {
    const statusEl = document.getElementById('lokasi-status');
    const alamatEl = document.getElementById('lokasi-alamat');

    statusEl.textContent = 'Mendeteksi lokasi...';

    if (!navigator.geolocation) {
        statusEl.textContent = 'GPS tidak didukung browser ini.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            latitudeData  = pos.coords.latitude;
            longitudeData = pos.coords.longitude;

            document.getElementById('input-latitude').value  = latitudeData;
            document.getElementById('input-longitude').value = longitudeData;

            const jarak = hitungJarakJS(latitudeData, longitudeData, KANTOR_LAT, KANTOR_LON);
            statusEl.textContent = `Lokasi terdeteksi (Jarak dari kantor: ${Math.round(jarak)} meter)`;

            // Reverse geocoding: Prioritas Nominatim (akurasi kecamatan lokal lebih baik)
            let namaAlamat = '';
            try {
                const res  = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitudeData}&lon=${longitudeData}&format=json&zoom=18&addressdetails=1`);
                const data = await res.json();
                
                if (data && data.display_name) {
                    namaAlamat = data.display_name;
                } else {
                    throw new Error("Nominatim missing address");
                }
            } catch(e) {
                // Fallback ke ArcGIS jika Nominatim terkena limit/error
                try {
                    const res2 = await fetch(`https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/reverseGeocode?location=${longitudeData},${latitudeData}&f=json`);
                    const data2 = await res2.json();
                    if (data2 && data2.address && data2.address.LongLabel) {
                        namaAlamat = data2.address.LongLabel.replace(/, IDN$/, '');
                    } else {
                        throw new Error("ArcGIS error");
                    }
                } catch (e2) {
                    namaAlamat = "Gagal mengambil nama jalan.";
                }
            }
            
            alamatData = namaAlamat;
            
            document.getElementById('input-alamat').value = alamatData;
            alamatEl.textContent = ' ' + alamatData;
            alamatEl.classList.remove('hidden');
        },
        (err) => {
            statusEl.textContent = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
}

async function submitAbsensi() {
    const errorEl = document.getElementById('absensi-error');
    errorEl.classList.add('hidden');

    if (!fotoData) {
        showError('Foto belum diambil. Silakan ambil foto selfie terlebih dahulu.');
        return;
    }

    if (!latitudeData || !longitudeData) {
        showError('Lokasi belum terdeteksi. Klik tombol Deteksi dan aktifkan GPS.');
        return;
    }

    const jarak = hitungJarakJS(latitudeData, longitudeData, KANTOR_LAT, KANTOR_LON);
    if (jarak > MAX_RADIUS) {
        showError(`Absensi gagal. Anda berada ${Math.round(jarak)} meter dari kantor. (Maksimal ${MAX_RADIUS} meter)`);
        return;
    }

    const btn = document.getElementById('btn-submit-absensi');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin">autorenew</span> Mengirim...';

    const tipe  = document.getElementById('input-tipe').value;
    const url   = tipe === 'masuk'
                  ? '{{ route("mahasiswa.absensi.checkin") }}'
                  : '{{ route("mahasiswa.absensi.checkout") }}';

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('foto',      fotoData);
    formData.append('latitude',  latitudeData);
    formData.append('longitude', longitudeData);
    formData.append('alamat',    alamatData ?? '');
    formData.append('keterangan_mahasiswa', document.getElementById('input-keterangan-mahasiswa').value);

    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        if (response.redirected || response.ok) {
            tutupModalAbsensi();
            window.location.href = "{{ route('mahasiswa.absensi.index') }}";
        } else {
            const text = await response.text();
            showError('Gagal mengirim absensi. Coba lagi.');
        }
    } catch(e) {
        showError('Koneksi bermasalah. Periksa internet Anda.');
    }

    btn.disabled = false;
    btn.innerHTML = '<span class="material-symbols-outlined">send</span> <span id="btn-submit-label">Kirim Absensi</span>';
}

function showError(msg) {
    const el = document.getElementById('absensi-error');
    el.textContent = msg;
    el.classList.remove('hidden');
}

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
