<!-- Mobile Splash Loading Screen matching Gaston YK UI Showcase Phone 1 -->
<div id="gaston-mobile-splash"
     x-data="{ show: false }"
     x-init="
        const hasLoaded = sessionStorage.getItem('gaston_splash_shown');
        if (!hasLoaded) {
            show = true;
            setTimeout(() => {
                show = false;
                sessionStorage.setItem('gaston_splash_shown', 'true');
            }, 1800);
        }
     "
     x-show="show"
     x-transition:leave="transition ease-out duration-700"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95 pointer-events-none"
     class="md:hidden fixed inset-0 z-50 bg-[#080a0f] flex flex-col justify-between items-center px-6 py-12 select-none overflow-hidden"
     style="display: none;">

    <!-- Top Left & Bottom Right Glow Ambient Background -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-[#e50914]/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-red-900/20 rounded-full blur-[100px] pointer-events-none"></div>

    <!-- Decorative Diagonal Accent Lines (Garis-garis miring Gaston) matching Phone 1 mockup -->
    <!-- Top-Left diagonal slashed red/gray lines -->
    <div class="absolute -top-8 -left-8 w-64 h-64 pointer-events-none opacity-40">
        <div class="w-72 h-1.5 bg-[#e50914] transform -rotate-45 translate-y-6 shadow-[0_0_12px_#e50914]"></div>
        <div class="w-60 h-0.5 bg-slate-300 transform -rotate-45 translate-y-12 opacity-80"></div>
        <div class="w-64 h-2 bg-[#1f2533] transform -rotate-45 translate-y-20"></div>
    </div>

    <!-- Bottom-Right angled slash geometry with red and gray lines -->
    <div class="absolute -bottom-10 -right-10 w-80 h-80 pointer-events-none opacity-50">
        <div class="w-80 h-1 bg-white/40 transform -rotate-45 translate-y-4"></div>
        <div class="w-96 h-2 bg-[#e50914] transform -rotate-45 translate-y-12 shadow-[0_0_14px_#e50914]"></div>
        <div class="w-80 h-1 bg-[#e50914] transform -rotate-45 translate-y-20 shadow-[0_0_10px_#e50914]"></div>
        <div class="w-72 h-0.5 bg-slate-400 transform -rotate-45 translate-y-28 opacity-70"></div>
    </div>

    <!-- Bottom-Left subtle diagonal geometric block -->
    <div class="absolute -bottom-14 -left-14 w-60 h-60 pointer-events-none opacity-30">
        <div class="w-64 h-1.5 bg-[#1f2533] transform rotate-45 translate-y-8"></div>
        <div class="w-64 h-2 bg-[#e50914] transform rotate-45 translate-y-16 shadow-[0_0_10px_#e50914]"></div>
    </div>

    <!-- Top Spacer for mobile status bar notch -->
    <div class="w-full flex justify-between items-center text-[11px] font-bold text-slate-500 pt-1 tracking-wider opacity-60">
        <span></span>
        <span></span>
    </div>

    <!-- Center Content: Big Gaston Logo + Title -->
    <div class="flex flex-col items-center justify-center text-center my-auto relative z-10 animate-fade-in">
        <!-- Logo Container with glowing radial backing -->
        <div class="relative mb-6">
            <div class="absolute inset-0 rounded-full bg-[#e50914]/30 blur-2xl animate-pulse"></div>
            <img src="{{ asset('logo/logo.png') }}" 
                 alt="Gaston YK Logo" 
                 class="h-28 sm:h-32 w-auto object-contain relative z-10 drop-shadow-[0_0_30px_rgba(229,9,20,0.6)]">
        </div>

        <!-- Gaston YK Text with red G & YK -->
        <h1 class="text-2xl font-black tracking-widest text-white uppercase drop-shadow-md">
            <span class="text-[#e50914]">G</span>ASTON <span class="text-[#e50914]">YK</span>
        </h1>

        <!-- Slogan / Tagline matching mockup -->
        <div class="mt-8 text-center space-y-1">
            <p class="text-xs text-slate-400 font-medium tracking-wide">Disiplin Hari Ini,</p>
            <p class="text-xs text-slate-400 font-medium tracking-wide">Masa Depan Lebih Baik</p>
        </div>
    </div>

    <!-- Bottom Progress / Loading Bar matching red pill in mockup Phone 1 -->
    <div class="w-full flex flex-col items-center pb-6 relative z-10">
        <div class="w-24 h-1.5 bg-[#1f2533] rounded-full overflow-hidden relative">
            <div class="h-full bg-gradient-to-r from-[#e50914] to-red-500 rounded-full animate-splash-progress"></div>
        </div>
    </div>
</div>
