@extends('layouts.admin')

@section('title', 'Detail Member - ' . $member->name)

@section('content')
<div class="space-y-6">

    <div>
        <a href="{{ route('admin.members.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Member</span>
        </a>
        <h2 class="text-2xl font-black text-white">Profil & Riwayat Member</h2>
    </div>

    <!-- Member Info Card -->
    <div class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center space-x-4">
            @if($member->photo)
                <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-gaston-orange shadow-lg shadow-gaston-orange/30">
            @else
                <div class="w-20 h-20 rounded-2xl bg-[#1c2230] text-gaston-orange flex items-center justify-center font-black text-2xl border border-gaston-border">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center space-x-3">
                    <h3 class="text-xl font-black text-white">{{ $member->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $member->isActive() ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                        {{ $member->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">{{ $member->email }} • {{ $member->phone ?? 'Tidak ada No. HP' }}</p>
                <div class="flex items-center space-x-2 mt-2">
                    <span class="px-2 py-0.5 rounded-lg text-xs font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border">
                        {{ $member->member_id }}
                    </span>
                    <span class="text-xs text-slate-500">Bergabung {{ $member->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.members.qr', $member) }}"
               class="px-4 py-2.5 rounded-2xl bg-[#0d1017] hover:bg-slate-800 text-gaston-orange text-xs font-bold border border-gaston-border transition-colors">
                Lihat QR Code
            </a>
            <a href="{{ route('admin.members.edit', $member) }}"
               class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-lg shadow-gaston-orange/25 transition-all">
                Edit Profil
            </a>
        </div>
    </div>

    <!-- Attendance History Table & Mobile Cards -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl p-5 sm:p-6 shadow-xl">
        <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Riwayat Kehadiran Member</h4>
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-xs font-bold uppercase text-slate-400 border-b border-gaston-border">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Olahraga</th>
                        <th class="px-4 py-3">Lokasi / GOR</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Jam Absen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($member->attendances as $att)
                        <tr>
                            <td class="px-4 py-3">{{ $att->session->date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-bold text-white">{{ $att->session->sport->name }}</td>
                            <td class="px-4 py-3">{{ $att->session->location->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border">
                                    {{ strtoupper($att->method) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-400">{{ $att->checked_in_at->format('H:i') }} WIB</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                Belum ada riwayat kehadiran tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($member->attendances as $att)
                <div class="py-3 first:pt-0 last:pb-0 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white text-xs">{{ $att->session->sport->name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border">
                            {{ strtoupper($att->method) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>{{ $att->session->date->format('d M Y') }} &bull; {{ $att->session->location->name }}</span>
                        <span class="text-slate-300 font-mono">{{ $att->checked_in_at->format('H:i') }} WIB</span>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-slate-500 text-xs">
                    Belum ada riwayat kehadiran tercatat.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
