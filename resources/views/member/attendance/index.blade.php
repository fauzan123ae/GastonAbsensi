@extends('layouts.member')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="space-y-4">

    <!-- Header matching Phone 5 in reference (Riwayat Absensi + Calendar Button) -->
    <div class="flex items-center justify-between pt-1">
        <h2 class="text-base font-black text-white">Riwayat Absensi</h2>
        <div class="p-2 rounded-xl bg-[#11141c] border border-[#1f2533] text-slate-400 hover:text-white cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
    </div>

    <!-- Filter Toggle Pills matching Phone 5 (Minggu [Red Active], Bulan, Semua) -->
    <div class="p-1 rounded-2xl bg-[#11141c] border border-[#1f2533] flex items-center">
        <button class="flex-1 py-1.5 px-3 rounded-xl text-xs font-black bg-[#e50914] text-white shadow-md shadow-[#e50914]/30">
            Minggu
        </button>
        <button class="flex-1 py-1.5 px-3 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">
            Bulan
        </button>
        <button class="flex-1 py-1.5 px-3 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">
            Semua
        </button>
    </div>

    <!-- Date Range Navigator matching Phone 5 (< 16 - 22 Juni 2025 >) -->
    <div class="flex items-center justify-between px-2 py-1 text-xs">
        <button class="p-1 text-slate-500 hover:text-white rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <span class="text-xs font-bold text-slate-300">
            {{ now()->startOfWeek()->translatedFormat('d') }} - {{ now()->endOfWeek()->translatedFormat('d F Y') }}
        </span>
        <button class="p-1 text-slate-500 hover:text-white rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <!-- Attendance Cards List -->
    <div class="space-y-2.5">
        @forelse($attendances as $att)
            <div class="p-3.5 rounded-2xl bg-[#11141c] border border-[#1f2533] hover:border-[#333d52] transition-colors flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-[#090c12] border border-[#1f2533] flex items-center justify-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <p class="text-xs font-bold text-white">{{ $att->checked_in_at->format('d M Y') }}</p>
                            @if($att->session && $att->session->sport)
                                <span class="text-[10px] font-bold text-gaston-orange bg-gaston-orange/15 px-2 py-0.2 rounded-md border border-gaston-orange/30">
                                    {{ $att->session->sport->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Check-in: <span class="font-mono text-slate-200">{{ $att->checked_in_at->format('H:i') }} WIB</span>
                            @if($att->session && $att->session->location)
                                • {{ $att->session->location->name }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    @if($att->status === 'present')
                        <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            Hadir
                        </span>
                    @elseif($att->status === 'cancelled')
                        <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/15 text-rose-400 border border-rose-500/30">
                            Dibatalkan
                        </span>
                    @else
                        <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                            {{ ucfirst($att->status) }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center rounded-2xl bg-[#11141c] border border-[#1f2533]">
                <div class="w-12 h-12 rounded-2xl bg-[#090c12] text-slate-500 mx-auto flex items-center justify-center mb-3 border border-[#1f2533]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xs font-bold text-slate-300">Belum ada riwayat kehadiran</p>
                <p class="text-[11px] text-slate-500 mt-1">Kehadiran sesi olahraga Anda akan tercatat di sini setelah melakukan check-in.</p>
            </div>
        @endforelse
    </div>

    @if($attendances->hasPages())
        <div class="pt-2">
            {{ $attendances->links() }}
        </div>
    @endif

</div>
@endsection
