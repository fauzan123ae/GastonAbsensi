<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Gaston YK - One Team One Goal</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#080a0f] gaston-stripes-bg gaston-diagonal-lines text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-[#e50914] selection:text-white relative overflow-x-hidden">
    @include('components.mobile-splash')

    <!-- Ambient Glow & Red Angled Slashes in Background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#e50914]/15 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-red-900/15 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/4 right-0 w-80 h-80 pointer-events-none opacity-25">
        <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-6 shadow-[0_0_12px_#e50914]"></div>
        <div class="w-full h-1 bg-white transform -rotate-45 translate-y-16"></div>
        <div class="w-full h-2 bg-[#e50914] transform -rotate-45 translate-y-28 shadow-[0_0_12px_#e50914]"></div>
    </div>

    <!-- Header Navbar -->
    <header class="border-b border-[#1f2533]/80 bg-[#0d1017]/80 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-10 w-auto object-contain drop-shadow-[0_0_15px_rgba(229,9,20,0.4)]">
                <div>
                    <h1 class="font-black text-xs tracking-wider text-white uppercase leading-tight">ABSENSI<br><span class="text-[#e50914]">GASTON YK</span></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="px-5 py-2.5 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-extrabold text-xs shadow-xl shadow-[#e50914]/30 transition-all">
                            Admin Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('member.dashboard') }}"
                           class="px-5 py-2.5 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-extrabold text-xs shadow-xl shadow-[#e50914]/30 transition-all">
                            Member Portal &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2.5 rounded-2xl bg-[#11141c] hover:bg-[#181d29] border border-[#1f2533] text-white font-bold text-xs transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-5 py-2.5 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-extrabold text-xs shadow-xl shadow-[#e50914]/30 transition-all">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Showcase Section matching Gaston UI Showcase -->
    <main class="flex-1 max-w-5xl mx-auto px-6 py-12 sm:py-16 flex flex-col items-center justify-center text-center relative z-10">
        
        <!-- Big Gaston Emblem -->
        <div class="relative inline-block mb-6 group">
            <div class="absolute inset-0 rounded-full bg-[#e50914]/20 blur-2xl group-hover:bg-[#e50914]/30 transition-all"></div>
            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-28 sm:h-36 w-auto object-contain relative z-10 drop-shadow-[0_0_35px_rgba(229,9,20,0.45)]">
        </div>

        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-[#11141c] border border-[#1f2533] text-xs font-bold text-slate-300 mb-4">
            <span class="w-2 h-2 rounded-full bg-[#e50914] animate-pulse"></span>
            <span>GASTON YK</span>
            <span class="text-slate-600">&bull;</span>
            <span class="text-[#e50914] font-black uppercase">One Team One Goal</span>
        </div>

        <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
            Sistem Absensi Digital<br>
            <span class="text-[#e50914]">Komunitas Olahraga Gaston</span>
        </h2>

        <p class="text-sm sm:text-base text-slate-400 mt-4 max-w-2xl leading-relaxed">
            Presensi instan dengan QR code token, pencatatan otomatis di lapangan, pengelolaan jadwal sesi latihan, dan rekapitulasi kehadiran komprehensif.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-extrabold text-xs shadow-xl shadow-[#e50914]/30 active:scale-[0.99] transition-all uppercase tracking-wider">
                Mulai Presensi Sekarang
            </a>
            <a href="{{ route('register') }}"
               class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-[#11141c] hover:bg-[#181d29] border border-[#1f2533] text-slate-200 hover:text-white font-bold text-xs transition-colors">
                Daftar Member Baru
            </a>
        </div>

        <!-- 3 Feature Highlights (Charcoal Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-12 w-full text-left">
            <div class="p-5 rounded-3xl bg-[#11141c] border border-[#1f2533] shadow-xl">
                <div class="w-10 h-10 rounded-2xl bg-[#0d1017] text-[#e50914] border border-[#1f2533] flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white">QR Token Pribadi</h3>
                <p class="text-xs text-slate-400 mt-1">Setiap member memiliki QR token unik permanen untuk check-in cepat di GOR.</p>
            </div>

            <div class="p-5 rounded-3xl bg-[#11141c] border border-[#1f2533] shadow-xl">
                <div class="w-10 h-10 rounded-2xl bg-[#0d1017] text-[#e50914] border border-[#1f2533] flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white">Manajemen Sesi & Izin</h3>
                <p class="text-xs text-slate-400 mt-1">Jadwal latihan terkoordinasi dan alur pengajuan izin dengan bukti berkas.</p>
            </div>

            <div class="p-5 rounded-3xl bg-[#11141c] border border-[#1f2533] shadow-xl">
                <div class="w-10 h-10 rounded-2xl bg-[#0d1017] text-[#e50914] border border-[#1f2533] flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-sm font-black text-white">Laporan Real-Time</h3>
                <p class="text-xs text-slate-400 mt-1">Audit log aktivitas dan rekap absensi otomatis terverifikasi sistem.</p>
            </div>
        </div>

    </main>

    <!-- Footer matching showcase image bottom bar -->
    <footer class="border-t border-[#1f2533]/80 bg-[#090c12] py-6 px-6">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <div class="flex items-center space-x-2">
                <span class="w-6 h-[2px] bg-[#e50914]"></span>
                <span class="text-white font-medium">Mobile & Desktop UI/UX</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-slate-400">Simple &bull; Modern &bull; Functional</span>
            </div>
        </div>
    </footer>

</body>
</html>
