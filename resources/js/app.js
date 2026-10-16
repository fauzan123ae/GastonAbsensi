import './bootstrap';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import { Html5Qrcode } from 'html5-qrcode';

import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.flatpickrIndonesian = Indonesian;
window.Html5Qrcode = Html5Qrcode;

// Gaston Theme Management
window.getGastonTheme = function () {
    return document.documentElement.classList.contains('light') ? 'light' : 'dark';
};

window.setGastonTheme = function (theme) {
    const isLight = theme === 'light';
    const root = document.documentElement;

    if (isLight) {
        root.classList.add('light');
        root.classList.remove('dark');
        root.setAttribute('data-theme', 'light');
        try { localStorage.setItem('gaston-theme', 'light'); } catch (e) {}
    } else {
        root.classList.remove('light');
        root.classList.add('dark');
        root.setAttribute('data-theme', 'dark');
        try { localStorage.setItem('gaston-theme', 'dark'); } catch (e) {}
    }

    // Update meta theme-color
    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (metaThemeColor) {
        metaThemeColor.setAttribute('content', isLight ? '#f4f6fa' : '#080a0f');
    }

    window.dispatchEvent(new CustomEvent('gaston-theme-changed', { detail: { theme: isLight ? 'light' : 'dark' } }));
};

window.toggleGastonTheme = function () {
    const next = window.getGastonTheme() === 'dark' ? 'light' : 'dark';
    window.setGastonTheme(next);
    return next;
};

// Enable smooth transitions only after initial load to avoid flash
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.documentElement.classList.add('theme-ready');
    }, 150);
});

Alpine.data('themeSwitcher', () => ({
    isLight: window.getGastonTheme() === 'light',
    init() {
        this.isLight = window.getGastonTheme() === 'light';
        window.addEventListener('gaston-theme-changed', (e) => {
            this.isLight = e.detail.theme === 'light';
        });
        window.addEventListener('storage', (e) => {
            if (e.key === 'gaston-theme') {
                window.setGastonTheme(e.newValue || 'dark');
            }
        });
    },
    toggle() {
        const next = window.toggleGastonTheme();
        this.isLight = next === 'light';
    }
}));

Alpine.start();
