/**
 * CRAD Officer dashboard — live grant approval workflow summary
 */
(function () {
    'use strict';

    var dock = document.querySelector('[data-crad-approval-dock="1"]');
    if (!dock) return;

    var POLL_MS = 5000;
    var listEl = document.getElementById('cradApprovalDockList');
    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/dashboard/');
        if (idx === -1) return '/modules/crad/api/grant-approval.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-approval.php';
    })();
    var lastFingerprint = '';

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function pipelineUrl(appId) {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/dashboard/');
        var base = idx === -1 ? '' : path.slice(0, idx);
        return base + '/modules/crad/pages/approval-workflows.php' + (appId ? '?id=' + encodeURIComponent(appId) : '');
    }

    function updateStats(data) {
        var inProg = dock.querySelector('[data-gaw-in-progress]');
        var completed = dock.querySelector('[data-gaw-completed]');
        if (inProg && typeof data.in_progress === 'number') {
            inProg.textContent = String(data.in_progress);
        }
        if (completed && typeof data.completed === 'number') {
            completed.textContent = String(data.completed);
        }
    }

    function renderList(workflows) {
        if (!listEl) return;

        if (!Array.isArray(workflows) || workflows.length === 0) {
            listEl.innerHTML = '<p class="gaw-dashboard-empty">No grant proposals in the approval pipeline yet. Proposals appear after the Review Committee recommends them.</p>';
            return;
        }

        listEl.innerHTML = workflows.slice(0, 8).map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var ref = esc(row.proposal_reference || 'Proposal');
            var title = esc(row.research_title || 'Untitled');
            var step = esc(row.current_step_label || '—');
            var status = esc(row.workflow_status || '');
            var statusClass = status === 'Completed' ? 'completed' : 'pending';
            var statusLabel = status === 'Completed' ? 'Completed' : ('At ' + step);
            return '<a class="gaw-dashboard-item" href="' + esc(pipelineUrl(appId)) + '">' +
                '<div class="gaw-dashboard-item-main">' +
                '<strong>' + ref + '</strong>' +
                '<span>' + title + '</span>' +
                '</div>' +
                '<span class="gaw-dashboard-item-status ' + statusClass + '">' + statusLabel + '</span>' +
                '</a>';
        }).join('');
    }

    function poll() {
        if (document.hidden) return;

        fetch(apiBase + '?action=get_workflows', {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;
                var fp = data.fingerprint || '';
                if (lastFingerprint !== '' && fp !== lastFingerprint) {
                    dock.classList.add('gaw-dashboard-dock-updated');
                    setTimeout(function () {
                        dock.classList.remove('gaw-dashboard-dock-updated');
                    }, 1200);
                }
                lastFingerprint = fp;
                updateStats(data);
                renderList(data.workflows || []);
            })
            .catch(function () { /* silent */ });
    }

    poll();
    setInterval(poll, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
})();
