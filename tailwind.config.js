/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
                    deep: 'rgb(var(--color-primary-deep) / <alpha-value>)',
                    light: '#b8203b',
                },
                gold: {
                    DEFAULT: 'rgb(var(--color-gold) / <alpha-value>)',
                    light: '#e2c26d',
                    dark: '#a88632',
                },
                obsidian: '#0f141c',
                surface: '#151c28',
            },
            fontFamily: {
                sans: ['Vazirmatn', 'Tahoma', 'sans-serif'],
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
