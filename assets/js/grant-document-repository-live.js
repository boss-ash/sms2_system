/**
 * SMS 2 – Real-time Document Repository
 */
(function () {
    'use strict';

    var POLL_MS = 5000;
    var root = document.querySelector('[data-grant-document-repository-live="1"]');
    if (!root) return;

    var selectedId = parseInt(root.getAttribute('data-selected-id') || '0', 10) || 0;
    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-document-repository.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-document-repository.php';
    })();

    var projectSelect = document.getElementById('gdrProjectSelect');
    var archiveBtn = document.querySelector('.gdrArchiveBtn');

    var lastOverviewFp = '';
    var lastDetailFp = '';
    var lastOverviewCount = null;
    var submitting = false;

    function forceHideLoader() {
        if (window.SMS2Loader && typeof window.SMS2Loader.forceHide === 'function') {
            window.SMS2Loader.forceHide();
        }
    }

    function updateStats(overview, pendingCount) {
        var projectCount = document.querySelector('[data-gdr-project-count]');
        var pendingEl = document.querySelector('[data-gdr-pending-count]');
        if (projectCount && Array.isArray(overview)) projectCount.textContent = String(overview.length);
        if (pendingEl && pendingCount !== undefined) pendingEl.textContent = String(pendingCount);
    }

    function renderProjectSelect(overview) {
        if (!projectSelect || !Array.isArray(overview)) return;
        projectSelect.innerHTML = overview.map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var ref = row.proposal_reference || 'Proposal';
            var title = row.research_title || 'Untitled';
            var label = row.workflow_label || '';
            var selected = appId === selectedId ? ' selected' : '';
            return '<option value="' + appId + '"' + selected + '>' +
                ref + ': ' + title + ' — ' + label + '</option>';
        }).join('');
    }

    function setBusy(btn, busy) {
        if (!btn) return;
        btn.disabled = !!busy;
        btn.setAttribute('aria-busy', busy ? 'true' : 'false');
    }

    function bindArchive() {
        var btn = document.querySelector('.gdrArchiveBtn');
        if (!btn) return;

        btn.addEventListener('click', function () {
            if (submitting) return;
            if (!window.confirm('Archive all research records to the Document Repository? This action is permanent.')) return;

            submitting = true;
            setBusy(btn, true);

            var formData = new FormData();
            formData.append('grant_application_id', String(selectedId));

            fetch(apiBase + '?action=archive', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data || !data.success) {
                        alert((data && data.message) || 'Archive failed.');
                        forceHideLoader();
                        return;
                    }
                    window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
                })
                .catch(function () {
                    alert('Archive failed.');
                    forceHideLoader();
                })
                .finally(function () {
                    submitting = false;
                    setBusy(btn, false);
                });
        });
    }

    function fetchOverview(isPoll) {
        var url = apiBase + '?action=get_overview';
        if (selectedId > 0) url += '&id=' + encodeURIComponent(selectedId);

        return fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;

                var overview = data.overview || [];
                if (lastOverviewCount !== null && overview.length !== lastOverviewCount) {
                    window.location.reload();
                    return;
                }
                lastOverviewCount = overview.length;

                var overviewFp = data.overview_fingerprint || '';
                var detailFp = data.detail_fingerprint || '';
                var overviewChanged = isPoll && lastOverviewFp !== '' && overviewFp !== lastOverviewFp;
                var detailChanged = isPoll && lastDetailFp !== '' && detailFp !== lastDetailFp;

                if (isPoll && (overviewChanged || detailChanged)) {
                    window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
                    return;
                }

                if (!isPoll) {
                    updateStats(overview, data.pending_count);
                    renderProjectSelect(overview);
                }

                lastOverviewFp = overviewFp;
                lastDetailFp = detailFp;
            })
            .catch(function () {});
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', function () {
            selectedId = parseInt(projectSelect.value, 10) || 0;
            root.setAttribute('data-selected-id', String(selectedId));
            window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
        });
    }

    bindArchive();
    fetchOverview(false);

    setInterval(function () {
        if (submitting) return;
        fetchOverview(true);
    }, POLL_MS);
})();
