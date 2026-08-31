/**
 * Auth navigation helpers:
 * - Soft fade between Sign In ↔ Forgot password
 * - Keep campus video time when going Sign In → Forgot
 * - Restart video when going Forgot → Sign In
 */
(function () {
    'use strict';

    var VIDEO_T = 'sms_auth_video_t';
    var VIDEO_CONTINUE = 'sms_auth_video_continue';
    var FADE_MS = 180;

    function prefersReducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function getVideo() {
        return document.querySelector('.login-video-bg video, .forgot-video-bg video');
    }

    function saveVideoTime() {
        var video = getVideo();
        if (!video) return;
        try {
            sessionStorage.setItem(VIDEO_T, String(video.currentTime || 0));
            sessionStorage.setItem(VIDEO_CONTINUE, '1');
        } catch (err) {
            // ignore
        }
    }

    function clearVideoContinue() {
        try {
            sessionStorage.removeItem(VIDEO_CONTINUE);
            sessionStorage.removeItem(VIDEO_T);
        } catch (err) {
            // ignore
        }
    }

    function restoreVideoIfNeeded() {
        var video = getVideo();
        if (!video) return;

        var shouldContinue = false;
        var time = 0;
        try {
            shouldContinue = sessionStorage.getItem(VIDEO_CONTINUE) === '1';
            time = parseFloat(sessionStorage.getItem(VIDEO_T) || '0');
            // One-shot: refresh after this should start normally
            sessionStorage.removeItem(VIDEO_CONTINUE);
        } catch (err) {
            return;
        }

        if (!shouldContinue || !(time > 0.05)) return;

        function apply() {
            try {
                video.currentTime = time;
                var playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(function () {});
                }
            } catch (err) {
                // ignore
            }
        }

        if (video.readyState >= 1) {
            apply();
        } else {
            video.addEventListener('loadedmetadata', apply, { once: true });
        }
    }

    function softGo(url) {
        if (prefersReducedMotion()) {
            window.location.href = url;
            return;
        }

        document.documentElement.classList.add('auth-soft-leave');
        window.setTimeout(function () {
            window.location.href = url;
        }, FADE_MS);
    }

    document.addEventListener('DOMContentLoaded', function () {
        restoreVideoIfNeeded();

        document.querySelectorAll('a[data-auth-transition]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (e.defaultPrevented) return;
                if (e.button !== 0) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                var href = link.getAttribute('href');
                if (!href || href.charAt(0) === '#') return;

                e.preventDefault();

                var direction = link.getAttribute('data-auth-direction') || '';
                var goingToForgot = href.toLowerCase().indexOf('forgot-password') !== -1 || direction === 'left';

                if (goingToForgot) {
                    saveVideoTime();
                } else {
                    clearVideoContinue();
                }

                softGo(href);
            });
        });
    });
})();
