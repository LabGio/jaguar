import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const script = readFileSync(new URL('../resources/js/app.js', import.meta.url), 'utf8');

function initializeHero({ mobile = false, reduced = false, desktopSrc = '/desktop.mp4', mobileSrc = '/mobile.mp4' } = {}) {
    const listeners = {};
    const viewport = {
        matches: mobile,
        addEventListener: (event, callback) => { listeners[event] = callback; },
    };
    const video = {
        dataset: { desktopSrc, mobileSrc },
        autoplay: true,
        src: null,
        pause() { this.paused = true; },
        getAttribute() { return this.src; },
        setAttribute(name, value) { this.src = value; },
    };
    const document = {
        querySelector: (selector) => selector === '[data-hero-video]' ? video : null,
        querySelectorAll: () => [],
        addEventListener: () => {},
    };
    const window = {
        matchMedia: (query) => query.includes('reduced-motion') ? { matches: reduced } : viewport,
    };
    vm.runInNewContext(script, { document, window });
    return { video, viewport, listeners };
}

test('selects and switches sources at the mobile breakpoint', () => {
    const { video, viewport, listeners } = initializeHero({ mobile: true });
    assert.equal(video.src, '/mobile.mp4');
    viewport.matches = false;
    listeners.change();
    assert.equal(video.src, '/desktop.mp4');
    viewport.matches = true;
    listeners.change();
    assert.equal(video.src, '/mobile.mp4');
});

test('does not autoplay after source selection when reduced motion is enabled', () => {
    const { video, viewport, listeners } = initializeHero({ reduced: true });
    assert.equal(video.src, '/desktop.mp4');
    assert.equal(video.autoplay, false);
    assert.equal(video.paused, true);
    viewport.matches = true;
    listeners.change();
    assert.equal(video.src, '/mobile.mp4');
    assert.equal(video.autoplay, false);
});

test('keeps the only configured source across viewport changes', () => {
    const { video, viewport, listeners } = initializeHero({ desktopSrc: '/only.mp4', mobileSrc: '/only.mp4' });
    viewport.matches = true;
    listeners.change();
    assert.equal(video.src, '/only.mp4');
});
