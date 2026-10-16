@extends('layouts.admin')

@section('title', 'Manajemen Pengelola & Admin')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#e50914] to-red-800 flex items-center justify-center text-white shadow-lg shadow-[#e50914]/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-content-heading">Kelola Admin & Master</h2>
            </div>
            <p class="text-xs text-content-muted">Hak istimewa Master Admin: Tambah, edit peran, dan kelola akses akun admin sistem Gaston.</p>
        </div>
        <div>
            <a href="{{ route('admin.admins.create') }}"
               class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#e50914] to-red-700 hover:from-red-600 hover:to-red-800 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-[#e50914]/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Tambah Admin Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl p-4">
        <form action="{{ route('admin.admins.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama admin, email, atau kontak..."
                       class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914]">
            </div>
            <div>
                <select name="role" class="w-full bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-[#e50914]">
                    <option value="">Semua Peran</option>
                    <option value="master_admin" {{ $role === 'master_admin' ? 'selected' : '' }}>👑 Master Admin</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>🛡️ Admin Biasa</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <select name="status" class="bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-[#e50914] flex-1">
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

    <!-- Admin List (Desktop Table + Mobile Cards) -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-elevated text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">Akun Pengelola</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line-base/70">
                    @forelse($admins as $a)
                        <tr class="hover:bg-surface-elevated/50 transition-colors {{ $a->id === auth()->id() ? 'bg-red-950/10' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-surface-elevated border border-line-strong flex-shrink-0 flex items-center justify-center text-xs font-bold text-content-heading shadow-sm">
                                        @if($a->photo)
                                            <img src="{{ asset('storage/' . $a->photo) }}" alt="{{ $a->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-mono">{{ strtoupper(substr($a->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-content-heading text-sm truncate">{{ $a->name }}</span>
                                            @if($a->id === auth()->id())
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/40">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-content-muted block truncate">{{ $a->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($a->role === 'master_admin')
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl text-xs font-black bg-gradient-to-r from-red-500/20 to-amber-500/20 text-rose-300 border border-rose-500/40">
                                        <span>👑</span>
                                        <span>Master Admin</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                        <span>🛡️</span>
                                        <span>Admin Biasa</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-content-muted whitespace-nowrap font-mono">
                                {{ $a->phone ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider {{ $a->isActive() ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                    {{ $a->isActive() ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-2">
                                    @if($a->id !== auth()->id())
                                        <form action="{{ route('admin.admins.toggle-status', $a) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    title="{{ $a->isActive() ? 'Nonaktifkan Admin' : 'Aktifkan Admin' }}"
                                                    class="p-2 rounded-xl {{ $a->isActive() ? 'bg-rose-500/15 text-rose-400 hover:bg-rose-500/25 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30' }} text-xs font-bold transition-colors">
                                                {{ $a->isActive() ? 'Suspend' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.admins.edit', $a) }}"
                                       class="p-2 rounded-xl bg-surface-elevated text-content-body hover:text-content-heading hover:bg-surface-elevated text-xs font-bold transition-colors border border-line-base"
                                       title="Edit Admin">
                                        Edit
                                    </a>

                                    @if($a->id !== auth()->id())
                                        <form action="{{ route('admin.admins.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $a->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Admin"
                                                    class="p-2 rounded-xl bg-red-950/40 text-red-400 hover:bg-red-900/60 text-xs font-bold transition-colors border border-red-800/40">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-content-faint text-xs">
                                Tidak ada data pengelola yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (block on small screens, hidden on md) -->
        <div class="block md:hidden divide-y divide-line-base/70">
            @forelse($admins as $a)
                <div class="p-4 space-y-3 hover:bg-surface-elevated/30 transition-colors {{ $a->id === auth()->id() ? 'bg-red-950/10' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl overflow-hidden bg-surface-elevated border border-line-strong flex-shrink-0 flex items-center justify-center text-xs font-bold text-content-heading shadow-sm">
                                @if($a->photo)
                                    <img src="{{ asset('storage/' . $a->photo) }}" alt="{{ $a->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-mono">{{ strtoupper(substr($a->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-1.5">
                                    <h4 class="text-sm font-bold text-content-heading truncate">{{ $a->name }}</h4>
                                    @if($a->id === auth()->id())
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/40">Anda</span>
                                    @endif
                                </div>
                                <p class="text-xs text-content-muted truncate">{{ $a->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $a->isActive() ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                            {{ $a->isActive() ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-line-base/40">
                        <div class="flex items-center space-x-2">
                            @if($a->role === 'master_admin')
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-gradient-to-r from-red-500/20 to-amber-500/20 text-rose-300 border border-rose-500/40">
                                    👑 Master Admin
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                    🛡️ Admin Biasa
                                </span>
                            @endif
                            <span class="text-[10px] text-content-muted font-mono">{{ $a->phone ?: 'No Phone' }}</span>
                        </div>

                        <div class="flex items-center space-x-1.5">
                            @if($a->id !== auth()->id())
                                <form action="{{ route('admin.admins.toggle-status', $a) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-xl {{ $a->isActive() ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' }} text-[11px] font-bold">
                                        {{ $a->isActive() ? 'Suspend' : 'Aktif' }}
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.admins.edit', $a) }}"
                               class="px-2.5 py-1 rounded-xl bg-surface-elevated text-content-body text-[11px] font-bold border border-line-base">
                                Edit
                            </a>

                            @if($a->id !== auth()->id())
                                <form action="{{ route('admin.admins.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun admin {{ $a->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-xl bg-red-950/40 text-red-400 text-[11px] font-bold border border-red-800/40">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-content-faint text-xs">
                    Tidak ada data pengelola yang cocok dengan filter pencarian.
                </div>
            @endforelse
        </div>

        @if($admins->hasPages())
            <div class="p-4 bg-surface-elevated border-t border-line-base">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
