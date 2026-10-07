<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Member Portal') - Gaston YK</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#080a0f] gaston-stripes-bg gaston-diagonal-lines text-slate-100 min-h-screen flex flex-col antialiased pb-20 md:pb-6 selection:bg-[#e50914] selection:text-white">
    @include('components.mobile-splash')

    <!-- Member Top Navbar Matching Mockup Phone 4 & 5 (Sticky Top-0) -->
    <header class="bg-[#080a0f]/95 backdrop-blur-md border-b border-[#1f2533]/80 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-8 w-auto object-contain drop-shadow-[0_0_10px_rgba(229,9,20,0.35)]">
                <div>
                    <h1 class="font-black text-[11px] tracking-wider text-white uppercase leading-tight">ABSENSI<br><span class="text-[#e50914]">GASTON YK</span></h1>
                </div>
            </div>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center space-x-1.5">
                <a href="{{ route('member.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('member.dashboard') ? 'bg-[#181216] text-white border border-[#e50914] shadow-md shadow-[#e50914]/20' : 'text-slate-400 hover:text-white hover:bg-[#141822]' }}">
                    Beranda
                </a>
                <a href="{{ route('member.qr') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('member.qr') ? 'bg-[#181216] text-white border border-[#e50914] shadow-md shadow-[#e50914]/20' : 'text-slate-400 hover:text-white hover:bg-[#141822]' }}">
                    QR Absen
                </a>
                <a href="{{ route('member.attendance.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('member.attendance.*') ? 'bg-[#181216] text-white border border-[#e50914] shadow-md shadow-[#e50914]/20' : 'text-slate-400 hover:text-white hover:bg-[#141822]' }}">
                    Riwayat
                </a>
                <a href="{{ route('member.permissions.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('member.permissions.*') ? 'bg-[#181216] text-white border border-[#e50914] shadow-md shadow-[#e50914]/20' : 'text-slate-400 hover:text-white hover:bg-[#141822]' }}">
                    Izin
                </a>
                <a href="{{ route('member.profile') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('member.profile') ? 'bg-[#181216] text-white border border-[#e50914] shadow-md shadow-[#e50914]/20' : 'text-slate-400 hover:text-white hover:bg-[#141822]' }}">
                    Profil
                </a>
            </nav>

            <!-- Bell Notification with Interactive Dropdown -->
            <div class="relative" x-data="{ notifOpen: false }">
                <button type="button"
                        @click="notifOpen = !notifOpen"
                        class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-[#11141c] transition-colors focus:outline-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#e50914] animate-pulse"></span>
                    @endif
                </button>

                <!-- Notification Dropdown Panel for Member -->
                <div x-show="notifOpen"
                     @click.away="notifOpen = false"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 sm:w-88 bg-[#10141d] border border-[#1e2433] rounded-3xl shadow-2xl z-50 overflow-hidden divide-y divide-[#1e2433]">
                    
                    <div class="p-3.5 flex items-center justify-between bg-[#0a0d13]">
                        <h4 class="text-xs font-black uppercase tracking-wider text-white">Notifikasi Saya</h4>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                                {{ $unreadNotificationsCount }} Info
                            </span>
                        @endif
                    </div>

                    <div class="max-h-72 overflow-y-auto divide-y divide-[#1e2433]/60">
                        @forelse($navbarNotifications ?? [] as $notif)
                            <a href="{{ $notif['link'] }}" class="p-3 flex items-start space-x-3 hover:bg-[#151a24] transition-colors block group">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 {{ $notif['type'] === 'success' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : ($notif['type'] === 'danger' ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'bg-[#e50914]/15 text-[#e50914] border border-[#e50914]/30') }}">
                                    @if($notif['type'] === 'success')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($notif['type'] === 'danger')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-white group-hover:text-[#e50914] transition-colors truncate">{{ $notif['title'] }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ $notif['message'] }}</p>
                                    <span class="text-[9px] text-slate-500 font-mono mt-1 block">{{ $notif['time'] }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="p-5 text-center text-slate-500 text-xs">
                                Belum ada pemberitahuan baru.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 sm:p-6 space-y-5 gaston-diagonal-lines">
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center space-x-3 shadow-lg text-xs">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 space-y-1 shadow-lg text-xs">
                <div class="flex items-center space-x-2 font-bold text-rose-400">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar Matching Mockup Phones 4, 5, 6 (Beranda, Riwayat, Izin, Profil) -->
    <div class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-[#0d1017]/95 backdrop-blur-lg border-t border-[#1f2533] px-6 py-2.5 flex items-center justify-around shadow-2xl">
        <!-- Beranda -->
        <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('member.dashboard') ? 'text-[#e50914]' : 'text-slate-500 hover:text-slate-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Beranda</span>
        </a>

        <!-- Riwayat -->
        <a href="{{ route('member.attendance.index') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('member.attendance.*') ? 'text-[#e50914]' : 'text-slate-500 hover:text-slate-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Riwayat</span>
        </a>

        <!-- Izin -->
        <a href="{{ route('member.permissions.index') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('member.permissions.*') ? 'text-[#e50914]' : 'text-slate-500 hover:text-slate-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Izin</span>
        </a>

        <!-- Profil -->
        <a href="{{ route('member.profile') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('member.profile') ? 'text-[#e50914]' : 'text-slate-500 hover:text-slate-300' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"></path></svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Profil</span>
        </a>
    </div>

    @stack('scripts')
</body>
</html>
