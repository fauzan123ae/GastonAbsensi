@extends('layouts.admin')

@section('title', 'Daftar Sesi Olahraga')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-content-heading">Sesi Olahraga</h2>
            </div>
            <p class="text-xs text-content-muted">Kelola jadwal sesi, peserta terdaftar, dan absensi di lapangan.</p>
        </div>
        <div>
            <a href="{{ route('admin.sessions.create') }}"
               class="inline-flex items-center space-x-2 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-gaston-orange/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Buat Sesi Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl p-4">
        <form action="{{ route('admin.sessions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <select name="sport_id" class="w-full bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua Olahraga</option>
                    @foreach($sports as $s)
                        <option value="{{ $s->id }}" {{ $sportId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="location_id" class="w-full bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua GOR</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}" {{ $locationId == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="ongoing" {{ $status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-line-base">
                    Filter Sesi
                </button>
            </div>
        </form>
    </div>

    <!-- Sessions Table & Mobile Card View -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-input text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">Sesi & Olahraga</th>
                        <th class="px-6 py-4">GOR / Waktu</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Peserta / Hadir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold uppercase bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30">
                                    {{ $session->sport->name }}
                                </span>
                                <p class="font-bold text-content-heading mt-1.5">{{ $session->date->translatedFormat('l, d F Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-white">{{ $session->location->name }}</p>
                                <p class="text-xs text-content-muted mt-0.5">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-surface-elevated text-content-muted',
                                        'open' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                        'ongoing' => 'bg-gaston-orange/20 text-gaston-orange border border-gaston-orange/40 font-black',
                                        'completed' => 'bg-surface-elevated text-content-muted border border-line-strong',
                                        'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusClasses[$session->status] ?? 'bg-surface-elevated text-content-muted' }}">
                                    {{ strtoupper($session->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-1.5 text-xs font-semibold">
                                    <span class="text-gaston-orange font-black text-sm">{{ $session->present_count }}</span>
                                    <span class="text-content-faint">/</span>
                                    <span class="text-white">{{ $session->registered_count }}</span>
                                    <span class="text-content-muted">Hadir</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.sessions.scan', $session) }}"
                                   class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-gaston-orange/15 hover:bg-gaston-orange/25 text-gaston-orange text-xs font-extrabold border border-gaston-orange/30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    <span>Scan</span>
                                </a>
                                <a href="{{ route('admin.sessions.participants', $session) }}" class="inline-block px-2.5 py-1.5 rounded-xl bg-surface-input hover:bg-surface-elevated text-content-body text-xs font-semibold border border-line-base">
                                    Peserta
                                </a>
                                <a href="{{ route('admin.sessions.attendance', $session) }}" class="inline-block px-2.5 py-1.5 rounded-xl bg-surface-input hover:bg-surface-elevated text-content-body text-xs font-semibold border border-line-base">
                                    Absensi
                                </a>
                                <a href="{{ route('admin.sessions.edit', $session) }}" class="inline-block px-2 py-1 rounded text-xs font-semibold text-content-muted hover:text-content-heading">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-content-faint">
                                Belum ada data sesi olahraga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($sessions as $session)
                @php
                    $statusClasses = [
                        'draft' => 'bg-surface-elevated text-content-muted',
                        'open' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                        'ongoing' => 'bg-gaston-orange/20 text-gaston-orange border border-gaston-orange/40 font-black',
                        'completed' => 'bg-surface-elevated text-content-muted border border-line-strong',
                        'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                    ];
                @endphp
                <div class="p-4 space-y-3 hover:bg-surface-elevated/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30">
                                {{ $session->sport->name }}
                            </span>
                            <h3 class="text-sm font-bold text-content-heading mt-1">{{ $session->date->translatedFormat('l, d F Y') }}</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $statusClasses[$session->status] ?? 'bg-surface-elevated text-content-muted' }}">
                            {{ strtoupper($session->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-surface-input/70 p-2.5 rounded-2xl border border-line-base/50">
                        <div>
                            <span class="text-[10px] text-content-faint uppercase block font-semibold">GOR / Lokasi</span>
                            <span class="text-content-body font-medium truncate block">{{ $session->location->name }}</span>
                            <span class="text-[10px] text-content-muted font-mono">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-content-faint uppercase block font-semibold">Kehadiran</span>
                            <div class="text-xs font-semibold mt-0.5">
                                <span class="text-gaston-orange font-black text-sm">{{ $session->present_count }}</span>
                                <span class="text-content-faint">/</span>
                                <span class="text-white">{{ $session->registered_count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('admin.sessions.scan', $session) }}"
                           class="flex-1 py-2 rounded-xl bg-gaston-orange hover:bg-orange-600 text-white text-xs font-bold flex items-center justify-center space-x-1 shadow-md shadow-gaston-orange/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Scan</span>
                        </a>
                        <a href="{{ route('admin.sessions.participants', $session) }}" class="px-3 py-2 rounded-xl bg-surface-input text-content-body text-xs font-semibold border border-line-base text-center">
                            Peserta
                        </a>
                        <a href="{{ route('admin.sessions.attendance', $session) }}" class="px-3 py-2 rounded-xl bg-surface-input text-content-body text-xs font-semibold border border-line-base text-center">
                            Absensi
                        </a>
                        <a href="{{ route('admin.sessions.edit', $session) }}" class="p-2 rounded-xl text-content-muted hover:text-content-heading hover:bg-surface-elevated">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-content-faint text-xs">
                    Belum ada data sesi olahraga.
                </div>
            @endforelse
        </div>

        @if($sessions->hasPages())
            <div class="px-6 py-4 border-t border-line-base">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
