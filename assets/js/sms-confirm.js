/**
 * SMS 2 — Global confirm modal (Bootstrap 5)
 * Uses .sms-confirm-modal styles from assets/css/components.css
 */
(function () {
    'use strict';

    var TYPE_META = {
        danger:  { kicker: 'Confirm', title: 'Delete confirmation', icon: 'trash', ok: 'Yes, delete' },
        warning: { kicker: 'Confirm', title: 'Please confirm',      icon: 'alert-triangle', ok: 'Yes, proceed' },
        info:    { kicker: 'Confirm', title: 'Confirm action',      icon: 'info-circle', ok: 'Yes, continue' },
        primary: { kicker: 'Confirm', title: 'Save changes',        icon: 'device-floppy', ok: 'Yes, save' }
    };

    function ensureModal() {
        if (document.getElementById('smsConfirmModal')) return;

        var html = [
            '<div class="modal fade sms-confirm-modal" id="smsConfirmModal" tabindex="-1" aria-modal="true" role="dialog">',
            '  <div class="modal-dialog modal-dialog-centered sms-confirm-dialog">',
            '    <div class="modal-content sms-confirm-content">',
            '      <div class="sms-confirm-header">',
            '        <div class="sms-confirm-header-text">',
            '          <span class="sms-confirm-kicker" id="smsConfirmKicker">Confirm</span>',
            '          <h6 class="sms-confirm-title" id="smsConfirmTitle">Are you sure?</h6>',
            '        </div>',
            '        <button type="button" class="sms-confirm-close" data-bs-dismiss="modal" aria-label="Close">'
            + (window.smsIconHtml ? window.smsIconHtml('x', '', { 'aria-hidden': 'true' }) : '<span aria-hidden="true">&times;</span>')
            + '</button>',
            '      </div>',
            '      <div class="sms-confirm-body">',
            '        <div class="sms-confirm-icon sms-confirm-icon--danger" id="smsConfirmIcon" aria-hidden="true"></div>',
            '        <p class="sms-confirm-msg" id="smsConfirmMsg"></p>',
            '      </div>',
            '      <div class="sms-confirm-footer">',
            '        <button type="button" class="btn btn-outline-secondary sms-confirm-cancel" data-bs-dismiss="modal" id="smsConfirmCancel">Cancel</button>',
            '        <button type="button" class="btn sms-confirm-ok sms-confirm-ok--danger" id="smsConfirmOk">Confirm</button>',
            '      </div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('');

        document.body.insertAdjacentHTML('beforeend', html);
    }

    /**
     * @param {string} message
     * @param {Function} onOk
     * @param {object} [opts] { title, kicker, type, okText, cancelText }
     */
    function smsConfirm(message, onOk, opts) {
        opts = opts || {};
        if (!window.bootstrap) return;

        ensureModal();

        var modal   = document.getElementById('smsConfirmModal');
        var kicker  = document.getElementById('smsConfirmKicker');
        var titleEl = document.getElementById('smsConfirmTitle');
        var msgEl   = document.getElementById('smsConfirmMsg');
        var iconEl  = document.getElementById('smsConfirmIcon');
        var okBtn   = document.getElementById('smsConfirmOk');
        var cancelBtn = document.getElementById('smsConfirmCancel');

        var type = opts.type || 'danger';
        var meta = TYPE_META[type] || TYPE_META.danger;

        if (kicker)  kicker.textContent  = opts.kicker || meta.kicker;
        if (titleEl) titleEl.textContent = opts.title || meta.title;
        if (msgEl)   msgEl.textContent   = message || 'Please confirm this action.';

        if (iconEl) {
            iconEl.className = 'sms-confirm-icon sms-confirm-icon--' + type;
            var iconName = opts.icon || meta.icon;
            iconEl.innerHTML = window.smsIconHtml
                ? window.smsIconHtml(iconName)
                : '<i class="ti ti-' + iconName + '"></i>';
        }

        if (cancelBtn && opts.cancelText) cancelBtn.textContent = opts.cancelText;

        if (okBtn) {
            okBtn.className = 'btn sms-confirm-ok sms-confirm-ok--' + type;
            if (opts.okHtml) {
                okBtn.innerHTML = opts.okHtml;
            } else {
                okBtn.textContent = opts.okText || meta.ok;
            }

            var freshOk = okBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(freshOk, okBtn);
            freshOk.addEventListener('click', function () {
                var inst = bootstrap.Modal.getInstance(modal);
                if (inst) inst.hide();
                if (typeof onOk === 'function') onOk();
            });
        }

        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    window.smsConfirm = smsConfirm;
})();
