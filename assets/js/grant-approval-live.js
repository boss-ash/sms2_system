/**
 * SMS 2 – Real-time grant approval workflow polling
 */
(function () {
    'use strict';

    var POLL_MS = 8000;
    var root = document.querySelector('[data-grant-approval-live="1"]');
    if (!root) return;

    var selectedId = parseInt(root.getAttribute('data-selected-id') || '0', 10) || 0;
    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) return '/modules/crad/api/grant-approval.php';
        return path.slice(0, idx) + '/modules/crad/api/grant-approval.php';
    })();

    var lastFingerprint = '';
    var timer = null;
    var paused = false;
    var signing = false;

    var queueList = document.getElementById('gawQueueList');
    var detailPanel = document.getElementById('gawDetailPanel');
    var statInProgress = document.getElementById('gawStatInProgress');
    var statCompleted = document.getElementById('gawStatCompleted');
    var statTotal = document.getElementById('gawStatTotal');
    var refreshBtn = document.getElementById('gawRefreshBtn');

    var signDialog = document.getElementById('gawSignDialog');
    var signCanvas = document.getElementById('gawSignCanvas');
    var returnDialog = document.getElementById('gawReturnDialog');
    var returnRemarks = document.getElementById('gawReturnRemarks');

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function pillClass(status) {
        if (status === 'Completed') return 'completed';
        if (status === 'Returned') return 'returned';
        return 'in-progress';
    }

    function stepDisplayState(step, currentStepKey, workflowStatus) {
        var status = step.status || 'Queued';
        var stepKey = step.step_key || '';
        var actedAt = step.acted_at || '';

        if (status === 'Approved') {
            var date = '';
            if (actedAt) {
                try {
                    date = new Date(actedAt.replace(' ', 'T')).toLocaleDateString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric'
                    });
                } catch (e) { date = actedAt; }
            }
            return { state: 'approved', label: 'Approved', sub: '', date: date };
        }
        if (status === 'Returned') {
            return { state: 'returned', label: 'Returned', sub: '', date: '' };
        }
        if (workflowStatus !== 'In Progress') {
            return { state: 'queued', label: 'Queued', sub: '', date: '' };
        }
        if (stepKey === currentStepKey && (status === 'Pending' || status === 'Queued')) {
            return { state: 'active', label: 'In Review', sub: 'Pending', date: '' };
        }
        return { state: 'queued', label: 'Queued', sub: '', date: '' };
    }

    function renderQueue(workflows) {
        if (!queueList) return;
        if (!Array.isArray(workflows) || workflows.length === 0) {
            queueList.innerHTML = '<div class="gaw-detail-empty" style="padding:2rem 1rem;">' +
                '<p style="margin:0;">No proposals in the approval workflow yet.</p></div>';
            return;
        }

        queueList.innerHTML = workflows.map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var ref = esc(row.proposal_reference || 'Proposal');
            var title = esc(row.research_title || 'Untitled');
            var wfStatus = row.workflow_status || '';
            var stepLabel = esc(row.current_step_label || '');
            var active = appId === selectedId ? ' active' : '';
            var meta = '<span class="gaw-status-pill ' + pillClass(wfStatus) + '">' + esc(wfStatus) + '</span>';
            if (stepLabel && wfStatus === 'In Progress') {
                meta += ' · ' + stepLabel;
            }
            return '<button type="button" class="gaw-queue-item' + active + '" data-app-id="' + appId + '">' +
                '<div class="gaw-queue-ref">' + ref + ' v' + (row.current_version || 1) + '</div>' +
                '<div class="gaw-queue-title">' + title + '</div>' +
                '<div class="gaw-queue-meta">' + meta + '</div></button>';
        }).join('');

        queueList.querySelectorAll('.gaw-queue-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                selectedId = parseInt(btn.getAttribute('data-app-id'), 10) || 0;
                root.setAttribute('data-selected-id', String(selectedId));
                queueList.querySelectorAll('.gaw-queue-item').forEach(function (b) {
                    b.classList.toggle('active', b === btn);
                });
                fetchDetail();
            });
        });
    }

    function renderDetail(detail) {
        if (!detailPanel) return;

        if (!detail || !detail.workflow) {
            detailPanel.innerHTML = '<div class="gaw-detail-empty"><p style="margin:0;">Select a proposal to view its sign-off sequence.</p></div>';
            return;
        }

        var wf = detail.workflow;
        var steps = detail.steps || [];
        var ref = esc(wf.proposal_reference || 'Proposal');
        var canAct = !!detail.can_act;
        var wfStatus = wf.workflow_status || '';
        var currentStepKey = wf.current_step_key || '';
        var roleLabel = esc(detail.role_label || '');

        var stepperHtml = steps.map(function (step) {
            var display = stepDisplayState(step, currentStepKey, wfStatus);
            var order = step.step_order || 0;
            var icon = display.state === 'approved'
                ? '<i class="ti ti-check" style="font-size:1rem;"></i>'
                : String(order);
            var sub = display.sub ? '<div class="gaw-step-sub">' + esc(display.sub) + '</div>' : '';
            var date = display.date ? '<div class="gaw-step-date">' + esc(display.date) + '</div>' : '';
            return '<div class="gaw-step ' + display.state + '">' +
                '<div class="gaw-step-icon">' + icon + '</div>' +
                '<div class="gaw-step-name">' + esc(step.step_label) + '</div>' +
                '<div class="gaw-step-status">' + esc(display.label) + '</div>' +
                sub + date + '</div>';
        }).join('');

        var actionHtml = '';
        if (canAct) {
            actionHtml = '<div class="gaw-action-panel" id="gawActionPanel">' +
                '<h3>ADMINISTRATIVE SIGN-OFF ACTION PANEL</h3>' +
                '<p>Logged in as: <strong>' + roleLabel + '</strong>. Signatures are timestamped and logged in the permanent audit trail.</p>' +
                '<div class="gaw-action-buttons">' +
                '<button type="button" class="mpl-btn gaw-btn-approve" id="gawSignApproveBtn">' +
                '<i class="ti ti-signature"></i> Sign &amp; Approve Current Level</button>' +
                '<button type="button" class="mpl-btn gaw-btn-return" id="gawReturnBtn">' +
                '<i class="ti ti-x"></i> Return to Proponent for Revision</button>' +
                '</div></div>';
        } else if (detail.is_monitor && wfStatus === 'In Progress' && detail.current_step) {
            actionHtml = '<p class="gaw-monitor-note">Monitoring mode — current stage: <strong>' +
                esc(detail.current_step.step_label) + '</strong></p>';
        }

        detailPanel.innerHTML = '<h2 class="gaw-detail-title">Sign-off Sequence for ' + ref + '</h2>' +
            '<div class="gaw-stepper" id="gawStepper">' + stepperHtml + '</div>' + actionHtml;

        bindActionButtons();
    }

    function updateStats(data) {
        if (statInProgress) statInProgress.textContent = String(data.in_progress || 0);
        if (statCompleted) statCompleted.textContent = String(data.completed || 0);
        if (statTotal) statTotal.textContent = String(data.count || 0);
    }

    function fetchWorkflows(reloadOnChange) {
        var url = apiBase + '?action=get_workflows';
        if (selectedId > 0) url += '&id=' + encodeURIComponent(selectedId);

        return fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;
                var fp = data.fingerprint || '';
                if (reloadOnChange && lastFingerprint !== '' && fp !== lastFingerprint) {
                    window.location.href = window.location.pathname + (selectedId ? '?id=' + selectedId : '');
                    return;
                }
                lastFingerprint = fp;
                updateStats(data);
                renderQueue(data.workflows || []);
                if (data.detail) {
                    renderDetail(data.detail);
                }
            })
            .catch(function () {});
    }

    function fetchDetail() {
        if (selectedId <= 0) return;
        fetch(apiBase + '?action=get_detail&id=' + encodeURIComponent(selectedId), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (data && data.success) renderDetail(data.detail);
            });
    }

  // Signature pad
    var signCtx = null;
    var drawing = false;

    function initSignPad() {
        if (!signCanvas) return;
        signCtx = signCanvas.getContext('2d');
        signCtx.strokeStyle = '#0f172a';
        signCtx.lineWidth = 2;
        signCtx.lineCap = 'round';

        function pos(e) {
            var rect = signCanvas.getBoundingClientRect();
            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
            var clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (signCanvas.width / rect.width),
                y: (clientY - rect.top) * (signCanvas.height / rect.height)
            };
        }

        function start(e) {
            drawing = true;
            var p = pos(e);
            signCtx.beginPath();
            signCtx.moveTo(p.x, p.y);
            e.preventDefault();
        }

        function move(e) {
            if (!drawing) return;
            var p = pos(e);
            signCtx.lineTo(p.x, p.y);
            signCtx.stroke();
            e.preventDefault();
        }

        function end() { drawing = false; }

        signCanvas.addEventListener('mousedown', start);
        signCanvas.addEventListener('mousemove', move);
        signCanvas.addEventListener('mouseup', end);
        signCanvas.addEventListener('mouseleave', end);
        signCanvas.addEventListener('touchstart', start, { passive: false });
        signCanvas.addEventListener('touchmove', move, { passive: false });
        signCanvas.addEventListener('touchend', end);
    }

    function clearSignPad() {
        if (!signCtx || !signCanvas) return;
        signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
    }

    function openSignDialog() {
        if (!signDialog) return;
        clearSignPad();
        signing = true;
        paused = true;
        signDialog.classList.add('show');
    }

    function closeSignDialog() {
        if (!signDialog) return;
        signDialog.classList.remove('show');
        signing = false;
        paused = false;
    }

    function openReturnDialog() {
        if (!returnDialog) return;
        if (returnRemarks) returnRemarks.value = '';
        signing = true;
        paused = true;
        returnDialog.classList.add('show');
    }

    function closeReturnDialog() {
        if (!returnDialog) return;
        returnDialog.classList.remove('show');
        signing = false;
        paused = false;
    }

    function submitSignoff() {
        if (!signCanvas || selectedId <= 0) return;
        var signature = signCanvas.toDataURL('image/png');
        fetch(apiBase + '?action=sign_approve', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ application_id: selectedId, signature_data: signature })
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                closeSignDialog();
                if (!data || !data.success) {
                    alert((data && data.message) || 'Sign-off failed.');
                    return;
                }
                renderDetail(data.detail);
                fetchWorkflows(false);
            })
            .catch(function () { alert('Sign-off request failed.'); });
    }

    function submitReturn() {
        if (selectedId <= 0) return;
        var remarks = returnRemarks ? returnRemarks.value.trim() : '';
        if (!remarks) {
            alert('Remarks are required.');
            return;
        }
        fetch(apiBase + '?action=return_revision', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ application_id: selectedId, remarks: remarks })
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                closeReturnDialog();
                if (!data || !data.success) {
                    alert((data && data.message) || 'Return failed.');
                    return;
                }
                renderDetail(data.detail);
                fetchWorkflows(false);
            })
            .catch(function () { alert('Return request failed.'); });
    }

    function bindActionButtons() {
        var approveBtn = document.getElementById('gawSignApproveBtn');
        var returnBtn = document.getElementById('gawReturnBtn');
        if (approveBtn) approveBtn.addEventListener('click', openSignDialog);
        if (returnBtn) returnBtn.addEventListener('click', openReturnDialog);
    }

    document.getElementById('gawSignClearBtn')?.addEventListener('click', clearSignPad);
    document.getElementById('gawSignCancelBtn')?.addEventListener('click', closeSignDialog);
    document.getElementById('gawSignConfirmBtn')?.addEventListener('click', submitSignoff);
    document.getElementById('gawReturnCancelBtn')?.addEventListener('click', closeReturnDialog);
    document.getElementById('gawReturnConfirmBtn')?.addEventListener('click', submitReturn);

    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            fetchWorkflows(false);
        });
    }

    bindActionButtons();
    initSignPad();

    fetchWorkflows(false).then(function () {
        lastFingerprint = lastFingerprint || '';
    });

    function poll() {
        if (paused || document.hidden || signing) return;
        fetchWorkflows(true);
    }

    timer = setInterval(poll, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
})();
