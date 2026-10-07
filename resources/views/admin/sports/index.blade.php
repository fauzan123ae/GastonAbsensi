@extends('layouts.admin')

@section('title', 'Cabang Olahraga')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, isEdit: false, formAction: '{{ route('admin.sports.store') }}', name: '', description: '', status: 'active', method: 'POST' }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-white">Cabang Olahraga</h2>
            </div>
            <p class="text-xs text-slate-400">Daftar olahraga seperti Futsal, Basket, Badminton, Tenis, dll.</p>
        </div>
        <div>
            <button @click="isEdit = false; formAction = '{{ route('admin.sports.store') }}'; name = ''; description = ''; status = 'active'; method = 'POST'; modalOpen = true"
                    class="inline-flex items-center space-x-2 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-gaston-orange/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Olahraga</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($sports as $sport)
            <div class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 hover:border-gaston-orange/40 transition-colors flex flex-col justify-between space-y-4 shadow-xl">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-white">{{ $sport->name }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $sport->status === 'active' ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $sport->status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">{{ $sport->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="mt-4 pt-3 border-t border-gaston-border text-xs text-slate-400">
                        Total Sesi Terlaksana: <span class="font-black text-gaston-orange">{{ $sport->sessions_count }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button @click="isEdit = true; formAction = '{{ route('admin.sports.update', $sport) }}'; name = '{{ addslashes($sport->name) }}'; description = '{{ addslashes($sport->description) }}'; status = '{{ $sport->status }}'; method = 'PUT'; modalOpen = true"
                            class="px-3.5 py-1.5 rounded-xl bg-[#0d1017] hover:bg-slate-800 text-slate-300 text-xs font-bold border border-gaston-border transition-colors">
                        Edit
                    </button>
                    @if($sport->sessions_count == 0)
                        <form action="{{ route('admin.sports.destroy', $sport) }}" method="POST" onsubmit="return confirm('Hapus cabang olahraga ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-bold border border-rose-500/20 transition-colors">
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 p-8 text-center bg-[#141822] border border-gaston-border rounded-3xl text-slate-500 text-xs">
                Belum ada data cabang olahraga.
            </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-white" x-text="isEdit ? 'Edit Cabang Olahraga' : 'Tambah Cabang Olahraga'"></h3>
            
            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Nama Olahraga *</label>
                    <input type="text" name="name" x-model="name" required placeholder="Contoh: Futsal"
                           class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" x-model="description" rows="3" placeholder="Deskripsi atau aturan umum..."
                              class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-gaston-orange"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1">Status</label>
                    <select name="status" x-model="status"
                            class="w-full bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-gaston-orange">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-[#1b212c] text-slate-300 text-xs font-bold border border-gaston-border">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
