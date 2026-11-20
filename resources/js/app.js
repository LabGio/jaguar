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
if (heroVideo) {
    const mobileViewport = window.matchMedia('(max-width: 700px)');
    const updateHeroVideo = () => {
        const source = mobileViewport.matches ? heroVideo.dataset.mobileSrc : heroVideo.dataset.desktopSrc;
        if (heroVideo.getAttribute('src') !== source) heroVideo.setAttribute('src', source);
    };

    if (reducedMotion.matches) {
        heroVideo.autoplay = false;
        heroVideo.pause();
    }
    updateHeroVideo();
    mobileViewport.addEventListener('change', updateHeroVideo);
}

const settingsForm = document.querySelector('[data-settings-form]');
if (settingsForm) {
    const uploads = Array.from(settingsForm.querySelectorAll('[data-video-upload]'), (container) => ({
        file: container.querySelector('[data-video-file]'),
        button: container.querySelector('[data-video-button]'),
        progress: container.querySelector('[data-video-progress]'),
        status: container.querySelector('[data-video-status]'),
    }));
    const saveButton = settingsForm.querySelector('[data-settings-save]');
    let uploading = false;

    const showStatus = (upload, message, isError = false) => {
        upload.status.textContent = message;
        upload.status.classList.toggle('text-red-700', isError);
    };

    const setUploading = (active) => {
        uploading = active;
        saveButton.disabled = active;
        uploads.forEach(({ button, file }) => {
            button.disabled = active;
            file.disabled = active;
        });
    };

    settingsForm.addEventListener('submit', (event) => {
        if (uploading || uploads.some(({ file }) => file.files.length > 0)) {
            event.preventDefault();
            uploads.filter(({ file }) => file.files.length > 0).forEach((upload) => {
                showStatus(upload, 'Premi «Carica file» prima di salvare le altre impostazioni.', true);
            });
        }
    });

    uploads.forEach((upload) => {
        upload.button.hidden = false;
        upload.file.addEventListener('change', () => {
            upload.progress.hidden = true;
            showStatus(upload, '');
        });
        upload.button.addEventListener('click', () => {
            const file = upload.file.files[0];
            if (!file) {
                showStatus(upload, 'Seleziona un video da caricare.', true);
                return;
            }
            if (file.size > 50 * 1024 * 1024) {
                showStatus(upload, 'Il video non può superare 50 MB.', true);
                return;
            }

            const data = new FormData();
            data.append('_token', settingsForm.querySelector('[name="_token"]').value);
            data.append('_method', 'PUT');
            data.append(upload.file.name, file);

            const request = new XMLHttpRequest();
            request.open('POST', settingsForm.action);
            request.responseType = 'json';
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    upload.progress.value = Math.round(event.loaded / event.total * 100);
                    showStatus(upload, `Caricamento: ${upload.progress.value}%`);
                }
            });
            request.upload.addEventListener('load', () => {
                showStatus(upload, 'Caricamento completato, salvataggio in corso...');
            });
            request.addEventListener('load', () => {
                setUploading(false);
                if (request.status >= 200 && request.status < 300 && request.response?.saved === true) {
                    upload.file.value = '';
                    showStatus(upload, 'Video caricato e salvato.');
                    return;
                }

                let message = `Caricamento non riuscito (HTTP ${request.status}). Verifica il server prima di riprovare.`;
                if (request.status === 413) {
                    message = 'Il file supera il limite di caricamento del server. Riduci il video o verifica i limiti PHP e del server web.';
                } else if (request.status === 401 || request.status === 419) {
                    message = 'Sessione scaduta. Accedi nuovamente e riprova.';
                } else if (request.status === 403) {
                    message = 'Accesso negato dal server (HTTP 403). Verifica i permessi o le protezioni del server.';
                } else if (request.status === 422 && request.response?.errors?.[upload.file.name]) {
                    message = request.response.errors[upload.file.name].join(' ');
                }
                showStatus(upload, message, true);
            });
            request.addEventListener('error', () => {
                setUploading(false);
                showStatus(upload, 'Errore di rete durante il caricamento. Verifica la connessione e riprova.', true);
            });
            request.addEventListener('abort', () => {
                setUploading(false);
                showStatus(upload, 'Caricamento interrotto.', true);
            });

            upload.progress.value = 0;
            upload.progress.hidden = false;
            showStatus(upload, 'Caricamento: 0%');
            setUploading(true);
            request.send(data);
        });
    });
}

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
