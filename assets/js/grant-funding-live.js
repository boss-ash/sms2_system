/**
 * SMS 2 – Real-time grant funding / disbursement polling
 */
(function () {
    'use strict';

    var POLL_MS = 5000;
    var root = document.querySelector('[data-grant-funding-live="1"]');
    if (!root) return;

    var selectedId = parseInt(root.getAttribute('data-selected-id') || '0', 10) || 0;
    var canRelease = root.getAttribute('data-can-release') === '1';
    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-funding.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-funding.php';
    })();

    var projectSelect = document.getElementById('gfdProjectSelect');
    var detailPanel = document.getElementById('gfdDetailPanel');
    var releaseDialog = document.getElementById('gfdReleaseDialog');
    var releaseForm = document.getElementById('gfdReleaseForm');
    var releaseTrancheLabel = document.getElementById('gfdReleaseTrancheLabel');
    var releaseDisbursementId = document.getElementById('gfdReleaseDisbursementId');
    var releaseAmount = document.getElementById('gfdReleaseAmount');
    var releaseDate = document.getElementById('gfdReleaseDate');
    var releaseReference = document.getElementById('gfdReleaseReference');
    var releaseRemarks = document.getElementById('gfdReleaseRemarks');

    var lastOverviewFp = '';
    var lastDetailFp = '';
    var lastOverviewCount = null;
    var paused = false;
    var submitting = false;

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatPeso(amount) {
        var n = Number(amount) || 0;
        return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function todayIso() {
        var d = new Date();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function updateStats(overview) {
        var funded = document.querySelector('[data-gfd-funded-count]');
        var pending = document.querySelector('[data-gfd-pending-count]');
        if (!Array.isArray(overview)) return;
        if (funded) funded.textContent = String(overview.length);
        if (pending) {
            var pendingCount = overview.filter(function (row) {
                return parseInt(row.pending_count, 10) > 0;
            }).length;
            pending.textContent = String(pendingCount);
        }
    }

    function renderProjectSelect(overview) {
        if (!projectSelect || !Array.isArray(overview) || overview.length === 0) return;

        var current = selectedId;
        projectSelect.innerHTML = overview.map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var ref = esc(row.proposal_reference || 'Proposal');
            var title = esc(row.research_title || 'Untitled');
            var status = esc(row.funding_status_label || '');
            var selected = appId === current ? ' selected' : '';
            return '<option value="' + appId + '"' + selected + '>' + ref + ': ' + title + ' — ' + status + '</option>';
        }).join('');

        if (current <= 0 && overview.length > 0) {
            selectedId = parseInt(overview[0].grant_application_id, 10) || 0;
            projectSelect.value = String(selectedId);
            root.setAttribute('data-selected-id', String(selectedId));
        }
    }

    function buildTrancheCard(tranche, canReleaseFunds) {
        var status = tranche.status || 'Pending';
        var isReleased = status === 'Released';
        var amount = Number(tranche.amount_released) || 0;
        var trancheNum = parseInt(tranche.tranche_number, 10) || 0;
        var trancheLabel = esc(tranche.tranche_label || ('Tranche ' + trancheNum));
        var cardClass = isReleased ? 'released' : 'pending';

        var html = '<div class="gfd-tranche-card ' + cardClass + '" data-tranche-id="' + esc(tranche.id) + '">' +
            '<div class="gfd-tranche-head">' +
            '<div><strong>' + trancheLabel + '</strong>' +
            '<div class="gfd-tranche-amount">' + esc(formatPeso(amount)) + '</div></div>' +
            '<span class="gfd-tranche-status ' + (isReleased ? 'is-released' : 'is-pending') + '">' +
            (isReleased ? 'Released' : 'Pending') + '</span></div>';

        if (isReleased) {
            html += '<div class="gfd-tranche-details">' +
                '<div><span>Release Date</span><strong>' + esc(tranche.release_date || '—') + '</strong></div>' +
                '<div><span>Reference No.</span><strong>' + esc(tranche.reference_number || '—') + '</strong></div>' +
                '<div><span>Released By</span><strong>' + esc(tranche.released_by_name || '—') + '</strong></div>';
            if (tranche.remarks) {
                html += '<div class="gfd-tranche-remarks"><span>Remarks</span><strong>' + esc(tranche.remarks) + '</strong></div>';
            }
            html += '</div>';
        } else if (canReleaseFunds) {
            html += '<button type="button" class="gfd-btn gfd-btn-release gfdReleaseTrancheBtn" ' +
                'data-disbursement-id="' + esc(tranche.id) + '" ' +
                'data-tranche-label="' + trancheLabel + '" ' +
                'data-default-amount="' + esc(amount) + '">' +
                '<i class="ti ti-cash"></i> Record Release</button>';
        }

        return html + '</div>';
    }

    function renderDetail(detail) {
        if (!detailPanel) return;

        if (!detail || !detail.application) {
            detailPanel.innerHTML = '<div class="gfd-empty"><i class="ti ti-tasks" style="font-size:2rem;color:#cbd5e1;"></i>' +
                '<p style="margin:.5rem 0 0;">Select a funded project to view disbursement tranches.</p></div>';
            return;
        }

        var app = detail.application;
        var tranches = detail.tranches || [];
        var ref = esc(app.proposal_reference || 'Proposal');
        var approved = Number(detail.approved_budget) || 0;
        var canReleaseFunds = !!detail.can_release;

        var trancheHtml = tranches.map(function (t) {
            return buildTrancheCard(t, canReleaseFunds);
        }).join('');

        detailPanel.innerHTML =
            '<h2 class="gfd-panel-title">Release Funds — ' + ref + '</h2>' +
            '<div class="gfd-summary-grid">' +
            '<div class="gfd-summary-card"><span>Approved Budget</span><strong>' + esc(formatPeso(approved)) + '</strong></div>' +
            '<div class="gfd-summary-card released"><span>Total Released</span><strong data-gfd-total-released>' +
            esc(formatPeso(detail.total_released)) + '</strong></div>' +
            '<div class="gfd-summary-card pending"><span>Balance Pending</span><strong data-gfd-balance-pending>' +
            esc(formatPeso(detail.balance_pending)) + '</strong></div>' +
            '<div class="gfd-summary-card"><span>Funding Status</span><strong data-gfd-funding-status>' +
            esc(app.funding_status_label || '') + '</strong></div></div>' +
            '<div class="gfd-meta">' +
            '<span><strong>Grant Program:</strong> ' + esc(app.funding_title || '—') + '</span>' +
            '<span><strong>Lead Proponent:</strong> ' + esc(app.applicant_name || '—') + '</span></div>' +
            '<h3 class="gfd-section-title"><i class="ti ti-layers-intersect me-1"></i>Funding Tranches</h3>' +
            '<div class="gfd-tranche-list" id="gfdTrancheList">' + trancheHtml + '</div>';

        bindReleaseButtons();
    }

    function openReleaseDialog(btn) {
        if (!releaseDialog) return;
        var id = btn.getAttribute('data-disbursement-id') || '';
        var label = btn.getAttribute('data-tranche-label') || 'Tranche';
        var amount = btn.getAttribute('data-default-amount') || '0';

        if (releaseDisbursementId) releaseDisbursementId.value = id;
        if (releaseTrancheLabel) releaseTrancheLabel.textContent = label;
        if (releaseAmount) releaseAmount.value = amount;
        if (releaseDate) releaseDate.value = todayIso();
        if (releaseReference) releaseReference.value = '';
        if (releaseRemarks) releaseRemarks.value = '';

        submitting = false;
        paused = true;
        releaseDialog.classList.add('show');
    }

    function closeReleaseDialog() {
        if (!releaseDialog) return;
        releaseDialog.classList.remove('show');
        paused = false;
        submitting = false;
    }

    function bindReleaseButtons() {
        if (!canRelease) return;
        var buttons = document.querySelectorAll('.gfdReleaseTrancheBtn');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                openReleaseDialog(btn);
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
                var overviewCount = overview.length;
                if (lastOverviewCount !== null && overviewCount !== lastOverviewCount) {
                    window.location.reload();
                    return;
                }
                lastOverviewCount = overviewCount;

                var overviewFp = data.overview_fingerprint || '';
                var detailFp = data.detail_fingerprint || '';
                var overviewChanged = isPoll && lastOverviewFp !== '' && overviewFp !== lastOverviewFp;
                var detailChanged = isPoll && lastDetailFp !== '' && detailFp !== lastDetailFp;

                lastOverviewFp = overviewFp;
                lastDetailFp = detailFp;

                updateStats(overview);
                renderProjectSelect(overview);
                if (data.detail) {
                    renderDetail(data.detail);
                }

                if (overviewChanged || detailChanged) {
                    flashUpdate();
                }
            })
            .catch(function () {});
    }

    function flashUpdate() {
        var panel = document.getElementById('gfdDetailPanel');
        if (!panel) return;
        panel.classList.add('gfd-panel-updated');
        setTimeout(function () {
            panel.classList.remove('gfd-panel-updated');
        }, 1200);
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', function () {
            selectedId = parseInt(projectSelect.value, 10) || 0;
            root.setAttribute('data-selected-id', String(selectedId));
            fetchOverview(false);
        });
    }

    if (releaseForm) {
        releaseForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitting) return;

            var disbursementId = parseInt(releaseDisbursementId ? releaseDisbursementId.value : '0', 10) || 0;
            if (disbursementId <= 0) return;

            submitting = true;
            fetch(apiBase + '?action=release_tranche', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    disbursement_id: disbursementId,
                    amount_released: releaseAmount ? releaseAmount.value : '',
                    release_date: releaseDate ? releaseDate.value : '',
                    reference_number: releaseReference ? releaseReference.value.trim() : '',
                    remarks: releaseRemarks ? releaseRemarks.value.trim() : ''
                })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    closeReleaseDialog();
                    if (!data || !data.success) {
                        alert((data && data.message) || 'Release failed.');
                        return;
                    }
                    lastOverviewFp = data.overview_fingerprint || lastOverviewFp;
                    lastDetailFp = data.detail_fingerprint || lastDetailFp;
                    if (data.overview) {
                        updateStats(data.overview);
                        renderProjectSelect(data.overview);
                    }
                    if (data.detail) {
                        renderDetail(data.detail);
                    }
                    flashUpdate();
                })
                .catch(function () {
                    alert('Release request failed.');
                })
                .finally(function () {
                    submitting = false;
                });
        });
    }

    document.getElementById('gfdReleaseCancelBtn')?.addEventListener('click', closeReleaseDialog);

    bindReleaseButtons();
    fetchOverview(false);

    function poll() {
        if (paused || document.hidden || submitting) return;
        if (releaseDialog && releaseDialog.classList.contains('show')) return;
        fetchOverview(true);
    }

    setInterval(poll, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
})();
