/**
 * SMS 2 – User Management Module JS
 */
(function () {
    'use strict';

    /* ── Live search / filter on user table ─────────────────── */
    function initUserTableFilter() {
        var searchInput  = document.getElementById('umSearch');
        var roleFilter   = document.getElementById('umRoleFilter');
        var statusFilter = document.getElementById('umStatusFilter');
        var tableBody    = document.getElementById('umTableBody');

        if (!tableBody) return;

        function applyFilters() {
            var term   = searchInput  ? searchInput.value.toLowerCase().trim()  : '';
            var role   = roleFilter   ? roleFilter.value.toLowerCase()           : '';
            var status = statusFilter ? statusFilter.value.toLowerCase()         : '';
            var rows   = tableBody.querySelectorAll('tr.um-user-row');
            var visible = 0;

            rows.forEach(function (row) {
                var name     = (row.dataset.name     || '').toLowerCase();
                var email    = (row.dataset.email    || '').toLowerCase();
                var username = (row.dataset.username || '').toLowerCase();
                var rowRole  = (row.dataset.role     || '').toLowerCase();
                var rowStatus = (row.dataset.status  || '').toLowerCase();

                var matchSearch = !term   || name.includes(term)  || email.includes(term) || username.includes(term);
                var matchRole   = !role   || rowRole   === role;
                var matchStatus = !status || rowStatus === status;

                if (matchSearch && matchRole && matchStatus) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            var noResults = document.getElementById('umNoResults');
            if (noResults) {
                noResults.style.display = visible === 0 ? '' : 'none';
            }

            tableBody.querySelectorAll('tr[data-group-row]').forEach(function (groupRow) {
                var hasVisibleRow = false;
                var cursor = groupRow.nextElementSibling;
                while (cursor && !cursor.hasAttribute('data-group-row')) {
                    if (cursor.classList.contains('um-user-row') && cursor.style.display !== 'none') {
                        hasVisibleRow = true;
                        break;
                    }
                    cursor = cursor.nextElementSibling;
                }
                groupRow.style.display = hasVisibleRow ? '' : 'none';
            });
        }

        if (searchInput)  searchInput.addEventListener('input',  applyFilters);
        if (roleFilter)   roleFilter.addEventListener('change',   applyFilters);
        if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    }

    /* ── Custom confirm modal ───────────────────────────────── */
    function buildConfirmModal() {
        if (document.getElementById('umConfirmModal')) return;
        var html = [
            '<div class="modal fade sms-confirm-modal um-confirm-modal" id="umConfirmModal" tabindex="-1" aria-modal="true" role="dialog">',
            '  <div class="modal-dialog modal-dialog-centered sms-confirm-dialog um-confirm-dialog">',
            '    <div class="modal-content sms-confirm-content um-confirm-content">',
            '      <div class="sms-confirm-header um-confirm-header">',
            '        <div class="sms-confirm-header-text um-confirm-header-text">',
            '          <span class="sms-confirm-kicker">Confirm</span>',
            '          <h6 class="sms-confirm-title um-confirm-title" id="umConfirmTitle">Are you sure?</h6>',
            '        </div>',
            '        <button type="button" class="sms-confirm-close" data-bs-dismiss="modal" aria-label="Close">'
            + '<span class="sms-confirm-close__glyph" aria-hidden="true">×</span></button>',
            '      </div>',
            '      <div class="sms-confirm-body um-confirm-body">',
            '        <div class="sms-confirm-icon um-confirm-icon" id="umConfirmIcon" aria-hidden="true"></div>',
            '        <p class="sms-confirm-msg um-confirm-msg" id="umConfirmMsg"></p>',
            '      </div>',
            '      <div class="sms-confirm-footer um-confirm-footer">',
            '        <button type="button" class="btn btn-outline-secondary sms-confirm-cancel um-confirm-cancel" data-bs-dismiss="modal">Cancel</button>',
            '        <button type="button" class="btn sms-confirm-ok um-confirm-ok" id="umConfirmOk">Confirm</button>',
            '      </div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('');
        document.body.insertAdjacentHTML('beforeend', html);
    }

    /**
     * Show the custom confirm modal.
     * @param {string}   message  Body text
     * @param {Function} onOk     Called when user clicks Confirm
     * @param {object}   [opts]   { title, type: 'danger'|'warning'|'info'|'primary' }
     */
    window.umConfirm = function (message, onOk, opts) {
        opts = opts || {};
        if (typeof window.smsConfirm === 'function') {
            var type = opts.type || 'danger';
            var titles = { danger: 'Delete confirmation', warning: 'Please confirm', info: 'Confirm action', primary: 'Save changes' };
            window.smsConfirm(message, onOk, {
                title: opts.title || titles[type] || 'Are you sure?',
                type: type,
                okText: opts.okText
            });
            return;
        }

        buildConfirmModal();

        var modal    = document.getElementById('umConfirmModal');
        var titleEl  = document.getElementById('umConfirmTitle');
        var msgEl    = document.getElementById('umConfirmMsg');
        var iconWrap = document.getElementById('umConfirmIcon');
        var okBtn    = document.getElementById('umConfirmOk');

        var type  = opts.type  || 'danger';
        var title = opts.title || (type === 'danger' ? 'Delete confirmation' : type === 'warning' ? 'Please confirm' : 'Confirm action');

        if (titleEl) titleEl.textContent = title;
        if (msgEl)   msgEl.textContent   = message;

        // icon + colour per type
        var icons = { danger:'fa-trash-alt', warning:'fa-exclamation-triangle', info:'fa-sign-out-alt', primary:'fa-save' };
        if (iconWrap) {
            iconWrap.className = 'sms-confirm-icon um-confirm-icon sms-confirm-icon--' + type + ' um-confirm-icon--' + type;
            iconWrap.innerHTML = window.smsIconHtml
                ? window.smsIconHtml((icons[type] || 'question-circle').replace(/^fa-/, ''))
                : '';
        }
        if (okBtn) {
            okBtn.className = 'btn sms-confirm-ok um-confirm-ok sms-confirm-ok--' + type + ' um-confirm-ok--' + type;
            var labels = { danger:'Yes, delete', warning:'Yes, proceed', info:'Yes, leave', primary:'Yes, save' };
            okBtn.textContent = labels[type] || 'Confirm';
        }

        // wire OK button — clone to remove stale listeners
        if (okBtn) {
            var newOk = okBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOk, okBtn);
            newOk.addEventListener('click', function () {
                var bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                if (typeof onOk === 'function') onOk();
            });
        }

        var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
        bsModal.show();
    };

    function initActionConfirm() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-um-confirm]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();

            var msg  = btn.dataset.umConfirm || 'Are you sure?';
            var type = btn.dataset.umConfirmType || 'danger';

            window.umConfirm(msg, function () {
                // Re-dispatch a synthetic click without the confirm guard so the
                // original action (form submit, link navigation, etc.) proceeds.
                btn.removeAttribute('data-um-confirm');
                btn.click();
                btn.setAttribute('data-um-confirm', msg);
            }, { type: type });
        });
    }

    /* ── User form modal — populate from row data ────────────── */
    function initUserModal() {
        var modal = document.getElementById('umUserModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var action = trigger.dataset.umAction || 'add';
            var title  = modal.querySelector('#umModalTitle');
            var form   = modal.querySelector('#umUserForm');

            if (title) {
                title.textContent = action === 'edit' ? 'Edit User' : 'Add New User';
            }

            if (action === 'edit' && form) {
                form.querySelector('[name="full_name"]').value  = trigger.dataset.name   || '';
                form.querySelector('[name="username"]').value   = trigger.dataset.username || '';
                form.querySelector('[name="email"]').value      = trigger.dataset.email  || '';
                form.querySelector('[name="role"]').value       = trigger.dataset.role   || '';
                form.querySelector('[name="status"]').value     = trigger.dataset.status || 'active';
                form.querySelector('[name="user_id"]').value    = trigger.dataset.uid    || '';

                var pwRow = form.querySelector('.um-pw-row');
                var pwLabel = pwRow && pwRow.querySelector('.um-pw-label');
                var pwInput = form.querySelector('[name="password"]');
                var pwRequired = pwRow && pwRow.querySelector('.um-pw-required');
                var pwStrength = form.querySelector('.um-pw-strength-row');
                if (pwLabel) {
                    pwLabel.innerHTML = 'New Password <span class="text-muted fw-normal">(leave blank to keep current)</span>';
                }
                if (pwInput) {
                    pwInput.removeAttribute('required');
                    pwInput.value = '';
                }
                if (pwRequired) pwRequired.hidden = true;
                if (pwStrength) pwStrength.hidden = true;
            } else if (form) {
                form.reset();
                form.querySelector('[name="user_id"]').value = '';
                var pwRow = form.querySelector('.um-pw-row');
                var pwLabel = pwRow && pwRow.querySelector('.um-pw-label');
                var pwInput = form.querySelector('[name="password"]');
                var pwRequired = pwRow && pwRow.querySelector('.um-pw-required');
                var pwStrength = form.querySelector('.um-pw-strength-row');
                if (pwLabel) {
                    pwLabel.innerHTML = 'Password <span class="text-danger um-pw-required">*</span>';
                }
                if (pwInput) pwInput.setAttribute('required', 'required');
                if (pwRequired) pwRequired.hidden = false;
                if (pwStrength) pwStrength.hidden = false;
            }

            var pwField = form && form.querySelector('[name="password"]');
            if (pwField) {
                pwField.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Update avatar initial
            var avatarEl = modal.querySelector('.um-modal-avatar');
            if (avatarEl) {
                var n = form ? (form.querySelector('[name="full_name"]').value || '') : '';
                avatarEl.textContent = n ? n.trim()[0].toUpperCase() : '?';
            }
        });

        // Live update avatar initial while typing name
        var nameInput = document.querySelector('#umUserForm [name="full_name"]');
        var avatarEl  = document.querySelector('#umUserModal .um-modal-avatar');
        if (nameInput && avatarEl) {
            nameInput.addEventListener('input', function () {
                avatarEl.textContent = this.value.trim() ? this.value.trim()[0].toUpperCase() : '?';
            });
        }
    }

    /* ── Log filter (Admin Activity Logs) ───────────────────── */
    function initLogFilter() {
        var actionFilter = document.getElementById('logActionFilter');
        var moduleFilter = document.getElementById('logModuleFilter');
        var userFilter   = document.getElementById('logUserFilter');
        var dateFrom     = document.getElementById('logDateFrom');
        var dateTo       = document.getElementById('logDateTo');
        var clearBtn     = document.getElementById('adminLogClear');
        var countEl      = document.getElementById('adminLogCount');
        var tableBody    = document.getElementById('logTableBody');
        if (!tableBody) return;

        var emptyFilter = tableBody.querySelector('.admin-log-empty-filter');

        function applyLog() {
            var action = actionFilter ? actionFilter.value.toLowerCase() : '';
            var module = moduleFilter ? moduleFilter.value.toLowerCase() : '';
            var user   = userFilter ? userFilter.value.toLowerCase().trim() : '';
            var from   = dateFrom ? dateFrom.value : '';
            var to     = dateTo ? dateTo.value : '';
            var rows   = tableBody.querySelectorAll('tr.log-row');
            var visible = 0;

            rows.forEach(function (row) {
                var rowAction = (row.dataset.action || '').toLowerCase();
                var rowUser   = (row.dataset.user || '').toLowerCase();
                var rowModule = (row.dataset.module || '').toLowerCase();
                var rowDate   = row.dataset.date || '';
                var ok = true;
                if (action && rowAction !== action) ok = false;
                if (ok && module && rowModule !== module) ok = false;
                if (ok && user && rowUser.indexOf(user) === -1) ok = false;
                if (ok && from && rowDate && rowDate < from) ok = false;
                if (ok && to && rowDate && rowDate > to) ok = false;
                row.hidden = !ok;
                if (ok) visible += 1;
            });

            if (emptyFilter) {
                if (visible === 0 && rows.length > 0) emptyFilter.removeAttribute('hidden');
                else emptyFilter.setAttribute('hidden', 'hidden');
            }
            if (countEl) countEl.textContent = visible + ' shown';
        }

        function clearFilters(e) {
            if (e) e.preventDefault();
            if (actionFilter) actionFilter.selectedIndex = 0;
            if (moduleFilter) moduleFilter.selectedIndex = 0;
            if (userFilter) userFilter.value = '';
            if (dateFrom) dateFrom.value = '';
            if (dateTo) dateTo.value = '';
            applyLog();
        }

        if (actionFilter) actionFilter.addEventListener('change', applyLog);
        if (moduleFilter) moduleFilter.addEventListener('change', applyLog);
        if (userFilter) userFilter.addEventListener('input', applyLog);
        if (dateFrom) dateFrom.addEventListener('change', applyLog);
        if (dateTo) dateTo.addEventListener('change', applyLog);
        if (clearBtn) clearBtn.addEventListener('click', clearFilters);
        applyLog();
    }

    /* ── Settings — unsaved changes / leave-site modal ─────── */
    function buildLeaveModal() {
        if (document.getElementById('umLeaveModal')) return;
        var html = [
            '<div class="modal fade sms-confirm-modal um-confirm-modal" id="umLeaveModal" tabindex="-1" aria-modal="true" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">',
            '  <div class="modal-dialog modal-dialog-centered sms-confirm-dialog um-confirm-dialog">',
            '    <div class="modal-content sms-confirm-content um-confirm-content">',
            '      <div class="sms-confirm-header um-confirm-header">',
            '        <div class="sms-confirm-header-text um-confirm-header-text">',
            '          <span class="sms-confirm-kicker">Unsaved changes</span>',
            '          <h6 class="sms-confirm-title um-confirm-title">Leave page?</h6>',
            '        </div>',
            '        <button type="button" class="sms-confirm-close" data-bs-dismiss="modal" aria-label="Close" id="umLeaveCancel">'
            + '<span class="sms-confirm-close__glyph" aria-hidden="true">×</span></button>',
            '      </div>',
            '      <div class="sms-confirm-body um-confirm-body">',
            '        <div class="sms-confirm-icon um-confirm-icon sms-confirm-icon--warning um-confirm-icon--warning" aria-hidden="true">',
            '          <i class="ti ti-alert-triangle"></i>',
            '        </div>',
            '        <p class="sms-confirm-msg um-confirm-msg">Changes you made may not be saved.</p>',
            '      </div>',
            '      <div class="sms-confirm-footer um-confirm-footer">',
            '        <button type="button" class="btn btn-outline-secondary sms-confirm-cancel um-confirm-cancel" id="umLeaveStay">Stay</button>',
            '        <button type="button" class="btn sms-confirm-ok um-confirm-ok sms-confirm-ok--warning um-confirm-ok--warning" id="umLeaveOk">',
            '          <i class="ti ti-logout me-1"></i>Leave',
            '        </button>',
            '      </div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('');
        document.body.insertAdjacentHTML('beforeend', html);
    }

    function initSettingsDirty() {
        var forms = document.querySelectorAll('.settings-form');
        if (!forms.length) return;

        var dirty           = false;
        var pendingHref     = null;
        var leavingViaModal = false;

        forms.forEach(function (form) {
            form.addEventListener('change', function () { dirty = true; });
            form.addEventListener('submit', function () { dirty = false; leavingViaModal = true; });
        });

        // Intercept ALL link / button navigations when dirty
        document.addEventListener('click', function (e) {
            if (!dirty || leavingViaModal) return;

            var anchor = e.target.closest('a[href]');
            if (!anchor) return;
            var href = anchor.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;

            e.preventDefault();
            pendingHref = anchor.href;
            buildLeaveModal();

            var leaveModal  = document.getElementById('umLeaveModal');
            var leaveOk     = document.getElementById('umLeaveOk');
            var leaveCancel = document.getElementById('umLeaveCancel');
            var leaveStay   = document.getElementById('umLeaveStay');

            function hideLeaveModal() {
                var bsModal = bootstrap.Modal.getInstance(leaveModal);
                if (bsModal) bsModal.hide();
            }

            // clone to remove stale listeners
            if (leaveOk) {
                var newOk = leaveOk.cloneNode(true);
                leaveOk.parentNode.replaceChild(newOk, leaveOk);
                newOk.addEventListener('click', function () {
                    hideLeaveModal();
                    dirty = false;
                    leavingViaModal = true;
                    window.location.href = pendingHref;
                });
            }
            [leaveCancel, leaveStay].forEach(function (btn) {
                if (!btn) return;
                var newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.addEventListener('click', hideLeaveModal);
            });

            var bsModal = bootstrap.Modal.getOrCreateInstance(leaveModal);
            bsModal.show();
        }, true); // capture phase so we intercept sidebar links too
    }

    /* ── Toast helper ───────────────────────────────────────── */
    window.umShowToast = function (message, type) {
        type = type || 'success';
        var container = document.getElementById('umToastContainer');
        if (!container) return;

        var id = 'toast-' + Date.now();
        var icons = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        var icon  = icons[type] || icons.info;

        var html = '<div id="' + id + '" class="toast align-items-center text-bg-' + type + ' border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">'
            + '<div class="d-flex"><div class="toast-body d-flex align-items-center gap-2">'
            + (window.smsIconHtml ? window.smsIconHtml(icon.replace(/^fa-/, '')) : '') + ' ' + message
            + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
            + '</div></div>';

        container.insertAdjacentHTML('beforeend', html);
        var el = document.getElementById(id);
        if (el && window.bootstrap) {
            var t = new bootstrap.Toast(el, { delay: 3500 });
            t.show();
            el.addEventListener('hidden.bs.toast', function () { el.remove(); });
        }
    };

    /* ── Boot ───────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        initUserTableFilter();
        initActionConfirm();
        initUserModal();
        initLogFilter();
        initSettingsDirty();

        // Show toast from URL param (after form submit redirect)
        var params = new URLSearchParams(window.location.search);
        if (params.get('saved') === '1')   window.umShowToast('Changes saved successfully.', 'success');
        if (params.get('created') === '1') window.umShowToast('User account created.', 'success');
        if (params.get('updated') === '1') window.umShowToast('User account updated.', 'success');
        if (params.get('archived') === '1') window.umShowToast('Moved to User Archive.', 'warning');
        if (params.get('restored') === '1') window.umShowToast('Restored to User Accounts.', 'success');
        if (params.get('purged') === '1') window.umShowToast('Permanently deleted from archive.', 'warning');
        if (params.get('deleted') === '1') window.umShowToast('Moved to User Archive.', 'warning');
    });
})();
