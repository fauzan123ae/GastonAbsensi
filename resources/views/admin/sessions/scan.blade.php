@extends('layouts.admin')

@section('title', 'Scanner Kamera QR - ' . $session->sport->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="scannerApp()">

    <!-- Session Info Header (Obsidian with Fiery Glow) -->
    <div class="bg-[#141822] border border-gaston-border rounded-[32px] p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 rounded-xl text-xs font-black uppercase bg-gaston-orange/15 text-gaston-orange border border-gaston-orange/30">
                    {{ $session->sport->name }}
                </span>
                <span class="text-xs text-slate-400 font-semibold">• {{ $session->location->name }}</span>
            </div>
            <h2 class="text-xl font-black text-white mt-2">{{ $session->date->translatedFormat('l, d F Y') }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
        </div>

        <div class="flex items-center space-x-4 bg-[#0d1017] px-5 py-3 rounded-2xl border border-gaston-border">
            <div class="text-right">
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Kehadiran Sesi</p>
                <p class="text-xl font-black text-white">
                    <span class="text-gaston-orange" x-text="stats.present">{{ $summary['present'] }}</span> /
                    <span x-text="stats.total_registered">{{ $summary['total_registered'] }}</span>
                </p>
            </div>
            <a href="{{ route('admin.sessions.attendance', $session) }}" class="p-2.5 bg-[#1b212c] hover:bg-slate-800 text-slate-200 rounded-xl text-xs font-bold transition-colors border border-gaston-border">
                Rekap
            </a>
        </div>
    </div>

    <!-- Scanner Main Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Camera Viewport (Matches reference mobile mockup camera scan) -->
        <div class="lg:col-span-7 bg-[#141822] border border-gaston-border rounded-[32px] p-5 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-white flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full" :class="isScanning ? 'bg-gaston-orange animate-pulse' : 'bg-slate-500'"></span>
                    <span>Pindai QR Member</span>
                </h3>
                <button @click="toggleCamera()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#1b212c] border border-gaston-border text-slate-300 hover:text-white transition-colors">
                    <span x-text="isScanning ? 'Jeda Kamera' : 'Mulai Kamera'"></span>
                </button>
            </div>

            <!-- Video Viewport with Neon Concentric Rings Style -->
            <div class="relative bg-black rounded-3xl overflow-hidden min-h-[320px] flex items-center justify-center border-2 border-gaston-border">
                <div id="reader" class="w-full"></div>
                
                <div x-show="!cameraStarted" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-[#0d1017]/95 z-10">
                    <div class="w-16 h-16 rounded-3xl bg-gaston-orange/15 text-gaston-orange flex items-center justify-center mb-3 border border-gaston-orange/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-sm font-black text-white">Kamera Belum Aktif</p>
                    <p class="text-xs text-slate-400 mt-1 mb-4">Izinkan akses kamera browser untuk memindai QR member.</p>
                    <button @click="startCamera()" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg shadow-gaston-orange/30 transition-all">
                        Izinkan & Aktifkan Kamera
                    </button>
                </div>
            </div>

            <!-- Alternative: Input Manual Member ID / QR Token -->
            <div class="pt-2 border-t border-gaston-border">
                <p class="text-xs text-slate-400 font-semibold mb-2">Atau cari berdasarkan Member ID / Token jika kamera bermasalah:</p>
                <form @submit.prevent="lookupManual()" class="flex space-x-2">
                    <input type="text" x-model="manualInput" placeholder="Contoh: GST-00001"
                           class="flex-1 bg-[#0d1017] border border-gaston-border rounded-xl px-4 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-gaston-orange">
                    <button type="submit" class="px-4 py-2 bg-[#1b212c] hover:bg-slate-800 text-slate-200 text-xs font-bold rounded-xl transition-colors border border-gaston-border">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Verification & Confirmation Card (Anti Titip Absen - Matches Phone Notification in Reference) -->
        <div class="lg:col-span-5 bg-[#141822] border border-gaston-border rounded-[32px] p-6 shadow-2xl relative min-h-[400px] flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-gaston-border flex items-center justify-between">
                    <span>Hasil Pemindaian</span>
                    <span class="text-[10px] text-gaston-orange font-bold uppercase tracking-widest">VERIFIKASI FISIK</span>
                </h3>

                <!-- Idle State -->
                <div x-show="!currentMember && !errorMessage && !alreadyCheckedIn" class="py-14 text-center text-slate-500 space-y-3">
                    <div class="w-14 h-14 rounded-3xl bg-[#0d1017] mx-auto flex items-center justify-center text-slate-600 border border-gaston-border">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-400">Arahkan kamera ke QR Member untuk memverifikasi identitas.</p>
                </div>

                <!-- Error Message Alert -->
                <div x-show="errorMessage" x-cloak class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 space-y-2">
                    <div class="flex items-center space-x-2 font-bold text-xs text-rose-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                    <button @click="resetScan()" class="text-[11px] underline text-rose-400 font-bold block">Scan Ulang</button>
                </div>

                <!-- Duplicate Attendance Alert -->
                <div x-show="alreadyCheckedIn" x-cloak class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 space-y-3">
                    <div class="flex items-center space-x-2 font-bold text-sm text-amber-400">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Sudah Hadir</span>
                    </div>
                    <div class="text-xs space-y-1 text-slate-300">
                        <p class="font-bold text-white text-sm" x-text="currentMember?.name"></p>
                        <p class="font-mono text-gaston-orange font-bold" x-text="currentMember?.member_id"></p>
                        <p class="text-slate-400">Jam Check-in: <span class="font-bold text-white" x-text="checkedInTime"></span> WIB</p>
                    </div>
                    <button @click="resetScan()" class="w-full py-2.5 bg-[#0d1017] hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-colors border border-gaston-border">
                        Lanjut Scan Member Berikutnya
                    </button>
                </div>

                <!-- Active Member Identity Card (Photo + Verification Info) -->
                <div x-show="currentMember && !alreadyCheckedIn" x-cloak class="space-y-4">
                    <div class="p-4 rounded-2xl bg-[#0d1017] border border-gaston-border flex items-center space-x-4">
                        <template x-if="currentMember?.photo_url">
                            <img :src="currentMember?.photo_url" class="w-20 h-20 rounded-2xl object-cover border-2 border-gaston-orange shadow-lg shadow-gaston-orange/30">
                        </template>
                        <template x-if="!currentMember?.photo_url">
                            <div class="w-20 h-20 rounded-2xl bg-[#1b212c] text-gaston-orange flex items-center justify-center font-black text-2xl border border-gaston-border">
                                <span x-text="currentMember?.name ? currentMember.name.charAt(0).toUpperCase() : '?'"></span>
                            </div>
                        </template>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-black text-white text-base truncate" x-text="currentMember?.name"></h4>
                            <p class="text-xs font-mono font-bold text-gaston-orange" x-text="currentMember?.member_id"></p>
                            <p class="text-[11px] text-slate-400 mt-1 truncate" x-text="currentMember?.email"></p>
                            <div class="mt-2">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                      :class="isRegistered ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
                                      x-text="isRegistered ? 'Status: Terdaftar' : 'Belum Terdaftar (Walk-in)'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#0d1017] border border-gaston-border text-xs text-slate-400">
                        <p class="font-bold text-gaston-orange mb-0.5">⚠️ Verifikasi Wajah:</p>
                        <p>Pastikan orang yang hadir di GOR cocok dengan foto profil di atas sebelum konfirmasi kehadiran.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div x-show="currentMember && !alreadyCheckedIn" x-cloak class="pt-4 border-t border-gaston-border flex space-x-2">
                <button @click="resetScan()" type="button" class="flex-1 py-3.5 bg-[#1b212c] hover:bg-slate-800 text-slate-300 rounded-2xl text-xs font-bold transition-colors border border-gaston-border">
                    Tolak / Batal
                </button>

                <template x-if="isRegistered">
                    <button @click="confirmAttendance(false)" :disabled="submitting"
                            class="flex-1 py-3.5 bg-gradient-to-r from-gaston-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-gaston-orange/30 transition-all flex items-center justify-center space-x-1">
                        <span x-text="submitting ? 'Menyimpan...' : '✓ Konfirmasi Hadir'"></span>
                    </button>
                </template>

                <template x-if="!isRegistered">
                    <button @click="confirmAttendance(true)" :disabled="submitting"
                            class="flex-1 py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-2xl text-xs font-black shadow-lg transition-all flex items-center justify-center space-x-1">
                        <span x-text="submitting ? 'Menyimpan...' : '+ Tambahkan Walk-in'"></span>
                    </button>
                </template>
            </div>
        </div>

    </div>

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

            this.html5QrCode = new window.Html5Qrcode("reader");
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
                this.html5QrCode.pause();
                this.isScanning = false;
            } else {
                this.html5QrCode.resume();
                this.isScanning = true;
            }
        },

        onScanSuccess(decodedText) {
            const now = Date.now();
            // 3-second cooldown for the same code
            if (this.lastScannedCode === decodedText && (now - this.lastScannedTime) < 3000) {
                return;
            }

            if (this.currentMember || this.submitting || this.isLookingUp) {
                return;
            }

            this.lastScannedCode = decodedText;
            this.lastScannedTime = now;
            this.verifyIdentifier(decodedText);
        },

        lookupManual() {
            if (!this.manualInput.trim() || this.isLookingUp || this.submitting) return;
            const token = this.manualInput.trim();
            this.lastScannedCode = token;
            this.lastScannedTime = Date.now();
            this.verifyIdentifier(token);
            this.manualInput = '';
        },

        verifyIdentifier(token) {
            this.errorMessage = null;
            this.alreadyCheckedIn = false;
            this.isLookingUp = true;

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
                    return;
                }

                if (!data.success && data.code !== 'NOT_REGISTERED') {
                    this.errorMessage = data.message;
                    this.currentMember = null;
                    return;
                }

                this.currentMember = data.member;
                this.isRegistered = data.is_registered;
                this.playBeep();
            })
            .catch(err => {
                this.isLookingUp = false;
                this.errorMessage = err.message || "Gagal memproses QR Code. Silakan coba lagi.";
            });
        },

        confirmAttendance(isWalkIn) {
            if (!this.currentMember || this.submitting) return;
            this.submitting = true;

            const confirmedMember = this.currentMember;

            fetch("{{ route('admin.sessions.scan.confirm', $session) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    member_id: confirmedMember.id,
                    is_walk_in: isWalkIn
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
                    // Reset scan and enforce 3-second cooldown before the same QR is scanned again
                    this.lastScannedTime = Date.now();
                    this.resetScan();
                } else {
                    alert(data.message || "Gagal menyimpan konfirmasi.");
                }
            })
            .catch(err => {
                this.submitting = false;
                alert(err.message || "Terjadi kesalahan jaringan.");
            });
        },

        resetScan() {
            this.currentMember = null;
            this.errorMessage = null;
            this.alreadyCheckedIn = false;
            this.isRegistered = false;
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
        }
    };
}
</script>
@endpush
