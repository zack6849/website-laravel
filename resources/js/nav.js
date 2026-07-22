/**
 * Mobile hamburger toggle for the site nav.
 *
 * This is plain JS (not part of the Vue app) on purpose: the nav is present
 * on every page, but the Vue bundle is only loaded on pages that opt into it
 * (`<x-base-page vue="true">`) — driving the nav off Vue's root instance meant
 * it silently stopped working on any page that didn't load vue.js.
 */
document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navContent = document.querySelector('[data-nav-content]');

    if (!navToggle || !navContent) {
        return;
    }

    const closeNav = () => {
        navContent.classList.add('hidden');
    };

    navToggle.addEventListener('click', () => {
        navContent.classList.toggle('hidden');
    });

    navContent.querySelectorAll('a, button[type="submit"]').forEach((el) => {
        el.addEventListener('click', closeNav);
    });
});
