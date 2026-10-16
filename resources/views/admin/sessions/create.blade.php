@extends('layouts.admin')

@section('title', 'Buat Sesi Olahraga Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-content-muted hover:text-content-heading mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Sesi</span>
        </a>
        <h2 class="text-2xl font-black text-content-heading">Buat Sesi Olahraga Baru</h2>
        <p class="text-xs text-content-muted mt-1">Tentukan cabang olahraga, lokasi GOR, dan jadwal waktu.</p>
    </div>

    <div class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 shadow-xl">
        <form action="{{ route('admin.sessions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sport_id" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Cabang Olahraga *</label>
                    <select name="sport_id" id="sport_id" required
                            class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                        <option value="">Pilih Olahraga...</option>
                        @foreach($sports as $s)
                            <option value="{{ $s->id }}" {{ old('sport_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Lokasi / GOR *</label>
                    <select name="location_id" id="location_id" required
                            class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                        <option value="">Pilih Lokasi GOR...</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ old('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="date" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Tanggal Pelaksanaan *</label>
                <div class="relative">
                    <input type="text" name="date" id="date" required value="{{ old('date', now()->toDateString()) }}"
                           placeholder="Pilih Tanggal..."
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-12 pr-4 py-3 text-sm text-white focus:outline-none focus:border-gaston-orange focus:ring-1 focus:ring-gaston-orange transition-all cursor-pointer">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gaston-orange">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Jam Mulai *</label>
                    <input type="time" name="start_time" id="start_time" required value="{{ old('start_time', '19:00') }}"
                           class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>
                <div>
                    <label for="end_time" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Jam Selesai *</label>
                    <input type="time" name="end_time" id="end_time" required value="{{ old('end_time', '21:00') }}"
                           class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>
            </div>

            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Status Sesi *</label>
                <select name="status" id="status" required
                        class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                    <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>Open (Buka Pendaftaran / Siap Absen)</option>
                    <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing (Sedang Berlangsung)</option>
                </select>
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Bawa jersey warna gelap, bola disediakan..."
                          class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.sessions.index') }}" class="px-5 py-3 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-content-body text-xs font-bold transition-colors border border-line-base">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all">
                    Simpan Sesi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "l, d F Y",
            locale: "id",
            disableMobile: true
        });
    });
</script>
@endpush
