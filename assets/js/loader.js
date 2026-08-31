/**
 * SMS 2 – Page transition (soft fade; no long fake loading)
 */
(function () {
    'use strict';

    window.__smsLoadStart = Date.now();

    var MIN_MS = 320;
    var FADE_MS = 340;
    var reduced = false;
    var softNav = false;
    var hideScheduled = false;

    try {
        reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch (e) { /* ignore */ }

    try {
        softNav = sessionStorage.getItem('sms2-visited') === '1';
        sessionStorage.setItem('sms2-visited', '1');
    } catch (e2) { /* ignore */ }

    if (reduced) {
        MIN_MS = 40;
        FADE_MS = 80;
    } else if (softNav) {
        // Already in the app — transition only, not a loading wait
        MIN_MS = 160;
        FADE_MS = 260;
        document.documentElement.classList.add('sms-soft-nav');
    }

    function getLoader() {
        return document.getElementById('smsPageLoader');
    }

    function isPublicPage() {
        var body = document.body;
        if (!body) return false;
        return body.classList.contains('login-page') || body.classList.contains('welcome-page');
    }

    function finishLoader() {
        var loader = getLoader();
        if (!loader || loader.classList.contains('is-done') || loader.classList.contains('is-leaving')) {
            document.documentElement.classList.add('sms-app-ready');
            if (document.body) document.body.classList.add('sms-loaded');
            return;
        }

        loader.classList.add('is-leaving');
        document.documentElement.classList.add('sms-app-ready');
        if (document.body) document.body.classList.add('sms-loaded');

        window.setTimeout(function () {
            loader.classList.add('is-done');
            loader.setAttribute('aria-busy', 'false');
            loader.setAttribute('aria-hidden', 'true');
            if (loader.parentNode) loader.parentNode.removeChild(loader);
        }, FADE_MS);
    }

    function scheduleHide() {
        if (hideScheduled) return;
        hideScheduled = true;

        if (isPublicPage() && !reduced) {
            MIN_MS = Math.min(MIN_MS, 240);
        }

        var elapsed = Date.now() - (window.__smsLoadStart || Date.now());
        var wait = Math.max(0, MIN_MS - elapsed);
        window.setTimeout(finishLoader, wait);
    }

    function boot() {
        if (!getLoader()) {
            document.documentElement.classList.add('sms-app-ready');
            if (document.body) document.body.classList.add('sms-loaded');
            return;
        }

        if (softNav) {
            var label = document.querySelector('.sms-loader-label');
            if (label) label.textContent = '';
        }

        // Don't wait on slow images/fonts/charts — content is ready at DOMContentLoaded
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            scheduleHide();
        } else {
            document.addEventListener('DOMContentLoaded', scheduleHide, { once: true });
        }
        // Safety: never leave the overlay stuck
        window.setTimeout(scheduleHide, 2500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    window.SMS2Loader = { hide: finishLoader };
})();
