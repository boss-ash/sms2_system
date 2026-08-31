/**
 * SMS 2 – Module Security tabs (Super Admin + staff per-module)
 * Smooth in-page panels — no full reload on tab click.
 */
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('secModuleRoot');
    if (root) {
        var tabs = root.querySelectorAll('[data-sec-tab]');
        var panels = root.querySelectorAll('[data-sec-panel]');
        var cards = root.querySelectorAll('[data-sec-tab-card]');
        var animTimer = null;

        function normalizePanel(name) {
            name = String(name || '').replace(/^panel-/, '');
            if (name === 'passwords' || name === 'account' || name === 'password' || name === 'request') return 'passwords';
            if (name === 'authenticator') return 'authenticator';
            if (name === 'module' || name === 'management' || name === 'module-management') return 'module';
            return 'logs';
        }

        function showPanel(name, animate) {
            var panelName = normalizePanel(name);
            var doAnim = animate !== false;

            panels.forEach(function (panel) {
                var match = panel.getAttribute('data-sec-panel') === panelName;
                if (match) {
                    panel.removeAttribute('hidden');
                    panel.classList.remove('is-leaving');
                    if (doAnim) {
                        panel.classList.remove('is-entering');
                        // Force reflow so enter animation restarts
                        void panel.offsetWidth;
                        panel.classList.add('is-entering');
                        if (animTimer) clearTimeout(animTimer);
                        animTimer = setTimeout(function () {
                            panel.classList.remove('is-entering');
                        }, 280);
                    }
                } else {
                    panel.setAttribute('hidden', 'hidden');
                    panel.classList.remove('is-entering');
                }
            });

            cards.forEach(function (card) {
                var on = card.getAttribute('data-sec-tab-card') === panelName;
                card.classList.toggle('is-active', on);
                if (card.classList.contains('nav-link')) {
                    card.classList.toggle('active', on);
                    card.setAttribute('aria-selected', on ? 'true' : 'false');
                }
            });
            tabs.forEach(function (tab) {
                var on = normalizePanel(tab.getAttribute('data-sec-tab') || '') === panelName;
                if (tab.classList.contains('nav-link')) {
                    tab.classList.toggle('active', on);
                    tab.setAttribute('aria-selected', on ? 'true' : 'false');
                }
            });

            try {
                var url = new URL(window.location.href);
                var mode = root.getAttribute('data-url-mode') || 'admin';
                if (mode === 'staff') {
                    var mod = root.getAttribute('data-module') || '';
                    if (mod) url.searchParams.set('module', mod);
                    // Keep ?tab= for bookmarks / POST redirects
                    if (panelName === 'passwords') url.searchParams.set('tab', 'account');
                    else if (panelName === 'authenticator') url.searchParams.set('tab', 'authenticator');
                    else url.searchParams.set('tab', 'logs');
                } else {
                    var focus = root.getAttribute('data-focus-module') || '';
                    if (focus) {
                        url.searchParams.set('focus', focus);
                        url.searchParams.delete('picker');
                        url.searchParams.delete('home');
                        url.searchParams.delete('view');
                        url.searchParams.delete('t');
                        url.searchParams.delete('module');
                        url.searchParams.delete('m');
                    }
                }
                if (panelName === 'passwords') url.hash = 'panel-passwords';
                else if (panelName === 'authenticator') url.hash = 'panel-authenticator';
                else if (panelName === 'module') url.hash = 'panel-module';
                else url.hash = 'panel-logs';
                history.replaceState(null, '', url.pathname + url.search + url.hash);
            } catch (e) { /* ignore */ }
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                showPanel(tab.getAttribute('data-sec-tab') || 'logs', true);
            });
        });

        var hash = (window.location.hash || '').replace(/^#/, '');
        var initial = root.getAttribute('data-initial-panel') || '';
        if (hash.indexOf('panel-') === 0) {
            showPanel(hash, false);
        } else if (initial) {
            showPanel(initial, false);
        } else {
            showPanel('logs', false);
        }

        // Activity log filters (admin + staff table ids)
        function bindLogFilters(opts) {
            var actionSel = document.getElementById(opts.action);
            var userInp = document.getElementById(opts.user);
            var dateFrom = document.getElementById(opts.from);
            var dateTo = document.getElementById(opts.to);
            var countEl = document.getElementById(opts.count);
            var clearBtn = document.getElementById(opts.clear);
            var rows = root.querySelectorAll(opts.rows);
            var emptyFilter = root.querySelector(opts.empty);
            if (!rows.length && !actionSel && !userInp) return;

            function applyLogFilters() {
                var action = actionSel ? actionSel.value : '';
                var user = userInp ? userInp.value.toLowerCase().trim() : '';
                var from = dateFrom ? dateFrom.value : '';
                var to = dateTo ? dateTo.value : '';
                var visible = 0;

                rows.forEach(function (row) {
                    var rowAction = row.getAttribute('data-action') || '';
                    var rowUser = (row.getAttribute('data-user') || '').toLowerCase();
                    var rowDate = row.getAttribute('data-date') || '';
                    var ok = true;
                    if (action && rowAction !== action && rowAction.toLowerCase() !== action.toLowerCase()) ok = false;
                    if (ok && user && rowUser.indexOf(user) === -1) ok = false;
                    if (ok && from && rowDate && rowDate < from) ok = false;
                    if (ok && to && rowDate && rowDate > to) ok = false;
                    row.hidden = !ok;
                    if (ok) visible += 1;
                });

                if (emptyFilter) {
                    if (rows.length > 0 && visible === 0) emptyFilter.removeAttribute('hidden');
                    else emptyFilter.setAttribute('hidden', 'hidden');
                }
                if (countEl) countEl.textContent = visible + ' shown';
            }

            if (actionSel) actionSel.addEventListener('change', applyLogFilters);
            if (userInp) userInp.addEventListener('input', applyLogFilters);
            if (dateFrom) dateFrom.addEventListener('change', applyLogFilters);
            if (dateTo) dateTo.addEventListener('change', applyLogFilters);
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (actionSel) actionSel.selectedIndex = 0;
                    if (userInp) userInp.value = '';
                    if (dateFrom) dateFrom.value = '';
                    if (dateTo) dateTo.value = '';
                    applyLogFilters();
                });
            }
            applyLogFilters();
        }

        bindLogFilters({
            action: 'secLogAction',
            user: 'secLogUser',
            from: 'secLogDateFrom',
            to: 'secLogDateTo',
            count: 'secLogCount',
            clear: 'secLogClear',
            rows: '.sec-log-row',
            empty: '.sec-log-empty-filter'
        });
        bindLogFilters({
            action: 'modLogAction',
            user: 'modLogUser',
            from: 'modLogDateFrom',
            to: 'modLogDateTo',
            count: 'modLogCount',
            clear: 'modLogClear',
            rows: '.mod-log-row',
            empty: '.mod-log-empty-filter'
        });
    }

    var approveModal = document.getElementById('approveModal');
    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            if (!btn) return;
            var idEl = document.getElementById('approveRequestId');
            var nameEl = document.getElementById('approveUserName');
            if (idEl) idEl.value = btn.getAttribute('data-request-id') || '';
            if (nameEl) nameEl.textContent = btn.getAttribute('data-user-name') || 'this user';
        });
    }

    var rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            if (!btn) return;
            var idEl = document.getElementById('rejectRequestId');
            var nameEl = document.getElementById('rejectUserName');
            var note = document.getElementById('admin_note');
            if (idEl) idEl.value = btn.getAttribute('data-request-id') || '';
            if (nameEl) nameEl.textContent = btn.getAttribute('data-user-name') || 'this user';
            if (note) note.value = '';
        });
    }
});
