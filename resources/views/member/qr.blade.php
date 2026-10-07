@extends('layouts.member')

@section('title', 'QR Code Saya')

@section('content')
<div class="max-w-md mx-auto space-y-6 text-center" x-data="{ fullscreen: false }">

    <div>
        <div class="flex items-center justify-center space-x-2 mb-1">
            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
            <h2 class="text-2xl font-black text-white">QR Code Pribadi</h2>
        </div>
        <p class="text-xs text-slate-400">Tunjukkan QR ini kepada admin saat tiba di GOR untuk verifikasi kehadiran.</p>
    </div>

    <!-- Phone-Mockup Styled Attendance Card matching reference mockup center screen -->
    <div class="bg-[#11141c] border border-[#1f2533] rounded-[36px] p-6 sm:p-8 shadow-2xl shadow-black/80 relative flex flex-col items-center overflow-hidden">
        
        <!-- Glowing Ring behind card -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-[#e50914]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-red-900/15 rounded-full blur-3xl pointer-events-none"></div>

        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-3xl object-cover border-2 border-[#e50914] shadow-lg shadow-[#e50914]/30 mb-3">
        @else
            <div class="w-20 h-20 rounded-3xl bg-[#0d1017] text-[#e50914] flex items-center justify-center font-black text-2xl border border-[#1f2533] shadow-lg mb-3">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif

        <h3 class="text-xl font-black text-white">{{ $user->name }}</h3>
        <p class="text-xs font-mono font-bold text-[#e50914] mt-1 px-3.5 py-1 bg-[#090c12] rounded-full border border-[#1f2533]">
            {{ $user->member_id }}
        </p>

        <!-- SVG QR Code with Concentric Glowing Rings (mimicking center phone in reference image) -->
        <div class="mt-6 relative flex items-center justify-center">
            <div class="absolute inset-0 rounded-3xl gaston-ring-pulsing pointer-events-none"></div>
            <div class="p-4 bg-white rounded-3xl shadow-2xl relative z-10 border-4 border-[#e50914]/40">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(230)->generate($user->qr_token) !!}
            </div>
        </div>

        <div class="mt-5 flex items-center space-x-2 text-xs text-slate-400">
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
    <div x-show="fullscreen" x-cloak class="fixed inset-0 z-50 bg-[#080a0f] flex flex-col items-center justify-center p-6 select-none">
        <button @click="fullscreen = false" class="absolute top-6 right-6 p-3 bg-[#11141c] text-slate-300 hover:text-white rounded-full border border-[#1f2533]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="text-center space-y-4">
            <div class="flex items-center justify-center mb-1">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-10 w-auto object-contain">
            </div>
            <h2 class="text-2xl font-black text-white">{{ $user->name }}</h2>
            <p class="text-sm font-mono text-[#e50914] font-bold">{{ $user->member_id }}</p>

            <div class="p-6 bg-white rounded-3xl shadow-2xl inline-block border-4 border-[#e50914]">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)->generate($user->qr_token) !!}
            </div>

            <p class="text-xs text-slate-400">Arahkan layar HP ini ke kamera scanner admin di GOR.</p>
        </div>
    </div>

</div>
@endsection
