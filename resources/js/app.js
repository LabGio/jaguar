document.querySelector('[data-menu-toggle]')?.addEventListener('click', (event) => {
    const menu = document.querySelector('[data-mobile-menu]');
    const isOpen = !menu.classList.contains('hidden');

    menu.classList.toggle('hidden', isOpen);
    event.currentTarget.setAttribute('aria-expanded', String(!isOpen));
});

const dialog = document.querySelector('[data-lightbox]');
const lightboxContent = document.querySelector('[data-lightbox-content]');

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
