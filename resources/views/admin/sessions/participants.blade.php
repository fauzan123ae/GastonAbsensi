@extends('layouts.admin')

@section('title', 'Peserta Sesi - ' . $session->sport->name)

@section('content')
<div class="space-y-6" x-data="{ addModal: false, importModal: false, searchMember: '' }">

    <!-- Header & Breadcrumb -->
    <div>
        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-content-muted hover:text-content-heading mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Sesi</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-content-heading">Peserta Terdaftar (Rencana Ikut)</h2>
                <p class="text-xs text-content-muted mt-1">
                    {{ $session->sport->name }} di {{ $session->location->name }} • {{ $session->date->format('d M Y') }} ({{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB)
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="importModal = true" class="px-4 py-2.5 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-content-body font-bold text-xs transition-colors border border-line-base">
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
    <div class="bg-surface-elevated border border-line-base rounded-3xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-line-base flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-content-muted">Total Peserta Terdaftar: {{ $participants->count() }} Orang</span>
            <span class="text-xs text-gaston-orange font-semibold italic">Prinsip: Terdaftar ≠ Hadir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-content-body">
                <thead class="bg-surface-input text-xs font-bold uppercase text-content-muted border-b border-line-base">
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
                        <tr class="hover:bg-surface-elevated/50 transition-colors">
                            <td class="px-6 py-4 text-content-faint text-xs">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-surface-elevated text-gaston-orange font-bold text-xs flex items-center justify-center border border-line-base">
                                        {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-content-heading text-sm">{{ $p->user->name }}</p>
                                        <p class="text-[11px] font-mono font-bold text-gaston-orange">{{ $p->user->member_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $p->registration_source === 'whatsapp' ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-surface-input text-content-body border border-line-base' }}">
                                    {{ strtoupper($p->registration_source) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-content-muted">
                                {{ $p->registered_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.sessions.participants.destroy', [$session, $p]) }}" method="POST" onsubmit="return confirm('Keluarkan peserta ini dari rencana sesi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-400 hover:text-rose-300 px-2 py-1 rounded hover:bg-surface-elevated transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-content-faint">
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
        <div @click.away="addModal = false" class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 max-w-lg w-full space-y-5">
            <h3 class="text-lg font-black text-content-heading">Pilih Peserta untuk Sesi Ini</h3>
            <p class="text-xs text-content-muted">Pilih dari member Gaston aktif yang belum terdaftar pada sesi ini.</p>

            <form action="{{ route('admin.sessions.participants.store', $session) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="registration_source" value="manual">

                <input type="text" x-model="searchMember" placeholder="Cari nama member..."
                       class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">

                <div class="max-h-60 overflow-y-auto space-y-1.5 p-2 bg-surface-input rounded-2xl border border-line-base">
                    @forelse($availableMembers as $mbr)
                        <label x-show="'{{ strtolower($mbr->name) }}'.includes(searchMember.toLowerCase()) || '{{ strtolower($mbr->member_id) }}'.includes(searchMember.toLowerCase())"
                               class="flex items-center space-x-3 p-2 rounded-xl hover:bg-surface-elevated cursor-pointer text-xs">
                            <input type="checkbox" name="user_ids[]" value="{{ $mbr->id }}" class="rounded bg-surface-input border-line-base text-gaston-orange focus:ring-gaston-orange/20">
                            <div class="flex-1">
                                <span class="font-bold text-content-heading">{{ $mbr->name }}</span>
                                <span class="font-mono text-gaston-orange font-bold ml-2">({{ $mbr->member_id }})</span>
                            </div>
                        </label>
                    @empty
                        <p class="text-xs text-content-faint text-center py-4">Semua member aktif sudah terdaftar pada sesi ini.</p>
                    @endforelse
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
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
        <div @click.away="importModal = false" class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-content-heading">Import Peserta via CSV</h3>
            <p class="text-xs text-content-muted">Unggah berkas CSV berisi kolom <code class="text-gaston-orange font-bold">member_id</code> atau <code class="text-gaston-orange font-bold">email</code>.</p>

            <form action="{{ route('admin.sessions.participants.import', $session) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="registration_source" value="whatsapp">

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Pilih Berkas CSV *</label>
                    <input type="file" name="import_file" required accept=".csv,.txt"
                           class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-muted file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gaston-orange file:text-white hover:file:bg-orange-600">
                </div>

                <div class="p-3 rounded-2xl bg-surface-input border border-line-base text-[11px] text-content-muted space-y-1">
                    <p class="font-bold text-content-body">Contoh format baris CSV:</p>
                    <p class="font-mono text-gaston-orange">GST-00001</p>
                    <p class="font-mono text-gaston-orange">GST-00002</p>
                    <p class="font-mono text-content-muted">budi@gaston.test</p>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3">
                    <button type="button" @click="importModal = false" class="px-4 py-2 rounded-xl bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
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
