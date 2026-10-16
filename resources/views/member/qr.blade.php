@extends('layouts.member')

@section('title', 'QR Code Saya')

@section('content')
<div class="max-w-md mx-auto space-y-6 text-center" x-data="{ fullscreen: false }">

    <div>
        <div class="flex items-center justify-center space-x-2 mb-1">
            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
            <h2 class="text-2xl font-black text-content-heading">QR Code Pribadi</h2>
        </div>
        <p class="text-xs text-content-muted">Tunjukkan QR ini kepada admin saat tiba di GOR untuk verifikasi kehadiran.</p>
    </div>

    @php
        $selectedSessionId = request('session_id') ?: ($todaySessions->first()?->id ?? null);
        $currentSession = $todaySessions->firstWhere('id', $selectedSessionId) ?? $todaySessions->first();
    @endphp

    <!-- Panel Kegiatan Hari Ini (Direct Activity Attendance Status & Picker) -->
    <div class="bg-surface border border-line-base rounded-[28px] p-5 text-left shadow-xl space-y-3"
         x-data="{
             selectedId: {{ $currentSession?->id ?? 'null' }},
             sessions: {{ Js::from($todaySessions->map(fn($s) => [
                 'id' => $s->id,
                 'sport_name' => $s->sport->name,
                 'location_name' => $s->location->name,
                 'time' => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5) . ' WIB',
                 'status' => $s->status,
                 'has_attended' => isset($attendances[$s->id]) && $attendances[$s->id]->status === 'present',
                 'has_permission' => isset($permissions[$s->id]) && $permissions[$s->id]->status === 'approved',
             ])) }}
         }">
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#e50914] animate-pulse"></span>
                <h3 class="text-xs font-black uppercase tracking-wider text-content-heading">Kegiatan Absensi Hari Ini</h3>
            </div>
            <span class="text-[10px] font-bold text-content-muted bg-surface-page px-2.5 py-1 rounded-full border border-line-base">
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>

        @if($todaySessions->isEmpty())
            <div class="p-4 rounded-2xl bg-surface-input border border-line-base text-center">
                <p class="text-xs font-bold text-content-body">Tidak ada jadwal kegiatan olahraga hari ini</p>
                <p class="text-[10px] text-content-faint mt-0.5">Admin belum membuka sesi absensi untuk hari ini.</p>
            </div>
        @else
            <!-- Pilihan Kegiatan Ketika Ada Lebih Dari 1 Sesi Hari Ini -->
            @if($todaySessions->count() > 1)
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-content-body">
                        Pilih Kegiatan Absensi (Tersedia {{ $todaySessions->count() }} Kegiatan):
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($todaySessions as $ses)
                            @php
                                $isAtt = isset($attendances[$ses->id]) && $attendances[$ses->id]->status === 'present';
                                $isPerm = isset($permissions[$ses->id]) && $permissions[$ses->id]->status === 'approved';
                            @endphp
                            <button type="button"
                                    @click="selectedId = {{ $ses->id }}"
                                    class="p-3 rounded-2xl text-left border transition-all flex items-start justify-between group"
                                    :class="selectedId === {{ $ses->id }} ? 'bg-surface-elevated border-[#e50914] ring-1 ring-[#e50914]' : 'bg-surface-input border-line-base hover:border-line-strong'">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-black text-content-heading group-hover:text-[#e50914] transition-colors">
                                            {{ $ses->sport->name }}
                                        </span>
                                        <span class="text-[9px] font-bold text-content-muted">
                                            {{ substr($ses->start_time, 0, 5) }} - {{ substr($ses->end_time, 0, 5) }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-content-muted mt-0.5 flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-[#e50914]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <span class="truncate">{{ $ses->location->name }}</span>
                                    </p>
                                </div>
                                <div>
                                    @if($isAtt)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            Hadir
                                        </span>
                                    @elseif($isPerm)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            Izin
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-surface-elevated text-content-muted border border-line-strong">
                                            Belum Absen
                                        </span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Detail Sesi Aktif yang Terpilih -->
            <div class="p-3.5 rounded-2xl bg-surface-page border border-line-base flex items-center justify-between">
                <div>
                    <template x-for="s in sessions" :key="s.id">
                        <div x-show="selectedId === s.id" class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-black text-content-heading" x-text="s.sport_name"></span>
                                <span class="text-[10px] font-mono text-[#e50914]" x-text="s.time"></span>
                            </div>
                            <p class="text-[11px] text-content-muted" x-text="s.location_name"></p>
                        </div>
                    </template>
                </div>

                <div>
                    <template x-for="s in sessions" :key="s.id">
                        <div x-show="selectedId === s.id">
                            <template x-if="s.has_attended">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    <span>Sudah Hadir</span>
                                </span>
                            </template>
                            <template x-if="!s.has_attended && s.has_permission">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-500/15 text-blue-400 border border-blue-500/30">
                                    Izin Disetujui
                                </span>
                            </template>
                            <template x-if="!s.has_attended && !s.has_permission">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-[#e50914]/20 text-[#e50914] border border-[#e50914]/30 animate-pulse">
                                    Siap Di-scan
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        @endif

    </div>

    <!-- Phone-Mockup Styled Attendance Card matching reference mockup center screen -->
    <div class="bg-surface border border-line-base rounded-[36px] p-6 sm:p-8 shadow-2xl shadow-black/80 relative flex flex-col items-center overflow-hidden">
        
        <!-- Glowing Ring behind card -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-[#e50914]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-red-900/15 rounded-full blur-3xl pointer-events-none"></div>

        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-3xl object-cover border-2 border-[#e50914] shadow-lg shadow-[#e50914]/30 mb-3">
        @else
            <div class="w-20 h-20 rounded-3xl bg-surface-input text-[#e50914] flex items-center justify-center font-black text-2xl border border-line-base shadow-lg mb-3">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif

        <h3 class="text-xl font-black text-content-heading">{{ $user->name }}</h3>
        <p class="text-xs font-mono font-bold text-[#e50914] mt-1 px-3.5 py-1 bg-surface-page rounded-full border border-line-base">
            {{ $user->member_id }}
        </p>

        <!-- SVG QR Code with Concentric Glowing Rings (mimicking center phone in reference image) -->
        <div class="mt-6 relative flex items-center justify-center">
            <div class="absolute inset-0 rounded-3xl gaston-ring-pulsing pointer-events-none"></div>
            <div class="p-4 bg-white rounded-3xl shadow-2xl relative z-10 border-4 border-[#e50914]/40">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(230)->generate($user->qr_token) !!}
            </div>
        </div>

        <div class="mt-5 flex items-center space-x-2 text-xs text-content-muted">
            <span class="w-2 h-2 rounded-full bg-[#e50914] animate-pulse"></span>
            <span class="font-semibold">QR Permanen Aktif</span>
        </div>

        <div class="mt-6 w-full">
            <button @click="fullscreen = true" class="w-full py-3.5 bg-[#e50914] hover:bg-[#ff1e27] text-white font-extrabold rounded-2xl text-xs shadow-xl shadow-[#e50914]/30 transition-all flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-5 15l-5-5m0 0h4m-4 0v-4m16 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                <span>Tampilan Layar Penuh (Fullscreen)</span>
            </button>
        </div>
    </div>

    <!-- Fullscreen Modal View for phone screen scanning -->
    <div x-show="fullscreen" x-cloak class="fixed inset-0 z-50 bg-surface-page flex flex-col items-center justify-center p-6 select-none">
        <button @click="fullscreen = false" class="absolute top-6 right-6 p-3 bg-surface text-content-body hover:text-content-heading rounded-full border border-line-base">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="text-center space-y-4">
            <div class="flex items-center justify-center mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-10 w-auto object-contain">
            </div>
            <h2 class="text-2xl font-black text-content-heading">{{ $user->name }}</h2>
            <p class="text-sm font-mono text-[#e50914] font-bold">{{ $user->member_id }}</p>

            <div class="p-6 bg-white rounded-3xl shadow-2xl inline-block border-4 border-[#e50914]">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)->generate($user->qr_token) !!}
            </div>

            <p class="text-xs text-content-muted">Arahkan layar HP ini ke kamera scanner admin di GOR.</p>
        </div>
    </div>

</div>
@endsection
