@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-white">Audit Log Aktivitas</h2>
            </div>
            <p class="text-xs text-slate-400">Histori aktivitas sistem, pencatatan absensi, pembatalan, dan perubahan data.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl p-4 shadow-xl">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari deskripsi aktivitas atau target..."
                       class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange">
            </div>
            <div class="flex space-x-2">
                <select name="action" class="bg-[#0d1017] border border-gaston-border rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange flex-1">
                    <option value="">Semua Action</option>
                    @foreach($distinctActions as $act)
                        <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-[#1b212c] hover:bg-slate-800 text-slate-200 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-gaston-border">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Table -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-gaston-border">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Aktor / User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#1a202c]/50 transition-colors">
                            <td class="px-6 py-4 text-xs whitespace-nowrap text-slate-400">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="font-bold text-white">{{ $log->user->name ?? 'System' }}</span>
                                @if($log->user && $log->user->member_id)
                                    <span class="block text-[10px] text-gaston-orange font-mono font-bold">{{ $log->user->member_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-[#0d1017] text-gaston-orange border border-gaston-border">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-200">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                Belum ada catatan audit log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gaston-border">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
