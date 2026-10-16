<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - Gaston YK</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#080a0f">
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('gaston-theme');
                var prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
                var isLight = saved ? (saved === 'light') : prefersLight;
                if (isLight) {
                    document.documentElement.classList.add('light');
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    var meta = document.querySelector('meta[name="theme-color"]');
                    if (meta) meta.setAttribute('content', '#f4f6fa');
                } else {
                    document.documentElement.classList.remove('light');
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-surface-page gaston-stripes-bg gaston-diagonal-lines text-content-body min-h-screen flex items-center justify-center p-4 antialiased relative overflow-hidden" x-data="{ showPassword: false }">
    @include('components.mobile-splash')
    <x-theme-toggle :floating="true" />

    <!-- Ambient Glow matching reference mobile screens -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-[#e50914]/15 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-red-900/15 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Diagonal slash decorative background lines matching Phone 1 & 2 in showcase -->
    <div class="absolute -bottom-10 -right-10 w-64 h-64 pointer-events-none opacity-30">
        <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-4 shadow-[0_0_10px_#e50914]"></div>
        <div class="w-full h-0.5 bg-line-strong transform -rotate-45 translate-y-10"></div>
        <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-16 shadow-[0_0_10px_#e50914]"></div>
    </div>
    <div class="absolute -top-10 -left-10 w-64 h-64 pointer-events-none opacity-20">
        <div class="w-full h-1 bg-line-strong transform -rotate-45 translate-y-6"></div>
        <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-14 shadow-[0_0_10px_#e50914]"></div>
    </div>

    <div class="w-full max-w-[420px] bg-surface border border-line-base rounded-[36px] p-8 sm:p-10 shadow-gaston relative z-10 overflow-hidden">
        
        <!-- Corner subtle slash inside card -->
        <div class="absolute -bottom-6 -left-6 w-24 h-24 pointer-events-none opacity-20">
            <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-2"></div>
            <div class="w-full h-0.5 bg-line-strong transform -rotate-45 translate-y-6"></div>
        </div>
        
        <!-- Header with Gaston Logo matching reference screenshot 2nd mobile phone -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-16 w-auto object-contain drop-shadow-[0_0_20px_rgba(229,9,20,0.35)]">
            </div>
            <h1 class="text-base font-black tracking-wider text-content-heading uppercase">GASTON YK</h1>
            
            <div class="mt-4">
                <h2 class="text-xl font-black text-content-heading">Masuk ke Akun</h2>
                <p class="text-xs text-content-muted mt-1">Silakan login untuk melanjutkan absensi</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-300 text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-300 text-xs space-y-1 font-medium">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email / Member ID Field -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-faint">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"></path></svg>
                    </div>
                    <input type="text" name="email" id="email" required autofocus value="{{ old('email') }}"
                           placeholder="Email atau Member ID"
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-11 pr-4 py-3.5 text-xs text-content-heading placeholder-content-faint focus:outline-none focus:border-gaston-red focus:ring-1 focus:ring-gaston-red transition-all">
                </div>
            </div>

            <!-- Password Field with eye toggle -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-faint">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                           placeholder="Password"
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-11 pr-11 py-3.5 text-xs text-content-heading placeholder-content-faint focus:outline-none focus:border-gaston-red focus:ring-1 focus:ring-gaston-red transition-all">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-content-faint hover:text-content-body">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Remember & Forgot Password -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 text-content-muted cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-surface-input border-line-base text-gaston-red focus:ring-gaston-red/30">
                    <span class="text-[11px]">Ingat Saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-[11px] text-gaston-red hover:underline cursor-pointer font-bold">Lupa Password?</a>
            </div>

            <!-- Red Button Masuk -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-[#e50914] hover:bg-[#ff1e27] text-white font-black py-3.5 px-4 rounded-2xl shadow-xl shadow-[#e50914]/30 active:scale-[0.99] transition-all text-xs tracking-wider uppercase">
                    Masuk
                </button>
            </div>

            <!-- Alternative / Outline Register Button -->
            <div class="pt-2">
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-line-base"></div>
                    <span class="flex-shrink mx-3 text-[10px] text-content-faint uppercase font-bold">atau</span>
                    <div class="flex-grow border-t border-line-base"></div>
                </div>

                <a href="{{ route('register') }}" class="w-full block text-center bg-surface hover:bg-surface-hover text-content-body hover:text-content-heading font-bold py-3 px-4 rounded-2xl border border-line-base transition-all text-xs">
                    Daftar Akun
                </a>
            </div>
        </form>

        <div class="mt-6 text-center text-[10px] text-content-faint">
            &copy; {{ date('Y') }} Gaston YK. All rights reserved.
        </div>
    </div>

</body>
</html>
