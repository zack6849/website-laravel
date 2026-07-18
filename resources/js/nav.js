/**
 * Mobile hamburger + "Tools" dropdown toggle for the site nav.
 *
 * This is plain JS (not part of the Vue app) on purpose: the nav is present
 * on every page, but the Vue bundle is only loaded on pages that opt into it
 * (`<x-base-page vue="true">`) — driving the nav off Vue's root instance meant
 * it silently stopped working on any page that didn't load vue.js.
 */
document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navContent = document.querySelector('[data-nav-content]');
    const toolsToggle = document.querySelector('[data-tools-toggle]');
    const toolsMenu = document.querySelector('[data-tools-menu]');
    const toolsChevron = document.querySelector('[data-tools-chevron]');

    if (!navToggle || !navContent) {
        return;
    }

    const closeNav = () => {
        navContent.classList.add('hidden');
        toolsMenu?.classList.add('hidden');
        toolsChevron?.classList.remove('rotate-180');
    };

    navToggle.addEventListener('click', () => {
        navContent.classList.toggle('hidden');
    });

    toolsToggle?.addEventListener('click', () => {
        toolsMenu.classList.toggle('hidden');
        toolsChevron?.classList.toggle('rotate-180');
    });

    navContent.querySelectorAll('a, button[type="submit"]').forEach((el) => {
        el.addEventListener('click', closeNav);
    });
});
