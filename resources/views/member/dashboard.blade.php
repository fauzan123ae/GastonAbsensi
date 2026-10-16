@extends('layouts.member')

@section('title', 'Dashboard Member')

@section('content')
<div class="space-y-6">

    <!-- Top Greeting Section matching Phone 4 -->
    <div class="pt-1">
        <h2 class="text-xl sm:text-2xl font-black text-content-heading">Halo, {{ $user->name }}</h2>
        <p class="text-xs text-content-muted mt-0.5">Tetap semangat dan jaga konsistensi!</p>
    </div>

    <!-- Status Hari Ini Card matching Phone 4 -->
    @php
        $firstTodaySession = $todaySessions->first();
        $firstTodayAttendance = $firstTodaySession ? $todayAttendances->get($firstTodaySession->id) : null;
        $firstTodayPermission = $firstTodaySession ? $todayPermissions->get($firstTodaySession->id) : null;
    @endphp
    <div class="bg-surface border border-line-base rounded-3xl p-5 shadow-2xl relative overflow-hidden">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-content-muted">Status Hari Ini</span>
            <span class="text-content-faint">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
        </div>

        <div class="mt-3 flex items-center space-x-2">
            @if($firstTodayAttendance && $firstTodayAttendance->status === 'present')
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="text-base font-black text-emerald-400">Sudah Hadir</span>
                @if($firstTodaySession)
                    <span class="text-xs text-content-muted font-semibold">• {{ $firstTodaySession->sport->name }}</span>
                @endif
            @elseif($firstTodayPermission && $firstTodayPermission->status === 'approved')
                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                <span class="text-base font-black text-blue-400">Izin Disetujui</span>
                @if($firstTodaySession)
                    <span class="text-xs text-content-muted font-semibold">• {{ $firstTodaySession->sport->name }}</span>
                @endif
            @elseif($todaySessions->isNotEmpty())
                <span class="w-2.5 h-2.5 rounded-full bg-[#e50914] animate-pulse"></span>
                <span class="text-base font-black text-[#e50914]">Belum Absen</span>
                @if($firstTodaySession)
                    <span class="text-xs text-content-muted font-semibold">• {{ $firstTodaySession->sport->name }}</span>
                @endif
            @else
                <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                <span class="text-base font-black text-content-muted">Tidak Ada Sesi Hari Ini</span>
            @endif
        </div>
        <p class="text-[11px] font-mono text-content-muted mt-1">
            @if($firstTodaySession)
                {{ substr($firstTodaySession->start_time, 0, 5) }} - {{ substr($firstTodaySession->end_time, 0, 5) }} WIB ({{ $firstTodaySession->location->name }})
            @elseif($upcomingSession)
                Sesi Berikutnya: {{ substr($upcomingSession->start_time, 0, 5) }} - {{ substr($upcomingSession->end_time, 0, 5) }} WIB
            @else
                Tidak ada agenda hari ini
            @endif
        </p>
    </div>

    <!-- 3 Action Cards matching Phone 4 Grid (Absen Sekarang [Red Card], Riwayat Absen, Pengajuan Izin) -->
    <div class="grid grid-cols-3 gap-3">
        <!-- Absen Sekarang (Red Card Tile with Fingerprint Icon) -->
        <a href="{{ route('member.qr') }}"
           class="p-4 sm:p-5 rounded-3xl bg-[#e50914] hover:bg-[#ff1e27] text-white shadow-xl shadow-[#e50914]/25 flex flex-col items-center text-center justify-between min-h-[125px] transition-all hover:scale-[1.02] group">
            <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center mb-2">
                <!-- Fingerprint / Touch ID Icon matching Phone 4 -->
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11m0 0a8.003 8.003 0 0115.357-2m1.519 4a10.012 10.012 0 01-.876 4"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-black tracking-tight leading-tight">Absen<br>Sekarang</h3>
            </div>
        </a>

        <!-- Riwayat Absen Tile -->
        <a href="{{ route('member.attendance.index') }}"
           class="p-4 sm:p-5 rounded-3xl bg-surface border border-line-base hover:border-[#333d52] text-content-body shadow-xl flex flex-col items-center text-center justify-between min-h-[125px] transition-all hover:scale-[1.02] group">
            <div class="w-10 h-10 rounded-2xl bg-surface-input flex items-center justify-center text-content-body mb-2 border border-line-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-black text-content-heading leading-tight">Riwayat<br>Absen</h3>
            </div>
        </a>

        <!-- Pengajuan Izin Tile -->
        <a href="{{ route('member.permissions.index') }}"
           class="p-4 sm:p-5 rounded-3xl bg-surface border border-line-base hover:border-[#333d52] text-content-body shadow-xl flex flex-col items-center text-center justify-between min-h-[125px] transition-all hover:scale-[1.02] group">
            <div class="w-10 h-10 rounded-2xl bg-surface-input flex items-center justify-center text-content-body mb-2 border border-line-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xs font-black text-content-heading leading-tight">Pengajuan<br>Izin</h3>
            </div>
        </a>
    </div>

    <!-- Attendance Stats -->
    <div class="space-y-3 pt-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-content-muted">Ringkasan Statistik Kehadiran</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-4 rounded-2xl bg-surface border border-line-base">
                <span class="text-[11px] font-bold uppercase tracking-wider text-content-muted block">Total Sesi</span>
                <span class="text-2xl font-black text-content-heading mt-1 block">{{ $totalRegistered }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-surface border border-line-base">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Hadir</span>
                <span class="text-2xl font-black text-emerald-400 mt-1 block">{{ $totalPresent }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-surface border border-line-base">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400 block">Izin</span>
                <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $totalPermission }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-surface border border-line-base">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Tidak Hadir</span>
                <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $totalAbsent }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-surface border border-line-base col-span-2 sm:col-span-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-content-muted block">Attendance Rate</span>
                <span class="text-2xl font-black text-[#e50914] mt-1 block">{{ $attendanceRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Upcoming Session Card -->
    <div class="space-y-3 pt-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-content-muted">Sesi Terdekat Yang Anda Ikuti</h3>
        @if($upcomingSession)
            <div class="bg-surface border border-line-base rounded-3xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-lg hover:border-[#e50914]/40 transition-colors">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-black uppercase bg-[#e50914]/15 text-[#e50914] border border-[#e50914]/30">
                            {{ $upcomingSession->sport->name }}
                        </span>
                        <span class="text-xs text-content-muted font-semibold">• {{ $upcomingSession->location->name }}</span>
                    </div>
                    <h4 class="text-lg font-black text-content-heading mt-2">{{ $upcomingSession->date->translatedFormat('l, d F Y') }}</h4>
                    <p class="text-xs text-content-muted mt-0.5">{{ substr($upcomingSession->start_time, 0, 5) }} - {{ substr($upcomingSession->end_time, 0, 5) }} WIB</p>
                </div>

                <div class="flex items-center space-x-3">
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        Status: Terdaftar
                    </span>
                    <a href="{{ route('member.permissions.index') }}" class="px-3.5 py-2 rounded-xl bg-surface-input hover:bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
                        Ajukan Izin
                    </a>
                </div>
            </div>
        @else
            <div class="p-8 rounded-3xl bg-surface/60 border border-line-base text-center text-content-muted text-xs">
                Tidak ada jadwal sesi terdekat yang sedang Anda ikuti saat ini.
            </div>
        @endif
    </div>

</div>
@endsection
