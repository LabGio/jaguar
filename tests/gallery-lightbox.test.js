import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const script = readFileSync(new URL('../resources/js/app.js', import.meta.url), 'utf8');

function eventTarget(properties = {}) {
    const listeners = {};
    return {
        ...properties,
        addEventListener(name, callback) { listeners[name] = callback; },
        dispatch(name, event = {}) { listeners[name]?.(event); },
    };
}

function initializeGallery(images) {
    const galleryImages = images.map(({ src, alt }) => eventTarget({ dataset: { lightboxImage: src, lightboxAlt: alt } }));
    const lightboxContent = { src: '', alt: '' };
    const lightboxPrevious = eventTarget({ disabled: false });
    const lightboxNext = eventTarget({ disabled: false });
    const lightboxCounter = { textContent: '' };
    const dialog = eventTarget({
        open: false,
        showModal() { this.open = true; },
        close() { this.open = false; },
    });
    const elements = {
        '[data-lightbox]': dialog,
        '[data-lightbox-content]': lightboxContent,
        '[data-lightbox-previous]': lightboxPrevious,
        '[data-lightbox-next]': lightboxNext,
        '[data-lightbox-counter]': lightboxCounter,
        '[data-lightbox-close]': eventTarget(),
    };
    const document = {
        querySelector: (selector) => elements[selector] || null,
        querySelectorAll: (selector) => selector === '[data-lightbox-image]' ? galleryImages : [],
        addEventListener() {},
    };

    vm.runInNewContext(script, {
        document,
        window: { matchMedia: () => ({ matches: false }) },
    });

    return { dialog, galleryImages, lightboxContent, lightboxCounter, lightboxNext, lightboxPrevious };
}

test('opens the selected full-size image and wraps with navigation controls and arrow keys', () => {
    const { dialog, galleryImages, lightboxContent, lightboxCounter, lightboxNext, lightboxPrevious } = initializeGallery([
        { src: '/first.webp', alt: 'Prima immagine' },
        { src: '/second.webp', alt: 'Seconda immagine' },
        { src: '/third.webp', alt: 'Terza immagine' },
    ]);

    galleryImages[1].dispatch('click');
    assert.equal(dialog.open, true);
    assert.equal(lightboxContent.src, '/second.webp');
    assert.equal(lightboxContent.alt, 'Seconda immagine');
    assert.equal(lightboxCounter.textContent, '2 di 3');

    lightboxNext.dispatch('click');
    assert.equal(lightboxContent.src, '/third.webp');
    lightboxPrevious.dispatch('click');
    assert.equal(lightboxContent.src, '/second.webp');

    let defaultPrevented = false;
    dialog.dispatch('keydown', { key: 'ArrowLeft', preventDefault() { defaultPrevented = true; } });
    assert.equal(defaultPrevented, true);
    assert.equal(lightboxContent.src, '/first.webp');

    dialog.dispatch('keydown', { key: 'ArrowLeft', preventDefault() {} });
    assert.equal(lightboxContent.src, '/third.webp');
});

test('disables navigation when the gallery contains one image', () => {
    const { galleryImages, lightboxNext, lightboxPrevious } = initializeGallery([
        { src: '/only.webp', alt: '' },
    ]);

    galleryImages[0].dispatch('click');

    assert.equal(lightboxPrevious.disabled, true);
    assert.equal(lightboxNext.disabled, true);
});
