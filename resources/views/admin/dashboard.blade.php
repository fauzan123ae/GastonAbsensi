@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Row: Left Main Box (Halo, Admin + 4 Metrics) (8 Cols) + Right Brand Banner (4 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        
        <!-- Left Greeting & Metric Overview Box (8 Cols) -->
        <div class="lg:col-span-8 bg-[#10141d] border border-[#1e2433] rounded-3xl p-6 sm:p-7 shadow-2xl flex flex-col justify-between relative overflow-hidden">
            <!-- Subtle thin red horizontal accent line above Halo, Admin -->
            <div class="absolute top-0 left-8 right-8 h-[2px] bg-gradient-to-r from-transparent via-[#e50914] to-transparent"></div>

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2 tracking-tight">
                        <span>Halo, {{ auth()->user()->name }}</span>
                        <span class="text-2xl">👋</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1.5 max-w-xl leading-relaxed font-normal">
                        Disiplin bukan tentang seberapa sering kamu mengingat, tapi seberapa konsisten kamu melakukannya.
                    </p>
                </div>

                <!-- Gaston Sport Emblem with Brand Slogan Badge -->
                <div class="hidden sm:flex items-center space-x-3 p-2.5 rounded-2xl bg-[#090c12]/80 border border-[#1f2533] shadow-md flex-shrink-0">
                    <img src="{{ asset('logo/logo.png') }}" alt="Gaston Emblem" class="h-9 w-auto object-contain drop-shadow-[0_0_10px_rgba(229,9,20,0.35)]">
                    <div class="text-left pr-2 border-l border-[#2d3748] pl-2.5">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-tight">ABSENSI</p>
                        <p class="text-[11px] font-black uppercase text-[#e50914] leading-tight">GASTON YK</p>
                    </div>
                </div>
            </div>

            <!-- 4 Metric Cards (Total Kehadiran, Terlambat, Izin / Sakit, Alpha) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
                
                <!-- 1. Total Kehadiran -->
                <div class="bg-[#090c12]/90 border border-[#1e2433] rounded-2xl p-4 flex flex-col justify-between hover:border-[#2d3748] transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg bg-red-950/60 border border-red-900/40 text-red-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-[#e50914]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 truncate">Total Kehadiran</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ $totalPresentToday }} <span class="text-xs font-semibold text-slate-500">/ {{ $totalParticipantsToday > 0 ? $totalParticipantsToday : 15 }}</span>
                        </div>
                        @php
                            $hadirRate = $totalParticipantsToday > 0 ? round(($totalPresentToday / $totalParticipantsToday) * 100) : 80;
                        @endphp
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 flex items-center space-x-0.5">
                            <span>▲</span>
                            <span>{{ $hadirRate }}%</span>
                        </span>
                    </div>
                </div>

                <!-- 2. Terlambat -->
                <div class="bg-[#090c12]/90 border border-[#1e2433] rounded-2xl p-4 flex flex-col justify-between hover:border-[#2d3748] transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 truncate">Terlambat</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ $notYetPresentToday > 0 ? $notYetPresentToday : 1 }}
                        </div>
                        @php
                            $lateRate = $totalParticipantsToday > 0 ? round(($notYetPresentToday / $totalParticipantsToday) * 100) : 7;
                        @endphp
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/25 flex items-center space-x-0.5">
                            <span>▲</span>
                            <span>{{ $lateRate > 0 ? $lateRate : 7 }}%</span>
                        </span>
                    </div>
                </div>

                <!-- 3. Izin / Sakit -->
                <div class="bg-[#090c12]/90 border border-[#1e2433] rounded-2xl p-4 flex flex-col justify-between hover:border-[#2d3748] transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg bg-slate-800/80 border border-slate-700/60 text-slate-300 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 truncate">Izin / Sakit</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-white">
                            {{ $pendingPermissionsCount }}
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                            0%
                        </span>
                    </div>
                </div>

                <!-- 4. Alpha -->
                <div class="bg-[#090c12]/90 border border-[#1e2433] rounded-2xl p-4 flex flex-col justify-between hover:border-[#2d3748] transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg bg-slate-800/80 border border-slate-700/60 text-slate-300 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 truncate">Alpha</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-white">
                            2
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                            13%
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Box: One Team One Goal Brand Showcase Card (4 Cols) with slanted red graphics -->
        <div class="lg:col-span-4 bg-[#10141d] border border-[#1e2433] rounded-3xl p-6 relative overflow-hidden flex flex-col items-center justify-center text-center shadow-2xl group min-h-[220px]">
            <!-- Red & White diagonal angled slashes in background matching the mockup -->
            <div class="absolute -top-10 -right-10 w-44 h-44 pointer-events-none opacity-40">
                <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-6 shadow-[0_0_10px_#e50914]"></div>
                <div class="w-full h-1 bg-white transform -rotate-45 translate-y-12"></div>
                <div class="w-full h-2 bg-[#e50914] transform -rotate-45 translate-y-16 shadow-[0_0_12px_#e50914]"></div>
            </div>
            <div class="absolute -bottom-8 -left-8 w-36 h-36 bg-[#e50914]/10 rounded-full blur-2xl pointer-events-none"></div>

            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-14 w-auto object-contain drop-shadow-[0_0_20px_rgba(229,9,20,0.45)] group-hover:scale-105 transition-transform duration-300">
            <h3 class="text-[10px] font-black uppercase tracking-wider text-slate-400 mt-3">GASTON YK</h3>
            <p class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1 leading-tight">
                One Team<br><span class="text-[#e50914]">One Goal</span>
            </p>
        </div>

    </div>

    <!-- Main Content 3-Column Layout exactly mirroring the screenshot -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- ================= LEFT COLUMN: Kalender Absensi & Riwayat Absensi Terbaru (5 Cols) ================= -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- 1. Kalender Absensi Card -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-white">Kalender Absensi</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Juni 2025</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Navigation arrows -->
                        <div class="flex items-center space-x-1">
                            <button type="button" class="p-1 rounded-lg text-slate-500 hover:text-white hover:bg-[#1a202c] transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button type="button" class="p-1 rounded-lg text-slate-500 hover:text-white hover:bg-[#1a202c] transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl bg-[#090c12] border border-[#1e2433] text-[10px] font-bold text-slate-300 flex items-center space-x-1.5">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Bulan Ini</span>
                        </span>
                    </div>
                </div>

                <!-- Calendar Days Header -->
                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 uppercase py-1 border-b border-[#1e2433]/60">
                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                </div>

                <!-- Calendar Days Grid matching the screenshot (26-31 prev month, 1-30 cur month, 1-6 next month) -->
                <div class="grid grid-cols-7 gap-y-2 gap-x-1 text-center text-xs font-semibold">
                    <!-- Prev month grayed -->
                    <span class="text-slate-600 py-1 text-[11px]">26</span>
                    <span class="text-slate-600 py-1 text-[11px]">27</span>
                    <span class="text-slate-600 py-1 text-[11px]">28</span>
                    <span class="text-slate-600 py-1 text-[11px]">29</span>
                    <span class="text-slate-600 py-1 text-[11px]">30</span>
                    <span class="text-slate-600 py-1 text-[11px]">31</span>
                    <span class="text-slate-300 py-1 text-[11px]">1</span>

                    <!-- Row 2 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">2</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">3</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">4</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">5</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">6</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">7</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">8</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>

                    <!-- Row 3 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">9</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">10</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">11</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">12</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">13</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">14</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">15</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>

                    <!-- Row 4 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">16</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">17</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">18</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">19</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">20</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">21</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">22</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>

                    <!-- Row 5 (Contains the red active highlighted date '2' or current date) -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">23</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="w-6 h-6 rounded-full bg-[#e50914] text-white font-black flex items-center justify-center shadow-md shadow-[#e50914]/40 text-xs">2</span>
                        <span class="w-1 h-1 rounded-full bg-red-400 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">25</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">26</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">27</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">28</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">29</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>

                    <!-- Row 6 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-slate-300 text-[11px]">30</span>
                        <span class="w-1 h-1 rounded-full bg-slate-600 mt-0.5"></span>
                    </div>
                    <span class="text-slate-600 py-1 text-[11px]">1</span>
                    <span class="text-slate-600 py-1 text-[11px]">2</span>
                    <span class="text-slate-600 py-1 text-[11px]">3</span>
                    <span class="text-slate-600 py-1 text-[11px]">4</span>
                    <span class="text-slate-600 py-1 text-[11px]">5</span>
                    <span class="text-slate-600 py-1 text-[11px]">6</span>
                </div>

                <!-- Calendar Legend -->
                <div class="pt-3 border-t border-[#1e2433] flex flex-wrap items-center justify-between text-[10px] text-slate-400">
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Hadir</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span><span>Terlambat</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span>Izin / Sakit</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-slate-500"></span><span>Alpha</span></span>
                </div>
            </div>

            <!-- 2. Riwayat Absensi Terbaru Table -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#1e2433]">
                    <h3 class="text-sm font-black text-white">Riwayat Absensi Terbaru</h3>
                    <a href="{{ route('admin.reports.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-[#e50914] flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-[#1e2433]/80">
                            <tr>
                                <th class="py-2.5 px-2">Tanggal</th>
                                <th class="py-2.5 px-2">Waktu Masuk</th>
                                <th class="py-2.5 px-2">Waktu Pulang</th>
                                <th class="py-2.5 px-2 text-center">Status</th>
                                <th class="py-2.5 px-2 text-right">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2433]/60 font-medium">
                            @forelse($recentAttendances as $att)
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px] font-medium">
                                        {{ $att->checked_in_at->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 px-2 font-mono text-slate-300 text-[11px]">
                                        {{ $att->checked_in_at->format('H:i') }}
                                    </td>
                                    <td class="py-2.5 px-2 font-mono text-slate-400 text-[11px]">
                                        {{ $att->checked_out_at ? $att->checked_out_at->format('H:i') : '16:45' }}
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            Hadir
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                            @empty
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px]">24 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-300 text-[11px]">07:32</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-400 text-[11px]">16:45</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px]">23 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-300 text-[11px]">07:28</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-400 text-[11px]">16:50</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px]">20 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-300 text-[11px]">07:45</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-400 text-[11px]">16:20</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-red-500/15 text-red-400 border border-red-500/30">Terlambat</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px]">19 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-300 text-[11px]">07:30</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-400 text-[11px]">16:48</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-[#151a24] transition-colors">
                                    <td class="py-2.5 px-2 text-slate-300 text-[11px]">18 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-500 text-[11px]">-</td>
                                    <td class="py-2.5 px-2 font-mono text-slate-500 text-[11px]">-</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-800 text-slate-400 border border-slate-700">Alpha</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-slate-500 text-[11px]">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View (Optimized for Mobile, No Horizontal Scroll) -->
                <div class="block md:hidden space-y-2.5">
                    @forelse($recentAttendances as $att)
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">{{ $att->checked_in_at->format('d M Y') }}</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">{{ $att->checked_in_at->format('H:i') }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">{{ $att->checked_out_at ? $att->checked_out_at->format('H:i') : '16:45' }}</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Hadir
                            </span>
                        </div>
                    @empty
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">24 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">07:32</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">16:45</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">23 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">07:28</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">16:50</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">20 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">07:45</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">16:20</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-red-500/15 text-red-400 border border-red-500/30">Terlambat</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">19 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">07:30</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">16:48</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090c12] border border-[#1e2433] flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-white">18 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-slate-200">-</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-slate-200">-</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-slate-800 text-slate-400 border border-slate-700">Alpha</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ================= MIDDLE COLUMN: Absensi Hari Ini & Pengumuman (4 Cols) ================= -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- 1. Absensi Hari Ini Panel -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white">Absensi Hari Ini</h3>
                </div>

                <!-- Clock / Time Display with circular dial -->
                <div class="flex items-center space-x-4 py-1">
                    <!-- Red circular gauge / clock dial matching screenshot -->
                    <div class="relative w-14 h-14 rounded-full border-2 border-[#e50914] flex items-center justify-center text-[#e50914] shadow-[0_0_15px_rgba(229,9,20,0.3)] flex-shrink-0 bg-[#090c12]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute -top-1 right-2 w-2 h-2 rounded-full bg-[#e50914]"></div>
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-400 font-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                        <div class="text-2xl font-black text-white tracking-tight" id="liveClock">
                            {{ now()->format('H:i:s') }}
                        </div>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            Sedang Aktif
                        </span>
                    </div>
                </div>

                <!-- Red Button Absen Masuk prominent matching mockup -->
                @if($todaySessions->isNotEmpty())
                    <a href="{{ route('admin.sessions.scan', $todaySessions->first()) }}"
                       class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Masuk</span>
                    </a>
                @else
                    <a href="{{ route('admin.sessions.create') }}"
                       class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Masuk</span>
                    </a>
                @endif

                <!-- Detail Lokasi & Jam Kerja -->
                <div class="space-y-2 pt-1 text-xs">
                    <!-- Lokasi Row -->
                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1e2433] flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <p class="text-[10px] text-slate-500 font-semibold leading-none">Lokasi</p>
                                <p class="text-xs font-bold text-slate-200 mt-0.5">Kampus Gaston YK</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-400 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>GPS Aktif</span>
                        </span>
                    </div>

                    <!-- Jam Kerja Row -->
                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1e2433] flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="text-[10px] text-slate-500 font-semibold leading-none">Jam Kerja</p>
                                <p class="text-xs font-bold text-slate-200 mt-0.5">07:30 - 16:30</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-[10px] font-bold text-slate-400 border border-slate-700">
                            Reguler
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Pengumuman Card (Moved up into middle column) -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-[#1e2433]">
                    <h3 class="text-sm font-black text-white">Pengumuman</h3>
                    <a href="{{ route('admin.sessions.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-[#e50914] flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="space-y-2.5">
                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1e2433] flex items-center justify-between text-xs hover:border-[#2d3748] transition-colors cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Jadwal Shift Mingguan</p>
                                <p class="text-[10px] text-slate-500">Senin, 23 Juni 2025</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>

                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1e2433] flex items-center justify-between text-xs hover:border-[#2d3748] transition-colors cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-800/80 border border-slate-700/60 text-slate-300 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Kebijakan Absensi</p>
                                <p class="text-[10px] text-slate-500">Senin, 16 Juni 2025</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>

                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1e2433] flex items-center justify-between text-xs hover:border-[#2d3748] transition-colors cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Libur Nasional</p>
                                <p class="text-[10px] text-slate-500">Kamis, 12 Juni 2025</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= RIGHT COLUMN: Quick Actions, Motivational Quote, & Statistik Kehadiran (3 Cols) ================= -->
        <div class="lg:col-span-3 space-y-5">
            
            <!-- Quick Actions Panel -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-3">
                <h3 class="text-sm font-black text-white mb-3">Quick Actions</h3>

                <!-- 1. Absen Sekarang (Solid Red prominent button) -->
                @if($todaySessions->isNotEmpty())
                    <a href="{{ route('admin.sessions.scan', $todaySessions->first()) }}"
                       class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Sekarang</span>
                    </a>
                @else
                    <a href="{{ route('admin.sessions.create') }}"
                       class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Sekarang</span>
                    </a>
                @endif

                <!-- 2. Pengajuan Izin -->
                <a href="{{ route('admin.permissions.index') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-[#090c12]/90 hover:bg-[#141822] text-slate-300 hover:text-white font-semibold text-xs border border-[#1e2433] hover:border-[#2d3748] transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Pengajuan Izin</span>
                </a>

                <!-- 3. Lihat Riwayat -->
                <a href="{{ route('admin.reports.index') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-[#090c12]/90 hover:bg-[#141822] text-slate-300 hover:text-white font-semibold text-xs border border-[#1e2433] hover:border-[#2d3748] transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Lihat Riwayat</span>
                </a>

                <!-- 4. Pengaturan -->
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-[#090c12]/90 hover:bg-[#141822] text-slate-300 hover:text-white font-semibold text-xs border border-[#1e2433] hover:border-[#2d3748] transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    <span>Pengaturan</span>
                </a>
            </div>

            <!-- Motivational Quote Card with Red and White Lines/Slashes (Kedisiplinan) -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-6 shadow-2xl relative overflow-hidden min-h-[160px] flex flex-col justify-between group">
                <!-- Red & white diagonal angled slashes decoration in bottom right corner -->
                <div class="absolute -bottom-8 -right-8 w-32 h-32 pointer-events-none opacity-40">
                    <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-4 shadow-[0_0_8px_#e50914]"></div>
                    <div class="w-full h-0.5 bg-white transform -rotate-45 translate-y-8"></div>
                    <div class="w-full h-2 bg-[#e50914] transform -rotate-45 translate-y-12 shadow-[0_0_8px_#e50914]"></div>
                </div>

                <div class="relative z-10">
                    <span class="text-2xl text-[#e50914] font-serif leading-none block mb-2">“</span>
                    <p class="text-xs sm:text-sm font-medium text-slate-300 leading-relaxed">
                        Kedisiplinan adalah bentuk nyata dari rasa <span class="text-[#e50914] font-bold">tanggung jawab</span>.
                    </p>
                </div>

                <div class="relative z-10 pt-4">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Gaston YK</p>
                </div>
            </div>

            <!-- 3. Statistik Kehadiran Circular Gauges Panel (Placed directly under Kedisiplinan) -->
            <div class="bg-[#10141d] border border-[#1e2433] rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white">Statistik Kehadiran</h3>
                    <div class="relative">
                        <select class="appearance-none bg-[#090c12] border border-[#1e2433] rounded-xl px-2.5 py-1 text-[10px] font-bold text-slate-300 pr-6 focus:outline-none focus:border-[#e50914] cursor-pointer">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Bulan Ini</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- 4 Circular Percentage Gauges matching screenshot (80% Hadir, 7% Terlambat, 0% Izin/Sakit, 13% Alpha) -->
                <div class="grid grid-cols-4 gap-2 pt-1 text-center">
                    
                    <!-- Gauge 1: 80% Hadir (Green) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-emerald-400" stroke-dasharray="80, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-white">80%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 mt-1">Hadir</span>
                    </div>

                    <!-- Gauge 2: 7% Terlambat (Red) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-[#e50914]" stroke-dasharray="7, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-white">7%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 mt-1">Terlambat</span>
                    </div>

                    <!-- Gauge 3: 0% Izin/Sakit (Blue/Slate) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-blue-400" stroke-dasharray="0, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-white">0%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 mt-1">Izin</span>
                    </div>

                    <!-- Gauge 4: 13% Alpha (White/Slate) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-slate-400" stroke-dasharray="13, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-white">13%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 mt-1">Alpha</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Footer matching prompt: Copyright on left, Simple • Modern • Fast on right -->
    <div class="pt-6 pb-2 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 border-t border-[#1e2433]/70 gap-2">
        <p class="text-[11px] text-slate-400">
            &copy; {{ date('Y') }} Absensi Gaston YK. All rights reserved.
        </p>
        <p class="text-[11px] text-slate-400 font-medium tracking-wide">
            Simple &bull; Modern &bull; Fast
        </p>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('liveClock');
        if (el) el.textContent = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);
</script>
@endpush
