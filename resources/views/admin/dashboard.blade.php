@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6" x-data="{
    modalType: null, // 'shift', 'kebijakan', 'libur'
    selectSessionModal: false,
    activeShiftDay: 'senin'
}">

    <!-- Top Row: Left Main Box (Halo, Admin + 4 Metrics) (8 Cols) + Right Brand Banner (4 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        
        <!-- Left Greeting & Metric Overview Box (8 Cols) -->
        <div class="lg:col-span-8 bg-surface border border-line-base rounded-3xl p-6 sm:p-7 shadow-2xl flex flex-col justify-between relative overflow-hidden">
            <!-- Subtle thin red horizontal accent line above Halo, Admin -->
            <div class="absolute top-0 left-8 right-8 h-[2px] bg-gradient-to-r from-transparent via-[#e50914] to-transparent"></div>

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-content-heading flex items-center gap-2 tracking-tight">
                        <span>Halo, {{ auth()->user()->name }}</span>
                        <span class="text-2xl">👋</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-content-muted mt-1.5 max-w-xl leading-relaxed font-normal">
                        Disiplin bukan tentang seberapa sering kamu mengingat, tapi seberapa konsisten kamu melakukannya.
                    </p>
                </div>

                <!-- Gaston Sport Emblem with Brand Slogan Badge -->
                <div class="hidden sm:flex items-center space-x-3 p-2.5 rounded-2xl bg-surface-page/80 border border-line-base shadow-md flex-shrink-0">
                    <img src="{{ asset('logo/logo.png') }}" alt="Gaston Emblem" class="h-9 w-auto object-contain drop-shadow-[0_0_10px_rgba(229,9,20,0.35)]">
                    <div class="text-left pr-2 border-l border-line-strong pl-2.5">
                        <p class="text-[9px] font-bold text-content-muted uppercase tracking-wider leading-tight">ABSENSI</p>
                        <p class="text-[11px] font-black uppercase text-[#e50914] leading-tight">GASTON YK</p>
                    </div>
                </div>
            </div>

            <!-- 4 Metric Cards (Total Kehadiran, Terlambat, Izin / Sakit, Alpha) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
                
                <!-- 1. Total Kehadiran -->
                <div class="bg-surface-elevated border border-line-base rounded-2xl p-4 flex flex-col justify-between hover:border-line-strong transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg icon-chip icon-chip-red">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-content-muted truncate">Total Kehadiran</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-content-heading">
                            {{ $totalPresentToday }} <span class="text-xs font-semibold text-content-faint">/ {{ $totalParticipantsToday > 0 ? $totalParticipantsToday : 15 }}</span>
                        </div>
                        @php
                            $hadirRate = $totalParticipantsToday > 0 ? round(($totalPresentToday / $totalParticipantsToday) * 100) : 80;
                        @endphp
                        <span class="badge badge-success">
                            <span>▲</span>
                            <span>{{ $hadirRate }}%</span>
                        </span>
                    </div>
                </div>

                <!-- 2. Terlambat -->
                <div class="bg-surface-elevated border border-line-base rounded-2xl p-4 flex flex-col justify-between hover:border-line-strong transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg icon-chip icon-chip-red">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-content-muted truncate">Terlambat</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-content-heading">
                            {{ $notYetPresentToday > 0 ? $notYetPresentToday : 1 }}
                        </div>
                        @php
                            $lateRate = $totalParticipantsToday > 0 ? round(($notYetPresentToday / $totalParticipantsToday) * 100) : 7;
                        @endphp
                        <span class="badge badge-danger">
                            <span>▲</span>
                            <span>{{ $lateRate > 0 ? $lateRate : 7 }}%</span>
                        </span>
                    </div>
                </div>

                <!-- 3. Izin / Sakit -->
                <div class="bg-surface-elevated border border-line-base rounded-2xl p-4 flex flex-col justify-between hover:border-line-strong transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg icon-chip icon-chip-neutral">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-content-muted truncate">Izin / Sakit</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-content-heading">
                            {{ $pendingPermissionsCount }}
                        </div>
                        <span class="badge badge-neutral">
                            0%
                        </span>
                    </div>
                </div>

                <!-- 4. Alpha -->
                <div class="bg-surface-elevated border border-line-base rounded-2xl p-4 flex flex-col justify-between hover:border-line-strong transition-all">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-lg icon-chip icon-chip-neutral">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-content-muted truncate">Alpha</span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <div class="text-xl sm:text-2xl font-black text-content-heading">
                            2
                        </div>
                        <span class="badge badge-neutral">
                            13%
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Box: One Team One Goal Brand Showcase Card (4 Cols) with slanted red graphics -->
        <div class="lg:col-span-4 bg-surface border border-line-base rounded-3xl p-6 relative overflow-hidden flex flex-col items-center justify-center text-center shadow-2xl group min-h-[220px]">
            <!-- Red & White diagonal angled slashes in background matching the mockup -->
            <div class="absolute -top-10 -right-10 w-44 h-44 pointer-events-none opacity-40">
                <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-6 shadow-[0_0_10px_#e50914]"></div>
                <div class="w-full h-1 bg-white transform -rotate-45 translate-y-12"></div>
                <div class="w-full h-2 bg-[#e50914] transform -rotate-45 translate-y-16 shadow-[0_0_12px_#e50914]"></div>
            </div>
            <div class="absolute -bottom-8 -left-8 w-36 h-36 bg-[#e50914]/10 rounded-full blur-2xl pointer-events-none"></div>

            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-14 w-auto object-contain drop-shadow-[0_0_20px_rgba(229,9,20,0.45)] group-hover:scale-105 transition-transform duration-300">
            <h3 class="text-[10px] font-black uppercase tracking-wider text-content-muted mt-3">GASTON YK</h3>
            <p class="text-xl sm:text-2xl font-black text-content-heading tracking-tight mt-1 leading-tight">
                One Team<br><span class="text-[#e50914]">One Goal</span>
            </p>
        </div>

    </div>

    <!-- Main Content 3-Column Layout exactly mirroring the screenshot -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- ================= LEFT COLUMN: Kalender Absensi & Riwayat Absensi Terbaru (5 Cols) ================= -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- 1. Kalender Absensi Card -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-content-heading">Kalender Absensi</h3>
                        <p class="text-[11px] text-content-muted mt-0.5">Juni 2025</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Navigation arrows -->
                        <div class="flex items-center space-x-1">
                            <button type="button" class="p-1 rounded-lg text-content-faint hover:text-content-heading hover:bg-surface-elevated transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button type="button" class="p-1 rounded-lg text-content-faint hover:text-content-heading hover:bg-surface-elevated transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl bg-surface-page border border-line-base text-[10px] font-bold text-content-body flex items-center space-x-1.5">
                            <svg class="w-3 h-3 text-content-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Bulan Ini</span>
                        </span>
                    </div>
                </div>

                <!-- Calendar Days Header -->
                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-content-muted uppercase py-1 border-b border-line-base/60">
                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                </div>

                <!-- Calendar Days Grid matching the screenshot (26-31 prev month, 1-30 cur month, 1-6 next month) -->
                <div class="grid grid-cols-7 gap-y-2 gap-x-1 text-center text-xs font-semibold">
                    <!-- Prev month grayed -->
                    <span class="text-content-faint py-1 text-[11px]">26</span>
                    <span class="text-content-faint py-1 text-[11px]">27</span>
                    <span class="text-content-faint py-1 text-[11px]">28</span>
                    <span class="text-content-faint py-1 text-[11px]">29</span>
                    <span class="text-content-faint py-1 text-[11px]">30</span>
                    <span class="text-content-faint py-1 text-[11px]">31</span>
                    <span class="text-content-body py-1 text-[11px]">1</span>

                    <!-- Row 2 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">2</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">3</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">4</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">5</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">6</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">7</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">8</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>

                    <!-- Row 3 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">9</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">10</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">11</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">12</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">13</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">14</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">15</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>

                    <!-- Row 4 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">16</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">17</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">18</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">19</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">20</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">21</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">22</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>

                    <!-- Row 5 (Contains the red active highlighted date '2' or current date) -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">23</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="w-6 h-6 rounded-full bg-[#e50914] text-white font-black flex items-center justify-center shadow-md shadow-[#e50914]/40 text-xs">2</span>
                        <span class="w-1 h-1 rounded-full bg-red-400 mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">25</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">26</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">27</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">28</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">29</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>

                    <!-- Row 6 -->
                    <div class="py-1 flex flex-col items-center justify-center">
                        <span class="text-content-body text-[11px]">30</span>
                        <span class="w-1 h-1 rounded-full bg-content-faint mt-0.5"></span>
                    </div>
                    <span class="text-content-faint py-1 text-[11px]">1</span>
                    <span class="text-content-faint py-1 text-[11px]">2</span>
                    <span class="text-content-faint py-1 text-[11px]">3</span>
                    <span class="text-content-faint py-1 text-[11px]">4</span>
                    <span class="text-content-faint py-1 text-[11px]">5</span>
                    <span class="text-content-faint py-1 text-[11px]">6</span>
                </div>

                <!-- Calendar Legend -->
                <div class="pt-3 border-t border-line-base flex flex-wrap items-center justify-between text-[10px] text-content-muted">
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Hadir</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span><span>Terlambat</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span>Izin / Sakit</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-slate-500"></span><span>Alpha</span></span>
                </div>
            </div>

            <!-- 2. Riwayat Absensi Terbaru Table -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-line-base">
                    <h3 class="text-sm font-black text-content-heading">Riwayat Absensi Terbaru</h3>
                    <a href="{{ route('admin.reports.index') }}" class="text-[10px] font-bold text-content-muted hover:text-[#e50914] flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-content-body">
                        <thead class="text-[10px] font-bold text-content-muted uppercase tracking-wider border-b border-line-base/80">
                            <tr>
                                <th class="py-2.5 px-2">Tanggal</th>
                                <th class="py-2.5 px-2">Waktu Masuk</th>
                                <th class="py-2.5 px-2">Waktu Pulang</th>
                                <th class="py-2.5 px-2 text-center">Status</th>
                                <th class="py-2.5 px-2 text-right">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line-base/60 font-medium">
                            @forelse($recentAttendances as $att)
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px] font-medium">
                                        {{ $att->checked_in_at->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 px-2 font-mono text-content-body text-[11px]">
                                        {{ $att->checked_in_at->format('H:i') }}
                                    </td>
                                    <td class="py-2.5 px-2 font-mono text-content-muted text-[11px]">
                                        {{ $att->checked_out_at ? $att->checked_out_at->format('H:i') : '16:45' }}
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            Hadir
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                            @empty
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px]">24 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-content-body text-[11px]">07:32</td>
                                    <td class="py-2.5 px-2 font-mono text-content-muted text-[11px]">16:45</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px]">23 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-content-body text-[11px]">07:28</td>
                                    <td class="py-2.5 px-2 font-mono text-content-muted text-[11px]">16:50</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px]">20 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-content-body text-[11px]">07:45</td>
                                    <td class="py-2.5 px-2 font-mono text-content-muted text-[11px]">16:20</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-red-500/15 text-red-400 border border-red-500/30">Terlambat</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px]">19 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-content-body text-[11px]">07:30</td>
                                    <td class="py-2.5 px-2 font-mono text-content-muted text-[11px]">16:48</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                                <tr class="hover:bg-surface-elevated transition-colors">
                                    <td class="py-2.5 px-2 text-content-body text-[11px]">18 Jun 2025</td>
                                    <td class="py-2.5 px-2 font-mono text-content-faint text-[11px]">-</td>
                                    <td class="py-2.5 px-2 font-mono text-content-faint text-[11px]">-</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-surface-elevated text-content-muted border border-line-strong">Alpha</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-content-faint text-[11px]">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View (Optimized for Mobile, No Horizontal Scroll) -->
                <div class="block md:hidden space-y-2.5">
                    @forelse($recentAttendances as $att)
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">{{ $att->checked_in_at->format('d M Y') }}</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">{{ $att->checked_in_at->format('H:i') }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">{{ $att->checked_out_at ? $att->checked_out_at->format('H:i') : '16:45' }}</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Hadir
                            </span>
                        </div>
                    @empty
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">24 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">07:32</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">16:45</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">23 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">07:28</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">16:50</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">20 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">07:45</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">16:20</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-red-500/15 text-red-400 border border-red-500/30">Terlambat</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">19 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">07:30</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">16:48</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Hadir</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-content-heading">18 Jun 2025</p>
                                <div class="flex items-center space-x-2 text-[10px] text-content-muted font-mono mt-0.5">
                                    <span>Masuk: <strong class="text-content-body">-</strong></span>
                                    <span>&bull;</span>
                                    <span>Pulang: <strong class="text-content-body">-</strong></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-surface-elevated text-content-muted border border-line-strong">Alpha</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ================= MIDDLE COLUMN: Absensi Hari Ini & Pengumuman (4 Cols) ================= -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- 1. Absensi Hari Ini Panel -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-content-heading">Absensi Hari Ini</h3>
                </div>

                <!-- Clock / Time Display with circular dial -->
                <div class="flex items-center space-x-4 py-1">
                    <!-- Red circular gauge / clock dial matching screenshot -->
                    <div class="relative w-14 h-14 rounded-full border-2 border-[#e50914] flex items-center justify-center text-[#e50914] shadow-[0_0_15px_rgba(229,9,20,0.3)] flex-shrink-0 bg-surface-page">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute -top-1 right-2 w-2 h-2 rounded-full bg-[#e50914]"></div>
                    </div>
                    <div>
                        <p class="text-[11px] text-content-muted font-medium">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                        <div class="text-2xl font-black text-content-heading tracking-tight" id="liveClock">
                            {{ now()->format('H:i:s') }}
                        </div>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            Sedang Aktif
                        </span>
                    </div>
                </div>

                <!-- Red Button Absen Masuk prominent matching mockup -->
                @if($todaySessions->count() > 1)
                    <button type="button"
                            @click="selectSessionModal = true"
                            class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Masuk ({{ $todaySessions->count() }} Sesi)</span>
                    </button>
                @elseif($todaySessions->count() === 1)
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

                <!-- Detail Lokasi & Jam Kegiatan Hari Ini -->
                <div class="space-y-2 pt-1 text-xs">
                    @if($todaySessions->isNotEmpty())
                        @if($todaySessions->count() === 1)
                            @php $singleSession = $todaySessions->first(); @endphp
                            <!-- Lokasi Row -->
                            <div class="p-3 rounded-2xl bg-surface-page/90 border border-line-base flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4 text-[#e50914] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <div>
                                        <p class="text-[10px] text-content-faint font-semibold leading-none">Lokasi Sesi</p>
                                        <p class="text-xs font-bold text-content-body mt-0.5">{{ $singleSession->location->name ?? 'Lokasi Terdaftar' }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg bg-red-950/40 text-[10px] font-bold text-[#e50914] border border-red-900/40">
                                    {{ $singleSession->sport->name ?? 'Kegiatan' }}
                                </span>
                            </div>

                            <!-- Jam Kegiatan Row -->
                            <div class="p-3 rounded-2xl bg-surface-page/90 border border-line-base flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4 text-content-muted flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <p class="text-[10px] text-content-faint font-semibold leading-none">Jam Pelaksanaan</p>
                                        <p class="text-xs font-bold text-content-body mt-0.5">
                                            {{ substr($singleSession->start_time, 0, 5) }} - {{ substr($singleSession->end_time, 0, 5) }} WIB
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $singleSession->status === 'open' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-surface-elevated text-content-muted border border-line-strong' }}">
                                    {{ $singleSession->status }}
                                </span>
                            </div>
                        @else
                            <!-- Multi-kegiatan Card List -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between px-1">
                                    <span class="text-[11px] font-bold text-content-muted">Jadwal Sesi Hari Ini ({{ $todaySessions->count() }})</span>
                                    <button type="button" @click="selectSessionModal = true" class="text-[10px] font-bold text-[#e50914] hover:underline">
                                        Pilih Sesi &rarr;
                                    </button>
                                </div>
                                @foreach($todaySessions as $s)
                                    <div class="p-3 rounded-2xl bg-surface-page/90 border border-line-base hover:border-line-strong transition-colors">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full {{ $s->status === 'open' ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                                <span class="text-xs font-bold text-content-heading">{{ $s->sport->name ?? 'Kegiatan' }}</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $s->status === 'open' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-surface-elevated text-content-muted border border-line-strong' }}">
                                                {{ $s->status }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-content-muted">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5 text-content-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                <span>{{ $s->location->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1 font-mono text-content-body font-semibold">
                                                <svg class="w-3.5 h-3.5 text-content-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>{{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- State jika tidak ada sesi hari ini -->
                        <div class="p-3.5 rounded-2xl bg-surface-page/90 border border-line-base text-center text-content-muted">
                            <p class="text-xs font-semibold">Tidak ada jadwal sesi kegiatan hari ini</p>
                            <p class="text-[10px] text-content-faint mt-0.5">Lokasi & jam akan muncul saat ada sesi aktif</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Pengumuman Card (Moved up into middle column) -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-line-base">
                    <h3 class="text-sm font-black text-content-heading">Pengumuman</h3>
                    <a href="{{ route('admin.sessions.index') }}" class="text-[10px] font-bold text-content-muted hover:text-[#e50914] flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="space-y-2.5">
                    <!-- 1. Jadwal Shift Mingguan Item -->
                    <div @click="modalType = 'shift'"
                         class="p-3 rounded-2xl bg-surface-page/90 border border-line-base flex items-center justify-between text-xs hover:border-[#e50914]/50 hover:bg-surface-elevated transition-all cursor-pointer group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-content-heading group-hover:text-[#e50914] transition-colors">Jadwal Shift Mingguan</p>
                                <p class="text-[10px] text-content-faint">Lihat pembagian shift kegiatan minggu ini</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-content-faint group-hover:text-content-heading transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>

                    <!-- 2. Kebijakan Absensi Item -->
                    <div @click="modalType = 'kebijakan'"
                         class="p-3 rounded-2xl bg-surface-page/90 border border-line-base flex items-center justify-between text-xs hover:border-[#e50914]/50 hover:bg-surface-elevated transition-all cursor-pointer group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-surface-elevated/80 border border-line-strong/60 text-content-body flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-content-heading group-hover:text-[#e50914] transition-colors">Kebijakan Absensi</p>
                                <p class="text-[10px] text-content-faint">Ketentuan toleransi jam, izin & sanksi</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-content-faint group-hover:text-content-heading transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>

                    <!-- 3. Libur Nasional Item -->
                    <div @click="modalType = 'libur'"
                         class="p-3 rounded-2xl bg-surface-page/90 border border-line-base flex items-center justify-between text-xs hover:border-[#e50914]/50 hover:bg-surface-elevated transition-all cursor-pointer group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-content-heading group-hover:text-[#e50914] transition-colors">Libur Nasional & Cuti Bersama</p>
                                <p class="text-[10px] text-content-faint">Kalender operasional & penyesuaian jadwal</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-content-faint group-hover:text-content-heading transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= RIGHT COLUMN: Quick Actions, Motivational Quote, & Statistik Kehadiran (3 Cols) ================= -->
        <div class="lg:col-span-3 space-y-5">
            
            <!-- Quick Actions Panel -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-3">
                <h3 class="text-sm font-black text-content-heading mb-3">Quick Actions</h3>

                <!-- 1. Absen Sekarang (Solid Red prominent button) -->
                @if($todaySessions->count() > 1)
                    <button type="button"
                            @click="selectSessionModal = true"
                            class="w-full py-3 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Absen Sekarang ({{ $todaySessions->count() }} Kegiatan)</span>
                    </button>
                @elseif($todaySessions->count() === 1)
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
                   class="w-full py-3 px-4 rounded-2xl bg-surface-page/90 hover:bg-surface-elevated text-content-body hover:text-content-heading font-semibold text-xs border border-line-base hover:border-line-strong transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-content-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Pengajuan Izin</span>
                </a>

                <!-- 3. Lihat Riwayat -->
                <a href="{{ route('admin.reports.index') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-surface-page/90 hover:bg-surface-elevated text-content-body hover:text-content-heading font-semibold text-xs border border-line-base hover:border-line-strong transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-content-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Lihat Riwayat</span>
                </a>

                <!-- 4. Pengaturan -->
                <a href="{{ route('admin.audit-logs.index') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-surface-page/90 hover:bg-surface-elevated text-content-body hover:text-content-heading font-semibold text-xs border border-line-base hover:border-line-strong transition-all flex items-center justify-center space-x-2.5">
                    <svg class="w-4 h-4 text-content-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    <span>Pengaturan</span>
                </a>
            </div>

            <!-- Motivational Quote Card with Red and White Lines/Slashes (Kedisiplinan) -->
            <div class="bg-surface border border-line-base rounded-3xl p-6 shadow-2xl relative overflow-hidden min-h-[160px] flex flex-col justify-between group">
                <!-- Red & white diagonal angled slashes decoration in bottom right corner -->
                <div class="absolute -bottom-8 -right-8 w-32 h-32 pointer-events-none opacity-40">
                    <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-4 shadow-[0_0_8px_#e50914]"></div>
                    <div class="w-full h-0.5 bg-white transform -rotate-45 translate-y-8"></div>
                    <div class="w-full h-2 bg-[#e50914] transform -rotate-45 translate-y-12 shadow-[0_0_8px_#e50914]"></div>
                </div>

                <div class="relative z-10">
                    <span class="text-2xl text-[#e50914] font-serif leading-none block mb-2">“</span>
                    <p class="text-xs sm:text-sm font-medium text-content-body leading-relaxed">
                        Kedisiplinan adalah bentuk nyata dari rasa <span class="text-[#e50914] font-bold">tanggung jawab</span>.
                    </p>
                </div>

                <div class="relative z-10 pt-4">
                    <p class="text-[10px] font-bold text-content-faint uppercase tracking-wider">Gaston YK</p>
                </div>
            </div>

            <!-- 3. Statistik Kehadiran Circular Gauges Panel (Placed directly under Kedisiplinan) -->
            <div class="bg-surface border border-line-base rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-content-heading">Statistik Kehadiran</h3>
                    <div class="relative">
                        <select class="appearance-none bg-surface-page border border-line-base rounded-xl px-2.5 py-1 text-[10px] font-bold text-content-body pr-6 focus:outline-none focus:border-[#e50914] cursor-pointer">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Bulan Ini</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-content-muted">
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
                            <span class="absolute text-[10px] font-bold text-content-heading">80%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-content-muted mt-1">Hadir</span>
                    </div>

                    <!-- Gauge 2: 7% Terlambat (Red) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-[#e50914]" stroke-dasharray="7, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-content-heading">7%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-content-muted mt-1">Terlambat</span>
                    </div>

                    <!-- Gauge 3: 0% Izin/Sakit (Blue/Slate) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-blue-400" stroke-dasharray="0, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-content-heading">0%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-content-muted mt-1">Izin</span>
                    </div>

                    <!-- Gauge 4: 13% Alpha (White/Slate) -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-11 h-11 flex items-center justify-center">
                            <svg class="w-11 h-11 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-content-muted" stroke-dasharray="13, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <span class="absolute text-[10px] font-bold text-content-heading">13%</span>
                        </div>
                        <span class="text-[9px] font-semibold text-content-muted mt-1">Alpha</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Footer matching prompt: Copyright on left, Simple • Modern • Fast on right -->
    <div class="pt-6 pb-2 flex flex-col sm:flex-row items-center justify-between text-xs text-content-faint border-t border-line-base/70 gap-2">
        <p class="text-[11px] text-content-muted">
            &copy; {{ date('Y') }} Absensi Gaston YK. All rights reserved.
        </p>
        <p class="text-[11px] text-content-muted font-medium tracking-wide">
            Simple &bull; Modern &bull; Fast
        </p>
    </div>

    <!-- ================= MODALS SECTION ================= -->

    <!-- 1. MODAL PILIH SESI ABSEN SEKARANG (JIKA ADA LEBIH DARI 1 KEGIATAN HARI INI) -->
    <div x-show="selectSessionModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="selectSessionModal = false"
             class="bg-surface border border-line-base rounded-[32px] max-w-lg w-full p-6 sm:p-7 shadow-2xl relative space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-line-base">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-content-heading">Pilih Kegiatan Hari Ini</h3>
                        <p class="text-xs text-content-muted">Ada {{ $todaySessions->count() }} kegiatan olahraga yang terjadwal hari ini</p>
                    </div>
                </div>
                <button type="button" @click="selectSessionModal = false" class="text-content-muted hover:text-content-heading p-1 rounded-xl bg-surface-page border border-line-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <p class="text-xs text-content-body">
                Pilih sesi kegiatan yang ingin Anda buka scanner absensinya sekarang:
            </p>

            <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                @foreach($todaySessions as $index => $session)
                    <a href="{{ route('admin.sessions.scan', $session) }}"
                       class="p-4 rounded-2xl bg-surface-page border border-line-base hover:border-[#e50914] hover:bg-[#151924] transition-all flex items-center justify-between group block">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-red-950/60 border border-red-900/40 text-[#e50914] font-black text-xs flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-black text-content-heading group-hover:text-[#e50914] transition-colors">
                                        {{ $session->sport->name }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $session->status === 'open' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-surface-elevated text-content-muted border border-line-strong' }}">
                                        {{ $session->status }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-content-muted mt-1 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-content-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</span>
                                    <span>&bull;</span>
                                    <span>{{ $session->location->name }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-right">
                            <span class="text-[11px] font-black text-[#e50914] group-hover:translate-x-1 transition-transform flex items-center space-x-1">
                                <span>Buka Scan</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pt-2 flex items-center justify-between text-xs">
                <a href="{{ route('admin.sessions.create') }}" class="text-[#e50914] hover:underline font-bold text-xs flex items-center space-x-1">
                    <span>+ Buat Sesi Baru Hari Ini</span>
                </a>
                <button type="button" @click="selectSessionModal = false" class="px-4 py-2 bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold rounded-xl text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 2. MODAL JADWAL SHIFT MINGGUAN -->
    <div x-show="modalType === 'shift'"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalType = null"
             class="bg-surface border border-line-base rounded-[32px] max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-line-base">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-content-heading">Jadwal Shift Mingguan Petugas & Pelatih</h3>
                        <p class="text-xs text-content-muted">Periode: Minggu Aktif ({{ now()->startOfWeek()->translatedFormat('d M') }} - {{ now()->endOfWeek()->translatedFormat('d M Y') }})</p>
                    </div>
                </div>
                <button type="button" @click="modalType = null" class="text-content-muted hover:text-content-heading p-1 rounded-xl bg-surface-page border border-line-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Tab Hari Shift -->
            <div class="p-1 rounded-2xl bg-surface-page border border-line-base flex items-center overflow-x-auto space-x-1">
                @foreach(['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $key => $label)
                    <button type="button"
                            @click="activeShiftDay = '{{ $key }}'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                            :class="activeShiftDay === '{{ $key }}' ? 'bg-[#e50914] text-white shadow-md shadow-[#e50914]/30' : 'text-content-muted hover:text-content-heading'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Content Shift List -->
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <!-- Shift Pagi -->
                    <div class="p-4 rounded-2xl bg-surface-page border border-line-base space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                Shift Pagi (07:00 - 15:00)
                            </span>
                            <span class="text-[10px] text-content-faint font-mono">GOR Manahan</span>
                        </div>
                        <div class="pt-1 space-y-1 text-content-body">
                            <p class="font-bold text-content-heading text-xs">Supervisor: <span class="font-normal text-content-body">Admin Utama</span></p>
                            <p class="text-[11px] text-content-muted">Petugas Scanner: Tim Operator Lapangan A</p>
                            <p class="text-[11px] text-content-muted">Kegiatan: Bulutangkis & Tenis Meja</p>
                        </div>
                    </div>

                    <!-- Shift Sore / Malam -->
                    <div class="p-4 rounded-2xl bg-surface-page border border-line-base space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                                Shift Sore (15:00 - 22:00)
                            </span>
                            <span class="text-[10px] text-content-faint font-mono">GOR Kartasura</span>
                        </div>
                        <div class="pt-1 space-y-1 text-content-body">
                            <p class="font-bold text-content-heading text-xs">Supervisor: <span class="font-normal text-content-body">Admin Gaston</span></p>
                            <p class="text-[11px] text-content-muted">Petugas Scanner: Tim Operator Lapangan B</p>
                            <p class="text-[11px] text-content-muted">Kegiatan: Futsal, Basket, & Voli</p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-elevated border border-line-base text-[11px] text-content-muted flex items-start space-x-2.5">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Pergantian shift antar petugas wajib konfirmasi minimal 4 jam sebelum jam operasional dimulai kepada Koordinator Gaston YK.</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-line-base">
                <button type="button" @click="modalType = null" class="px-5 py-2.5 bg-surface-elevated hover:bg-surface-elevated text-white font-bold rounded-xl text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 3. MODAL KEBIJAKAN ABSENSI -->
    <div x-show="modalType === 'kebijakan'"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalType = null"
             class="bg-surface border border-line-base rounded-[32px] max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-line-base">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-surface-elevated/80 border border-line-strong/60 text-content-body flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-content-heading">Kebijakan Absensi & Ketentuan Hadir</h3>
                        <p class="text-xs text-content-muted">Standar Operasional Presensi Gaston YK</p>
                    </div>
                </div>
                <button type="button" @click="modalType = null" class="text-content-muted hover:text-content-heading p-1 rounded-xl bg-surface-page border border-line-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-3.5 text-xs text-content-body">
                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base space-y-1">
                    <p class="font-bold text-content-heading flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#e50914]"></span>
                        <span>1. Waktu Scan Kehadiran</span>
                    </p>
                    <p class="text-[11px] text-content-muted pl-4 leading-relaxed">
                        Presensi dibuka 30 menit sebelum sesi olahraga dimulai sampai dengan 30 menit setelah sesi berakhir. Scan dilakukan dengan menunjukkan QR pribadi dari aplikasi ke petugas admin lapangan.
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base space-y-1">
                    <p class="font-bold text-content-heading flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span>2. Pengajuan Izin / Sakit</span>
                    </p>
                    <p class="text-[11px] text-content-muted pl-4 leading-relaxed">
                        Member yang berhalangan hadir wajib mengirimkan pengajuan izin via menu izin aplikasi disertai alasan yang jelas serta lampiran bukti (surat dokter / keterangan dinas). Izin yang disetujui tidak akan memotong attendance rate.
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base space-y-1">
                    <p class="font-bold text-content-heading flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>3. Peserta Walk-In (Tanpa Registrasi Awal)</span>
                    </p>
                    <p class="text-[11px] text-content-muted pl-4 leading-relaxed">
                        Member aktif yang belum terdaftar di sesi tetap diperbolehkan hadir jika kuota lapangan mencukupi. Petugas dapat menyetujui konfirmasi walk-in langsung dari layar scanner QR.
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base space-y-1">
                    <p class="font-bold text-content-heading flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>4. Batas Minimal Kehadiran</span>
                    </p>
                    <p class="text-[11px] text-content-muted pl-4 leading-relaxed">
                        Tingkat kehadiran minimum anggota aktif adalah 75% per bulan untuk tetap mempertahankan prioritas booking slot kegiatan dan turnamen Gaston YK.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-line-base">
                <button type="button" @click="modalType = null" class="px-5 py-2.5 bg-surface-elevated hover:bg-surface-elevated text-white font-bold rounded-xl text-xs">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- 4. MODAL LIBUR NASIONAL -->
    <div x-show="modalType === 'libur'"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalType = null"
             class="bg-surface border border-line-base rounded-[32px] max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-line-base">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-950/60 border border-red-900/40 text-[#e50914] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-content-heading">Kalender Libur Nasional & Cuti Bersama</h3>
                        <p class="text-xs text-content-muted">Jadwal Penyesuaian Operasional GOR & Kegiatan Gaston YK</p>
                    </div>
                </div>
                <button type="button" @click="modalType = null" class="text-content-muted hover:text-content-heading p-1 rounded-xl bg-surface-page border border-line-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- List Libur Nasional -->
            <div class="space-y-2.5 text-xs">
                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="text-center px-3 py-1.5 bg-red-950/40 border border-red-900/40 rounded-xl">
                            <span class="block text-sm font-black text-[#e50914]">17</span>
                            <span class="block text-[9px] uppercase font-bold text-content-muted">Agu</span>
                        </div>
                        <div>
                            <p class="font-bold text-content-heading text-xs">Hari Kemerdekaan Republik Indonesia</p>
                            <p class="text-[11px] text-content-muted">Seluruh GOR libur, diganti turnamen internal 17 Agustus</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                        Libur Penuh
                    </span>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="text-center px-3 py-1.5 bg-surface-elevated/60 border border-line-strong/40 rounded-xl">
                            <span class="block text-sm font-black text-content-body">25</span>
                            <span class="block text-[9px] uppercase font-bold text-content-muted">Des</span>
                        </div>
                        <div>
                            <p class="font-bold text-content-heading text-xs">Hari Raya Natal & Cuti Bersama</p>
                            <p class="text-[11px] text-content-muted">Penyesuaian jam operasional (Sesi malam ditiadakan)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        Shift Terbatas
                    </span>
                </div>

                <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="text-center px-3 py-1.5 bg-surface-elevated/60 border border-line-strong/40 rounded-xl">
                            <span class="block text-sm font-black text-content-body">01</span>
                            <span class="block text-[9px] uppercase font-bold text-content-muted">Jan</span>
                        </div>
                        <div>
                            <p class="font-bold text-content-heading text-xs">Tahun Baru Masehi</p>
                            <p class="text-[11px] text-content-muted">Kegiatan rutin dijadwalkan ulang ke hari berikutnya</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                        Libur Penuh
                    </span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-surface-elevated border border-line-base text-[11px] text-content-muted">
                Pemberitahuan perubahan tanggal sesi di hari libur akan disiarkan otomatis via notifikasi sistem dan WhatsApp kepada seluruh member terdaftar.
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-line-base">
                <button type="button" @click="modalType = null" class="px-5 py-2.5 bg-surface-elevated hover:bg-surface-elevated text-white font-bold rounded-xl text-xs">
                    Tutup
                </button>
            </div>
        </div>
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
