@extends('layouts.member')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-md mx-auto space-y-6" x-data="{
    activeTab: 'data-diri', // 'data-diri', 'ubah-password', 'notifikasi', 'bantuan'
    showNotifToast: false,
    notifMsg: '',
    notifWa: true,
    notifApp: true,
    notifReminder: true,
    saveNotifSettings() {
        this.notifMsg = 'Pengaturan preferensi notifikasi berhasil disimpan!';
        this.showNotifToast = true;
        setTimeout(() => { this.showNotifToast = false; }, 3000);
    }
}">

    <!-- Notification Toast Popup -->
    <div x-show="showNotifToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 right-5 z-50 bg-surface-elevated border border-emerald-500/40 text-emerald-400 px-4 py-3 rounded-2xl shadow-2xl flex items-center space-x-2 text-xs font-bold"
         style="display: none;">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span x-text="notifMsg"></span>
    </div>

    <!-- Avatar & Info Center Header matching Phone 6 in reference -->
    <div class="text-center pt-2">
        <div class="relative inline-block group">
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-line-strong shadow-xl mx-auto">
            @else
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-[#1f2533] to-[#2a3245] text-white flex items-center justify-center font-black text-2xl shadow-xl mx-auto border-2 border-line-strong">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <button type="button" @click="activeTab = 'data-diri'" class="absolute bottom-0 right-0 p-1.5 rounded-full bg-[#e50914] text-white shadow-md hover:scale-110 transition-transform">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </button>
        </div>

        <h2 class="text-base font-black text-content-heading mt-2">{{ $user->name }}</h2>
        <p class="text-[11px] text-content-muted font-medium">
            {{ $user->role === 'admin' ? 'Admin' : 'Member Gaston' }} &bull; {{ $user->member_number ?? 'GST-00' . $user->id }}
        </p>
    </div>

    <!-- Navigation Option Rows matching Phone 6 (Data Diri, Ubah Password, Pengaturan Notifikasi, Bantuan) -->
    <div class="bg-surface border border-line-base rounded-3xl p-2 shadow-xl divide-y divide-line-base/80">
        <!-- Data Diri -->
        <button type="button"
                @click="activeTab = (activeTab === 'data-diri' ? '' : 'data-diri')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'data-diri' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'data-diri' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-content-muted group-hover:text-content-heading'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Data Diri</span>
                    <span class="text-[10px] text-content-muted">Nama, email, no handphone, & foto</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'data-diri'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'data-diri' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Ubah Password -->
        <button type="button"
                @click="activeTab = (activeTab === 'ubah-password' ? '' : 'ubah-password')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'ubah-password' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'ubah-password' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-content-muted group-hover:text-content-heading'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Ubah Password</span>
                    <span class="text-[10px] text-content-muted">Keamanan akun dan kata sandi</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'ubah-password'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'ubah-password' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Pengaturan Notifikasi -->
        <button type="button"
                @click="activeTab = (activeTab === 'notifikasi' ? '' : 'notifikasi')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'notifikasi' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'notifikasi' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-content-muted group-hover:text-content-heading'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Pengaturan Notifikasi</span>
                    <span class="text-[10px] text-content-muted">Pengingat jadwal latihan & WhatsApp</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'notifikasi'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'notifikasi' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Bantuan -->
        <button type="button"
                @click="activeTab = (activeTab === 'bantuan' ? '' : 'bantuan')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'bantuan' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'bantuan' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-content-muted group-hover:text-content-heading'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Bantuan & FAQ</span>
                    <span class="text-[10px] text-content-muted">Pusat bantuan & kontak admin Gaston</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'bantuan'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'bantuan' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Jadwal Shift Mingguan -->
        <button type="button"
                @click="activeTab = (activeTab === 'shift' ? '' : 'shift')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'shift' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'shift' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-[#e50914]'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Jadwal Shift Mingguan</span>
                    <span class="text-[10px] text-content-muted">Pembagian shift & jadwal pelatih</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'shift'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'shift' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Kebijakan Absensi -->
        <button type="button"
                @click="activeTab = (activeTab === 'kebijakan' ? '' : 'kebijakan')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'kebijakan' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'kebijakan' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-content-muted group-hover:text-content-heading'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Kebijakan Absensi</span>
                    <span class="text-[10px] text-content-muted">Aturan kehadiran, izin & cuti</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'kebijakan'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'kebijakan' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>

        <!-- Libur Nasional -->
        <button type="button"
                @click="activeTab = (activeTab === 'libur' ? '' : 'libur')"
                class="w-full p-3.5 flex items-center justify-between text-xs text-left transition-all rounded-2xl group"
                :class="activeTab === 'libur' ? 'bg-surface-elevated text-white' : 'text-content-body hover:bg-surface'">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors"
                     :class="activeTab === 'libur' ? 'bg-[#e50914]/20 text-[#e50914]' : 'bg-surface text-[#e50914]'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-content-heading text-xs block">Libur Nasional & Cuti</span>
                    <span class="text-[10px] text-content-muted">Kalender operasional kegiatan olahraga</span>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <span x-show="activeTab === 'libur'" class="text-[10px] text-[#e50914] font-bold uppercase tracking-wider mr-1">Aktif</span>
                <svg class="w-4 h-4 text-content-faint transition-transform duration-200" :class="activeTab === 'libur' ? 'rotate-90 text-[#e50914]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </button>
    </div>

    <!-- TAB 1: DATA DIRI CONTENT -->
    <div x-show="activeTab === 'data-diri'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#e50914]"></span>
                <span>Edit Data Pribadi</span>
            </h3>
            <span class="text-[10px] text-content-faint">Member ID: {{ $user->member_number ?? 'GST-00' . $user->id }}</span>
        </div>
        
        <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div>
                <label for="email" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Email</label>
                <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div>
                <label for="phone" class="block text-[11px] font-bold uppercase text-content-muted mb-1">No. WhatsApp</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div>
                <label for="photo" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Ganti Foto Profil</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2 text-xs text-content-muted file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-[#e50914] file:text-white cursor-pointer">
                <p class="text-[10px] text-content-faint mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-bold text-xs shadow-lg shadow-[#e50914]/20 transition-all flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan Data Diri</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: UBAH PASSWORD CONTENT -->
    <div x-show="activeTab === 'ubah-password'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>Ubah Kata Sandi Akun</span>
            </h3>
            <span class="text-[10px] text-content-faint">Keamanan</span>
        </div>

        <form action="{{ route('member.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <!-- Retain current data so validation doesn't fail -->
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="phone" value="{{ $user->phone }}">

            <div>
                <label for="current_password" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Password Saat Ini</label>
                <input type="password" name="current_password" id="current_password" required placeholder="Masukkan kata sandi lama"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div>
                <label for="password" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Password Baru</label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-[11px] font-bold uppercase text-content-muted mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi kata sandi baru"
                       class="w-full bg-surface-input border border-line-base rounded-2xl px-4 py-2.5 text-xs text-content-heading focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Perbarui Password</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: PENGATURAN NOTIFIKASI CONTENT -->
    <div x-show="activeTab === 'notifikasi'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-5">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Preferensi Notifikasi</span>
            </h3>
            <span class="text-[10px] text-content-faint">Pengingat</span>
        </div>

        <div class="space-y-4">
            <!-- WA Toggle -->
            <div class="flex items-center justify-between p-3.5 bg-surface-input rounded-2xl border border-line-base">
                <div>
                    <p class="text-xs font-bold text-content-heading">Notifikasi WhatsApp</p>
                    <p class="text-[10px] text-content-muted">Pengingat 1 jam sebelum sesi latihan dimulai</p>
                </div>
                <button type="button" @click="notifWa = !notifWa" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="notifWa ? 'bg-[#e50914]' : 'bg-surface-elevated'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="notifWa ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
            </div>

            <!-- In-App Notification -->
            <div class="flex items-center justify-between p-3.5 bg-surface-input rounded-2xl border border-line-base">
                <div>
                    <p class="text-xs font-bold text-content-heading">Pemberitahuan Sistem & Absen</p>
                    <p class="text-[10px] text-content-muted">Status kehadiran dan persetujuan pengajuan izin</p>
                </div>
                <button type="button" @click="notifApp = !notifApp" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="notifApp ? 'bg-[#e50914]' : 'bg-surface-elevated'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="notifApp ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
            </div>

            <!-- Reminder Alert -->
            <div class="flex items-center justify-between p-3.5 bg-surface-input rounded-2xl border border-line-base">
                <div>
                    <p class="text-xs font-bold text-content-heading">Broadcast Event & Pengumuman</p>
                    <p class="text-[10px] text-content-muted">Info turnamen dan agenda Gaston YK</p>
                </div>
                <button type="button" @click="notifReminder = !notifReminder" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="notifReminder ? 'bg-[#e50914]' : 'bg-surface-elevated'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="notifReminder ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
            </div>
        </div>

        <div class="pt-2">
            <button type="button" @click="saveNotifSettings()" class="w-full py-3 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-white font-bold text-xs border border-line-base transition-all flex items-center justify-center space-x-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Simpan Preferensi Notifikasi</span>
            </button>
        </div>
    </div>

    <!-- TAB 4: BANTUAN CONTENT -->
    <div x-show="activeTab === 'bantuan'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Pusat Bantuan Gaston YK</span>
            </h3>
            <span class="text-[10px] text-content-faint">FAQ & Kontak</span>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-3">
            <div class="p-3 bg-surface-input rounded-2xl border border-line-base">
                <p class="text-xs font-bold text-content-heading flex items-center space-x-2">
                    <span class="text-[#e50914]">Q:</span>
                    <span>Bagaimana cara melakukan absensi kehadiran?</span>
                </p>
                <p class="text-[11px] text-content-muted mt-1 pl-4 leading-relaxed">
                    Buka menu <strong>QR Absen</strong> atau klik tombol <em>Absen Sekarang</em> di beranda, lalu tunjukkan kode QR pribadi Anda ke scanner panitia di lokasi lapangan.
                </p>
            </div>

            <div class="p-3 bg-surface-input rounded-2xl border border-line-base">
                <p class="text-xs font-bold text-content-heading flex items-center space-x-2">
                    <span class="text-[#e50914]">Q:</span>
                    <span>Bagaimana jika saya berhalangan hadir?</span>
                </p>
                <p class="text-[11px] text-content-muted mt-1 pl-4 leading-relaxed">
                    Buka menu <strong>Izin</strong> pada navigasi bawah, lalu isi formulir alasan izin/sakit disertai bukti keterangan untuk disetujui oleh admin.
                </p>
            </div>

            <div class="p-3 bg-surface-input rounded-2xl border border-line-base">
                <p class="text-xs font-bold text-content-heading flex items-center space-x-2">
                    <span class="text-[#e50914]">Q:</span>
                    <span>Apakah QR Code saya berubah setiap hari?</span>
                </p>
                <p class="text-[11px] text-content-muted mt-1 pl-4 leading-relaxed">
                    Tidak. QR Code Anda bersifat unik dan permanen sesuai ID keanggotaan Gaston Anda.
                </p>
            </div>
        </div>

        <!-- Customer Service Action Button -->
        <div class="pt-2">
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Gaston%20YK,%20saya%20member%20{{ urlencode($user->name) }}%20memerlukan%20bantuan"
               target="_blank"
               class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.966.527 1.83.81 2.791.81 3.181 0 5.767-2.587 5.767-5.766.001-3.181-2.585-5.796-5.767-5.796zm0 10.435c-.886 0-1.748-.239-2.502-.686l-.179-.107-1.859.488.497-1.812-.117-.187c-.496-.789-.757-1.706-.757-2.56 0-2.617 2.129-4.746 4.747-4.746 2.617 0 4.746 2.129 4.747 4.746 0 2.618-2.129 4.866-4.738 4.866z"/></svg>
                <span>Hubungi Admin Gaston via WhatsApp</span>
            </a>
        </div>
    </div>

    <!-- TAB 5: JADWAL SHIFT MINGGUAN CONTENT -->
    <div x-show="activeTab === 'shift'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#e50914]"></span>
                <span>Jadwal Shift & Sesi Mingguan</span>
            </h3>
            <span class="text-[10px] text-content-faint font-mono">{{ now()->translatedFormat('d M Y') }}</span>
        </div>

        <p class="text-xs text-content-muted leading-relaxed">
            Berikut jadwal pembagian shift pelatih dan operasional venue olahraga Gaston YK minggu ini:
        </p>

        <div class="space-y-3">
            <div class="p-4 rounded-2xl bg-surface-input border border-line-base space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        Shift Pagi (07:00 - 15:00 WIB)
                    </span>
                    <span class="text-[10px] text-content-muted font-mono">GOR Manahan</span>
                </div>
                <div class="space-y-1 text-xs">
                    <p class="text-white font-bold">Cabang: <span class="font-normal text-content-body">Badminton & Tenis Lapangan</span></p>
                    <p class="text-content-muted text-[11px]">Koordinator: Coach Bambang & Tim Lapangan</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-input border border-line-base space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                        Shift Sore & Malam (15:00 - 22:00 WIB)
                    </span>
                    <span class="text-[10px] text-content-muted font-mono">GOR Kartasura</span>
                </div>
                <div class="space-y-1 text-xs">
                    <p class="text-white font-bold">Cabang: <span class="font-normal text-content-body">Futsal, Basket, & Voli</span></p>
                    <p class="text-content-muted text-[11px]">Koordinator: Coach Dimas & Tim Scanner</p>
                </div>
            </div>
        </div>

        <div class="p-3 bg-surface-elevated rounded-2xl border border-line-base text-[11px] text-content-muted">
            Jadwal kegiatan olahraga Anda dapat dilihat di menu <strong>Sesi</strong> atau <strong>Beranda</strong>.
        </div>
    </div>

    <!-- TAB 6: KEBIJAKAN ABSENSI CONTENT -->
    <div x-show="activeTab === 'kebijakan'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                <span>Kebijakan Presensi Gaston YK</span>
            </h3>
            <span class="text-[10px] text-content-faint">SOP Kehadiran</span>
        </div>

        <div class="space-y-3 text-xs">
            <div class="p-3.5 bg-surface-input rounded-2xl border border-line-base space-y-1">
                <p class="font-bold text-content-heading flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-[#e50914]"></span>
                    <span>1. Metode Check-in QR</span>
                </p>
                <p class="text-[11px] text-content-muted leading-relaxed pl-4">
                    Member wajib menunjukkan QR Code dari menu <strong>QR Absen</strong> kepada admin di lokasi. QR bersifat unik dan otomatis mencatat jam kedatangan.
                </p>
            </div>

            <div class="p-3.5 bg-surface-input rounded-2xl border border-line-base space-y-1">
                <p class="font-bold text-content-heading flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span>2. Pengajuan Izin Tidak Masuk</span>
                </p>
                <p class="text-[11px] text-content-muted leading-relaxed pl-4">
                    Jika tidak bisa hadir, kirim formulir izin paling lambat sebelum sesi berakhir. Izin yang telah di-approve tidak menurunkan statistik kehadiran Anda.
                </p>
            </div>

            <div class="p-3.5 bg-surface-input rounded-2xl border border-line-base space-y-1">
                <p class="font-bold text-content-heading flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>3. Ketentuan Walk-in</span>
                </p>
                <p class="text-[11px] text-content-muted leading-relaxed pl-4">
                    Member yang datang tanpa registrasi terlebih dahulu dapat langsung di-scan oleh admin jika slot lapangan masih tersedia.
                </p>
            </div>
        </div>
    </div>

    <!-- TAB 7: LIBUR NASIONAL CONTENT -->
    <div x-show="activeTab === 'libur'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="bg-surface border border-line-base rounded-3xl p-6 shadow-xl space-y-4">
        
        <div class="flex items-center justify-between pb-2 border-b border-line-base">
            <h3 class="text-xs font-bold uppercase tracking-wider text-content-body flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-[#e50914]"></span>
                <span>Kalender Libur & Cuti Bersama</span>
            </h3>
            <span class="text-[10px] text-content-faint">Tahun {{ date('Y') }}</span>
        </div>

        <p class="text-xs text-content-muted leading-relaxed">
            Pada tanggal merah resmi, jadwal sesi latihan reguler disesuaikan sebagai berikut:
        </p>

        <div class="space-y-2.5 text-xs">
            <div class="p-3 bg-surface-input rounded-2xl border border-line-base flex items-center justify-between">
                <div>
                    <p class="font-bold text-content-heading">17 Agustus</p>
                    <p class="text-[11px] text-content-muted">Hari Proklamasi Kemerdekaan RI</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                    Libur Penuh
                </span>
            </div>

            <div class="p-3 bg-surface-input rounded-2xl border border-line-base flex items-center justify-between">
                <div>
                    <p class="font-bold text-content-heading">25 Desember</p>
                    <p class="text-[11px] text-content-muted">Hari Raya Natal</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                    Shift Siang
                </span>
            </div>

            <div class="p-3 bg-surface-input rounded-2xl border border-line-base flex items-center justify-between">
                <div>
                    <p class="font-bold text-content-heading">1 Januari</p>
                    <p class="text-[11px] text-content-muted">Tahun Baru Masehi</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30">
                    Libur Penuh
                </span>
            </div>
        </div>
    </div>

    <!-- Red Logout Button matching Phone 6 bottom button -->
    <div class="pt-2">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-[#e50914] hover:bg-[#ff1e27] text-white font-black text-xs shadow-xl shadow-[#e50914]/30 active:scale-[0.99] transition-all flex items-center justify-center space-x-2 tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>

</div>
@endsection
