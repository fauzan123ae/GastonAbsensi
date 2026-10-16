@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-content-heading">Audit Log Aktivitas</h2>
            </div>
            <p class="text-xs text-content-muted">Histori aktivitas sistem, pencatatan absensi, pembatalan, dan perubahan data.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl p-4 shadow-xl">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari deskripsi aktivitas atau target..."
                       class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
            </div>
            <div class="flex space-x-2">
                <select name="action" class="bg-surface-input border border-line-base rounded-xl px-3 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange flex-1">
                    <option value="">Semua Action</option>
                    @foreach($distinctActions as $act)
                        <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-line-base">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Table -->
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-input text-xs font-bold uppercase tracking-wider text-content-muted border-b border-line-base">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Aktor / User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($logs as $log)
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4 text-xs whitespace-nowrap text-content-muted">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="font-bold text-content-heading">{{ $log->user->name ?? 'System' }}</span>
                                @if($log->user && $log->user->member_id)
                                    <span class="block text-[10px] text-gaston-orange font-mono font-bold">{{ $log->user->member_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-surface-input text-gaston-orange border border-line-base">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-content-body">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-content-faint">
                                Belum ada catatan audit log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-line-base">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
