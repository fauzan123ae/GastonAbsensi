@extends('layouts.admin')

@section('title', 'QR Code Member - ' . $member->name)

@section('content')
<div class="max-w-xl mx-auto space-y-6 text-center">

    <div class="text-left">
        <a href="{{ route('admin.members.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-content-muted hover:text-content-heading mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kembali ke Daftar Member</span>
        </a>
        <div class="flex items-center space-x-2">
            <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-6 w-auto object-contain">
            <h2 class="text-2xl font-black text-content-heading">QR Code Permanen Member</h2>
        </div>
        <p class="text-xs text-content-muted mt-1">QR Code ini unik, aman, dan berlaku permanen untuk seluruh sesi olahraga.</p>
    </div>

    <!-- QR Badge Card with Phone Mockup Aesthetics -->
    <div class="bg-surface-elevated border border-line-base rounded-[36px] p-8 sm:p-10 shadow-2xl relative overflow-hidden flex flex-col items-center">
        <!-- Glow -->
        <div class="absolute -top-20 -right-20 w-56 h-56 bg-gaston-orange/15 rounded-full blur-3xl pointer-events-none"></div>

        @if($member->photo)
            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-20 h-20 rounded-3xl object-cover border-2 border-gaston-orange shadow-lg shadow-gaston-orange/30 mb-4">
        @else
            <div class="w-20 h-20 rounded-3xl bg-surface-elevated text-gaston-orange flex items-center justify-center font-black text-2xl border border-line-base shadow-lg mb-4">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
        @endif

        <h3 class="text-xl font-black text-content-heading">{{ $member->name }}</h3>
        <p class="text-xs text-content-muted mt-0.5">{{ $member->email }}</p>

        <div class="mt-4 px-3.5 py-1 rounded-full bg-surface-input border border-line-base text-xs font-mono font-bold text-gaston-orange">
            MEMBER ID: {{ $member->member_id }}
        </div>

        <!-- Rendered SVG QR Code with concentric glow -->
        <div class="mt-6 p-4 bg-white rounded-3xl shadow-xl flex items-center justify-center border-4 border-gaston-orange/30">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate($member->qr_token) !!}
        </div>

        <p class="text-[11px] font-mono text-content-faint mt-4">TOKEN: {{ $member->qr_token }}</p>

        <div class="mt-6 pt-6 border-t border-line-base w-full flex items-center justify-center space-x-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-surface-elevated hover:bg-surface-elevated text-content-body text-xs font-bold border border-line-base transition-colors">
                🖨️ Cetak Kartu QR
            </button>
        </div>
    </div>

</div>
@endsection
