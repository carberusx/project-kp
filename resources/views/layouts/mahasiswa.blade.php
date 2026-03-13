<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SiMagang</title>
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
<div class="flex min-h-screen">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-30">
        {{-- Logo --}}
        <div class="p-6 flex items-center gap-3 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-xl">account_balance</span>
            </div>
            <div>
                <h1 class="font-bold text-base leading-tight text-slate-900">SiMagang</h1>
                <p class="text-xs text-slate-400">Portal Mahasiswa</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-4 py-5 space-y-1">
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('mahasiswa.absensi.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.absensi.*') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                Absensi
            </a>
            <a href="{{ route('mahasiswa.tugas.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.tugas.*') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">assignment</span>
                Tugas
            </a>
            <a href="{{ route('mahasiswa.profil') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('mahasiswa.profil') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-xl">person</span>
                Profil
            </a>
        </nav>

        {{-- User --}}
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-2 mb-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
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
    <main class="ml-64 flex-1 flex flex-col min-h-screen">
        {{-- Top Bar --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-20">
            <div>
                <h2 class="font-bold text-lg text-slate-900">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-slate-400">@yield('page-subtitle', now()->isoFormat('dddd, D MMMM Y'))</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 bg-primary/5 border border-primary/10 px-3 py-1.5 rounded-full font-medium">
                    Mahasiswa Magang
                </span>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="flex-1 p-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
@yield('scripts')
</body>
</html>
