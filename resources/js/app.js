const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');
const siteHeader = document.querySelector('[data-site-header]');

function setMenuOpen(isOpen) {
    mobileMenu?.classList.toggle('hidden', !isOpen);
    siteHeader?.classList.toggle('menu-open', isOpen);
    menuToggle?.setAttribute('aria-expanded', String(isOpen));
}

menuToggle?.addEventListener('click', () => {
    setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuToggle?.getAttribute('aria-expanded') === 'true') {
        setMenuOpen(false);
        menuToggle.focus();
    }
});

if (siteHeader) {
    const updateHeader = () => siteHeader.classList.toggle('is-scrolled', window.scrollY > 40);
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
}

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const heroVideo = document.querySelector('[data-hero-video]');
if (heroVideo && reducedMotion.matches) heroVideo.pause();

if ('IntersectionObserver' in window && !reducedMotion.matches) {
    const revealElements = document.querySelectorAll('[data-reveal]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.body.classList.add('js-reveal');
    revealElements.forEach((element) => observer.observe(element));
}

const dialog = document.querySelector('[data-lightbox]');
const lightboxContent = document.querySelector('[data-lightbox-content]');

dialog?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        event.preventDefault();
        dialog.close();
    }
});

document.querySelectorAll('[data-lightbox-image]').forEach((button) => {
    button.addEventListener('click', () => {
        lightboxContent.src = button.dataset.lightboxImage;
        lightboxContent.alt = button.dataset.lightboxAlt || '';
        dialog.showModal();
    });
});

document.querySelector('[data-lightbox-close]')?.addEventListener('click', () => dialog.close());
dialog?.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close();
});

document.querySelectorAll('[data-sortable]').forEach((container) => {
    let activeItem;

    container.addEventListener('dragstart', (event) => {
        activeItem = event.target.closest('[data-sortable-item]');
        activeItem?.classList.add('opacity-50');
    });

    container.addEventListener('dragend', () => activeItem?.classList.remove('opacity-50'));

    container.addEventListener('dragover', (event) => {
        event.preventDefault();
        const target = event.target.closest('[data-sortable-item]');
        if (target && activeItem && target !== activeItem) target.before(activeItem);
    });
});

const cookieBanner = document.querySelector('[data-cookie-banner]');

if (cookieBanner && !localStorage.getItem('cookie-consent')) {
    cookieBanner.classList.remove('hidden');
}

document.querySelector('[data-cookie-accept]')?.addEventListener('click', () => {
    localStorage.setItem('cookie-consent', 'technical');
    cookieBanner?.classList.add('hidden');
});
