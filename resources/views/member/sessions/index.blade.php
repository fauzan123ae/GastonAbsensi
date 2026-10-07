@extends('layouts.member')

@section('title', 'Sesi Saya')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-white tracking-wide">Sesi Olahraga Terdaftar</h2>
            <p class="text-xs text-slate-400 mt-1">Daftar agenda olahraga aktif di mana akun Anda tercatat sebagai peserta.</p>
        </div>
        <div class="w-10 h-10 rounded-2xl bg-gaston-card border border-gaston-border flex items-center justify-center text-gaston-orange shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($participations as $part)
            @php
                $session = $part->session;
                $att = $attendances->get($session->id);
                $perm = $permissions->get($session->id);

                $statusClasses = [
                    'draft' => 'bg-slate-800 text-slate-400 border border-slate-700',
                    'open' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                    'ongoing' => 'bg-gaston-orange/20 text-gaston-orange border border-gaston-orange/40 font-black',
                    'completed' => 'bg-slate-800 text-slate-400 border border-slate-700',
                    'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                ];
            @endphp
            <div class="bg-gaston-card border border-gaston-border hover:border-gaston-orange/40 rounded-3xl p-6 flex flex-col justify-between gap-5 transition-all duration-300 shadow-xl relative overflow-hidden group">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-gaston-orange/10 rounded-full blur-2xl group-hover:bg-gaston-orange/20 transition-all pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30">
                            {{ $session->sport->name }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$session->status] ?? 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                            {{ strtoupper($session->status) }}
                        </span>
                    </div>

                    <h3 class="text-lg font-black text-white group-hover:text-gaston-orange transition-colors">
                        {{ $session->date->translatedFormat('l, d F Y') }}
                    </h3>
                    
                    <div class="mt-3 space-y-1.5 text-xs text-slate-400">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gaston-orange flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="font-medium text-slate-300">{{ $session->location->name }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</span>
                        </div>
                    </div>

                    @if($session->notes)
                        <p class="text-xs text-slate-500 mt-3 p-2.5 rounded-xl bg-gaston-dark/60 border border-gaston-border/60 italic">
                            "{{ $session->notes }}"
                        </p>
                    @endif
                </div>

                <div class="pt-4 border-t border-gaston-border flex items-center justify-between">
                    <div class="text-[11px] flex items-center space-x-1.5">
                        <span class="text-slate-500">Kehadiran:</span>
                        @if($att && $att->status === 'present')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Hadir
                            </span>
                        @elseif($perm && $perm->status === 'approved')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-500/15 text-blue-400 border border-blue-500/30">
                                Izin
                            </span>
                        @elseif($perm && $perm->status === 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                Izin (Menunggu)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-slate-800 text-slate-400 border border-slate-700">
                                Belum hadir
                            </span>
                        @endif
                    </div>
                    @if($session->canAcceptAttendance() && (!$att || $att->status !== 'present') && (!$perm || $perm->status !== 'approved'))
                        <a href="{{ route('member.permissions.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-600 text-xs font-bold transition-all">
                            Ajukan Izin
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-gaston-card border border-gaston-border rounded-3xl text-slate-500 text-sm space-y-2">
                <svg class="w-10 h-10 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="font-bold text-slate-400">Belum Ada Sesi Terdaftar</p>
                <p class="text-xs">Anda belum terdaftar dalam sesi olahraga apa pun saat ini.</p>
            </div>
        @endforelse
    </div>

    @if($participations->hasPages())
        <div class="pt-4">
            {{ $participations->links() }}
        </div>
    @endif

</div>
@endsection
