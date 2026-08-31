/**
 * SMS 2 – Real-time grant opportunities & proposals polling
 */
(function () {
    'use strict';

    var POLL_MS = 10000;
    var REVISIONS_POLL_MS = 5000;
    var root = document.querySelector('[data-grant-live="1"]');
    if (!root) return;

    var isOppPage = root.hasAttribute('data-grant-opp-page');
    var isPropPage = root.hasAttribute('data-proposals-page');
    var isRevisionsPage = root.hasAttribute('data-revisions-page');
    if (!isOppPage && !isPropPage && !isRevisionsPage) return;

    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-management.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-management.php';
    })();

    var lastFingerprint = '';
    var timer = null;
    var paused = false;

    function fingerprintFromOpportunities(list) {
        if (!Array.isArray(list)) return '';
        return list.map(function (o) {
            return [
                o.id,
                o.status,
                o.application_count,
                o.updated_at,
                o.created_at
            ].join(':');
        }).join('|');
    }

    function fingerprintFromApplications(list) {
        if (!Array.isArray(list)) return '';
        return list.map(function (a) {
            return [a.id, a.status, a.current_version, a.updated_at, a.submitted_at, a.proposal_reference].join(':');
        }).join('|');
    }

    function modalOpen() {
        return !!document.querySelector('.modal.show');
    }

    function fingerprintFromRevisions(list) {
        if (!Array.isArray(list)) return '';
        return list.map(function (r) {
            return [
                r.id,
                r.status,
                r.current_version,
                r.updated_at,
                r.returned_at,
                r.returned_by,
                r.approval_level,
                r.return_reason,
                r.return_source
            ].join(':');
        }).join('|');
    }

    function poll() {
        if (paused || document.hidden || modalOpen()) return;

        var action = isOppPage ? 'get_opportunities' : (isRevisionsPage ? 'get_revisions' : 'get_applications');
        fetch(apiBase + '?action=' + encodeURIComponent(action), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;

                var fp = isOppPage
                    ? fingerprintFromOpportunities(data.opportunities)
                    : (isRevisionsPage
                        ? fingerprintFromRevisions(data.revisions)
                        : fingerprintFromApplications(data.applications));

                if (lastFingerprint === '') {
                    lastFingerprint = fp;
                    return;
                }

                if (fp !== lastFingerprint) {
                    window.location.reload();
                }
            })
            .catch(function () { /* silent retry on next interval */ });
    }

    function start() {
        if (timer) return;
        poll();
        timer = window.setInterval(poll, isRevisionsPage ? REVISIONS_POLL_MS : POLL_MS);
    }

    function stop() {
        if (!timer) return;
        window.clearInterval(timer);
        timer = null;
    }

    document.addEventListener('visibilitychange', function () {
        paused = document.hidden;
        if (!paused) poll();
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    window.SMS2GrantLive = { poll: poll, stop: stop, start: start };
})();
