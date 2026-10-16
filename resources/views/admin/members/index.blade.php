@extends('layouts.admin')

@section('title', 'Manajemen Member')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-content-heading">Daftar Member Gaston</h2>
            </div>
            <p class="text-xs text-content-muted">Kelola data peserta, lihat QR pribadi permanen, dan atur status akun.</p>
        </div>
        <div>
            <a href="{{ route('admin.members.create') }}"
               class="inline-flex items-center space-x-2 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-gaston-orange/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Member</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl p-4">
        <form action="{{ route('admin.members.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, Member ID (GST-00001), email, atau no HP..."
                       class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-gaston-orange">
            </div>
            <div class="flex space-x-2">
                <select name="status" class="bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange flex-1">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-line-base">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Member Table & Mobile Card View -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-elevated text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">QR Code</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($members as $m)
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($m->photo)
                                        <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-2xl object-cover border border-line-base">
                                    @else
                                        <div class="w-10 h-10 rounded-2xl bg-surface-elevated text-gaston-orange flex items-center justify-center font-bold text-sm border border-line-base">
                                            {{ strtoupper(substr($m->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-content-heading">{{ $m->name }}</p>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-surface-input text-gaston-orange border border-line-base">
                                            {{ $m->member_id }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-content-body">{{ $m->email }}</p>
                                <p class="text-xs text-content-faint mt-0.5">{{ $m->phone ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $m->isActive() ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                    {{ $m->isActive() ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.members.qr', $m) }}"
                                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-surface-input hover:bg-surface-elevated text-gaston-orange text-xs font-bold border border-line-base transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    <span>Lihat QR</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.members.show', $m) }}" class="text-xs font-semibold text-content-body hover:text-content-heading px-2 py-1 rounded-lg hover:bg-surface-elevated">
                                    Detail
                                </a>
                                <a href="{{ route('admin.members.edit', $m) }}" class="text-xs font-bold text-gaston-orange hover:text-orange-400 px-2 py-1 rounded-lg hover:bg-surface-elevated">
                                    Edit
                                </a>
                                <form action="{{ route('admin.members.toggle-status', $m) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Ubah status member ini?')" class="text-xs font-semibold {{ $m->isActive() ? 'text-rose-400 hover:text-rose-300' : 'text-gaston-orange hover:text-orange-400' }} px-2 py-1 rounded-lg hover:bg-surface-elevated">
                                        {{ $m->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-content-faint">
                                Tidak ada data member yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($members as $m)
                <div class="p-4 space-y-3 hover:bg-surface-elevated/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-3">
                            @if($m->photo)
                                <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->name }}" class="w-11 h-11 rounded-2xl object-cover border border-line-base">
                            @else
                                <div class="w-11 h-11 rounded-2xl bg-surface-elevated text-gaston-orange flex items-center justify-center font-bold text-sm border border-line-base">
                                    {{ strtoupper(substr($m->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-content-heading text-sm">{{ $m->name }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-surface-input text-gaston-orange border border-line-base">
                                    {{ $m->member_id }}
                                </span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $m->isActive() ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                            {{ $m->isActive() ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-surface-input/70 border border-line-base/50 text-xs space-y-1">
                        <div class="flex items-center justify-between text-content-body">
                            <span class="text-content-faint text-[10px] uppercase font-semibold">Email:</span>
                            <span class="truncate max-w-[180px]">{{ $m->email }}</span>
                        </div>
                        <div class="flex items-center justify-between text-content-body">
                            <span class="text-content-faint text-[10px] uppercase font-semibold">WhatsApp:</span>
                            <span>{{ $m->phone ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-line-base/40">
                        <a href="{{ route('admin.members.qr', $m) }}"
                           class="flex-1 py-1.5 px-2.5 rounded-xl bg-surface-input hover:bg-surface-elevated text-gaston-orange text-xs font-bold border border-line-base text-center flex items-center justify-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>QR</span>
                        </a>
                        <a href="{{ route('admin.members.show', $m) }}" class="py-1.5 px-3 rounded-xl bg-surface-elevated text-content-body text-xs font-semibold border border-line-base">
                            Detail
                        </a>
                        <a href="{{ route('admin.members.edit', $m) }}" class="py-1.5 px-3 rounded-xl bg-surface-elevated text-gaston-orange text-xs font-bold border border-line-base">
                            Edit
                        </a>
                        <form action="{{ route('admin.members.toggle-status', $m) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Ubah status member ini?')" class="py-1.5 px-2 rounded-xl text-xs font-bold {{ $m->isActive() ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $m->isActive() ? 'Off' : 'On' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-content-faint text-xs">
                    Tidak ada data member yang cocok dengan pencarian.
                </div>
            @endforelse
        </div>

        @if($members->hasPages())
            <div class="px-6 py-4 border-t border-line-base">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
