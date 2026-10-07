<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Gaston YK</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#080a0f] gaston-stripes-bg gaston-diagonal-lines text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased relative overflow-hidden">

    <!-- Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-[#e50914]/15 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-red-900/15 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Diagonal slash decorative background lines -->
    <div class="absolute -bottom-10 -right-10 w-64 h-64 pointer-events-none opacity-30">
        <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-4 shadow-[0_0_10px_#e50914]"></div>
        <div class="w-full h-0.5 bg-white transform -rotate-45 translate-y-10"></div>
        <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-16 shadow-[0_0_10px_#e50914]"></div>
    </div>

    <div class="w-full max-w-[420px] bg-[#0d1017] border border-[#1f2533] rounded-[36px] p-8 sm:p-10 shadow-2xl shadow-black relative z-10 overflow-hidden">
        
        <!-- Corner subtle slash -->
        <div class="absolute -bottom-6 -left-6 w-24 h-24 pointer-events-none opacity-20">
            <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-2"></div>
            <div class="w-full h-0.5 bg-white transform -rotate-45 translate-y-6"></div>
        </div>

        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-16 w-auto object-contain drop-shadow-[0_0_20px_rgba(229,9,20,0.35)]">
            </div>
            <h1 class="text-base font-black tracking-wider text-white uppercase">GASTON YK</h1>
            
            <div class="mt-4">
                <h2 class="text-xl font-black text-white">Reset Password</h2>
                <p class="text-xs text-slate-400 mt-1">Masukkan email terdaftar untuk menerima tautan setel ulang password</p>
            </div>
        </div>

        @if(session('status'))
            <div class="mb-5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}"
                           placeholder="Alamat Email Terdaftar"
                           class="w-full bg-[#11141c] border border-[#1f2533] rounded-2xl pl-11 pr-4 py-3.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-[#e50914] hover:bg-[#ff1e27] text-white font-black py-3.5 px-4 rounded-2xl shadow-xl shadow-[#e50914]/30 active:scale-[0.99] transition-all text-xs tracking-wider uppercase">
                    Kirim Link Reset
                </button>
            </div>

            <div class="pt-2 text-center">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-white transition-colors inline-flex items-center space-x-1">
                    <span>&larr;</span>
                    <span>Kembali ke Halaman Login</span>
                </a>
            </div>
        </form>

        <div class="mt-8 text-center text-[10px] text-slate-600">
            &copy; {{ date('Y') }} Gaston YK. All rights reserved.
        </div>
    </div>

</body>
</html>
