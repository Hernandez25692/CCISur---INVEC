import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Colores institucionales CCISur (tomados del logo oficial)
            colors: {
                brand: {
                    gold: {
                        50: '#FBF8EA',
                        100: '#F5EFD0',
                        200: '#EADFA3',
                        400: '#C9B554',
                        DEFAULT: '#AF9730',
                        600: '#8F7A26',
                        700: '#6F5F1E',
                    },
                    navy: {
                        50: '#EEF2F8',
                        100: '#D9E2F0',
                        700: '#0B2A5E',
                        DEFAULT: '#031940',
                        900: '#020F28',
                    },
                },
            },
        },
    },

    plugins: [forms],
};
