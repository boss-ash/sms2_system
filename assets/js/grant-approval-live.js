/**
 * SMS 2 – Real-time grant approval workflow polling
 */
(function () {
    'use strict';

    var POLL_MS = 5000;
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
    var lastWorkflowCount = null;
    var paused = false;
    var signing = false;

    var projectSelect = document.getElementById('gawProjectSelect');
    var detailPanel = document.getElementById('gawDetailPanel');

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
            return { state: 'approved', label: 'Approved', date: date };
        }
        if (status === 'Returned') {
            return { state: 'returned', label: 'Returned', date: '' };
        }
        if (workflowStatus !== 'In Progress') {
            return { state: 'queued', label: 'Queued', date: '--' };
        }
        if (stepKey === currentStepKey && (status === 'Pending' || status === 'Queued')) {
            return { state: 'active', label: 'In Review', date: 'Pending' };
        }
        return { state: 'queued', label: 'Queued', date: '--' };
    }

    function updateMonitorStats(data) {
        var inProg = document.querySelector('[data-gaw-in-progress]');
        var completed = document.querySelector('[data-gaw-completed]');
        if (inProg && typeof data.in_progress === 'number') {
            inProg.textContent = String(data.in_progress);
        }
        if (completed && typeof data.completed === 'number') {
            completed.textContent = String(data.completed);
        }
    }

    function workflowOptionLabel(row) {
        var ref = esc(row.proposal_reference || 'Proposal');
        var title = esc(row.research_title || 'Untitled');
        var step = esc(row.current_step_label || '');
        var status = esc(row.workflow_status || '');
        if (step && status === 'In Progress') {
            return ref + ': ' + title + ' — ' + step;
        }
        if (status === 'Completed') {
            return ref + ': ' + title + ' — Completed';
        }
        return ref + ': ' + title;
    }
    function renderProjectSelect(workflows) {
        if (!projectSelect) return;
        if (!Array.isArray(workflows) || workflows.length === 0) return;

        var current = selectedId;
        projectSelect.innerHTML = workflows.map(function (row) {
            var appId = parseInt(row.grant_application_id, 10) || 0;
            var selected = appId === current ? ' selected' : '';
            return '<option value="' + appId + '"' + selected + '>' + workflowOptionLabel(row) + '</option>';
        }).join('');

        if (current <= 0 && workflows.length > 0) {
            selectedId = parseInt(workflows[0].grant_application_id, 10) || 0;
            projectSelect.value = String(selectedId);
            root.setAttribute('data-selected-id', String(selectedId));
        }
    }

    function renderDetail(detail) {
        if (!detailPanel) return;

        if (!detail || !detail.workflow) {
            detailPanel.innerHTML = '<div class="gaw-detail-empty">' +
                '<i class="ti ti-tasks" style="font-size:2.2rem;color:#cbd5e1;display:block;margin-bottom:.65rem;"></i>' +
                '<p style="margin:0;">Select a project to view its sign-off sequence.</p></div>';
            return;
        }

        var wf = detail.workflow;
        var steps = detail.steps || [];
        var ref = esc(wf.proposal_reference || 'Proposal');
        var canAct = !!detail.can_act;
        var needsAdviserScore = !!detail.needs_adviser_score;
        var wfStatus = wf.workflow_status || '';
        var currentStepKey = wf.current_step_key || '';
        var roleLabel = esc(detail.role_label || '');
        var reviewerEvalUrl = (function () {
            var path = window.location.pathname || '';
            var idx = path.indexOf('/modules/');
            var base = idx === -1 ? '' : path.slice(0, idx);
            return base + '/modules/crad/pages/reviewer-evaluation.php?id=' + encodeURIComponent(wf.grant_application_id || selectedId);
        })();

        var stepperHtml = steps.map(function (step) {
            var display = stepDisplayState(step, currentStepKey, wfStatus);
            var order = step.step_order || 0;
            var icon = display.state === 'approved'
                ? '<i class="ti ti-check"></i>'
                : String(order);
            var dateHtml = display.date
                ? '<div class="gaw-step-date">' + esc(display.date) + '</div>'
                : '';
            return '<div class="gaw-step ' + display.state + '">' +
                '<div class="gaw-step-icon">' + icon + '</div>' +
                '<div class="gaw-step-name">' + esc(step.step_label) + '</div>' +
                '<div class="gaw-step-status">' + esc(display.label) + '</div>' +
                dateHtml + '</div>';
        }).join('');

        var actionHtml = '';
        if (needsAdviserScore) {
            actionHtml = '<div class="gaw-action-panel gaw-action-panel-warn" id="gawAdviserScorePanel">' +
                '<h3><i class="ti ti-clipboard-check me-1"></i>Adviser Evaluation Required</h3>' +
                '<p>Score this proposal in <strong>Reviewer Evaluation</strong> before you can sign and approve here.</p>' +
                '<a class="gaw-btn-approve" href="' + reviewerEvalUrl + '" style="display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;">' +
                '<i class="ti ti-star-half-filled"></i> Go to Reviewer Evaluation</a></div>';
        } else if (canAct) {
            actionHtml = '<div class="gaw-action-panel" id="gawActionPanel">' +
                '<h3>Administrative Sign-off Action Panel</h3>' +
                '<p>Logged in as: <strong>' + roleLabel + '</strong>. Signatures are timestamped and logged in the permanent audit trail.</p>' +
                '<div class="gaw-action-buttons">' +
                '<button type="button" class="gaw-btn-approve" id="gawSignApproveBtn">' +
                '<i class="ti ti-signature"></i> Sign &amp; Approve Current Level</button>' +
                '<button type="button" class="gaw-btn-return" id="gawReturnBtn">' +
                '<i class="ti ti-x"></i> Return to Proponent for Revision</button>' +
                '</div></div>';
        } else if (detail.is_monitor) {
            if (wfStatus === 'Completed') {
                actionHtml = '<div class="gaw-monitor-note">' +
                    '<i class="ti ti-check me-1"></i> Monitoring mode — all sign-offs completed for this proposal.</div>';
            } else if (detail.current_step) {
                var approverLabel = esc(detail.current_approver_label || detail.current_step.approver_role_key || '');
                actionHtml = '<div class="gaw-monitor-note">' +
                    '<i class="ti ti-eye me-1"></i> Monitoring mode — current stage: ' +
                    '<strong>' + esc(detail.current_step.step_label) + '</strong>' +
                    (approverLabel ? ' (' + approverLabel + ')' : '') + '</div>';
            }
        }

        detailPanel.innerHTML = '<h2 class="gaw-pipeline-title">Sign-off Sequence for ' + ref + '</h2>' +
            '<div class="gaw-stepper" id="gawStepper">' + stepperHtml + '</div>' + actionHtml;

        bindActionButtons();
    }

    function fetchWorkflows(isPoll) {
        var url = apiBase + '?action=get_workflows';
        if (selectedId > 0) url += '&id=' + encodeURIComponent(selectedId);

        return fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;
                var fp = data.fingerprint || '';
                var changed = isPoll && lastFingerprint !== '' && fp !== lastFingerprint;
                lastFingerprint = fp;
                renderProjectSelect(data.workflows || []);
                if (data.detail) {
                    renderDetail(data.detail);
                } else if (selectedId > 0) {
                    fetchDetail();
                }
                if (changed) {
                    flashLiveUpdate();
                }
            })
            .catch(function () {});
    }

    function flashLiveUpdate() {
        var stepper = document.getElementById('gawStepper');
        if (!stepper) return;
        stepper.classList.add('gaw-stepper-updated');
        setTimeout(function () {
            stepper.classList.remove('gaw-stepper-updated');
        }, 1200);
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

    if (projectSelect) {
        projectSelect.addEventListener('change', function () {
            selectedId = parseInt(projectSelect.value, 10) || 0;
            root.setAttribute('data-selected-id', String(selectedId));
            var newUrl = window.location.pathname + (selectedId ? '?id=' + selectedId : '');
            window.history.replaceState({}, '', newUrl);
            fetchDetail();
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

    bindActionButtons();
    initSignPad();

    fetchWorkflows(false);

    function poll() {
        if (paused || document.hidden || signing) return;
        fetchWorkflows(true);
    }

    setInterval(poll, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
})();
