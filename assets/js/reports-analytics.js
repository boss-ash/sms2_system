/**
 * SMS 2 - Reports & Analytics charts + summary table
 */
(function () {
    'use strict';

    var root = document.getElementById('raDash');
    if (!root || typeof Chart === 'undefined') return;

    var payload;
    try {
        payload = JSON.parse(root.getAttribute('data-dashboard') || '{}');
    } catch (e) {
        return;
    }

    var textColor = '#64748b';
    var gridColor = 'rgba(148,163,184,0.25)';

    function syncThemeColors() {
        textColor = getComputedStyle(document.documentElement).getPropertyValue('--sms-chart-text').trim()
            || getComputedStyle(document.documentElement).getPropertyValue('--sms-text').trim()
            || '#64748b';
        gridColor = getComputedStyle(document.documentElement).getPropertyValue('--sms-chart-grid').trim()
            || 'rgba(148,163,184,0.25)';
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = textColor;
    }

    syncThemeColors();

    function mountChart(canvas, config) {
        if (!canvas) return null;
        try {
            var existing = typeof Chart.getChart === 'function' ? Chart.getChart(canvas) : null;
            if (existing) existing.destroy();
        } catch (e) { /* ignore */ }
        try {
            return new Chart(canvas, config);
        } catch (err) {
            console.error('SMS2 reports chart failed:', err);
            return null;
        }
    }

    function makeDonut() {
        var cfg = payload.donut || {};
        var canvas = document.getElementById('raDonutChart');
        if (!canvas) return;
        mountChart(canvas, {
            type: 'doughnut',
            data: {
                labels: cfg.labels || [],
                datasets: [{
                    data: cfg.values || [],
                    backgroundColor: cfg.colors || ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { display: false } },
            },
        });
    }

    function makeBar() {
        var cfg = payload.bar || {};
        var canvas = document.getElementById('raBarChart');
        if (!canvas) return;
        mountChart(canvas, {
            type: 'bar',
            data: {
                labels: cfg.labels || [],
                datasets: [{
                    data: cfg.values || [],
                    backgroundColor: cfg.color || '#22c55e',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
                },
            },
        });
    }

    function makeGrouped() {
        var cfg = payload.grouped || {};
        var canvas = document.getElementById('raGroupedChart');
        if (!canvas) return;
        var series = (cfg.series || []).map(function (s) {
            return {
                label: s.label,
                data: s.values || [],
                backgroundColor: s.color,
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 28,
            };
        });
        mountChart(canvas, {
            type: 'bar',
            data: { labels: cfg.labels || [], datasets: series },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, usePointStyle: true, pointStyle: 'circle', color: textColor },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
                },
            },
        });
    }

    function makeHorizontal() {
        var cfg = payload.horizontal || {};
        var canvas = document.getElementById('raHorizontalChart');
        if (!canvas) return;
        mountChart(canvas, {
            type: 'bar',
            data: {
                labels: cfg.labels || [],
                datasets: [{
                    data: cfg.values || [],
                    backgroundColor: cfg.color || '#3b82f6',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
                    y: { grid: { display: false }, ticks: { color: textColor } },
                },
            },
        });
    }

    function initSummaryTable() {
        var rows = Array.isArray(payload.summary) ? payload.summary.slice() : [];
        var body = document.getElementById('raSummaryBody');
        var meta = document.getElementById('raSummaryMeta');
        var search = document.getElementById('raSummarySearch');
        var rowsSelect = document.getElementById('raSummaryRows');
        var pager = document.getElementById('raSummaryPager');
        if (!body || !pager) return;

        var state = { page: 1, perPage: 5, query: '' };

        function filtered() {
            var q = state.query.trim().toLowerCase();
            if (!q) return rows;
            return rows.filter(function (r) {
                return String(r.metric || '').toLowerCase().indexOf(q) !== -1
                    || String(r.value || '').toLowerCase().indexOf(q) !== -1;
            });
        }

        function render() {
            var list = filtered();
            var total = list.length;
            var pages = Math.max(1, Math.ceil(total / state.perPage));
            if (state.page > pages) state.page = pages;
            var start = (state.page - 1) * state.perPage;
            var slice = list.slice(start, start + state.perPage);

            body.innerHTML = '';
            if (!slice.length) {
                body.innerHTML = '<tr><td colspan="2">No matching rows</td></tr>';
            } else {
                slice.forEach(function (r) {
                    var tr = document.createElement('tr');
                    tr.innerHTML = '<td>' + escapeHtml(r.metric) + '</td><td>' + escapeHtml(String(r.value)) + '</td>';
                    body.appendChild(tr);
                });
            }

            if (meta) {
                var from = total ? start + 1 : 0;
                var to = Math.min(start + state.perPage, total);
                meta.textContent = 'Showing ' + from + '-' + to + ' of ' + total + ' rows';
            }

            pager.innerHTML = '';
            var prev = document.createElement('button');
            prev.type = 'button';
            prev.innerHTML = '&lsaquo;';
            prev.disabled = state.page <= 1;
            prev.addEventListener('click', function () {
                state.page -= 1;
                render();
            });
            pager.appendChild(prev);

            for (var i = 1; i <= pages; i += 1) {
                (function (pageNum) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = String(pageNum);
                    if (pageNum === state.page) btn.className = 'active';
                    btn.addEventListener('click', function () {
                        state.page = pageNum;
                        render();
                    });
                    pager.appendChild(btn);
                })(i);
            }

            var next = document.createElement('button');
            next.type = 'button';
            next.innerHTML = '&rsaquo;';
            next.disabled = state.page >= pages;
            next.addEventListener('click', function () {
                state.page += 1;
                render();
            });
            pager.appendChild(next);
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        if (search) {
            search.addEventListener('input', function () {
                state.query = search.value;
                state.page = 1;
                render();
            });
        }
        if (rowsSelect) {
            rowsSelect.addEventListener('change', function () {
                state.perPage = parseInt(rowsSelect.value, 10) || 5;
                state.page = 1;
                render();
            });
        }

        render();
    }

    function buildAll() {
        syncThemeColors();
        makeDonut();
        makeBar();
        makeGrouped();
        makeHorizontal();
    }

    buildAll();
    initSummaryTable();

    window.addEventListener('sms2:themechange', function () {
        window.setTimeout(buildAll, 0);
    });
})();
