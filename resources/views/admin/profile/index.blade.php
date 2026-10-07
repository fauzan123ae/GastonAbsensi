@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black text-white">Profil Pengelola</h2>
        <p class="text-xs text-slate-400 mt-1">Perbarui data diri akun, foto profil, dan kata sandi masuk admin.</p>
    </div>

    <!-- Admin Identity Card -->
    <div class="bg-[#141822] border border-[#1e2433] rounded-[32px] p-6 sm:p-8 flex items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center space-x-4">
            <div class="relative">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 {{ $user->isMasterAdmin() ? 'border-amber-500' : 'border-gaston-orange' }} shadow-lg">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-[#1c2230] text-gaston-orange flex items-center justify-center font-black text-2xl border border-[#1e2433]">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <span class="absolute -bottom-1 -right-1 text-base">
                    {{ $user->isMasterAdmin() ? '👑' : '🛡️' }}
                </span>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-lg font-black text-white">{{ $user->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $user->isMasterAdmin() ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                        {{ $user->isMasterAdmin() ? 'Master Admin' : 'Admin' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">{{ $user->email }}</p>
                <p class="text-[11px] text-slate-500 mt-1 font-mono">Status: <span class="text-emerald-400 font-bold uppercase">{{ $user->status }}</span></p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Profile Form -->
    <div class="bg-[#141822] border border-[#1e2433] rounded-[32px] p-6 sm:p-8 shadow-xl">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-[#1e2433]">Informasi Dasar</h4>
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                               class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Akun *</label>
                            <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                                   class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">No. Handphone / WhatsApp</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                                   class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="photo" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ganti Foto Profil (Opsional)</label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1b212c] file:text-slate-200 hover:file:bg-slate-800 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="pt-4">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2 pb-2 border-b border-[#1e2433]">Ganti Password</h4>
                <p class="text-[11px] text-slate-500 mb-4">Kosongkan kolom di bawah jika Anda tidak ingin mengubah password akun.</p>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" placeholder="Masukkan password lama untuk konfirmasi"
                               class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password Baru</label>
                            <input type="password" name="password" id="password" placeholder="Min. 6 karakter"
                                   class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ketik ulang password baru"
                                   class="w-full bg-[#0d1017] border border-[#1e2433] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-[#e50914] transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/25 transition-all">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
