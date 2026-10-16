@props([
    'floating' => false,
    'showLabel' => false,
])

<div x-data="themeSwitcher()"
     x-init="init()"
     class="{{ $floating ? 'fixed top-4 right-4 sm:top-6 sm:right-6 z-50' : 'relative inline-flex items-center' }}">

    <button type="button"
            @click="toggle()"
            :title="isLight ? 'Ganti ke tema gelap' : 'Ganti ke tema terang'"
            :aria-label="isLight ? 'Ganti ke tema gelap' : 'Ganti ke tema terang'"
            class="group relative inline-flex items-center justify-center transition-all duration-200 focus:outline-none select-none {{ $floating ? 'px-3.5 py-2 rounded-2xl bg-surface hover:bg-surface-hover border border-line-base shadow-gaston backdrop-blur-md hover:border-gaston-red/50 hover:shadow-[0_0_20px_rgba(229,9,20,0.25)] active:scale-95 text-xs' : 'p-2 rounded-xl text-content-muted hover:text-content-heading hover:bg-surface-hover border border-transparent hover:border-line-base transition-colors focus:outline-none active:scale-95' }}">

        <!-- Sun Icon: shown in Dark Mode to switch to Light -->
        <span x-show="!isLight"
              x-transition:enter="transition ease-out duration-200 transform"
              x-transition:enter-start="opacity-0 rotate-90 scale-50"
              x-transition:enter-end="opacity-100 rotate-0 scale-100"
              class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-amber-400 group-hover:rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="4" stroke-width="2" stroke="currentColor" fill="rgba(251, 191, 36, 0.2)"></circle>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
            </svg>
            @if($floating || $showLabel)
                <span class="hidden sm:inline font-bold text-xs text-content-body group-hover:text-content-heading transition-colors">Tema Terang</span>
            @endif
        </span>

        <!-- Moon Icon: shown in Light Mode to switch to Dark -->
        <span x-show="isLight"
              x-cloak
              x-transition:enter="transition ease-out duration-200 transform"
              x-transition:enter-start="opacity-0 -rotate-90 scale-50"
              x-transition:enter-end="opacity-100 rotate-0 scale-100"
              class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-indigo-500 group-hover:-rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" fill="rgba(99, 102, 241, 0.15)" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            @if($floating || $showLabel)
                <span class="hidden sm:inline font-bold text-xs text-content-body group-hover:text-content-heading transition-colors">Tema Gelap</span>
            @endif
        </span>

    </button>
</div>
