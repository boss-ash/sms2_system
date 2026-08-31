/**
 * SMS 2 – Real-time grant evaluation queue polling (Review Committee)
 */
(function () {
    'use strict';

    var POLL_MS = 10000;
    var root = document.querySelector('[data-grant-eval-live="1"]');
    if (!root) return;

    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-evaluation.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-evaluation.php';
    })();

    var lastFingerprint = '';
    var timer = null;

    function fingerprint(queue) {
        if (!Array.isArray(queue)) return '';
        return queue.map(function (r) {
            return [
                r.id,
                r.status,
                r.my_evaluation_id,
                r.my_total_score,
                r.workflow_status,
                r.current_step_key,
                r.workflow_updated_at || r.updated_at,
                r.committee_total_score
            ].join(':');
        }).join('|');
    }

    function poll() {
        if (document.hidden || document.querySelector('.modal.show')) return;

        fetch(apiBase + '?action=get_queue', {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;

                var pendingEl = document.querySelector('[data-eval-pending-count]');
                var scoredEl = document.querySelector('[data-eval-scored-count]');
                if (pendingEl) pendingEl.textContent = String(data.pending || 0);
                if (scoredEl) scoredEl.textContent = String(data.scored || 0);

                var fp = fingerprint(data.queue);
                if (lastFingerprint === '') {
                    lastFingerprint = fp;
                    return;
                }
                if (fp !== lastFingerprint) {
                    window.location.reload();
                }
            })
            .catch(function () { /* silent */ });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            poll();
            timer = window.setInterval(poll, POLL_MS);
        }, { once: true });
    } else {
        poll();
        timer = window.setInterval(poll, POLL_MS);
    }
})();
