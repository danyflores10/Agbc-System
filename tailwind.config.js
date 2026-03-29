import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sidebar: {
                    DEFAULT: '#F5C518',
                    dark: '#E0B200',
                    light: '#FFF3C4',
                    text: '#1a202c',
                },
                brand: {
                    blue: '#1a365d',
                    'blue-light': '#2a4a7f',
                    gold: '#F5C518',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
