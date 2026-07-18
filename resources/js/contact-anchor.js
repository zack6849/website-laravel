const contactHash = '#contact';
const highlightClass = 'contact-highlight';
const highlightDurationMs = 1200;

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const contactUrl = () => `${window.location.pathname}${window.location.search}${contactHash}`;

const highlightContact = (contact) => {
    if (contact.classList.contains(highlightClass)) {
        return;
    }

    contact.classList.add(highlightClass);

    const clearHighlight = () => contact.classList.remove(highlightClass);
    contact.addEventListener('animationend', clearHighlight, { once: true });
    window.setTimeout(clearHighlight, highlightDurationMs);
};

const showContactOverview = (contact, { updateHash = true, behavior = 'smooth' } = {}) => {
    if (updateHash && window.location.hash !== contactHash) {
        window.history.pushState(null, '', contactUrl());
    }

    window.scrollTo({
        top: 0,
        behavior: prefersReducedMotion() ? 'auto' : behavior,
    });

    highlightContact(contact);
};

document.addEventListener('DOMContentLoaded', () => {
    const contact = document.getElementById('contact');

    if (!contact) {
        return;
    }

    document.querySelectorAll(`a[href="${contactHash}"]`).forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            showContactOverview(contact);
        });
    });

    const correctContactHashScroll = () => {
        if (window.location.hash === contactHash) {
            showContactOverview(contact, { updateHash: false, behavior: 'auto' });
        }
    };

    window.requestAnimationFrame(correctContactHashScroll);
    window.addEventListener('load', correctContactHashScroll, { once: true });

    window.addEventListener('hashchange', () => {
        if (window.location.hash === contactHash) {
            showContactOverview(contact, { updateHash: false });
        }
    });
});
