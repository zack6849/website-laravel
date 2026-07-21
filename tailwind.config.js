/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "resources/**/*.php",
        "resources/**/*.js",
        "resources/**/*.vue",
        './vendor/laravel/jetstream/**/*.blade.php',
        "app/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                // primary action/accent color (buttons, active states, links, featured
                // highlights) — was scattered across teal/emerald/blue before this was
                // centralized; values match Tailwind's built-in teal scale exactly, so
                // this is a rename, not a visual change. Change it here, not per-usage.
                brand: {
                    50: 'oklch(98.4% 0.014 180.72)',
                    100: 'oklch(95.3% 0.051 180.801)',
                    200: 'oklch(91% 0.096 180.426)',
                    300: 'oklch(85.5% 0.138 181.071)',
                    500: 'oklch(70.4% 0.14 182.503)',
                    600: 'oklch(60% 0.118 184.704)',
                    700: 'oklch(51.1% 0.096 186.391)',
                    900: 'oklch(38.6% 0.063 188.416)',
                },
                // site header/nav chrome color, kept distinct from `brand` since it's a
                // surface color, not an action color. Matches Tailwind's emerald scale.
                nav: {
                    200: 'oklch(90.5% 0.093 164.15)',
                    400: 'oklch(76.5% 0.177 163.223)',
                    950: 'oklch(26.2% 0.051 172.552)',
                },
            },
        },
    },
    plugins: [],
}
