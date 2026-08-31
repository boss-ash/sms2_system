/**
 * SMS 2 – Real-time Publications & IP (Final Output) dashboard
 */
(function () {
    'use strict';

    var POLL_MS = 5000;
    var root = document.querySelector('[data-grant-publications-ip-live="1"]');
    if (!root) return;

    var selectedId = parseInt(root.getAttribute('data-selected-id') || '0', 10) || 0;
    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-final-output.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-final-output.php';
    })();

    var projectSelect = document.getElementById('gpipProjectSelect');
    var detailPanel = document.getElementById('gpipDetailPanel');
    var submitDialog = document.getElementById('gpipSubmitDialog');
    var submitForm = document.getElementById('gpipSubmitForm');
    var submitApplicationId = document.getElementById('gpipSubmitApplicationId');
    var submitCancelBtn = document.getElementById('gpipSubmitCancelBtn');
    var submitBtn = document.getElementById('gpipSubmitBtn');
    var returnDialog = document.getElementById('gpipReturnDialog');
    var returnForm = document.getElementById('gpipReturnForm');
    var returnApplicationId = document.getElementById('gpipReturnApplicationId');
    var returnCancelBtn = document.getElementById('gpipReturnCancelBtn');
    var returnBtn = document.getElementById('gpipReturnBtn');

    var lastOverviewFp = '';
    var lastDetailFp = '';
    var lastOverviewCount = null;
    var paused = false;
    var submitting = false;

    function forceHideLoader() {
        if (window.SMS2Loader && typeof window.SMS2Loader.forceHide === 'function') {
            window.SMS2Loader.forceHide();
        }
    }

    function updateStats(overview, pendingCount) {
        var projectCount = document.querySelector('[data-gpip-project-count]');
        var pendingEl = document.querySelector('[data-gpip-pending-count]');
        if (projectCount && Array.isArray(overview)) projectCount.textContent = String(overview.length);
        if (pendingEl && pendingCount !== undefined) pendingEl.textContent = String(pendingCount);
    }

    function renderProjectSelect(overview) {
        if (!projectSelect || !Array.isArray(overview)) return;
        var html = overview.map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var ref = row.proposal_reference || 'Proposal';
            var title = row.research_title || 'Untitled';
            var label = row.workflow_label || '';
            var selected = appId === selectedId ? ' selected' : '';
            return '<option value="' + appId + '"' + selected + '>' +
                ref + ': ' + title + ' — ' + label + '</option>';
        }).join('');
        projectSelect.innerHTML = html;
    }

    function openSubmitDialog(detail) {
        if (!submitDialog || !submitForm) return;
        var app = (detail && detail.application) || {};
        var appId = parseInt(app.id, 10) || selectedId;
        if (submitApplicationId) submitApplicationId.value = String(appId);

        var titleEl = document.getElementById('gpipFinalTitle');
        var authorsEl = document.getElementById('gpipAuthors');
        var abstractEl = document.getElementById('gpipAbstract');
        var typeEl = document.getElementById('gpipPublicationType');
        var sub = detail && detail.submission;

        if (titleEl) titleEl.value = (sub && sub.final_research_title) || detail.default_title || app.research_title || '';
        if (authorsEl) authorsEl.value = (sub && sub.authors) || detail.default_authors || app.applicant_name || '';
        if (abstractEl) abstractEl.value = (sub && sub.abstract) || '';
        if (typeEl && sub && sub.publication_type) typeEl.value = sub.publication_type;

        var journalEl = document.getElementById('gpipJournal');
        var doiEl = document.getElementById('gpipDoi');
        var urlEl = document.getElementById('gpipPubUrl');
        var ipEl = document.getElementById('gpipIpInfo');
        var copyrightEl = document.getElementById('gpipCopyright');
        var patentEl = document.getElementById('gpipPatent');
        var otherIpEl = document.getElementById('gpipOtherIp');

        if (journalEl) journalEl.value = (sub && sub.journal_conference) || '';
        if (doiEl) doiEl.value = (sub && sub.doi) || '';
        if (urlEl) urlEl.value = (sub && sub.publication_url) || '';
        if (ipEl) ipEl.value = (sub && sub.ip_information) || '';
        if (copyrightEl) copyrightEl.value = (sub && sub.copyright_info) || '';
        if (patentEl) patentEl.value = (sub && sub.patent_info) || '';
        if (otherIpEl) otherIpEl.value = (sub && sub.other_ip_info) || '';

        submitDialog.classList.add('open');
        paused = true;
    }

    function closeSubmitDialog() {
        if (!submitDialog) return;
        submitDialog.classList.remove('open');
        if (submitForm) submitForm.reset();
        paused = false;
    }

    function openReturnDialog() {
        if (!returnDialog) return;
        if (returnApplicationId) returnApplicationId.value = String(selectedId);
        returnDialog.classList.add('open');
        paused = true;
    }

    function closeReturnDialog() {
        if (!returnDialog) return;
        returnDialog.classList.remove('open');
        if (returnForm) returnForm.reset();
        paused = false;
    }

    function setBusy(btn, busy) {
        if (!btn) return;
        btn.disabled = !!busy;
        btn.setAttribute('aria-busy', busy ? 'true' : 'false');
    }

    function bindDetailActions() {
        var openSubmit = detailPanel && detailPanel.querySelector('.gpipOpenSubmitBtn');
        if (openSubmit) {
            openSubmit.addEventListener('click', function () {
                fetch(apiBase + '?action=get_overview&id=' + encodeURIComponent(selectedId), {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: { Accept: 'application/json' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data && data.success && data.detail) openSubmitDialog(data.detail);
                    });
            });
        }

        var verifyBtn = detailPanel && detailPanel.querySelector('.gpipVerifyBtn');
        if (verifyBtn) {
            verifyBtn.addEventListener('click', function () {
                if (submitting) return;
                if (!window.confirm('Verify this final output and record it to the Publications & IP Repository?')) return;

                submitting = true;
                setBusy(verifyBtn, true);

                var formData = new FormData();
                formData.append('grant_application_id', String(selectedId));

                fetch(apiBase + '?action=verify', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data || !data.success) {
                            alert((data && data.message) || 'Verification failed.');
                            forceHideLoader();
                            return;
                        }
                        window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
                    })
                    .catch(function () {
                        alert('Verification failed.');
                        forceHideLoader();
                    })
                    .finally(function () {
                        submitting = false;
                        setBusy(verifyBtn, false);
                    });
            });
        }

        var openReturn = detailPanel && detailPanel.querySelector('.gpipOpenReturnBtn');
        if (openReturn) {
            openReturn.addEventListener('click', openReturnDialog);
        }
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

    if (submitCancelBtn) submitCancelBtn.addEventListener('click', closeSubmitDialog);
    if (returnCancelBtn) returnCancelBtn.addEventListener('click', closeReturnDialog);

    if (submitDialog) {
        submitDialog.addEventListener('click', function (e) {
            if (e.target === submitDialog) closeSubmitDialog();
        });
    }
    if (returnDialog) {
        returnDialog.addEventListener('click', function (e) {
            if (e.target === returnDialog) closeReturnDialog();
        });
    }

    if (submitForm) {
        submitForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitting) return;

            submitting = true;
            setBusy(submitBtn, true);

            var formData = new FormData(submitForm);
            fetch(apiBase + '?action=submit_final_output', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data || !data.success) {
                        alert((data && data.message) || 'Submission failed.');
                        forceHideLoader();
                        return;
                    }
                    closeSubmitDialog();
                    window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
                })
                .catch(function () {
                    alert('Submission failed.');
                    forceHideLoader();
                })
                .finally(function () {
                    submitting = false;
                    setBusy(submitBtn, false);
                });
        });
    }

    if (returnForm) {
        returnForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitting) return;

            submitting = true;
            setBusy(returnBtn, true);

            var formData = new FormData(returnForm);
            fetch(apiBase + '?action=return_for_correction', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data || !data.success) {
                        alert((data && data.message) || 'Return failed.');
                        forceHideLoader();
                        return;
                    }
                    closeReturnDialog();
                    window.location.href = window.location.pathname + '?id=' + encodeURIComponent(selectedId);
                })
                .catch(function () {
                    alert('Return failed.');
                    forceHideLoader();
                })
                .finally(function () {
                    submitting = false;
                    setBusy(returnBtn, false);
                });
        });
    }

    bindDetailActions();
    fetchOverview(false);

    setInterval(function () {
        if (paused || submitting) return;
        fetchOverview(true);
    }, POLL_MS);
})();
