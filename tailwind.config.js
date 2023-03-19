/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "resources/**/*.php",
        "resources/**/*.js",
        "resources/**/*.vue",
        './vendor/laravel/jetstream/**/*.blade.php',
        './vendor/rappasoft/laravel-livewire-tables/resources/views/tailwind/**/*.blade.php',
        "app/**/*.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
