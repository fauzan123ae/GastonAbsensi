import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gaston: {
                    dark: '#080a0f',
                    body: '#0a0d13',
                    card: '#11141c',
                    cardElevated: '#141822',
                    cardHover: '#181d29',
                    border: '#1f2533',
                    borderLight: '#2a3245',
                    input: '#0d1017',
                    red: {
                        DEFAULT: '#e50914',
                        crimson: '#dc2626',
                        dark: '#b91c1c',
                        hover: '#ff1e27',
                        light: '#ef4444',
                        glow: 'rgba(229, 9, 20, 0.45)',
                        subtle: 'rgba(229, 9, 20, 0.12)',
                    },
                    orange: {
                        DEFAULT: '#e50914',
                        hover: '#ff1e27',
                        light: '#ef4444',
                        glow: 'rgba(229, 9, 20, 0.4)',
                        subtle: 'rgba(229, 9, 20, 0.12)',
                    },
                    accent: '#e50914',
                }
            },
            boxShadow: {
                'gaston': '0 8px 30px rgba(0, 0, 0, 0.7)',
                'gaston-glow': '0 0 25px rgba(229, 9, 20, 0.4)',
                'gaston-glow-lg': '0 0 45px rgba(229, 9, 20, 0.55)',
                'gaston-ring': '0 0 0 2px rgba(229, 9, 20, 0.6)',
            }
        },
    },
    plugins: [],
};
