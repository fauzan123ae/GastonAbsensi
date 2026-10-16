@extends('layouts.admin')

@section('title', 'Edit Member - ' . $member->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.members.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-content-muted hover:text-content-heading mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Member</span>
        </a>
        <h2 class="text-2xl font-black text-content-heading">Edit Data Member</h2>
        <p class="text-xs text-content-muted mt-1">Perbarui data kontak atau ganti foto member.</p>
    </div>

    <div class="bg-surface-elevated border border-line-base rounded-[32px] p-6 sm:p-8 shadow-xl">
        <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="p-4 rounded-2xl bg-surface-input border border-line-base flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-content-muted font-bold uppercase tracking-wider block">Member ID (Permanen)</span>
                    <span class="text-base font-black text-gaston-orange font-mono">{{ $member->member_id }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-content-muted font-bold uppercase tracking-wider block">Token QR</span>
                    <span class="text-xs font-mono text-content-body">{{ $member->qr_token }}</span>
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Nama Lengkap *</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $member->name) }}"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-gaston-orange">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Email *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $member->email) }}"
                           class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-gaston-orange">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">No. WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $member->phone) }}"
                           class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-gaston-orange">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="password" placeholder="Biarkan kosong jika tidak diubah"
                           class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                </div>
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Status Akun *</label>
                    <select name="status" id="status" required
                            class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-3 text-sm text-content-heading focus:outline-none focus:border-gaston-orange">
                        <option value="active" {{ old('status', $member->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $member->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="photo" class="block text-xs font-bold uppercase tracking-wider text-content-body mb-2">Foto Profil</label>
                @if($member->photo)
                    <div class="mb-3 flex items-center space-x-3">
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-16 h-16 rounded-2xl object-cover border border-line-base">
                        <span class="text-xs text-content-muted">Foto saat ini. Pilih berkas baru untuk mengganti.</span>
                    </div>
                @endif
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-content-muted file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gaston-orange file:text-white hover:file:bg-orange-600 transition-all">
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.members.index') }}" class="px-5 py-3 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-content-body text-xs font-bold transition-colors border border-line-base">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
