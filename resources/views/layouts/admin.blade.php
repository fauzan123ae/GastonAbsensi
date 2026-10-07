<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Gaston Attendance</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        @media (max-width: 1023px) {
            aside#admin-sidebar:not(.sidebar-open) {
                transform: translateX(-100%) !important;
                display: none !important;
            }
            aside#admin-sidebar.sidebar-open {
                display: flex !important;
                transform: translateX(0) !important;
            }
        }
    </style>
</head>
<body class="bg-[#080a0f] gaston-stripes-bg gaston-diagonal-lines text-slate-100 min-h-screen antialiased selection:bg-[#e50914] selection:text-white"
      x-data="{ sidebarOpen: false }"
      @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

    <div class="flex min-h-screen w-full relative">

        <!-- Left Sidebar (Sticky & Fixed In-View when scrolling on lg, strictly hidden on mobile until clicked) -->
        <aside id="admin-sidebar"
               :class="{ 'sidebar-open': sidebarOpen }"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0a0d13] border-r border-[#1a1f2c] flex-col lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0 lg:!flex lg:!transform-none shadow-2xl">
        
        <!-- Brand / Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-[#1a1f2c]/80">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(229,9,20,0.35)]">
                <div>
                    <h1 class="font-black text-[11px] tracking-wider text-white leading-tight uppercase">ABSENSI<br><span class="text-[#e50914]">GASTON YK</span></h1>
                </div>
            </div>
            <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Links with exact active pill styling matching image -->
        <nav class="flex-1 overflow-y-auto px-3.5 py-6 space-y-1.5">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.sessions.index') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.sessions.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.sessions.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <span>Absensi</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('admin.permissions.index') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.permissions.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.permissions.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                <span>Pengajuan Izin</span>
            </a>

            <a href="{{ route('admin.members.index') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.members.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.members.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"></path></svg>
                <span>Profil</span>
            </a>

            @if(auth()->user()->isMasterAdmin())
                <a href="{{ route('admin.admins.index') }}"
                   class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.admins.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-amber-400/90 hover:text-amber-300 hover:bg-[#151922]' }}">
                    <svg class="w-4 h-4 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="flex items-center space-x-1.5">
                        <span>Kelola Admin</span>
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Master</span>
                    </span>
                </a>
            @endif

            <a href="{{ route('admin.audit-logs.index') }}"
               class="flex items-center space-x-3.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#991b1b]/30 text-white border border-[#e50914]/80 shadow-[0_0_15px_rgba(229,9,20,0.25)]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.audit-logs.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Pengaturan</span>
            </a>

            <div class="pt-4 pb-1">
                <span class="px-4 text-[9px] font-black uppercase tracking-wider text-slate-500">Master Data</span>
            </div>

            <a href="{{ route('admin.sports.index') }}"
               class="flex items-center space-x-3 px-4 py-2 rounded-2xl text-[11px] font-bold transition-all {{ request()->routeIs('admin.sports.*') ? 'text-white bg-[#1a1f2c]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Cabang Olahraga</span>
            </a>

            <a href="{{ route('admin.locations.index') }}"
               class="flex items-center space-x-3 px-4 py-2 rounded-2xl text-[11px] font-bold transition-all {{ request()->routeIs('admin.locations.*') ? 'text-white bg-[#1a1f2c]' : 'text-slate-400 hover:text-white hover:bg-[#151922]' }}">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                <span>GOR / Lokasi</span>
            </a>
        </nav>

        <!-- Bottom Left Slogan with dynamic red decorative slashes (Matching Image) -->
        <div class="p-4 border-t border-[#1a1f2c] bg-[#0a0d13] relative overflow-hidden">
            <div class="absolute -bottom-6 -left-6 w-24 h-24 pointer-events-none opacity-30">
                <div class="w-full h-1 bg-[#e50914] transform rotate-45 translate-y-3"></div>
                <div class="w-full h-0.5 bg-white transform rotate-45 translate-y-6"></div>
                <div class="w-full h-1.5 bg-[#e50914] transform rotate-45 translate-y-9"></div>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[9px] text-slate-400 font-bold leading-tight">Disiplin Hari Ini,</p>
                    <p class="text-[9px] text-slate-400 font-medium leading-tight">Masa Depan Lebih Baik</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-500 hover:text-[#e50914] hover:bg-[#141822] rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Top Navbar with Search Input and User Profile Pill (Sticky Top-0) -->
        <header class="sticky top-0 z-30 h-16 bg-[#080a0f]/95 backdrop-blur-md border-b border-[#1a1f2c] px-6 flex items-center justify-between gap-4">
            <div class="flex items-center space-x-3 flex-1 max-w-md">
                <button type="button" @click.stop="sidebarOpen = true" class="lg:hidden p-2 text-slate-400 hover:text-white rounded-xl hover:bg-[#1a1f2c]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Global Search Bar Component (Desktop & Mobile with ⌘K / Ctrl+K shortcut) -->
                <div class="relative w-full"
                     x-data="{
                        query: '',
                        results: { menu: [], members: [], sessions: [] },
                        open: false,
                        mobileSearchOpen: false,
                        loading: false,
                        timeout: null,
                        performSearch() {
                            clearTimeout(this.timeout);
                            this.loading = true;
                            this.timeout = setTimeout(() => {
                                fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(this.query))
                                    .then(res => res.json())
                                    .then(data => {
                                        this.results = data;
                                        this.loading = false;
                                        this.open = true;
                                    })
                                    .catch(() => { this.loading = false; });
                            }, 200);
                        }
                     }"
                     @keydown.window.prevent.cmd.k="if(window.innerWidth < 640) { mobileSearchOpen = true; $nextTick(() => $refs.mobileSearchInput.focus()); } else { $refs.searchInput.focus(); } performSearch()"
                     @keydown.window.prevent.ctrl.k="if(window.innerWidth < 640) { mobileSearchOpen = true; $nextTick(() => $refs.mobileSearchInput.focus()); } else { $refs.searchInput.focus(); } performSearch()"
                     @click.away="open = false">
                    
                    <!-- Desktop Search Bar -->
                    <div class="relative hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               x-ref="searchInput"
                               x-model="query"
                               @focus="performSearch()"
                               @input="performSearch()"
                               @keydown.escape="open = false; $refs.searchInput.blur()"
                               placeholder="Cari menu, fitur, sesi, atau data member..."
                               class="w-full bg-[#10141d] border border-[#1e2433] rounded-full pl-10 pr-12 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500 text-[10px] font-mono">
                            <span x-show="!loading">⌘ K</span>
                            <svg x-show="loading" x-cloak class="animate-spin w-3.5 h-3.5 text-[#e50914]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </div>
                    </div>

                    <!-- Mobile Search Trigger Button -->
                    <button type="button"
                            @click="mobileSearchOpen = true; $nextTick(() => { $refs.mobileSearchInput.focus(); performSearch(); })"
                            class="sm:hidden p-2 text-slate-400 hover:text-white rounded-xl hover:bg-[#1a1f2c] transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>

                    <!-- Mobile Fullscreen / Modal Search Bar -->
                    <div x-show="mobileSearchOpen"
                         x-cloak
                         class="fixed inset-0 z-50 bg-[#080a0f]/95 backdrop-blur-xl sm:hidden p-4 flex flex-col space-y-4"
                         @keydown.escape="mobileSearchOpen = false">
                        <div class="flex items-center space-x-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text"
                                       x-ref="mobileSearchInput"
                                       x-model="query"
                                       @input="performSearch()"
                                       placeholder="Cari menu, sesi, member..."
                                       class="w-full bg-[#10141d] border border-[#1e2433] rounded-2xl pl-10 pr-10 py-3 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914]">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-500">
                                    <svg x-show="loading" x-cloak class="animate-spin w-4 h-4 text-[#e50914]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            <button type="button"
                                    @click="mobileSearchOpen = false"
                                    class="p-2.5 text-slate-400 hover:text-white rounded-xl bg-[#10141d] border border-[#1e2433]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Mobile Results List -->
                        <div class="flex-1 overflow-y-auto divide-y divide-[#1e2433]/70 bg-[#10141d] border border-[#1e2433] rounded-2xl p-2">
                            <!-- Menu & Fitur Section -->
                            <template x-if="results.menu && results.menu.length > 0">
                                <div class="p-2">
                                    <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Menu & Navigasi</span>
                                    <div class="space-y-1">
                                        <template x-for="item in results.menu" :key="item.title">
                                            <a :href="item.url" class="p-2.5 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                                <div class="flex items-center space-x-2.5 min-w-0">
                                                    <div class="w-7 h-7 rounded-lg bg-[#090c12] border border-[#1e2433] flex items-center justify-center text-slate-400 group-hover:text-[#e50914] flex-shrink-0">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate" x-text="item.title"></p>
                                                        <p class="text-[10px] text-slate-400 truncate" x-text="item.description"></p>
                                                    </div>
                                                </div>
                                                <span class="text-slate-600 group-hover:text-slate-400 text-xs">&rarr;</span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Members Section -->
                            <template x-if="results.members && results.members.length > 0">
                                <div class="p-2">
                                    <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Member Terkait</span>
                                    <div class="space-y-1">
                                        <template x-for="m in results.members" :key="m.title">
                                            <a :href="m.url" class="p-2.5 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate" x-text="m.title"></p>
                                                    <p class="text-[10px] text-slate-400 font-mono truncate" x-text="m.subtitle"></p>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                                                      :class="m.badge_type === 'success' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30'"
                                                      x-text="m.badge"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Sessions Section -->
                            <template x-if="results.sessions && results.sessions.length > 0">
                                <div class="p-2">
                                    <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Sesi Olahraga</span>
                                    <div class="space-y-1">
                                        <template x-for="s in results.sessions" :key="s.title">
                                            <a :href="s.url" class="p-2.5 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate" x-text="s.title"></p>
                                                    <p class="text-[10px] text-slate-400 font-mono truncate" x-text="s.subtitle"></p>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#0d1017] text-slate-300 border border-[#1e2433]"
                                                      x-text="s.badge"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty State -->
                            <div x-show="query && (!results.menu || results.menu.length === 0) && (!results.members || results.members.length === 0) && (!results.sessions || results.sessions.length === 0)" class="p-8 text-center text-xs text-slate-500">
                                Tidak ditemukan hasil untuk "<span class="text-white" x-text="query"></span>"
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Search Results Dropdown -->
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="hidden sm:block absolute left-0 right-0 mt-2 bg-[#10141d] border border-[#1e2433] rounded-3xl shadow-2xl z-50 overflow-hidden max-h-[460px] overflow-y-auto divide-y divide-[#1e2433]/70">
                        
                        <!-- Menu & Fitur Section -->
                        <template x-if="results.menu && results.menu.length > 0">
                            <div class="p-3">
                                <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Menu & Navigasi</span>
                                <div class="space-y-1">
                                    <template x-for="item in results.menu" :key="item.title">
                                        <a :href="item.url" class="p-2 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                            <div class="flex items-center space-x-2.5 min-w-0">
                                                <div class="w-6 h-6 rounded-lg bg-[#090c12] border border-[#1e2433] flex items-center justify-center text-slate-400 group-hover:text-[#e50914] group-hover:border-[#e50914]/40 flex-shrink-0 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate transition-colors" x-text="item.title"></p>
                                                    <p class="text-[10px] text-slate-400 truncate" x-text="item.description"></p>
                                                </div>
                                            </div>
                                            <span class="text-slate-600 group-hover:text-slate-400 text-xs transition-colors">&rarr;</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Members Section -->
                        <template x-if="results.members && results.members.length > 0">
                            <div class="p-3">
                                <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Member Terkait</span>
                                <div class="space-y-1">
                                    <template x-for="m in results.members" :key="m.title">
                                        <a :href="m.url" class="p-2 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate transition-colors" x-text="m.title"></p>
                                                <p class="text-[10px] text-slate-400 font-mono truncate" x-text="m.subtitle"></p>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                                                  :class="m.badge_type === 'success' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30'"
                                                  x-text="m.badge"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Sessions Section -->
                        <template x-if="results.sessions && results.sessions.length > 0">
                            <div class="p-3">
                                <span class="px-2 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-1">Sesi Olahraga</span>
                                <div class="space-y-1">
                                    <template x-for="s in results.sessions" :key="s.title">
                                        <a :href="s.url" class="p-2 rounded-xl flex items-center justify-between hover:bg-[#151a24] group transition-colors">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-white group-hover:text-[#e50914] truncate transition-colors" x-text="s.title"></p>
                                                <p class="text-[10px] text-slate-400 font-mono truncate" x-text="s.subtitle"></p>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#0d1017] text-slate-300 border border-[#1e2433]"
                                                  x-text="s.badge"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="query && (!results.menu || results.menu.length === 0) && (!results.members || results.members.length === 0) && (!results.sessions || results.sessions.length === 0)" class="p-6 text-center text-xs text-slate-500">
                            Tidak ditemukan hasil untuk "<span class="text-white" x-text="query"></span>"
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Notification Bell with Interactive Dropdown -->
                <div class="relative" x-data="{ notifOpen: false }">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            class="relative p-2 text-slate-400 hover:text-white rounded-xl hover:bg-[#11141c] transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#e50914] animate-pulse"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown Panel -->
                    <div x-show="notifOpen"
                         @click.away="notifOpen = false"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-[#10141d] border border-[#1e2433] rounded-3xl shadow-2xl z-50 overflow-hidden divide-y divide-[#1e2433]">
                        
                        <!-- Header -->
                        <div class="p-4 flex items-center justify-between bg-[#0a0d13]">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-xs font-black uppercase tracking-wider text-white">Notifikasi</h4>
                                @if(($unreadNotificationsCount ?? 0) > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                                        {{ $unreadNotificationsCount }} Baru
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">Real-time</span>
                        </div>

                        <!-- Notification Items List -->
                        <div class="max-h-80 overflow-y-auto divide-y divide-[#1e2433]/60">
                            @forelse($navbarNotifications ?? [] as $notif)
                                <a href="{{ $notif['link'] }}"
                                   class="p-3.5 flex items-start space-x-3 hover:bg-[#151a24] transition-colors block group">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ $notif['type'] === 'warning' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : ($notif['type'] === 'success' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : ($notif['type'] === 'danger' ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'bg-[#e50914]/15 text-[#e50914] border border-[#e50914]/30')) }}">
                                        @if($notif['type'] === 'warning')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        @elseif($notif['type'] === 'success')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-white group-hover:text-[#e50914] transition-colors truncate">{{ $notif['title'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 leading-snug line-clamp-2">{{ $notif['message'] }}</p>
                                        <span class="text-[9px] text-slate-500 font-mono mt-1 block">{{ $notif['time'] }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-slate-500 text-xs space-y-1">
                                    <svg class="w-6 h-6 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <p class="font-bold text-slate-400">Tidak ada notifikasi baru</p>
                                    <p class="text-[10px]">Semua aktivitas terbaru sudah Anda tinjau.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer -->
                        <div class="p-2.5 bg-[#0a0d13] text-center">
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-white transition-colors">
                                Lihat Log Aktivitas &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Pill in Navbar with Interactive Dropdown -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center space-x-2.5 cursor-pointer group p-1.5 rounded-2xl hover:bg-[#151922] transition-colors focus:outline-none">
                        <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-700 border border-slate-600 flex items-center justify-center text-white text-xs font-bold shadow-md">
                            @if(auth()->user()->profile_photo_url ?? false)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="flex items-center space-x-1">
                                <p class="text-xs font-bold text-white leading-tight truncate max-w-[130px]">{{ auth()->user()->name }}</p>
                                <svg class="w-3 h-3 text-slate-400 group-hover:text-white transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <p class="text-[10px] {{ auth()->user()->isMasterAdmin() ? 'text-amber-400 font-bold' : 'text-slate-400 font-medium' }} leading-none mt-0.5">
                                {{ auth()->user()->isMasterAdmin() ? '👑 Master Admin' : 'Admin' }}
                            </p>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-56 bg-[#11141c] border border-[#1f2533] rounded-2xl shadow-2xl py-2 z-50 divide-y divide-[#1f2533]"
                         style="display: none;">
                        
                        <div class="px-4 py-2.5">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            <div class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ auth()->user()->isMasterAdmin() ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                {{ auth()->user()->isMasterAdmin() ? '👑 Master Admin' : 'Admin Portal' }}
                            </div>
                        </div>

                        <div class="py-1 text-xs">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 text-slate-300 hover:text-white hover:bg-[#181d29] transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.profile') }}" class="flex items-center space-x-2 px-4 py-2 text-slate-300 hover:text-white hover:bg-[#181d29] transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"></path></svg>
                                <span>Profil Saya</span>
                            </a>
                            @if(auth()->user()->isMasterAdmin())
                                <a href="{{ route('admin.admins.index') }}" class="flex items-center space-x-2 px-4 py-2 text-amber-300 hover:bg-[#181d29] transition-colors">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span>Kelola Admin Master</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center space-x-2 px-4 py-2 text-slate-300 hover:text-white hover:bg-[#181d29] transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Audit Log</span>
                            </a>
                        </div>

                        <div class="py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>Keluar / Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages & Main Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-[#080a0f] gaston-diagonal-lines">
            <div class="w-full max-w-7xl mx-auto space-y-6">
                
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center space-x-3 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 space-y-1 shadow-lg">
                        <div class="flex items-center space-x-2 font-bold text-rose-400 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Terjadi kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
