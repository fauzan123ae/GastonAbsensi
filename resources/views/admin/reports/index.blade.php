@extends('layouts.admin')

@section('title', 'Laporan & Rekap Kehadiran')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-white">Laporan & Rekap Absensi</h2>
            </div>
            <p class="text-xs text-slate-400">Analisis tingkat kehadiran (Attendance Rate) dan unduh data ke format CSV.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="inline-flex items-center space-x-2 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-gaston-orange/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl p-5 shadow-xl">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Dari Tanggal</label>
                <div class="relative">
                    <input type="text" name="start_date" id="start_date" value="{{ $startDate }}"
                           placeholder="Pilih Tanggal..."
                           class="w-full bg-[#0d1017] border border-gaston-border rounded-xl pl-9 pr-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange cursor-pointer">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gaston-orange">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Sampai Tanggal</label>
                <div class="relative">
                    <input type="text" name="end_date" id="end_date" value="{{ $endDate }}"
                           placeholder="Pilih Tanggal..."
                           class="w-full bg-[#0d1017] border border-gaston-border rounded-xl pl-9 pr-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange cursor-pointer">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gaston-orange">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Olahraga</label>
                <select name="sport_id" class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua</option>
                    @foreach($sports as $s)
                        <option value="{{ $s->id }}" {{ $sportId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">GOR</label>
                <select name="location_id" class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}" {{ $locationId == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Metode</label>
                <select name="method" class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua</option>
                    <option value="qr" {{ $method === 'qr' ? 'selected' : '' }}>QR Code</option>
                    <option value="manual" {{ $method === 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Member</label>
                <select name="member_id" class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua Member</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ $memberId == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->member_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#1b212c] hover:bg-slate-800 text-slate-200 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-gaston-border">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Sesi</span>
            <span class="text-2xl font-black text-white mt-1 block">{{ $totalSessions }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Terdaftar</span>
            <span class="text-2xl font-black text-white mt-1 block">{{ $totalRegistered }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gaston-orange block">Hadir</span>
            <span class="text-2xl font-black text-gaston-orange mt-1 block">{{ $totalPresent }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400 block">Izin</span>
            <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $totalPermission }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Tidak Hadir</span>
            <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $totalAbsent }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gaston-orange block">Attendance Rate</span>
            <span class="text-2xl font-black text-white mt-1 block">{{ $attendanceRate }}%</span>
        </div>
    </div>

    <!-- Attendance Details Table & Mobile Card View -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-gaston-border">
                    <tr>
                        <th class="px-6 py-4">Waktu Check-in</th>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Sesi & GOR</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-[#1a202c]/50 transition-colors">
                            <td class="px-6 py-4 text-xs">
                                <p class="font-bold text-white">{{ $att->checked_in_at->format('d M Y') }}</p>
                                <p class="text-slate-400">{{ $att->checked_in_at->format('H:i:s') }} WIB</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-white">{{ $att->user->name }}</p>
                                <p class="text-[11px] font-mono font-bold text-gaston-orange">{{ $att->user->member_id }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-white">{{ $att->session->sport->name }}</p>
                                <p class="text-xs text-slate-400">{{ $att->session->location->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border">
                                    {{ strtoupper($att->method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $att->checkedInBy->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Tidak ada data absensi untuk filter terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($attendances as $att)
                <div class="p-4 space-y-2.5 hover:bg-[#1a202c]/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold text-white text-sm leading-tight">{{ $att->user->name }}</p>
                            <p class="text-[10px] font-mono font-bold text-gaston-orange mt-0.5">{{ $att->user->member_id }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border flex-shrink-0">
                            {{ strtoupper($att->method) }}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-[#0d1017]/70 border border-gaston-border/50 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">{{ $att->session->sport->name }} &bull; {{ $att->session->location->name }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono pt-0.5 border-t border-gaston-border/40">
                            <span>Check-in: <strong class="text-white">{{ $att->checked_in_at->format('d M Y, H:i') }}</strong></span>
                            <span>Oleh: <strong class="text-slate-300">{{ $att->checkedInBy->name ?? 'Sistem' }}</strong></span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">
                    Tidak ada data absensi untuk filter terpilih.
                </div>
            @endforelse
        </div>

        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gaston-border">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            locale: "id",
            disableMobile: true,
            theme: "dark"
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            locale: "id",
            disableMobile: true,
            theme: "dark"
        });
    });
</script>
@endpush
