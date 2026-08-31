/**
 * Real-time Outputs & Records sidebar pending badges (CRAD Staff)
 */
(function () {
    'use strict';

    var POLL_MS = 8000;
    var verifyBadge = document.querySelector('[data-outputs-badge="pending-verify"]');
    var archiveBadge = document.querySelector('[data-outputs-badge="pending-archive"]');
    if (!verifyBadge && !archiveBadge) return;

    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-final-output.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-final-output.php';
    })();

    var lastFp = '';

    function setBadge(el, count) {
        if (!el) return;
        var n = parseInt(count, 10) || 0;
        if (n <= 0) {
            el.hidden = true;
            el.textContent = '';
            return;
        }
        el.hidden = false;
        el.textContent = String(n);
        el.setAttribute('title', n + ' pending');
    }

    function poll() {
        fetch(apiBase + '?action=outputs_stats', {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;
                var fp = data.stats_fingerprint || '';
                setBadge(verifyBadge, data.pending_verification);
                setBadge(archiveBadge, data.pending_archive);
                lastFp = fp;
            })
            .catch(function () {});
    }

    poll();
    setInterval(poll, POLL_MS);
})();
