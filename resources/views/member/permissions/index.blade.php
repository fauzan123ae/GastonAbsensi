@extends('layouts.member')

@section('title', 'Pengajuan Izin')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-wide">Pengajuan Izin</h2>
            <p class="text-xs text-slate-400 mt-1">Ajukan permohonan izin berhalangan hadir jika tidak dapat mengikuti jadwal olahraga.</p>
        </div>
        <div>
            @if($eligibleSessions->isNotEmpty())
                <button @click="createModal = true" class="inline-flex items-center space-x-2 bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold px-5 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-[#e50914]/25 gaston-glow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Ajukan Izin Baru</span>
                </button>
            @else
                <button disabled class="px-4 py-2.5 rounded-2xl bg-[#11141c] border border-[#1f2533] text-slate-500 font-semibold text-xs cursor-not-allowed">
                    Tidak ada sesi mendatang untuk diajukan izin
                </button>
            @endif
        </div>
    </div>

    <!-- History Table & Mobile Cards -->
    <div class="bg-[#11141c] border border-[#1f2533] rounded-3xl overflow-hidden shadow-2xl">
        <!-- Desktop Table (md and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-[11px] font-black uppercase tracking-wider text-slate-400 border-b border-[#1f2533]">
                    <tr>
                        <th class="px-6 py-4">Sesi Olahraga</th>
                        <th class="px-6 py-4">Alasan Izin</th>
                        <th class="px-6 py-4">Dokumen Bukti</th>
                        <th class="px-6 py-4 text-right">Status Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1f2533]/60">
                    @forelse($permissions as $perm)
                        <tr class="hover:bg-[#151922] transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-white">{{ $perm->session->sport->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $perm->session->date->translatedFormat('d M Y') }} • {{ $perm->session->location->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-300 max-w-xs leading-relaxed">
                                <p>{{ $perm->reason }}</p>
                                @if($perm->review_note)
                                    <div class="mt-2 p-2 rounded-xl bg-rose-950/40 border border-rose-900/40 text-rose-300 text-[11px]">
                                        <span class="font-bold block uppercase text-[10px] text-rose-400">Catatan Admin:</span>
                                        <p class="mt-0.5 italic">{{ $perm->review_note }}</p>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($perm->proof)
                                    <a href="{{ route('permissions.proof', $perm) }}" target="_blank" class="inline-flex items-center space-x-1.5 text-[#e50914] hover:underline font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Lihat Berkas</span>
                                    </a>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $badge = match($perm->status) {
                                        'approved' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                        'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                        default => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                    };
                                @endphp
                                <div class="flex items-center justify-end space-x-2">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badge }}">
                                        {{ strtoupper($perm->status) }}
                                    </span>
                                    @if($perm->status === 'pending')
                                        <form action="{{ route('member.permissions.destroy', $perm) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 text-[11px] font-bold border border-slate-700 hover:border-rose-800 transition-colors">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <div class="space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-bold text-slate-400">Belum Ada Pengajuan Izin</p>
                                    <p class="text-xs">Daftar permohonan izin Anda akan tercatat di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="block md:hidden divide-y divide-[#1f2533]/60">
            @forelse($permissions as $perm)
                @php
                    $badge = match($perm->status) {
                        'approved' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                        'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                        default => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                    };
                @endphp
                <div class="p-4 space-y-2.5 hover:bg-[#151922] transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-red-950/60 text-[#e50914] border border-red-900/40">
                                {{ $perm->session->sport->name }}
                            </span>
                            <h4 class="text-xs font-bold text-white mt-1">{{ $perm->session->date->translatedFormat('d M Y') }}</h4>
                            <p class="text-[10px] text-slate-400">{{ $perm->session->location->name }}</p>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $badge }}">
                                {{ strtoupper($perm->status) }}
                            </span>
                            @if($perm->status === 'pending')
                                <form action="{{ route('member.permissions.destroy', $perm) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-0.5 rounded bg-slate-800 text-rose-400 text-[10px] font-bold border border-slate-700">
                                        Batal
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 rounded-2xl bg-[#090c12]/90 border border-[#1f2533] text-xs space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500 uppercase block">Alasan Izin:</span>
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $perm->reason }}</p>
                        @if($perm->review_note)
                            <div class="pt-1.5 border-t border-rose-900/40 text-rose-300">
                                <span class="text-[10px] text-rose-400 block uppercase font-semibold">Catatan Admin:</span>
                                <p class="text-xs mt-0.5 leading-relaxed italic">{{ $perm->review_note }}</p>
                            </div>
                        @endif
                        @if($perm->proof)
                            <div class="pt-1.5 border-t border-[#1f2533]/60">
                                <a href="{{ route('permissions.proof', $perm) }}" target="_blank" class="inline-flex items-center space-x-1.5 text-xs text-[#e50914] font-bold hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    <span>Lihat Berkas Bukti</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs space-y-2">
                    <svg class="w-8 h-8 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="font-bold text-slate-400">Belum Ada Pengajuan Izin</p>
                </div>
            @endforelse
        </div>

        @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-[#1f2533] bg-[#0d1017]/40">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Ajukan Izin -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="createModal = false" class="bg-[#11141c] border border-[#1f2533] rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-5 shadow-2xl relative">
            <div>
                <h3 class="text-lg font-black text-white tracking-wide">Ajukan Izin Tidak Hadir</h3>
                <p class="text-xs text-slate-400 mt-1">Izin yang disetujui admin tidak akan dihitung sebagai ketidakhadiran (absent).</p>
            </div>

            <form action="{{ route('member.permissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Pilih Sesi Olahraga *</label>
                    <select name="session_id" required
                            class="w-full bg-gaston-dark border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange focus:ring-1 focus:ring-gaston-orange transition-all">
                        @foreach($eligibleSessions as $s)
                            <option value="{{ $s->id }}">{{ $s->sport->name }} - {{ $s->location->name }} ({{ $s->date->translatedFormat('d M Y') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Alasan Berhalangan *</label>
                    <textarea name="reason" required rows="3" placeholder="Contoh: Sedang sakit demam, tugas luar kota..."
                              class="w-full bg-gaston-dark border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange focus:ring-1 focus:ring-gaston-orange transition-all resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Bukti Foto / Surat Dokter (Opsional)</label>
                    <input type="file" name="proof" accept="image/*,.pdf"
                           class="w-full bg-gaston-dark border border-gaston-border rounded-xl px-4 py-2 text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gaston-orange file:text-white hover:file:bg-orange-500 cursor-pointer">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="createModal = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-gaston-orange to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-bold text-xs shadow-lg shadow-gaston-orange/20 gaston-glow transition-all">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
