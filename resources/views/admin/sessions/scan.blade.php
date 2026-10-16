@extends('layouts.admin')

@section('title', 'Scanner Kamera QR - ' . $session->sport->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="scannerApp()" @keydown.window.escape="handleEscapeKey()">

    <!-- Session Info Header (Obsidian with Fiery Glow) -->
    <div class="bg-surface-elevated border border-line-base rounded-[32px] p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase bg-[#e50914]/15 text-[#e50914] border border-[#e50914]/30">
                    {{ $session->sport->name }}
                </span>
                <span class="text-xs text-content-muted font-semibold">• {{ $session->location->name }}</span>
            </div>
            <h2 class="text-xl font-black text-content-heading mt-2">{{ $session->date->translatedFormat('l, d F Y') }}</h2>
            <p class="text-xs text-content-muted mt-0.5">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
        </div>

        <div class="flex items-center space-x-4 bg-surface-input px-5 py-3 rounded-2xl border border-line-base">
            <div class="text-right">
                <p class="text-[11px] uppercase tracking-wider text-content-muted font-bold">Kehadiran Sesi</p>
                <p class="text-xl font-black text-content-heading">
                    <span class="text-[#e50914]" x-text="stats.present">{{ $summary['present'] }}</span> /
                    <span x-text="stats.total_registered">{{ $summary['total_registered'] }}</span>
                </p>
            </div>
            <a href="{{ route('admin.sessions.attendance', $session) }}" class="p-2.5 bg-surface-elevated hover:bg-surface text-content-body rounded-xl text-xs font-bold transition-colors border border-line-base">
                Rekap
            </a>
        </div>
    </div>

    <!-- Scanner Main Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Camera Viewport (Matches reference mobile mockup camera scan) -->
        <div class="lg:col-span-7 bg-surface-elevated border border-line-base rounded-[32px] p-5 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-content-heading flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full" :class="isScanning ? 'bg-[#e50914] animate-pulse' : 'bg-slate-500'"></span>
                    <span>Pindai QR Member</span>
                </h3>
                <button @click="toggleCamera()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-surface-elevated hover:bg-surface border border-line-base text-content-body hover:text-content-heading transition-colors">
                    <span x-text="isScanning ? 'Jeda Kamera' : 'Mulai Kamera'"></span>
                </button>
            </div>

            <!-- Video Viewport with Neon Concentric Rings Style -->
            <div class="relative bg-black rounded-3xl overflow-hidden min-h-[320px] flex items-center justify-center border-2 border-line-base">
                <div id="reader" class="w-full"></div>
                
                <div x-show="!cameraStarted" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-surface-input/95 z-10">
                    <div class="w-16 h-16 rounded-3xl bg-[#e50914]/15 text-[#e50914] flex items-center justify-center mb-3 border border-[#e50914]/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-sm font-black text-content-heading">Kamera Belum Aktif</p>
                    <p class="text-xs text-content-muted mt-1 mb-4">Izinkan akses kamera browser untuk memindai QR member.</p>
                    <button @click="startCamera()" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-[#e50914] to-red-700 hover:from-red-600 hover:to-red-800 text-white font-extrabold text-xs shadow-lg shadow-[#e50914]/30 transition-all">
                        Izinkan & Aktifkan Kamera
                    </button>
                </div>
            </div>

            <!-- Alternative: Input Manual Member ID / QR Token -->
            <div class="pt-2 border-t border-line-base">
                <p class="text-xs text-content-muted font-semibold mb-2">Atau cari berdasarkan Member ID / Token jika kamera bermasalah:</p>
                <form @submit.prevent="lookupManual()" class="flex space-x-2">
                    <input type="text" x-model="manualInput" placeholder="Contoh: GST-00001 atau token QR"
                           :disabled="isLookingUp || submitting || activeModal !== null"
                           class="flex-1 bg-surface-input border border-line-base rounded-xl px-4 py-2 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914] disabled:opacity-50">
                    <button type="submit" :disabled="!manualInput.trim() || isLookingUp || submitting || activeModal !== null"
                            class="px-4 py-2 bg-surface-elevated hover:bg-surface text-content-body text-xs font-bold rounded-xl transition-colors border border-line-base disabled:opacity-50 flex items-center space-x-1">
                        <template x-if="isLookingUp">
                            <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-content-heading" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </template>
                        <span x-text="isLookingUp ? 'Mencari...' : 'Cari'"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Recent Scans History (Replaces Old Confirmation Card) -->
        <div class="lg:col-span-5 bg-surface-elevated border border-line-base rounded-[32px] p-6 shadow-2xl relative min-h-[440px] flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-line-base">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-content-heading flex items-center space-x-1.5">
                            <span>Scan Terbaru</span>
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold badge-neutral border border-line-base"
                              x-text="recentScans.length"></span>
                    </div>
                    <a href="{{ route('admin.sessions.attendance', $session) }}"
                       class="text-[11px] font-bold text-gaston-red hover:underline flex items-center space-x-1 transition-colors">
                        <span>Lihat semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Empty State -->
                <div x-show="recentScans.length === 0" class="py-14 text-center text-content-faint space-y-3">
                    <div class="w-14 h-14 rounded-3xl bg-surface mx-auto flex items-center justify-center text-content-faint border border-line-base">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <p class="text-xs font-semibold text-content-muted">Belum ada yang discan.</p>
                    <p class="text-[11px] text-content-faint">Arahkan kamera ke QR member.</p>
                </div>

                <!-- List of Recent Scans (Scrollable on desktop, compact item height ~56px) -->
                <div x-show="recentScans.length > 0" class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
                    <template x-for="(scan, idx) in recentScans" :key="scan.id">
                        <div x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="-translate-y-2 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             :class="highlightedScanId === scan.id ? 'ring-2 ring-gaston-red/50 bg-surface' : 'bg-surface'"
                             class="p-2.5 rounded-2xl border border-line-base flex items-center space-x-3 transition-all hover:border-line-strong min-h-[56px]">
                            <!-- Thumbnail / Initial -->
                            <template x-if="scan.photo_url">
                                <img :src="scan.photo_url" :alt="scan.name"
                                     class="w-10 h-10 rounded-xl object-cover border border-line-base flex-shrink-0">
                            </template>
                            <template x-if="!scan.photo_url">
                                <div class="w-10 h-10 rounded-xl bg-surface-elevated text-gaston-red flex items-center justify-center font-black text-sm border border-line-base flex-shrink-0">
                                    <span x-text="scan.name ? scan.name.charAt(0).toUpperCase() : '?'"></span>
                                </div>
                            </template>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <p class="font-bold text-xs text-content-heading truncate" x-text="scan.name"></p>
                                    <span class="text-[10px] font-mono text-content-faint flex-shrink-0" x-text="scan.time + ' WIB'"></span>
                                </div>
                                <div class="flex items-center justify-between mt-0.5">
                                    <p class="text-[11px] font-mono text-gaston-red font-semibold truncate" x-text="scan.member_id"></p>
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider"
                                          :class="scan.is_walk_in ? 'badge-warning' : 'badge-success'"
                                          x-text="scan.is_walk_in ? 'Walk-in' : 'Hadir'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Hint footer -->
            <div class="pt-3 mt-3 border-t border-line-base text-center">
                <p class="text-[11px] text-content-faint flex items-center justify-center space-x-1">
                    <svg class="w-3.5 h-3.5 text-gaston-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Pop-up verifikasi otomatis muncul saat scan QR berhasil.</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Teleported Modal UI (Z-[60] Above Admin Sidebar & Topbar) -->
    <template x-teleport="body">
        <div>
            <!-- Backdrop Overlay with Blur -->
            <div x-show="activeModal !== null"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="handleBackdropClick()"
                 class="fixed inset-0 z-[60] bg-black/60 dark:bg-black/80 backdrop-blur-md"></div>

            <!-- Modal / Bottom-sheet Container -->
            <div x-show="activeModal !== null"
                 x-cloak
                 class="fixed inset-0 z-[65] pointer-events-none flex flex-col justify-end sm:justify-center sm:items-center sm:p-4 overflow-y-auto">

                <!-- 1. MODAL ACC KEHADIRAN (MEMBER TERDAFTAR ATAU WALK-IN) -->
                <div x-show="activeModal === 'acc'"
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave="transition ease-in duration-150 transform"
                     x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="acc-modal-title"
                     class="pointer-events-auto w-full sm:max-w-[440px] bg-surface border-t sm:border border-line-base rounded-t-[32px] sm:rounded-[32px] p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">

                    <!-- Mobile Pull Indicator -->
                    <div class="w-12 h-1.5 bg-line-strong rounded-full mx-auto mb-4 sm:hidden"></div>

                    <!-- Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-line-base">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#e50914] animate-pulse"></span>
                            <h3 id="acc-modal-title" class="text-xs font-black uppercase tracking-wider text-content-heading">
                                Verifikasi Kehadiran
                            </h3>
                        </div>
                        <button type="button" @click="closeModal()" :disabled="submitting"
                                class="p-1 rounded-lg text-content-muted hover:text-content-heading hover:bg-surface-elevated transition-colors disabled:opacity-50"
                                aria-label="Tutup dialog">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Member Identity Section -->
                    <div class="mt-4 space-y-4">
                        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-surface-elevated border border-line-base">
                            <!-- Large Member Photo -->
                            <div class="relative mb-3">
                                <template x-if="currentMember?.photo_url">
                                    <img :src="currentMember?.photo_url" :alt="currentMember?.name"
                                         class="w-28 h-28 rounded-2xl object-cover border-2 border-[#e50914] shadow-lg shadow-[#e50914]/25">
                                </template>
                                <template x-if="!currentMember?.photo_url">
                                    <div class="w-28 h-28 rounded-2xl bg-surface text-[#e50914] flex items-center justify-center font-black text-4xl border-2 border-[#e50914]/50 shadow-lg">
                                        <span x-text="currentMember?.name ? currentMember.name.charAt(0).toUpperCase() : '?'"></span>
                                    </div>
                                </template>

                                <!-- Status Badge Overlay on Photo Corner -->
                                <div class="absolute -bottom-2 inset-x-0 flex justify-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow"
                                          :class="isRegistered ? 'badge-success' : 'badge-warning'"
                                          x-text="isRegistered ? 'Terdaftar' : 'Belum Terdaftar (Walk-in)'"></span>
                                </div>
                            </div>

                            <h4 class="font-black text-content-heading text-lg mt-2 leading-tight" x-text="currentMember?.name"></h4>
                            <p class="font-mono text-sm font-black text-[#e50914] mt-0.5" x-text="currentMember?.member_id"></p>
                            
                            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 mt-2 text-xs text-content-muted">
                                <template x-if="currentMember?.phone">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-content-faint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span x-text="currentMember?.phone"></span>
                                    </span>
                                </template>
                                <template x-if="currentMember?.email">
                                    <span class="flex items-center space-x-1 truncate max-w-[240px]">
                                        <svg class="w-3.5 h-3.5 text-content-faint flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span class="truncate" x-text="currentMember?.email"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Face Verification Notice -->
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-content-body flex items-start space-x-3">
                            <span class="text-base flex-shrink-0">⚠️</span>
                            <div>
                                <p class="font-bold text-amber-500 dark:text-amber-400">Verifikasi Wajah:</p>
                                <p class="text-[11px] text-content-muted mt-0.5">Pastikan orang di GOR cocok dengan foto di atas sebelum menyetujui kehadiran.</p>
                            </div>
                        </div>

                        <!-- Optional Notes Input -->
                        <div>
                            <label for="scan-confirm-notes" class="block text-[11px] font-bold text-content-muted mb-1">
                                Catatan Kehadiran (Opsional):
                            </label>
                            <input id="scan-confirm-notes"
                                   type="text"
                                   x-model="attendanceNotes"
                                   maxlength="255"
                                   placeholder="Contoh: Datang terlambat 10 menit, raket pinjam..."
                                   :disabled="submitting"
                                   class="w-full bg-surface-input border border-line-base rounded-xl px-3.5 py-2 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914] disabled:opacity-50">
                        </div>

                        <!-- In-modal Error Alert (When confirmAttendance fails) -->
                        <div x-show="modalErrorMessage" x-cloak
                             class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-500 dark:text-rose-400 text-xs font-semibold flex items-center space-x-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="modalErrorMessage"></span>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-5 pt-4 border-t border-line-base flex flex-col sm:flex-row-reverse gap-2">
                        <!-- ACC Primary Button -->
                        <button x-ref="accButton"
                                @click="confirmAttendance(!isRegistered)"
                                :disabled="submitting"
                                type="button"
                                class="w-full sm:flex-1 py-3.5 px-4 bg-[#e50914] hover:bg-[#c40812] active:bg-[#a6070f] text-white rounded-2xl text-xs font-black shadow-lg shadow-[#e50914]/30 transition-all flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <template x-if="submitting">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            </template>
                            <span x-text="submitting ? 'Menyimpan...' : (isRegistered ? '✓ ACC / Konfirmasi Hadir' : '+ ACC & Tambahkan Walk-in')"></span>
                        </button>

                        <!-- Secondary Cancel Button -->
                        <button type="button"
                                @click="closeModal()"
                                :disabled="submitting"
                                class="w-full sm:w-auto px-5 py-3.5 bg-surface-elevated hover:bg-surface text-content-body hover:text-content-heading rounded-2xl text-xs font-bold transition-colors border border-line-base disabled:opacity-50">
                            Tolak / Batal
                        </button>
                    </div>
                </div>

                <!-- 2. MODAL SUDAH HADIR (ALREADY CHECKED IN) -->
                <div x-show="activeModal === 'already_checked_in'"
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave="transition ease-in duration-150 transform"
                     x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="already-modal-title"
                     class="pointer-events-auto w-full sm:max-w-[420px] bg-surface border-t sm:border border-line-base rounded-t-[32px] sm:rounded-[32px] p-6 shadow-2xl relative">

                    <div class="w-12 h-1.5 bg-line-strong rounded-full mx-auto mb-4 sm:hidden"></div>

                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 rounded-3xl bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center border border-amber-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>

                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-warning">
                                Sudah Hadir
                            </span>
                            <h3 id="already-modal-title" class="text-lg font-black text-content-heading mt-2" x-text="currentMember?.name"></h3>
                            <p class="font-mono text-xs font-bold text-[#e50914] mt-0.5" x-text="currentMember?.member_id"></p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-surface-elevated border border-line-base text-xs text-content-muted">
                            <p>Sudah check-in pukul <span class="font-black text-content-heading" x-text="checkedInTime"></span> WIB</p>
                        </div>

                        <div class="pt-2">
                            <button x-ref="alreadyButton"
                                    type="button"
                                    @click="closeModal()"
                                    class="w-full py-3.5 bg-surface-elevated hover:bg-surface text-content-heading rounded-2xl text-xs font-black transition-colors border border-line-base">
                                Lanjut Scan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. MODAL ERROR (MEMBER NOT FOUND / SERVER ERROR / SESI HABIS) -->
                <div x-show="activeModal === 'error'"
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave="transition ease-in duration-150 transform"
                     x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                     x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95 sm:opacity-0"
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="error-modal-title"
                     class="pointer-events-auto w-full sm:max-w-[420px] bg-surface border-t sm:border border-line-base rounded-t-[32px] sm:rounded-[32px] p-6 shadow-2xl relative">

                    <div class="w-12 h-1.5 bg-line-strong rounded-full mx-auto mb-4 sm:hidden"></div>

                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 rounded-3xl bg-rose-500/15 text-rose-500 mx-auto flex items-center justify-center border border-rose-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>

                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-danger">
                                Pemindaian Gagal
                            </span>
                            <h3 id="error-modal-title" class="text-base font-black text-content-heading mt-2">Tidak Dapat Memverifikasi</h3>
                            <p class="text-xs text-rose-500 dark:text-rose-400 font-semibold mt-2 px-2" x-text="errorMessage"></p>
                        </div>

                        <div class="pt-2">
                            <button x-ref="errorButton"
                                    type="button"
                                    @click="closeModal()"
                                    class="w-full py-3.5 bg-surface-elevated hover:bg-surface text-content-heading rounded-2xl text-xs font-black transition-colors border border-line-base">
                                Scan Ulang
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SUCCESS TOAST NOTIFICATION -->
            <div x-show="toast.visible"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="-translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                 x-transition:leave-end="-translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                 class="fixed top-5 inset-x-4 sm:inset-x-auto sm:right-6 z-[70] pointer-events-none flex justify-center">
                <div class="pointer-events-auto max-w-md w-full bg-surface border border-line-base rounded-2xl p-4 shadow-2xl flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center flex-shrink-0 border border-emerald-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-black text-content-heading" x-text="toast.title"></p>
                        <p class="text-[11px] text-content-muted truncate mt-0.5" x-text="toast.message"></p>
                    </div>
                    <button type="button" @click="toast.visible = false" class="text-content-faint hover:text-content-heading p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
function scannerApp() {
    return {
        html5QrCode: null,
        cameraStarted: false,
        isScanning: false,
        isLookingUp: false,
        lastScannedCode: null,
        lastScannedTime: 0,
        manualInput: '',
        currentMember: null,
        isRegistered: false,
        errorMessage: null,
        alreadyCheckedIn: false,
        checkedInTime: '',
        submitting: false,
        activeModal: null, // 'acc' | 'already_checked_in' | 'error' | null
        modalErrorMessage: null,
        attendanceNotes: '',
        toast: {
            visible: false,
            title: '',
            message: '',
            timeoutId: null
        },
        recentScans: @json($recentScans ?? []),
        highlightedScanId: null,
        highlightTimeout: null,
        stats: {
            present: {{ $summary['present'] }},
            total_registered: {{ $summary['total_registered'] }}
        },

        init() {
            this.$nextTick(() => {
                this.startCamera();
            });
        },

        startCamera() {
            if (typeof window.Html5Qrcode === 'undefined') {
                console.warn('Html5Qrcode not ready yet.');
                return;
            }

            try {
                this.html5QrCode = new window.Html5Qrcode("reader");
            } catch (err) {
                console.warn("Failed to initialize Html5Qrcode instance:", err);
                return;
            }

            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            this.html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                (errorMessage) => {}
            ).then(() => {
                this.cameraStarted = true;
                this.isScanning = true;
            }).catch((err) => {
                console.warn("Camera start failed:", err);
                this.cameraStarted = false;
                this.isScanning = false;
            });
        },

        toggleCamera() {
            if (!this.html5QrCode) return;
            if (this.isScanning) {
                this.pauseCamera();
            } else {
                this.resumeCamera();
            }
        },

        pauseCamera() {
            if (!this.html5QrCode) return;
            try {
                if (typeof this.html5QrCode.getState === 'function') {
                    // Html5QrcodeScannerState.SCANNING is 2
                    if (this.html5QrCode.getState() === 2) {
                        this.html5QrCode.pause();
                    }
                } else {
                    this.html5QrCode.pause();
                }
            } catch (err) {
                console.warn("pauseCamera warning:", err);
            }
            this.isScanning = false;
        },

        resumeCamera() {
            if (!this.html5QrCode || !this.cameraStarted) return;
            try {
                if (typeof this.html5QrCode.getState === 'function') {
                    // Html5QrcodeScannerState.PAUSED is 3
                    if (this.html5QrCode.getState() === 3) {
                        this.html5QrCode.resume();
                    }
                } else {
                    this.html5QrCode.resume();
                }
            } catch (err) {
                console.warn("resumeCamera warning:", err);
            }
            this.isScanning = true;
        },

        onScanSuccess(decodedText) {
            const now = Date.now();
            // 3-second cooldown for the same code
            if (this.lastScannedCode === decodedText && (now - this.lastScannedTime) < 3000) {
                return;
            }

            if (this.activeModal !== null || this.submitting || this.isLookingUp) {
                return;
            }

            this.lastScannedCode = decodedText;
            this.lastScannedTime = now;
            this.verifyIdentifier(decodedText);
        },

        lookupManual() {
            if (!this.manualInput.trim() || this.isLookingUp || this.submitting || this.activeModal !== null) return;
            const token = this.manualInput.trim();
            this.lastScannedCode = token;
            this.lastScannedTime = Date.now();
            this.verifyIdentifier(token);
            this.manualInput = '';
        },

        verifyIdentifier(token) {
            this.errorMessage = null;
            this.modalErrorMessage = null;
            this.alreadyCheckedIn = false;
            this.isLookingUp = true;
            this.attendanceNotes = '';

            fetch("{{ route('admin.sessions.scan.lookup', $session) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_token: token })
            })
            .then(async res => {
                if (res.status === 419 || res.status === 401) {
                    throw new Error("Sesi login habis, muat ulang halaman.");
                }
                if (res.status >= 500) {
                    throw new Error("Terjadi kesalahan pada server. Silakan coba lagi.");
                }
                const contentType = res.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Sesi login habis, muat ulang halaman.");
                }
                return res.json();
            })
            .then(data => {
                this.isLookingUp = false;

                if (data.code === 'ALREADY_CHECKED_IN') {
                    this.alreadyCheckedIn = true;
                    this.currentMember = data.member;
                    this.checkedInTime = data.attendance?.checked_in_at || '-';
                    this.openModal('already_checked_in');
                    return;
                }

                if (!data.success && data.code !== 'NOT_REGISTERED') {
                    this.errorMessage = data.message || "Member tidak ditemukan atau QR tidak valid.";
                    this.currentMember = null;
                    this.openModal('error');
                    return;
                }

                this.currentMember = data.member;
                this.isRegistered = Boolean(data.is_registered);
                this.playBeep();
                this.openModal('acc');
            })
            .catch(err => {
                this.isLookingUp = false;
                this.errorMessage = err.message || "Gagal memproses QR Code. Silakan coba lagi.";
                this.currentMember = null;
                this.openModal('error');
            });
        },

        confirmAttendance(isWalkIn) {
            if (!this.currentMember || this.submitting) return;
            this.submitting = true;
            this.modalErrorMessage = null;

            const confirmedMember = { ...this.currentMember };
            const notesToSend = this.attendanceNotes ? this.attendanceNotes.trim() : null;

            fetch("{{ route('admin.sessions.scan.confirm', $session) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    member_id: confirmedMember.id,
                    is_walk_in: isWalkIn,
                    notes: notesToSend
                })
            })
            .then(async res => {
                if (res.status === 419 || res.status === 401) {
                    throw new Error("Sesi login habis, muat ulang halaman.");
                }
                const contentType = res.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Sesi login habis, muat ulang halaman.");
                }
                return res.json();
            })
            .then(data => {
                this.submitting = false;
                if (data.success) {
                    if (data.summary) {
                        this.stats.present = data.summary.present;
                        this.stats.total_registered = data.summary.total_registered;
                    }

                    // Build scan entry from data.scan or fallback to confirmedMember
                    const newScan = data.scan || {
                        id: data.attendance?.id || Date.now(),
                        user_id: confirmedMember.id,
                        name: confirmedMember.name,
                        member_id: confirmedMember.member_id,
                        photo_url: confirmedMember.photo_url,
                        time: data.attendance?.checked_in_at || new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':'),
                        is_walk_in: Boolean(isWalkIn),
                        status: 'present'
                    };

                    // Avoid duplicate by attendance id
                    this.recentScans = this.recentScans.filter(s => s.id !== newScan.id);
                    this.recentScans.unshift(newScan);
                    if (this.recentScans.length > 10) {
                        this.recentScans = this.recentScans.slice(0, 10);
                    }

                    // Set highlight for ~2 seconds
                    this.highlightedScanId = newScan.id;
                    if (this.highlightTimeout) clearTimeout(this.highlightTimeout);
                    this.highlightTimeout = setTimeout(() => {
                        if (this.highlightedScanId === newScan.id) {
                            this.highlightedScanId = null;
                        }
                    }, 2000);

                    // Play success sound
                    this.playSuccessBeep();

                    // Close modal and show toast
                    this.closeModal();
                    this.showToast("Verifikasi Berhasil", `Kehadiran ${confirmedMember.name} berhasil diverifikasi!`);

                    // Cooldown reset
                    this.lastScannedTime = Date.now();
                } else {
                    this.modalErrorMessage = data.message || "Gagal menyimpan konfirmasi.";
                }
            })
            .catch(err => {
                this.submitting = false;
                this.modalErrorMessage = err.message || "Terjadi kesalahan jaringan.";
            });
        },

        openModal(type) {
            this.activeModal = type;
            this.pauseCamera();
            document.body.classList.add('overflow-hidden');

            this.$nextTick(() => {
                if (type === 'acc' && this.$refs.accButton) {
                    this.$refs.accButton.focus();
                } else if (type === 'already_checked_in' && this.$refs.alreadyButton) {
                    this.$refs.alreadyButton.focus();
                } else if (type === 'error' && this.$refs.errorButton) {
                    this.$refs.errorButton.focus();
                }
            });
        },

        closeModal() {
            if (this.submitting) return;
            this.activeModal = null;
            this.currentMember = null;
            this.errorMessage = null;
            this.modalErrorMessage = null;
            this.alreadyCheckedIn = false;
            this.isRegistered = false;
            this.attendanceNotes = '';
            document.body.classList.remove('overflow-hidden');

            // Resume camera
            this.lastScannedTime = Date.now();
            this.resumeCamera();
        },

        handleBackdropClick() {
            if (this.submitting) return;
            this.closeModal();
        },

        handleEscapeKey() {
            if (this.activeModal !== null && !this.submitting) {
                this.closeModal();
            }
        },

        showToast(title, message) {
            if (this.toast.timeoutId) {
                clearTimeout(this.toast.timeoutId);
            }
            this.toast.title = title;
            this.toast.message = message;
            this.toast.visible = true;

            this.toast.timeoutId = setTimeout(() => {
                this.toast.visible = false;
            }, 2500);
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                osc.type = "sine";
                osc.frequency.setValueAtTime(850, ctx.currentTime);
                osc.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.12);
            } catch (e) {}
        },

        playSuccessBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;

                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = "sine";
                osc1.frequency.setValueAtTime(600, now);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
                osc1.start(now);
                osc1.stop(now + 0.1);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = "sine";
                osc2.frequency.setValueAtTime(900, now + 0.1);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                gain2.gain.setValueAtTime(0.25, now + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.28);
                osc2.start(now + 0.1);
                osc2.stop(now + 0.28);
            } catch (e) {}
        }
    };
}
</script>
@endpush
