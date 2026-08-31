/**
 * SMS 2 – Page loader (initial load + module navigation + refresh)
 */
(function () {
    'use strict';

    window.__smsLoadStart = Date.now();

    var MIN_MS = 380;
    var FADE_MS = 280;
    var reduced = false;

    try {
        reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch (e) { /* ignore */ }

    if (reduced) {
        MIN_MS = 60;
        FADE_MS = 80;
    }

    function getLoader() {
        return document.getElementById('smsPageLoader');
    }

    function isPublicPage() {
        var body = document.body;
        if (!body) return false;
        return body.classList.contains('login-page') || body.classList.contains('welcome-page');
    }

    function setPageLoading(loading) {
        if (loading) {
            document.documentElement.classList.remove('sms-app-ready');
            if (document.body) document.body.classList.remove('sms-loaded');
        } else {
            document.documentElement.classList.add('sms-app-ready');
            if (document.body) document.body.classList.add('sms-loaded');
        }
    }

    function showLoader(message) {
        var loader = getLoader();
        if (!loader || isPublicPage()) return;

        loader.classList.remove('is-leaving', 'is-done');
        loader.classList.add('is-active');
        loader.setAttribute('aria-busy', 'true');
        loader.removeAttribute('aria-hidden');

        var label = loader.querySelector('.sms-loader-label');
        if (label) label.textContent = message || 'Loading…';

        setPageLoading(true);
    }

    function hideLoader(force) {
        var loader = getLoader();
        if (!loader) {
            setPageLoading(false);
            return;
        }

        if (!force && (loader.classList.contains('is-done') || loader.classList.contains('is-leaving'))) {
            setPageLoading(false);
            return;
        }

        hideScheduled = true;
        loader.classList.remove('is-active');
        loader.classList.add('is-leaving');
        setPageLoading(false);

        window.setTimeout(function () {
            loader.classList.add('is-done');
            loader.setAttribute('aria-busy', 'false');
            loader.setAttribute('aria-hidden', 'true');
            loader.classList.remove('is-leaving');
        }, FADE_MS);
    }

    function finishLoader() {
        hideLoader(false);
    }

    var hideScheduled = false;

    function scheduleHide() {
        if (hideScheduled) return;
        hideScheduled = true;

        if (isPublicPage() && !reduced) {
            MIN_MS = Math.min(MIN_MS, 260);
        }

        var elapsed = Date.now() - (window.__smsLoadStart || Date.now());
        var wait = Math.max(0, MIN_MS - elapsed);
        window.setTimeout(finishLoader, wait);
    }

    function boot() {
        if (!getLoader()) {
            setPageLoading(false);
            return;
        }

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            scheduleHide();
        } else {
            document.addEventListener('DOMContentLoaded', scheduleHide, { once: true });
        }

        window.setTimeout(scheduleHide, 2800);
    }

    function shouldShowForLink(anchor, event) {
        if (!anchor || !anchor.href) return false;
        if (anchor.hasAttribute('data-no-loader') || anchor.hasAttribute('download')) return false;
        if (anchor.hasAttribute('data-logout-confirm') || anchor.id === 'logoutConfirmBtn') return false;
        if (anchor.target === '_blank' || anchor.dataset.bsToggle || anchor.getAttribute('data-bs-toggle')) return false;
        if (anchor.closest('[data-no-loader]')) return false;

        if (event) {
            if (event.defaultPrevented) return false;
            if (event.button !== 0) return false;
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
        }

        var raw = anchor.getAttribute('href');
        if (!raw || raw === '#' || raw.charAt(0) === '#') return false;

        try {
            var next = new URL(anchor.href, window.location.href);
            if (next.origin !== window.location.origin) return false;
            if (next.protocol === 'mailto:' || next.protocol === 'tel:') return false;
        } catch (err) {
            return false;
        }

        return true;
    }

    function bindNavigationLoader() {
        document.addEventListener('click', function (event) {
            var anchor = event.target.closest('a[href]');
            if (!shouldShowForLink(anchor, event)) return;
            showLoader('Loading…');
        }, true);

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM') return;
            if (form.hasAttribute('data-no-loader')) return;
            if (form.target === '_blank') return;
            if (event.defaultPrevented) return;

            var method = (form.getAttribute('method') || 'get').toLowerCase();
            if (method === 'get') {
                showLoader('Loading…');
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    bindNavigationLoader();

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.__smsLoadStart = Date.now();
            hideScheduled = false;
            showLoader('Loading…');
            scheduleHide();
        }
    });

    window.SMS2Loader = {
        show: showLoader,
        hide: finishLoader,
        forceHide: function () { hideLoader(true); }
    };
})();
