<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - MagangDPMPTSP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { "primary": "#1152d4" },
                    fontFamily: { "sans": ["Public Sans", "sans-serif"] },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined { vertical-align: middle; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 antialiased">

@php
    $tugasBelumKumpul = 0;
    if (auth()->check() && auth()->user()->role === 'mahasiswa') {
        $tugasBelumKumpul = \App\Models\Tugas::where('is_aktif', true)
            ->whereHas('mahasiswas', fn($q) => $q->where('users.id', auth()->id()))
            ->get()
            ->filter(fn($t) => !$t->pengumpulanByUser(auth()->id()))
            ->count();
    }
@endphp

<div class="flex min-h-screen">

    {{-- ── Overlay mobile ─────────────────────────────────────────────── --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
         onclick="toggleSidebar()"></div>

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside id="sidebar"
           class="w-64 bg-white border-r border-slate-200 flex flex-col
                  fixed h-full z-30
                  -translate-x-full lg:translate-x-0
                  transition-transform duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="p-5 flex items-center gap-3 border-b border-slate-100">
            <div class="h-10 w-auto flex-shrink-0">
                <img src="{{ asset('images/ptsp.png') }}" alt="PTSP Jateng" class="h-full w-auto object-contain">
            </div>
            <div class="min-w-0">
                <h1 class="font-bold text-base leading-tight text-slate-900 truncate">Magang DPMPTSP</h1>
                <p class="text-xs text-slate-400">Portal Mahasiswa</p>
            </div>
            {{-- Tombol tutup sidebar di mobile --}}
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">
            <a href="{{ route('mahasiswa.dashboard') }}"
               onclick="closeSidebarOnMobile()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('mahasiswa.absensi.index') }}"
               onclick="closeSidebarOnMobile()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.absensi.*') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                Absensi
            </a>
            <a href="{{ route('mahasiswa.tugas.index') }}"
               onclick="closeSidebarOnMobile()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.tugas.*') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">assignment</span>
                <span class="flex-1">Tugas</span>
                @if($tugasBelumKumpul > 0)
                <span class="bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center">
                    {{ $tugasBelumKumpul }}
                </span>
                @endif
            </a>
            <a href="{{ route('mahasiswa.profil') }}"
               onclick="closeSidebarOnMobile()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.profil') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">person</span>
                Profil
            </a>
        </nav>

        {{-- User --}}
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-2 mb-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-lg">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────────────────── --}}
    <main class="flex-1 min-w-0 w-full flex flex-col min-h-screen lg:ml-64">

        {{-- Top Bar --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                {{-- Hamburger menu (mobile only) --}}
                <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div>
                    <h2 class="font-bold text-base md:text-lg text-slate-900 leading-tight">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', now()->isoFormat('dddd, D MMMM Y'))</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="hidden sm:block text-xs text-slate-500 bg-primary/5 border border-primary/10 px-3 py-1.5 rounded-full font-medium">
                    Mahasiswa Magang
                </span>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="flex-1 p-4 md:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if (session('success'))
            <div class="mb-5 flex items-start gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">
                <span class="material-symbols-outlined text-green-600 flex-shrink-0">check_circle</span>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
            @endif
            @if (session('error'))
            <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
                <span class="material-symbols-outlined text-red-600 flex-shrink-0">error</span>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
            @endif

            @yield('content')
        </div>

        {{-- Bottom Nav (mobile only) --}}
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-10 flex items-center justify-around px-2 py-2 safe-area-bottom">
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg
                      {{ request()->routeIs('mahasiswa.dashboard') ? 'text-primary' : 'text-slate-500' }}">
                <span class="material-symbols-outlined text-2xl">dashboard</span>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            <a href="{{ route('mahasiswa.absensi.index') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg
                      {{ request()->routeIs('mahasiswa.absensi.*') ? 'text-primary' : 'text-slate-500' }}">
                <span class="material-symbols-outlined text-2xl">calendar_today</span>
                <span class="text-[10px] font-medium">Absensi</span>
            </a>
            <a href="{{ route('mahasiswa.tugas.index') }}"
               class="relative flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg
                      {{ request()->routeIs('mahasiswa.tugas.*') ? 'text-primary' : 'text-slate-500' }}">
                <span class="material-symbols-outlined text-2xl">assignment</span>
                @if($tugasBelumKumpul > 0)
                <span class="absolute top-0 right-1 bg-amber-400 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
                    {{ $tugasBelumKumpul }}
                </span>
                @endif
                <span class="text-[10px] font-medium">Tugas</span>
            </a>
            <a href="{{ route('mahasiswa.profil') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg
                      {{ request()->routeIs('mahasiswa.profil') ? 'text-primary' : 'text-slate-500' }}">
                <span class="material-symbols-outlined text-2xl">person</span>
                <span class="text-[10px] font-medium">Profil</span>
            </a>
        </nav>

        {{-- Spacer untuk bottom nav di mobile --}}
        <div class="lg:hidden h-16"></div>

    </main>
</div>

@yield('scripts')

<script>
    function toggleSidebar() {
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    function closeSidebarOnMobile() {
        if (window.innerWidth < 1024) {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    }
</script>
</body>
</html>
