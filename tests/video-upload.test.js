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

function initializeUploads() {
    const requests = [];
    const uploads = ['hero_video', 'hero_video_mobile'].map((name) => {
        const file = eventTarget({ name, files: [], disabled: false });
        Object.defineProperty(file, 'value', {
            set(value) { if (value === '') this.files = []; },
        });
        const button = eventTarget({ hidden: true, disabled: false });
        const progress = { hidden: true, value: 0 };
        const status = { textContent: '', classList: { toggle() {} } };
        const elements = {
            '[data-video-file]': file,
            '[data-video-button]': button,
            '[data-video-progress]': progress,
            '[data-video-status]': status,
        };
        return { file, button, progress, status, container: { querySelector: (selector) => elements[selector] } };
    });
    const saveButton = { disabled: false };
    const form = eventTarget({
        action: '/admin/settings',
        querySelectorAll: () => uploads.map(({ container }) => container),
        querySelector: (selector) => selector === '[data-settings-save]' ? saveButton : { value: 'csrf-token' },
    });
    const document = {
        querySelector: (selector) => selector === '[data-settings-form]' ? form : null,
        querySelectorAll: () => [],
        addEventListener() {},
    };
    class FormData {
        constructor() { this.entries = []; }
        append(name, value) { this.entries.push([name, value]); }
    }
    class XMLHttpRequest {
        constructor() {
            Object.assign(this, eventTarget());
            this.upload = eventTarget();
            this.headers = {};
            requests.push(this);
        }
        open(method, url) { this.method = method; this.url = url; }
        setRequestHeader(name, value) { this.headers[name] = value; }
        send(data) { this.data = data; }
    }
    vm.runInNewContext(script, {
        document,
        window: { matchMedia: () => ({ matches: false }) },
        FormData,
        XMLHttpRequest,
    });
    return { uploads, requests, form, saveButton };
}

test('uploads one video with progress and waits for server confirmation', () => {
    const { uploads, requests, form, saveButton } = initializeUploads();
    const [desktop, mobile] = uploads;
    const file = { name: 'desktop.mp4', size: 1024 };
    desktop.file.files = [file];
    desktop.button.dispatch('click');

    const request = requests[0];
    assert.equal(request.method, 'POST');
    assert.equal(request.url, '/admin/settings');
    assert.deepEqual(request.data.entries, [['_token', 'csrf-token'], ['_method', 'PUT'], ['hero_video', file]]);
    assert.equal(request.headers.Accept, 'application/json');
    assert.equal(mobile.button.disabled, true);
    assert.equal(saveButton.disabled, true);

    request.upload.dispatch('progress', { lengthComputable: true, loaded: 1, total: 2 });
    assert.equal(desktop.progress.value, 50);
    assert.match(desktop.status.textContent, /50%/);
    request.upload.dispatch('load');
    assert.match(desktop.status.textContent, /salvataggio in corso/);
    request.status = 200;
    request.response = { saved: true };
    request.dispatch('load');
    assert.equal(desktop.status.textContent, 'Video caricato e salvato.');
    assert.equal(desktop.file.files.length, 0);
    assert.equal(saveButton.disabled, false);
    assert.equal(mobile.button.disabled, false);

    let prevented = false;
    form.dispatch('submit', { preventDefault() { prevented = true; } });
    assert.equal(prevented, false);
});

test('shows server validation and request size errors and allows retry', () => {
    const { uploads, requests } = initializeUploads();
    const mobile = uploads[1];
    mobile.file.files = [{ name: 'mobile.mp4', size: 1024 }];
    mobile.button.dispatch('click');
    requests[0].status = 422;
    requests[0].response = { errors: { hero_video_mobile: ['Formato non valido.'] } };
    requests[0].dispatch('load');
    assert.equal(mobile.status.textContent, 'Formato non valido.');
    assert.equal(mobile.file.files.length, 1);
    assert.equal(mobile.button.disabled, false);

    mobile.button.dispatch('click');
    requests[1].status = 413;
    requests[1].response = null;
    requests[1].dispatch('load');
    assert.match(mobile.status.textContent, /limite di caricamento del server/);
});

test('reports network errors and prevents saving unuploaded videos', () => {
    const { uploads, requests, form, saveButton } = initializeUploads();
    const desktop = uploads[0];
    desktop.file.files = [{ name: 'desktop.mp4', size: 1024 }];
    let prevented = false;
    form.dispatch('submit', { preventDefault() { prevented = true; } });
    assert.equal(prevented, true);
    assert.match(desktop.status.textContent, /Carica file/);

    desktop.button.dispatch('click');
    requests[0].dispatch('error');
    assert.match(desktop.status.textContent, /Errore di rete/);
    assert.equal(saveButton.disabled, false);
    assert.equal(desktop.file.files.length, 1);
});

test('rejects missing and oversized files without sending a request', () => {
    const { uploads, requests } = initializeUploads();
    const desktop = uploads[0];
    desktop.button.dispatch('click');
    assert.match(desktop.status.textContent, /Seleziona un video/);
    desktop.file.files = [{ name: 'large.mp4', size: 50 * 1024 * 1024 + 1 }];
    desktop.button.dispatch('click');
    assert.match(desktop.status.textContent, /50 MB/);
    assert.equal(requests.length, 0);
});

test('does not report success for an unexpected server response', () => {
    const { uploads, requests } = initializeUploads();
    const desktop = uploads[0];
    desktop.file.files = [{ name: 'desktop.mp4', size: 1024 }];
    desktop.button.dispatch('click');
    requests[0].status = 200;
    requests[0].response = null;
    requests[0].dispatch('load');
    assert.match(desktop.status.textContent, /Caricamento non riuscito/);
    assert.equal(desktop.file.files.length, 1);
});
