/**
 * Real-time grant dashboard metrics (CRAD Staff / Admin)
 */
(function () {
    'use strict';

    var POLL_MS = 5000;
    var root = document.querySelector('[data-grant-dashboard-metrics="1"]');
    if (!root) return;

    var apiBase = (function () {
        var path = window.location.pathname || '';
        var idx = path.indexOf('/modules/');
        if (idx === -1) {
            var dashIdx = path.indexOf('/dashboard/');
            if (dashIdx === -1) return '/modules/crad/api/grant-management.php';
            return path.slice(0, dashIdx) + '/modules/crad/api/grant-management.php';
        }
        return path.slice(0, idx) + '/modules/crad/api/grant-management.php';
    })();

    var lastFingerprint = '';
    var updatedEl = root.querySelector('[data-gdm-updated]');

    function formatCurrency(amount) {
        var n = Number(amount) || 0;
        return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function formatValue(key, value) {
        if (key === 'total_funding') return formatCurrency(value);
        return String(parseInt(value, 10) || 0);
    }

    function applyMetrics(metrics, flash) {
        if (!metrics) return;

        root.querySelectorAll('[data-gdm-value]').forEach(function (el) {
            var key = el.getAttribute('data-gdm-value');
            if (!key || metrics[key] === undefined) return;
            var next = formatValue(key, metrics[key]);
            if (el.textContent !== next) {
                el.textContent = next;
                if (flash) {
                    var card = el.closest('.gdm-card');
                    if (card) {
                        card.classList.add('updated');
                        setTimeout(function () { card.classList.remove('updated'); }, 1200);
                    }
                }
            }
        });

        if (updatedEl && metrics.updated_at) {
            try {
                var dt = new Date(String(metrics.updated_at).replace(' ', 'T'));
                updatedEl.textContent = 'Updated ' + dt.toLocaleTimeString('en-US', {
                    hour: 'numeric', minute: '2-digit', second: '2-digit'
                });
            } catch (e) {
                updatedEl.textContent = 'Updated just now';
            }
        }
    }

    function poll(isInitial) {
        if (!isInitial && document.hidden) return;

        fetch(apiBase + '?action=get_dashboard_metrics', {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' }
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success || !data.metrics) return;
                var fp = data.fingerprint || '';
                var changed = !isInitial && lastFingerprint !== '' && fp !== lastFingerprint;
                applyMetrics(data.metrics, changed);
                lastFingerprint = fp;
            })
            .catch(function () {});
    }

    poll(true);
    setInterval(function () { poll(false); }, POLL_MS);
})();
