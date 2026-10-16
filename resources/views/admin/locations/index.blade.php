@extends('layouts.admin')

@section('title', 'GOR & Lokasi Olahraga')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, isEdit: false, formAction: '{{ route('admin.locations.store') }}', name: '', address: '', description: '', status: 'active', method: 'POST' }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
                <h2 class="text-2xl font-black text-content-heading">GOR & Lokasi Olahraga</h2>
            </div>
            <p class="text-xs text-content-muted">Daftar tempat pelaksanaan kegiatan (GOR Manahan, GOR Klaten, dsb).</p>
        </div>
        <div>
            <button @click="isEdit = false; formAction = '{{ route('admin.locations.store') }}'; name = ''; address = ''; description = ''; status = 'active'; method = 'POST'; modalOpen = true"
                    class="inline-flex items-center space-x-2 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold px-4 py-2.5 rounded-2xl text-xs transition-all shadow-lg shadow-gaston-orange/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah GOR</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($locations as $location)
            <div class="bg-surface-elevated border border-line-base rounded-[32px] p-6 hover:border-gaston-orange/40 transition-colors flex flex-col justify-between space-y-4 shadow-xl">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-content-heading">{{ $location->name }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $location->status === 'active' ? 'bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30' : 'bg-surface-elevated text-content-muted' }}">
                            {{ $location->status }}
                        </span>
                    </div>
                    <p class="text-xs text-content-muted mt-2 flex items-start space-x-1.5">
                        <svg class="w-4 h-4 text-gaston-orange flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $location->address ?? 'Alamat belum diatur' }}</span>
                    </p>
                    <div class="mt-4 pt-3 border-t border-line-base text-xs text-content-muted">
                        Total Sesi: <span class="font-black text-gaston-orange">{{ $location->sessions_count }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button @click="isEdit = true; formAction = '{{ route('admin.locations.update', $location) }}'; name = '{{ addslashes($location->name) }}'; address = '{{ addslashes($location->address) }}'; description = '{{ addslashes($location->description) }}'; status = '{{ $location->status }}'; method = 'PUT'; modalOpen = true"
                            class="px-3.5 py-1.5 rounded-xl bg-surface-input hover:bg-surface-elevated text-content-body text-xs font-bold border border-line-base transition-colors">
                        Edit
                    </button>
                    @if($location->sessions_count == 0)
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Hapus lokasi GOR ini?')">
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
            <div class="col-span-3 p-8 text-center bg-surface-elevated border border-line-base rounded-3xl text-content-faint text-xs">
                Belum ada data GOR/lokasi.
            </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 max-w-md w-full space-y-5">
            <h3 class="text-lg font-black text-content-heading" x-text="isEdit ? 'Edit Lokasi GOR' : 'Tambah Lokasi GOR'"></h3>
            
            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Nama GOR / Lokasi *</label>
                    <input type="text" name="name" x-model="name" required placeholder="Contoh: GOR Manahan"
                           class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Alamat Lengkap</label>
                    <input type="text" name="address" x-model="address" placeholder="Contoh: Jl. Adi Sucipto, Solo"
                           class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Deskripsi Tambahan</label>
                    <textarea name="description" x-model="description" rows="2" placeholder="Catatan fasilitas lapangan dsb..."
                              class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2 text-xs text-content-heading focus:outline-none focus:border-gaston-orange"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-content-body mb-1">Status</label>
                    <select name="status" x-model="status"
                            class="w-full bg-surface-input border border-line-base rounded-xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-gaston-orange">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-surface-elevated text-content-body text-xs font-bold border border-line-base">
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
