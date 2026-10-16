@extends('layouts.admin')

@section('title', 'Rekap Absensi - ' . $session->sport->name)

@section('content')
<div class="space-y-6" x-data="{ manualModal: false, closeSessionModal: false }">

    <!-- Header & Breadcrumbs -->
    <div>
        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-content-muted hover:text-content-heading mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Sesi</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                    <h2 class="text-2xl font-black text-content-heading">Rekap Absensi Sesi</h2>
                </div>
                <p class="text-xs text-content-muted">
                    {{ $session->sport->name }} di {{ $session->location->name }} • {{ $session->date->format('d M Y') }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                @if($session->canAcceptAttendance())
                    <a href="{{ route('admin.sessions.scan', $session) }}"
                       class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Buka Scanner Kamera</span>
                    </a>
                    <button @click="manualModal = true" class="px-4 py-2.5 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold text-xs transition-colors border border-line-base">
                        + Absen Manual
                    </button>
                    <button @click="closeSessionModal = true" class="px-4 py-2.5 rounded-2xl bg-surface-input border border-rose-500/30 hover:border-rose-500 text-rose-400 font-bold text-xs transition-colors">
                        Selesaikan Sesi
                    </button>
                @else
                    <span class="px-3.5 py-2 rounded-2xl bg-surface-elevated text-content-muted font-bold text-xs border border-line-base">
                        Sesi Selesai / Ditutup
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 rounded-2xl bg-surface-elevated border border-line-base">
            <span class="text-[11px] font-bold uppercase tracking-wider text-content-muted block">Terdaftar</span>
            <span class="text-2xl font-black text-content-heading mt-1 block">{{ $summary['total_registered'] }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-surface-elevated border border-line-base">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gaston-orange block">Hadir</span>
            <span class="text-2xl font-black text-gaston-orange mt-1 block">{{ $summary['present'] }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-surface-elevated border border-line-base">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400 block">Izin Disetujui</span>
            <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $summary['permission'] }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-surface-elevated border border-line-base">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Tidak Hadir</span>
            <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $summary['absent'] }}</span>
        </div>
        <div class="p-4 rounded-2xl bg-surface-elevated border border-line-base col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-content-muted block">Attendance Rate</span>
            <span class="text-2xl font-black text-gaston-orange mt-1 block">{{ $summary['attendance_rate'] }}%</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex items-center space-x-2">
        <span class="text-xs text-content-muted font-bold uppercase tracking-wider mr-2">Filter:</span>
        @foreach(['all' => 'Semua', 'hadir' => 'Hadir', 'izin' => 'Izin', 'tidak hadir' => 'Tidak Hadir', 'walk-in' => 'Walk-in'] as $key => $label)
            <a href="{{ route('admin.sessions.attendance', [$session, 'status' => $key]) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === $key ? 'bg-gradient-to-r from-gaston-orange to-orange-600 text-white shadow-lg shadow-gaston-orange/20' : 'bg-surface-elevated text-content-muted hover:text-content-heading border border-line-base' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Reconciled Attendance Table & Mobile Card View -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-input text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Peserta</th>
                        <th class="px-6 py-4">Status Rekonsiliasi</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Check-in</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($reconciled as $index => $row)
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4 text-xs text-content-faint">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($row->user->photo)
                                        <img src="{{ asset('storage/' . $row->user->photo) }}" class="w-9 h-9 rounded-2xl object-cover border border-line-base">
                                    @else
                                        <div class="w-9 h-9 rounded-2xl bg-surface-elevated text-gaston-orange font-bold text-xs flex items-center justify-center border border-line-base">
                                            {{ strtoupper(substr($row->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-content-heading text-sm">{{ $row->user->name }}</p>
                                        <p class="text-[11px] font-mono font-bold text-gaston-orange">{{ $row->user->member_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $st = strtolower($row->status);
                                    $badge = match($st) {
                                        'hadir' => 'bg-gaston-orange/15 text-gaston-orange border-gaston-orange/30',
                                        'izin' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
                                        'walk-in' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                        default => 'bg-rose-500/15 text-rose-400 border-rose-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badge }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-content-muted">{{ $row->method }}</td>
                            <td class="px-6 py-4 text-xs text-content-body">{{ $row->checked_in_at }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($row->attendance_id && in_array(strtolower($row->status), ['hadir', 'walk-in']))
                                    <form action="{{ route('admin.sessions.attendance.cancel', [$session, $row->attendance_id]) }}" method="POST" onsubmit="return confirm('Batalkan absensi member ini?')">
                                        @csrf
                                        <input type="hidden" name="cancellation_reason" value="Koreksi Admin">
                                        <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-bold px-2 py-1 rounded hover:bg-surface-elevated">
                                            Batalkan Kehadiran
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-content-faint">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-content-faint">
                                Belum ada data absensi untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-gaston-border/60">
            @forelse($reconciled as $index => $row)
                @php
                    $st = strtolower($row->status);
                    $badge = match($st) {
                        'hadir' => 'bg-gaston-orange/15 text-gaston-orange border-gaston-orange/30',
                        'izin' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
                        'walk-in' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                        default => 'bg-rose-500/15 text-rose-400 border-rose-500/30',
                    };
                @endphp
                <div class="p-4 space-y-2.5 hover:bg-surface-elevated/40 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-3">
                            @if($row->user->photo)
                                <img src="{{ asset('storage/' . $row->user->photo) }}" class="w-10 h-10 rounded-2xl object-cover border border-line-base">
                            @else
                                <div class="w-10 h-10 rounded-2xl bg-surface-elevated text-gaston-orange font-bold text-xs flex items-center justify-center border border-line-base">
                                    {{ strtoupper(substr($row->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-content-heading text-sm">{{ $row->user->name }}</p>
                                <p class="text-[10px] font-mono font-bold text-gaston-orange">{{ $row->user->member_id }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge }}">
                            {{ $row->status }}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-surface-input/70 border border-line-base/50 text-[11px] flex items-center justify-between text-content-body">
                        <div>
                            <span class="text-[10px] text-content-faint block uppercase font-semibold">Metode & Check-in</span>
                            <span class="font-mono text-content-muted">{{ $row->method }}</span> &bull; 
                            <span class="text-white">{{ $row->checked_in_at ?: '-' }}</span>
                        </div>
                        @if($row->attendance_id && in_array(strtolower($row->status), ['hadir', 'walk-in']))
                            <form action="{{ route('admin.sessions.attendance.cancel', [$session, $row->attendance_id]) }}" method="POST" onsubmit="return confirm('Batalkan absensi member ini?')">
                                @csrf
                                <input type="hidden" name="cancellation_reason" value="Koreksi Admin">
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10px] font-bold hover:bg-rose-500/25">
                                    Batal
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-content-faint text-xs">
                    Belum ada data absensi untuk filter ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Absen Manual -->
    <div x-show="manualModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="manualModal = false" class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-content-heading">Absensi Manual (Kamera Error)</h3>
            <p class="text-xs text-content-muted">Masukkan Member ID atau Email member untuk memverifikasi absensi.</p>

            <form action="{{ route('admin.sessions.attendance.manual', $session) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Member ID atau Email *</label>
                    <input type="text" name="identifier" required placeholder="Contoh: GST-00001 atau andi@gaston.test"
                           class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" placeholder="Contoh: Kamera HP rusak"
                           class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>

                <div class="p-3 rounded-xl bg-surface-input border border-line-base/80 flex items-center space-x-2.5">
                    <input type="checkbox" name="allow_walk_in" value="1" id="allow_walk_in" class="rounded bg-surface-page border-line-base text-gaston-orange focus:ring-0">
                    <label for="allow_walk_in" class="text-xs text-content-body cursor-pointer">
                        Catat sebagai <strong>Walk-in</strong> jika member belum terdaftar
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="manualModal = false" class="px-4 py-2 rounded-xl bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25">
                        Simpan Absensi Manual
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tutup Sesi -->
    <div x-show="closeSessionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="closeSessionModal = false" class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-content-heading">Selesaikan & Kunci Sesi?</h3>
            <p class="text-xs text-content-body leading-relaxed">
                Menyelesaikan sesi akan merekonsiliasi seluruh peserta menjadi status akhir (<span class="text-gaston-orange font-bold">Hadir</span>, <span class="text-blue-400 font-bold">Izin</span>, atau <span class="text-rose-400 font-bold">Tidak Hadir</span>). Sesi tidak akan menerima absensi biasa lagi.
            </p>

            <form action="{{ route('admin.sessions.close', $session) }}" method="POST" class="pt-3 flex items-center justify-end space-x-3">
                @csrf
                <button type="button" @click="closeSessionModal = false" class="px-4 py-2 rounded-xl bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-black text-xs shadow-lg">
                    Kunci & Selesaikan
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
