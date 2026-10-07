@extends('layouts.admin')

@section('title', 'Detail Sesi - ' . $session->sport->name)

@section('content')
<div class="space-y-6">

    <!-- Header & Navigation -->
    <div>
        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Sesi</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                    <h2 class="text-2xl font-black text-white">Detail Sesi Olahraga</h2>
                </div>
                <p class="text-xs text-slate-400">
                    Informasi lengkap sesi, ringkasan kehadiran, dan daftar peserta terdaftar.
                </p>
            </div>
            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if($session->canAcceptAttendance())
                    <a href="{{ route('admin.sessions.scan', $session) }}"
                       class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Scan QR</span>
                    </a>
                @endif
                <a href="{{ route('admin.sessions.participants', $session) }}"
                   class="px-4 py-2.5 rounded-2xl bg-[#1b212c] hover:bg-slate-800 text-slate-200 font-bold text-xs transition-colors border border-gaston-border flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Peserta</span>
                </a>
                <a href="{{ route('admin.sessions.attendance', $session) }}"
                   class="px-4 py-2.5 rounded-2xl bg-[#1b212c] hover:bg-slate-800 text-slate-200 font-bold text-xs transition-colors border border-gaston-border flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('admin.sessions.edit', $session) }}"
                   class="px-4 py-2.5 rounded-2xl bg-[#0d1017] hover:bg-slate-800 text-slate-300 font-bold text-xs transition-colors border border-gaston-border flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Edit Sesi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Session Info Card -->
    @php
        $statusClasses = [
            'draft' => 'bg-slate-800 text-slate-400 border border-slate-700',
            'open' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
            'ongoing' => 'bg-gaston-orange/20 text-gaston-orange border border-gaston-orange/40 font-black',
            'completed' => 'bg-slate-800 text-slate-400 border border-slate-700',
            'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
        ];
    @endphp
    <div class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gaston-border/80 pb-6">
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-[#1c2230] text-gaston-orange flex items-center justify-center font-black text-2xl border border-gaston-border flex-shrink-0">
                    <svg class="w-8 h-8 text-gaston-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h3 class="text-xl sm:text-2xl font-black text-white">{{ $session->sport->name }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $statusClasses[$session->status] ?? 'bg-slate-800 text-slate-400' }}">
                            {{ strtoupper($session->status) }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1 flex items-center space-x-2">
                        <span>📍 {{ $session->location->name }} ({{ $session->location->address ?? 'Alamat belum diisi' }})</span>
                    </p>
                </div>
            </div>

            <div class="text-left md:text-right">
                <div class="text-xs text-slate-400">Jadwal Sesi</div>
                <div class="text-base font-extrabold text-white mt-0.5">
                    {{ $session->date->translatedFormat('l, d F Y') }}
                </div>
                <div class="text-xs font-bold text-gaston-orange mt-0.5">
                    {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB
                </div>
            </div>
        </div>

        <!-- Extra Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 text-xs">
            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider block">Dibuat Oleh</span>
                <span class="text-slate-300 font-medium mt-1 block">{{ $session->creator ? $session->creator->name : 'Sistem' }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider block">Waktu Dibuat</span>
                <span class="text-slate-300 font-medium mt-1 block">{{ $session->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider block">Catatan Sesi</span>
                <span class="text-slate-300 font-medium mt-1 block">{{ $session->notes ?: '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Ringkasan Kehadiran</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Terdaftar</span>
                <span class="text-2xl font-black text-white mt-1 block">{{ $summary['total_registered'] ?? 0 }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gaston-orange block">Hadir</span>
                <span class="text-2xl font-black text-gaston-orange mt-1 block">{{ $summary['present'] ?? 0 }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400 block">Izin Disetujui</span>
                <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $summary['permission'] ?? 0 }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Tidak Hadir</span>
                <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $summary['absent'] ?? 0 }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-[#141822] border border-gaston-border col-span-2 sm:col-span-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Attendance Rate</span>
                <span class="text-2xl font-black text-gaston-orange mt-1 block">{{ $summary['attendance_rate'] ?? 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Participants & Attendance Status Table -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-gaston-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Daftar Peserta & Kehadiran</h4>
                <p class="text-xs text-slate-400">Total: {{ $participants->count() }} Peserta terdaftar</p>
            </div>
            <span class="text-xs text-gaston-orange font-semibold italic">Prinsip: Terdaftar ≠ Hadir</span>
        </div>

        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-gaston-border">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Sumber Registrasi</th>
                        <th class="px-6 py-4">Status Kehadiran</th>
                        <th class="px-6 py-4">Waktu Check-in</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($participants as $index => $participant)
                        @php
                            $user = $participant->user;
                            $attendance = $user ? $user->attendances->first() : null;
                        @endphp
                        <tr class="hover:bg-[#1a202c]/50 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                @if($user)
                                    <div class="flex items-center space-x-3">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" class="w-9 h-9 rounded-xl object-cover border border-gaston-border">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-[#1b212c] text-gaston-orange flex items-center justify-center font-bold text-xs border border-gaston-border">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.members.show', $user) }}" class="font-bold text-white hover:text-gaston-orange transition-colors">
                                                {{ $user->name }}
                                            </a>
                                            <div class="text-[11px] text-slate-400">{{ $user->member_id ?? $user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-500 italic">User telah dihapus</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-[#0d1017] text-slate-300 border border-gaston-border">
                                    {{ strtoupper($participant->registration_source) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($attendance && $attendance->status === 'present')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        ✓ HADIR ({{ strtoupper($attendance->method) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                        BELUM HADIR
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400 font-mono">
                                @if($attendance && $attendance->checked_in_at)
                                    {{ $attendance->checked_in_at->format('H:i:s') }} WIB
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada peserta yang didaftarkan untuk sesi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($participants as $index => $participant)
                @php
                    $user = $participant->user;
                    $attendance = $user ? $user->attendances->first() : null;
                @endphp
                <div class="p-4 space-y-3 hover:bg-[#1a202c]/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-3">
                            @if($user && $user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" class="w-10 h-10 rounded-xl object-cover border border-gaston-border">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-[#1b212c] text-gaston-orange flex items-center justify-center font-bold text-xs border border-gaston-border">
                                    {{ strtoupper(substr($user ? $user->name : '?', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <a href="{{ $user ? route('admin.members.show', $user) : '#' }}" class="font-bold text-white text-sm">
                                    {{ $user ? $user->name : 'User telah dihapus' }}
                                </a>
                                <div class="text-[11px] text-slate-400">{{ $user ? ($user->member_id ?? $user->email) : '-' }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#0d1017] text-slate-400 border border-gaston-border">
                            {{ strtoupper($participant->registration_source) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-gaston-border/40">
                        <div>
                            @if($attendance && $attendance->status === 'present')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    ✓ HADIR ({{ strtoupper($attendance->method) }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                    BELUM HADIR
                                </span>
                            @endif
                        </div>
                        <div class="text-slate-400 font-mono text-[11px]">
                            {{ $attendance && $attendance->checked_in_at ? $attendance->checked_in_at->format('H:i') . ' WIB' : '-' }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">
                    Belum ada peserta yang didaftarkan untuk sesi ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
