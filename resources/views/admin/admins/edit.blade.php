@extends('layouts.admin')

@section('title', 'Edit Akun Pengelola')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Admin</span>
        </a>
        <h2 class="text-2xl font-black text-white">Edit Akun Admin: {{ $admin->name }}</h2>
        <p class="text-xs text-slate-400 mt-1">Perbarui profil, hak akses peran (Role), status, atau setel ulang kata sandi.</p>
    </div>

    <div class="bg-[#141822] border border-[#1e2433] rounded-[32px] p-6 sm:p-8 shadow-xl">
        <form action="{{ route('admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Selector Pill -->
            <div class="p-4 rounded-2xl bg-[#0d1017] border border-[#1e2433] space-y-2">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-300">Tingkat Hak Akses (Role) *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all bg-[#141822] border-[#1e2433] hover:border-slate-600 has-[:checked]:border-[#e50914] has-[:checked]:bg-[#e50914]/10">
                        <input type="radio" name="role" value="admin" class="sr-only" {{ old('role', $admin->role) === 'admin' ? 'checked' : '' }} {{ $admin->id === auth()->id() ? 'disabled' : '' }}>
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">🛡️</span>
                            <div>
                                <p class="text-xs font-bold text-white leading-tight">Admin Biasa</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Kelola sesi olahraga, scan QR, rekap laporan, & review izin.</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all bg-[#141822] border-[#1e2433] hover:border-slate-600 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                        <input type="radio" name="role" value="master_admin" class="sr-only" {{ old('role', $admin->role) === 'master_admin' ? 'checked' : '' }}>
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">👑</span>
                            <div>
                                <p class="text-xs font-bold text-amber-300 leading-tight">Master Admin</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Hak penuh termasuk tambah/edit/hapus akun pengelola admin lain.</p>
                            </div>
                        </div>
                    </label>
                </div>
                @if($admin->id === auth()->id())
                    <p class="text-[10px] text-amber-400 italic mt-1">* Anda tidak dapat menurunkan role akun Anda sendiri yang sedang aktif.</p>
                @endif
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Lengkap *</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $admin->name) }}"
                       class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#e50914]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Login *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $admin->email) }}"
                           class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#e50914]">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">No. WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->phone) }}" placeholder="081234567890"
                           class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#e50914]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ganti Password (Opsional)</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah"
                           class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#e50914]">
                </div>
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Status Akun *</label>
                    <select name="status" id="status" required {{ $admin->id === auth()->id() ? 'disabled' : '' }}
                            class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#e50914]">
                        <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @if($admin->id === auth()->id())
                        <input type="hidden" name="status" value="active">
                    @endif
                </div>
            </div>

            <div>
                <label for="photo" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Foto Profil</label>
                @if($admin->photo)
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ asset('storage/' . $admin->photo) }}" alt="{{ $admin->name }}" class="w-12 h-12 rounded-full object-cover border border-[#1e2433]">
                        <span class="text-xs text-slate-400">Foto saat ini</span>
                    </div>
                @endif
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-2.5 text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#e50914] file:text-white hover:file:bg-red-700 transition-all">
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.admins.index') }}" class="px-5 py-3 rounded-2xl bg-[#1b212c] hover:bg-slate-800 text-slate-300 text-xs font-bold transition-colors border border-[#1e2433]">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-[#e50914] to-red-700 hover:from-red-600 hover:to-red-800 text-white font-extrabold text-xs shadow-lg shadow-[#e50914]/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
