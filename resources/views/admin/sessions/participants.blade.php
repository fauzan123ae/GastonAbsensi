@extends('layouts.admin')

@section('title', 'Peserta Sesi - ' . $session->sport->name)

@section('content')
<div class="space-y-6" x-data="{ addModal: false, importModal: false, searchMember: '' }">

    <!-- Header & Breadcrumb -->
    <div>
        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Sesi</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-white">Peserta Terdaftar (Rencana Ikut)</h2>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $session->sport->name }} di {{ $session->location->name }} • {{ $session->date->format('d M Y') }} ({{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB)
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="importModal = true" class="px-4 py-2.5 rounded-2xl bg-[#1b212c] hover:bg-slate-800 text-slate-200 font-bold text-xs transition-colors border border-gaston-border">
                    📥 Import CSV/WA
                </button>
                <button @click="addModal = true"
                        class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all">
                    + Tambah Peserta
                </button>
            </div>
        </div>
    </div>

    @if(session('import_errors'))
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs space-y-1">
            <p class="font-bold">Beberapa data tidak dapat diimpor:</p>
            <ul class="list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Participants Table -->
    <div class="bg-[#141822] border border-gaston-border rounded-3xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-gaston-border flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Peserta Terdaftar: {{ $participants->count() }} Orang</span>
            <span class="text-xs text-gaston-orange font-semibold italic">Prinsip: Terdaftar ≠ Hadir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-[#0d1017] text-xs font-bold uppercase text-slate-400 border-b border-gaston-border">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Member</th>
                        <th class="px-6 py-3">Sumber Registrasi</th>
                        <th class="px-6 py-3">Waktu Terdaftar</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gaston-border/70">
                    @forelse($participants as $index => $p)
                        <tr class="hover:bg-[#1a202c]/50 transition-colors">
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#1c2230] text-gaston-orange font-bold text-xs flex items-center justify-center border border-gaston-border">
                                        {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-sm">{{ $p->user->name }}</p>
                                        <p class="text-[11px] font-mono font-bold text-gaston-orange">{{ $p->user->member_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $p->registration_source === 'whatsapp' ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-[#0d1017] text-slate-300 border border-gaston-border' }}">
                                    {{ strtoupper($p->registration_source) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $p->registered_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.sessions.participants.destroy', [$session, $p]) }}" method="POST" onsubmit="return confirm('Keluarkan peserta ini dari rencana sesi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-400 hover:text-rose-300 px-2 py-1 rounded hover:bg-slate-800 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada peserta terdaftar. Tambahkan member secara manual atau import CSV.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Peserta Manual / Multiple Select -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 sm:p-8 max-w-lg w-full space-y-5">
            <h3 class="text-lg font-black text-white">Pilih Peserta untuk Sesi Ini</h3>
            <p class="text-xs text-slate-400">Pilih dari member Gaston aktif yang belum terdaftar pada sesi ini.</p>

            <form action="{{ route('admin.sessions.participants.store', $session) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="registration_source" value="manual">

                <input type="text" x-model="searchMember" placeholder="Cari nama member..."
                       class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange">

                <div class="max-h-60 overflow-y-auto space-y-1.5 p-2 bg-[#0d1017] rounded-2xl border border-gaston-border">
                    @forelse($availableMembers as $mbr)
                        <label x-show="'{{ strtolower($mbr->name) }}'.includes(searchMember.toLowerCase()) || '{{ strtolower($mbr->member_id) }}'.includes(searchMember.toLowerCase())"
                               class="flex items-center space-x-3 p-2 rounded-xl hover:bg-[#1a202c] cursor-pointer text-xs">
                            <input type="checkbox" name="user_ids[]" value="{{ $mbr->id }}" class="rounded bg-[#0d1017] border-gaston-border text-gaston-orange focus:ring-gaston-orange/20">
                            <div class="flex-1">
                                <span class="font-bold text-white">{{ $mbr->name }}</span>
                                <span class="font-mono text-gaston-orange font-bold ml-2">({{ $mbr->member_id }})</span>
                            </div>
                        </label>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-4">Semua member aktif sudah terdaftar pada sesi ini.</p>
                    @endforelse
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-[#1b212c] text-slate-300 text-xs font-bold border border-gaston-border">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25">
                        Tambahkan Peserta Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div x-show="importModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="importModal = false" class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-white">Import Peserta via CSV</h3>
            <p class="text-xs text-slate-400">Unggah berkas CSV berisi kolom <code class="text-gaston-orange font-bold">member_id</code> atau <code class="text-gaston-orange font-bold">email</code>.</p>

            <form action="{{ route('admin.sessions.participants.import', $session) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="registration_source" value="whatsapp">

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Pilih Berkas CSV *</label>
                    <input type="file" name="import_file" required accept=".csv,.txt"
                           class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gaston-orange file:text-white hover:file:bg-orange-600">
                </div>

                <div class="p-3 rounded-2xl bg-[#0d1017] border border-gaston-border text-[11px] text-slate-400 space-y-1">
                    <p class="font-bold text-slate-300">Contoh format baris CSV:</p>
                    <p class="font-mono text-gaston-orange">GST-00001</p>
                    <p class="font-mono text-gaston-orange">GST-00002</p>
                    <p class="font-mono text-slate-400">budi@gaston.test</p>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="importModal = false" class="px-4 py-2 rounded-xl bg-[#1b212c] text-slate-300 text-xs font-bold border border-gaston-border">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25">
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
