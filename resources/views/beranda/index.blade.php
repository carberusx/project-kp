@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="min-h-screen flex flex-col">

{{-- ── NAVBAR ─────────────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-50 flex items-center justify-between px-6 md:px-20 py-4 bg-white border-b border-slate-200 shadow-sm">
    <div class="flex items-center gap-3 text-primary">
        <div class="h-14 w-auto">
            <img src="{{ asset('images/ptsp.png') }}" alt="PTSP Jateng" class="h-full w-auto object-contain">
        </div>
        <!--<h2 class="text-slate-900 text-xl font-bold tracking-tight">MagangDPMPTSP</h2>-->
    </div>
    <div class="hidden md:flex items-center gap-8">
        <a href="#tentang" class="text-slate-600 text-sm font-medium hover:text-primary transition-colors">Tentang Program</a>
        <a href="#daftar" class="text-slate-600 text-sm font-medium hover:text-primary transition-colors">Pendaftaran</a>
        <a href="#kontak" class="text-slate-600 text-sm font-medium hover:text-primary transition-colors">Kontak</a>
    </div>
    <a href="{{ route('login') }}" class="flex items-center gap-2 bg-primary text-white text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
        <span class="material-symbols-outlined text-lg">login</span>
        Portal Login
    </a>
</header>

{{-- ── HERO ────────────────────────────────────────────────────────────────── --}}
<section class="flex-1 bg-gradient-to-br from-slate-50 via-blue-50/30 to-white px-6 md:px-20 py-16 md:py-24">
    <div class="max-w-6xl mx-auto">

        {{-- Alert success --}}
        @if (session('success'))
        <div class="mb-8 flex items-start gap-3 p-5 bg-green-50 border border-green-200 text-green-800 rounded-2xl">
            <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            <div>
                <p class="font-bold text-sm">Pendaftaran Berhasil!</p>
                <p class="text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        {{-- Alert error --}}
        @if (session('error'))
        <div class="mb-8 flex items-start gap-3 p-5 bg-red-50 border border-red-200 text-red-800 rounded-2xl">
            <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
            <div>
                <p class="font-bold text-sm">Pendaftaran Gagal!</p>
                <p class="text-sm mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="flex flex-col gap-10 lg:flex-row items-center">
            {{-- Left: Copy --}}
            <div class="w-full lg:w-1/2 flex flex-col gap-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    Program Magang DPMPTSP
                </div>
                <h1 class="text-slate-900 text-4xl md:text-5xl font-black leading-tight tracking-tight">
                    Bentuk Masa Depan<br><span class="text-primary">Pelayanan Publik</span>
                </h1>
                <p class="text-slate-500 text-lg leading-relaxed">
                    Bergabunglah dengan Program Magang DPMPTSP. Dapatkan pengalaman langsung sesuai dengan Jurusan anda.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#daftar" class="flex items-center gap-2 bg-primary text-white font-bold px-6 py-3.5 rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/25">
                        <span class="material-symbols-outlined text-xl">edit_document</span>
                        Daftar Sekarang
                    </a>
                    <a href="#tentang" class="flex items-center gap-2 border border-slate-300 text-slate-700 font-bold px-6 py-3.5 rounded-xl hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-xl">info</span>
                        Pelajari Lebih Lanjut
                    </a>
                </div>
                <!--<div class="flex items-center gap-6 pt-2">
                    <div class="text-center">
                        <p class="text-2xl font-black text-primary">500+</p>
                        <p class="text-xs text-slate-500 font-medium">Alumni</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-primary">20+</p>
                        <p class="text-xs text-slate-500 font-medium">Kementerian</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-primary">94%</p>
                        <p class="text-xs text-slate-500 font-medium">Tingkat Kepuasan</p>
                    </div>
                </div>-->
            </div>

            {{-- Right: Visual --}}
            <div class="w-full lg:w-1/2">
                <div class="relative bg-gradient-to-br from-primary to-blue-700 rounded-2xl p-8 shadow-2xl shadow-primary/30 overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    <div class="relative z-10 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">school</span>
                            </div>
                            <div>
                                <p class="font-bold text-lg">Magang DPMPTSP</p>
                                <p class="text-blue-200 text-sm">Sedang Berjalan</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-3xl font-black">{{ $mahasiswaAktif }}</p>
                            <p class="text-blue-200 text-sm">Magang Diterima</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-3xl font-black">{{ $reviewPending }}</p>
                            <p class="text-blue-200 text-sm">Review Pending</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-100">Total Pendaftar</span>
                            <span class="font-bold">{{ $persenDiterima }}%</span>
                        </div>
                        <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full" style="width: {{ $persenDiterima }}%"></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── TENTANG PROGRAM ─────────────────────────────────────────────────────── --}}
<section id="tentang" class="bg-white py-20 px-6 md:px-20">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-4">
                Tentang Program
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Mengapa Bergabung?</h2>
            <p class="text-slate-500 max-w-xl mx-auto mt-4 leading-relaxed">
                Program magang kami memberikan kesempatan bagi mahasiswa untuk berkontribusi langsung pada proyek-proyek yang bermakna.
            </p>
        </div>
       <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="group flex flex-col gap-5 rounded-2xl border border-slate-200 p-7 hover:border-primary/40 hover:shadow-lg hover:shadow-primary/5 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-3xl">groups</span>
                </div>
                <div>
                    <h3 class="text-slate-900 text-xl font-bold mb-2">Mentorship Ahli</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Bekerja langsung dengan para mentor yang berdedikasi pada pertumbuhan profesional Anda.</p>
                </div>
            </div>
            <div class="group flex flex-col gap-5 rounded-2xl border border-slate-200 p-7 hover:border-primary/40 hover:shadow-lg hover:shadow-primary/5 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-3xl">public</span>
                </div>
                <div>
                    <h3 class="text-slate-900 text-xl font-bold mb-2">Dampak Nasional</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Berkontribusi pada proyek nyata yang memengaruhi jutaan warga, mulai dari kebijakan lingkungan hingga infrastruktur digital.</p>
                </div>
            </div>
            <div class="group flex flex-col gap-5 rounded-2xl border border-slate-200 p-7 hover:border-primary/40 hover:shadow-lg hover:shadow-primary/5 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center group-hover:bg-violet-500 group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-3xl">work_history</span>
                </div>
                <div>
                    <h3 class="text-slate-900 text-xl font-bold mb-2">Jalur Karir</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Intern terbaik mendapatkan pertimbangan preferensial untuk posisi junior tetap dalam layanan sipil.</p>
                </div>
            </div> -->
        </div>
    </div>
</section>

{{-- ── FORM PENDAFTARAN ─────────────────────────────────────────────────────── --}}
<section id="daftar" class="bg-gray-50 py-20 px-6 md:px-20">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header form --}}
            <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-7">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-2xl">edit_document</span>
                    </div>
                    <div>
                        <h2 class="text-white text-2xl font-bold">Formulir Pendaftaran</h2>
                        <p class="text-blue-100 text-sm">Isi data berikut untuk mendaftar Magang</p>
                    </div>
                </div>
            </div>

            {{-- Info Kuota --}}
            <div class="px-8 py-3 {{ $pendaftaranTutup ? 'bg-red-50 border-b border-red-200' : 'bg-green-50 border-b border-green-200' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg {{ $pendaftaranTutup ? 'text-red-500' : 'text-green-500' }}">
                            {{ $pendaftaranTutup ? 'lock' : 'how_to_reg' }}
                        </span>
                        <span class="text-sm font-semibold {{ $pendaftaranTutup ? 'text-red-700' : 'text-green-700' }}">
                            {{ $pendaftaranTutup ? 'Pendaftaran Ditutup — Kuota Penuh' : 'Pendaftaran Masih Dibuka' }}
                        </span>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $pendaftaranTutup ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $jumlahPendaftar }} / {{ $batasPendaftar }} pendaftar
                        {{ $pendaftaranTutup ? '' : '· Sisa ' . $sisaKuota . ' slot' }}
                    </span>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-bold text-red-800 mb-2">Terdapat kesalahan dalam formulir:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                        <li class="text-sm text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama sesuai KTP"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('nama_lengkap') border-red-400 @enderror"/>
                        @error('nama_lengkap')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@gmail.com"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('email') border-red-400 @enderror"/>
                        @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">NIM <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" value="{{ old('nim') }}" placeholder="Nomor Induk Mahasiswa"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('nim') border-red-400 @enderror"/>
                        @error('nim')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Nomor Telepon <span class="text-red-500">*</span></label>
                        <input type="tel" name="no_telpon" value="{{ old('no_telpon') }}" placeholder="08xxxxxxxxxx"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('no_telpon') border-red-400 @enderror"/>
                        @error('no_telpon')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Universitas / Institusi <span class="text-red-500">*</span></label>
                        <input type="text" name="universitas" value="{{ old('universitas') }}" placeholder="Universitas Indonesia"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('universitas') border-red-400 @enderror"/>
                        @error('universitas')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Jurusan / Program Studi <span class="text-red-500">*</span></label>
                        <input type="text" name="jurusan" list="jurusan-options" value="{{ old('jurusan') }}" placeholder="Contoh: Teknik Informatika"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('jurusan') border-red-400 @enderror" autocomplete="off"/>
                        <datalist id="jurusan-options">
                            <option value="Sistem Informasi">
                            <option value="Administrasi Negara">
                            <option value="Ekonomi">
                            <option value="Hukum">
                            <option value="Teknik Informatika">
                            <option value="Komunikasi">
                        </datalist>
                        @error('jurusan')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Tanggal Mulai Magang <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('tanggal_mulai') border-red-400 @enderror"/>
                        @error('tanggal_mulai')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-slate-700">Tanggal Selesai Magang <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary h-11 px-4 text-sm @error('tanggal_selesai') border-red-400 @enderror"/>
                        @error('tanggal_selesai')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Upload CV --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-slate-700">Upload Dokumen/CV (opsional)</label>
                    <label class="relative border-2 border-dashed border-slate-300 rounded-xl hover:border-primary transition-colors cursor-pointer bg-slate-50 hover:bg-primary/5 block">
                        <div id="upload-placeholder" class="flex flex-col items-center justify-center py-8 pointer-events-none">
                            <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">upload_file</span>
                            <p class="text-sm font-medium text-slate-600">Klik untuk upload atau drag & drop</p>
                            <p class="text-xs text-slate-400 mt-1">PDF, DOCX, ZIP hingga 10MB</p>
                        </div>
                        <div id="upload-selected" class="hidden flex-col items-center justify-center py-8 pointer-events-none">
                            <span class="material-symbols-outlined text-3xl text-green-500 mb-2">check_circle</span>
                            <p id="upload-filename" class="text-sm font-medium text-green-700"></p>
                            <p class="text-xs text-slate-400 mt-1">Klik untuk mengganti file</p>
                        </div>
                        <input type="file" name="file_dokumen" accept=".pdf,.doc,.docx,.zip"
                               onchange="handleFileSelect(this)"
                               class="absolute inset-0 opacity-0 cursor-pointer z-10"/>
                    </label>
                    @error('file_dokumen')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Motivasi --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-slate-700">Motivasi & Pernyataan Diri <span class="text-red-500">*</span></label>
                    <textarea name="motivasi" rows="5" placeholder="Ceritakan mengapa Anda ingin bergabung dengan program ini dan apa yang ingin Anda capai... (min. 20 karakter)"
                              class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary p-4 text-sm resize-none @error('motivasi') border-red-400 @enderror">{{ old('motivasi') }}</textarea>
                    @error('motivasi')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Persetujuan --}}
                <div id="box-setuju" class="flex items-start gap-3 p-4 rounded-xl border transition-all
                    {{ $errors->has('setuju') ? 'bg-red-50 border-red-300' : 'bg-slate-50 border-slate-200' }}">
                    <input type="checkbox" name="setuju" id="setuju" value="1"
                        onchange="handleSetuju(this)"
                        class="mt-0.5 rounded border-slate-300 text-primary focus:ring-primary
                                {{ $errors->has('setuju') ? 'border-red-400' : '' }}"/>
                    <label for="setuju" class="text-sm text-slate-600 leading-relaxed cursor-pointer">
                        Saya menyatakan bahwa informasi yang diberikan adalah benar dan lengkap sesuai pengetahuan saya. Saya memahami bahwa pernyataan palsu dapat mendiskualifikasi pendaftaran saya.
                    </label>
                </div>

                {{-- Warning belum centang --}}
                <div id="warning-setuju" class="{{ $errors->has('setuju') ? 'flex' : 'hidden' }} items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <span class="material-symbols-outlined text-red-500 flex-shrink-0">warning</span>
                    <span>Anda harus mencentang pernyataan di atas sebelum mengirim formulir.</span>
                </div>

                <button type="submit"
                    onclick="return checkSetuju()"
                    {{ $pendaftaranTutup ? 'disabled' : '' }}
                    class="w-full flex items-center justify-center gap-2 font-bold py-4 rounded-xl transition-all text-base
                    {{ $pendaftaranTutup
                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                        : 'bg-primary hover:bg-primary/90 text-white shadow-lg shadow-primary/25' }}">
                    <span class="material-symbols-outlined text-xl">
                        {{ $pendaftaranTutup ? 'lock' : 'send' }}
                    </span>
                    {{ $pendaftaranTutup ? 'Pendaftaran Ditutup' : 'Kirim Pendaftaran' }}
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ── FOOTER ─────────────────────────────────────────────────────────────── --}}
<footer id="kontak" class="bg-white border-t border-slate-200 px-6 md:px-20 py-12">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 text-primary mb-4">
                <div class="h-14 w-auto">
                <img src="{{ asset('images/ptsp.png') }}" alt="PTSP Jateng" class="h-full w-auto object-contain">
            </div>
                <h2 class="text-slate-900 text-lg font-bold">Magang DPMPTSP</h2>
            </div>
            <p class="text-slate-500 text-sm leading-relaxed max-w-sm">
                Program Magang DPMPTSP untuk pengembangan skill para mahasiswa.
            </p>
        </div>
        <!--<div>
            <h4 class="text-slate-900 font-bold mb-5 text-sm uppercase tracking-wider">Tautan Cepat</h4>
            <ul class="space-y-3 text-sm text-slate-500">
                <li><a href="#tentang" class="hover:text-primary transition-colors">Panduan Program</a></li>
                <li><a href="#daftar" class="hover:text-primary transition-colors">Kriteria Eligibilitas</a></li>
                <li><a href="#" class="hover:text-primary transition-colors">Kisah Sukses</a></li>
                <li><a href="#" class="hover:text-primary transition-colors">FAQ</a></li>
            </ul>
        </div>-->
        <div>
            <h4 class="text-slate-900 font-bold mb-5 text-sm uppercase tracking-wider">Kontak</h4>
            <ul class="space-y-3 text-sm text-slate-500">
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-primary">mail</span>
                    penerimaan@magangdpmptsp.go.id
                </li>
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-primary">call</span>
                    1-800-MagangDPMPTSP
                </li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto mt-10 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-400">
        <p>© {{ date('Y') }} Program Magang DPMPTSP. Hak cipta dilindungi.</p>
        <!--<div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-green-500">verified_user</span>
            Sistem terenkripsi dan aman
        </div>-->
    </div>
