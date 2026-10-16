@extends('layouts.admin')

@section('title', 'Kelola Permohonan Izin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-content-heading">Permohonan Izin Member</h2>
            </div>
            <p class="text-xs text-content-muted">Tinjau permohonan izin tidak hadir dari peserta sesi olahraga.</p>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="flex items-center space-x-2">
        <span class="text-xs text-content-muted font-bold uppercase tracking-wider mr-2">Status:</span>
        @foreach(['all' => 'Semua', 'pending' => 'Menunggu Review', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
            <a href="{{ route('admin.permissions.index', ['status' => $key]) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === $key ? 'bg-gradient-to-r from-gaston-orange to-orange-600 text-white shadow-lg shadow-gaston-orange/20' : 'bg-surface-elevated text-content-muted hover:text-content-heading border border-line-base' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Permissions Table & Mobile Cards -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-input text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Sesi & Tanggal</th>
                        <th class="px-6 py-4">Alasan & Bukti</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($permissions as $perm)
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-content-heading">{{ $perm->user->name }}</p>
                                <p class="text-[11px] font-mono font-bold text-gaston-orange">{{ $perm->user->member_id }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-content-heading">{{ $perm->session->sport->name }}</p>
                                <p class="text-xs text-content-muted mt-0.5">{{ $perm->session->date->format('d M Y') }} • {{ $perm->session->location->name }}</p>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-content-body">{{ $perm->reason }}</p>
                                @if($perm->proof)
                                    <a href="{{ route('permissions.proof', $perm) }}" target="_blank" class="inline-flex items-center space-x-1 text-[11px] text-gaston-orange hover:underline mt-1 font-bold">
                                        <span>📎 Lihat Bukti Foto / Dokumen</span>
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($perm->status) {
                                        'approved' => 'bg-gaston-orange/15 text-gaston-orange border-gaston-orange/30',
                                        'rejected' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                                        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                    {{ strtoupper($perm->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($perm->status === 'pending')
                                    <div class="flex items-center justify-end space-x-2" x-data="{ rejectOpen: false, reviewNote: '' }">
                                        <form action="{{ route('admin.permissions.update', $perm) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-md transition-all">
                                                Setujui
                                            </button>
                                        </form>
                                        <button type="button" @click="rejectOpen = true" class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs border border-rose-500/20 transition-colors">
                                            Tolak
                                        </button>

                                        <!-- Reject Modal -->
                                        <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm text-left">
                                            <div @click.away="rejectOpen = false" class="bg-surface-elevated border border-line-base rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
                                                <h3 class="text-base font-bold text-content-heading">Tolak Permohonan Izin</h3>
                                                <p class="text-xs text-content-muted">Masukkan alasan penolakan yang akan ditampilkan kepada member {{ $perm->user->name }}:</p>
                                                <form action="{{ route('admin.permissions.update', $perm) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <textarea name="review_note" rows="3" required placeholder="Contoh: Dokumen bukti tidak jelas / kuota izin habis" class="w-full bg-surface-input border border-line-base rounded-xl p-3 text-xs text-content-body focus:outline-none focus:border-gaston-orange"></textarea>
                                                    <div class="flex items-center justify-end space-x-2 pt-2">
                                                        <button type="button" @click="rejectOpen = false" class="px-3 py-1.5 rounded-xl bg-surface-elevated text-content-body text-xs font-bold">Batal</button>
                                                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs">Konfirmasi Tolak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-content-muted">
                                        <p class="text-content-faint">Direview oleh {{ $perm->reviewer->name ?? 'Admin' }}</p>
                                        @if($perm->review_note)
                                            <p class="text-[11px] text-rose-400 mt-1 italic">"{{ $perm->review_note }}"</p>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-content-faint">
                                Tidak ada permohonan izin dalam filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($permissions as $perm)
                @php
                    $badgeClass = match($perm->status) {
                        'approved' => 'bg-gaston-orange/15 text-gaston-orange border-gaston-orange/30',
                        'rejected' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                    };
                @endphp
                <div class="p-4 space-y-3 hover:bg-surface-elevated/40 transition-colors" x-data="{ rejectOpen: false }">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold text-content-heading text-sm">{{ $perm->user->name }}</p>
                            <p class="text-[10px] font-mono font-bold text-gaston-orange">{{ $perm->user->member_id }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                            {{ strtoupper($perm->status) }}
                        </span>
                    </div>

                    <div class="p-3 rounded-2xl bg-surface-input/70 border border-line-base/50 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-content-body">{{ $perm->session->sport->name }}</span>
                            <span class="text-content-muted text-[10px]">{{ $perm->session->date->format('d M Y') }}</span>
                        </div>
                        <p class="text-[11px] text-content-muted">{{ $perm->session->location->name }}</p>
                        <div class="pt-1.5 border-t border-line-base/40">
                            <span class="text-[10px] text-content-faint block uppercase font-semibold">Alasan:</span>
                            <p class="text-xs text-content-body mt-0.5 leading-relaxed">{{ $perm->reason }}</p>
                        </div>
                        @if($perm->proof)
                            <div class="pt-1">
                                <a href="{{ route('permissions.proof', $perm) }}" target="_blank" class="inline-flex items-center space-x-1 text-[11px] text-gaston-orange hover:underline font-bold">
                                    <span>📎 Bukti Foto / Dokumen</span>
                                </a>
                            </div>
                        @endif
                        @if($perm->review_note)
                            <div class="pt-1.5 border-t border-rose-500/20 text-rose-300">
                                <span class="text-[10px] text-rose-400 block uppercase font-semibold">Catatan Penolakan:</span>
                                <p class="text-xs mt-0.5 leading-relaxed italic">{{ $perm->review_note }}</p>
                            </div>
                        @endif
                    </div>

                    @if($perm->status === 'pending')
                        <div class="flex items-center gap-2 pt-1">
                            <form action="{{ route('admin.permissions.update', $perm) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="w-full py-2 rounded-xl bg-gradient-to-r from-gaston-orange to-orange-600 text-white font-extrabold text-xs shadow-md transition-all text-center">
                                    Setujui
                                </button>
                            </form>
                            <button type="button" @click="rejectOpen = true" class="flex-1 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs border border-rose-500/20 transition-colors text-center">
                                Tolak
                            </button>
                        </div>

                        <!-- Mobile Reject Modal -->
                        <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm text-left">
                            <div @click.away="rejectOpen = false" class="bg-surface-elevated border border-line-base rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
                                <h3 class="text-base font-bold text-content-heading">Tolak Permohonan Izin</h3>
                                <p class="text-xs text-content-muted">Masukkan alasan penolakan:</p>
                                <form action="{{ route('admin.permissions.update', $perm) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="review_note" rows="3" required placeholder="Alasan penolakan..." class="w-full bg-surface-input border border-line-base rounded-xl p-3 text-xs text-content-body focus:outline-none focus:border-gaston-orange"></textarea>
                                    <div class="flex items-center justify-end space-x-2 pt-2">
                                        <button type="button" @click="rejectOpen = false" class="px-3 py-1.5 rounded-xl bg-surface-elevated text-content-body text-xs font-bold">Batal</button>
                                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-rose-600 text-white font-bold text-xs">Konfirmasi Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="text-[10px] text-content-faint text-right">Direview oleh {{ $perm->reviewer->name ?? 'Admin' }}</p>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-content-faint text-xs">
                    Tidak ada permohonan izin dalam filter ini.
                </div>
            @endforelse
        </div>

        @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-line-base">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
