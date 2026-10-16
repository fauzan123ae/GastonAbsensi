<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setel Ulang Password - Gaston YK</title>
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
<body class="bg-surface-page gaston-stripes-bg gaston-diagonal-lines text-content-body min-h-screen flex items-center justify-center p-4 antialiased relative overflow-hidden transition-colors duration-200" x-data="{ showPassword: false }">
    <x-theme-toggle :floating="true" />

    <!-- Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-[#e50914]/15 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-red-900/15 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Diagonal slash decorative background lines -->
    <div class="absolute -bottom-10 -right-10 w-64 h-64 pointer-events-none opacity-30">
        <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-4 shadow-[0_0_10px_#e50914]"></div>
        <div class="w-full h-0.5 bg-line-strong transform -rotate-45 translate-y-10"></div>
        <div class="w-full h-1.5 bg-[#e50914] transform -rotate-45 translate-y-16 shadow-[0_0_10px_#e50914]"></div>
    </div>

    <div class="w-full max-w-[420px] bg-surface border border-line-base rounded-[36px] p-8 sm:p-10 shadow-2xl relative z-10 overflow-hidden">
        
        <!-- Corner subtle slash -->
        <div class="absolute -bottom-6 -left-6 w-24 h-24 pointer-events-none opacity-20">
            <div class="w-full h-1 bg-[#e50914] transform -rotate-45 translate-y-2"></div>
            <div class="w-full h-0.5 bg-line-strong transform -rotate-45 translate-y-6"></div>
        </div>

        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Gaston Logo" class="h-16 w-auto object-contain drop-shadow-[0_0_20px_rgba(229,9,20,0.35)]">
            </div>
            <h1 class="text-base font-black tracking-wider text-content-heading uppercase">GASTON YK</h1>
            
            <div class="mt-4">
                <h2 class="text-xl font-black text-content-heading">Buat Password Baru</h2>
                <p class="text-xs text-content-muted mt-1">Masukkan kata sandi baru untuk akun Anda</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 dark:text-rose-300 text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Field -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-faint">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" name="email" id="email" required value="{{ old('email', $email) }}"
                           placeholder="Alamat Email"
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-11 pr-4 py-3.5 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
                </div>
            </div>

            <!-- New Password -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-faint">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                           placeholder="Password Baru (min 6 karakter)"
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-11 pr-11 py-3.5 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-content-muted hover:text-content-heading">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-content-faint">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                           placeholder="Ulangi Password Baru"
                           class="w-full bg-surface-input border border-line-base rounded-2xl pl-11 pr-4 py-3.5 text-xs text-content-heading placeholder:text-content-faint focus:outline-none focus:border-[#e50914] focus:ring-1 focus:ring-[#e50914] transition-all">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-[#e50914] hover:bg-[#ff1e27] text-white font-black py-3.5 px-4 rounded-2xl shadow-xl shadow-[#e50914]/30 active:scale-[0.99] transition-all text-xs tracking-wider uppercase">
                    Simpan Password Baru
                </button>
            </div>

            <div class="pt-2 text-center">
                <a href="{{ route('login') }}" class="text-xs text-content-muted hover:text-content-heading transition-colors inline-flex items-center space-x-1">
                    <span>&larr;</span>
                    <span>Kembali ke Halaman Login</span>
                </a>
            </div>
        </form>

        <div class="mt-8 text-center text-[10px] text-content-faint">
            &copy; {{ date('Y') }} Gaston YK. All rights reserved.
        </div>
    </div>

</body>
</html>
