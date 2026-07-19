const contactHash = '#contact';
const highlightClass = 'contact-highlight';

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const contactUrl = () => `${window.location.pathname}${window.location.search}${contactHash}`;

const highlightContact = (contact) => {
    if (!contact.classList.contains(highlightClass)) {
        contact.classList.add(highlightClass);
    }
};

const focusContactHeading = (contact) => {
    const heading = contact.querySelector('#contact-heading');

    if (heading) {
        heading.focus({ preventScroll: true });
    }
};

const showContactOverview = (contact, { updateHash = true, behavior = 'smooth' } = {}) => {
    if (updateHash && window.location.hash !== contactHash) {
        window.history.pushState(null, '', contactUrl());
    }

    contact.scrollIntoView({
        behavior: prefersReducedMotion() ? 'auto' : behavior,
        block: 'start',
    });

    highlightContact(contact);
    window.setTimeout(() => focusContactHeading(contact), prefersReducedMotion() ? 0 : 300);
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