</footer>

</div>
@endsection

@section('scripts')
<script> 
    function handleSetuju(checkbox) {
        const box     = document.getElementById('box-setuju');
        const warning = document.getElementById('warning-setuju');

        if (checkbox.checked) {
            box.classList.remove('bg-red-50', 'border-red-300');
            box.classList.add('bg-green-50', 'border-green-300');
            warning.classList.add('hidden');
            warning.classList.remove('flex');
        } else {
            box.classList.remove('bg-green-50', 'border-green-300');
            box.classList.add('bg-slate-50', 'border-slate-200');
        }
    }

    function checkSetuju() {
        const checkbox = document.getElementById('setuju');
        const box      = document.getElementById('box-setuju');
        const warning  = document.getElementById('warning-setuju');

        if (!checkbox.checked) {
            // Tampilkan warning
            box.classList.remove('bg-slate-50', 'border-slate-200', 'bg-green-50', 'border-green-300');
            box.classList.add('bg-red-50', 'border-red-300');
            warning.classList.remove('hidden');
            warning.classList.add('flex');

            // Scroll ke warning
            warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        return true;
    }

    function handleFileSelect(input) {
        const placeholder = document.getElementById('upload-placeholder');
        const selected    = document.getElementById('upload-selected');
        const filename    = document.getElementById('upload-filename');

        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            filename.textContent = file.name + ' (' + sizeMB + ' MB)';
            placeholder.classList.add('hidden');
            placeholder.classList.remove('flex');
            selected.classList.remove('hidden');
            selected.classList.add('flex');
        } else {
            placeholder.classList.remove('hidden');
            placeholder.classList.add('flex');
            selected.classList.add('hidden');
            selected.classList.remove('flex');
        }
    }
</script>
@endsection
