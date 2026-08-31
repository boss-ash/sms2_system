/**
 * SMS 2 – Glass dashboard charts
 * Solid colors only. Recreates on theme change (never chart.update via theme proxies).
 */
(function () {
    'use strict';

    var board = document.getElementById('glassBoard');
    if (!board || typeof Chart === 'undefined') return;

    function themeVar(name, fallback) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
    }

    function destroyAll() {
        ['glassDonut', 'glassTrend', 'glassCash', 'glassNet'].forEach(function (id) {
            var el = document.getElementById(id);
            if (!el || typeof Chart.getChart !== 'function') return;
            try {
                var existing = Chart.getChart(el);
                if (existing) existing.destroy();
            } catch (e) { /* ignore */ }
        });
    }

    function mountChart(el, config) {
        if (!el) return null;
        try {
            var existing = typeof Chart.getChart === 'function' ? Chart.getChart(el) : null;
            if (existing) existing.destroy();
        } catch (e) { /* ignore */ }
        try {
            return new Chart(el, config);
        } catch (err) {
            console.error('SMS2 chart mount failed:', err);
            return null;
        }
    }

    function scaleSeries(values, factor) {
        return values.map(function (value) {
            return Math.max(1, Math.round(value * factor));
        });
    }

    function buildCharts() {
        var text = themeVar('--sms-chart-text', '#94a3b8');
        var grid = themeVar('--sms-chart-grid', 'rgba(148,163,184,0.12)');
        var doughnutBorder = themeVar('--sms-chart-doughnut-border', '#121c34');
        var role = board.getAttribute('data-role') || 'admin';
        var periodFactor = parseFloat(board.getAttribute('data-period-factor') || '1');
        if (!isFinite(periodFactor) || periodFactor <= 0) {
            periodFactor = 1;
        }

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = text;

        var months = ['May 1', 'May 5', 'May 9', 'May 13', 'May 17', 'May 21', 'May 25', 'May 29'];
        var blue = '#3b82f6';
        var green = '#22c55e';
        var orange = '#fb923c';
        var violet = '#8b5cf6';
        var cyan = '#06b6d4';
        var amber = '#f59e0b';

        var donutData = [612, 543, 487, 415, 836];
        var donutColors = [blue, violet, green, amber, cyan];

        if (role === 'superadmin' || role === 'admin') {
            donutData = [36, 22, 18, 14, 10];
        } else if (role === 'admission') {
            donutData = [30, 21, 18, 16, 15];
        } else if (role === 'registrar') {
            donutData = [42, 18, 16, 14, 10];
        } else if (role === 'finance') {
            donutData = [68, 12, 8, 7, 5];
        } else if (role === 'hr') {
            donutData = [18, 22, 20, 14, 28];
        } else if (role === 'it_office') {
            donutData = [82, 74, 68, 71, 65];
        } else if (role === 'osa') {
            donutData = [34, 24, 18, 14, 10];
        } else if (role === 'qa') {
            donutData = [32, 24, 18, 14, 12];
        } else if (role === 'crad_officer' || role === 'research_coordinator' || role === 'research_grant' || role === 'adviser' || role === 'panel' || role === 'research_director') {
            donutData = [32, 22, 18, 14, 14];
        }

        var trendData = [42, 55, 48, 70, 62, 78, 74, 90];
        if (role === 'superadmin' || role === 'admin') trendData = [52, 64, 58, 73, 80, 92, 88, 100];
        if (role === 'admission') trendData = [18, 24, 31, 28, 36, 41, 48, 54];
        if (role === 'registrar') trendData = [88, 96, 102, 118, 126, 134, 140, 152];
        if (role === 'hr') trendData = [3, 5, 8, 6, 4, 7, 5, 9];
        if (role === 'it_office') trendData = [820, 980, 1100, 1240, 1180, 1320, 1400, 1510];
        if (role === 'osa') trendData = [8, 10, 13, 12, 16, 18, 21, 24];
        if (role === 'qa') trendData = [70, 82, 91, 96, 105, 114, 130, 142];
        if (role === 'crad_officer') trendData = [2, 4, 3, 6, 5, 7, 8, 9];
        if (role === 'research_coordinator') trendData = [1, 2, 4, 4, 7, 8, 10, 12];
        if (role === 'research_grant') trendData = [2, 3, 5, 4, 7, 8, 12, 16];
        if (role === 'adviser' || role === 'panel') trendData = [1, 2, 3, 5, 4, 7, 8, 11];
        if (role === 'research_director') trendData = [3, 4, 6, 8, 9, 13, 17, 21];

        donutData = scaleSeries(donutData, periodFactor);
        trendData = scaleSeries(trendData, periodFactor);

        var cashIn = [48, 52, 50, 64, 70, 78, 82, 90];
        var cashOut = [30, 34, 38, 42, 40, 48, 52, 55];
        var netParts = [64, 36];
        if (role === 'superadmin' || role === 'admin') {
            cashIn = [6, 7, 8, 9, 10, 11, 12, 12];
            cashOut = [1, 1, 2, 2, 2, 2, 2, 2];
            netParts = [86, 14];
        } else if (role === 'admission') {
            cashIn = [12, 18, 24, 28, 34, 41, 48, 54];
            cashOut = [24, 22, 20, 28, 30, 31, 32, 31];
            netParts = [64, 36];
        } else if (role === 'registrar') {
            cashIn = [32, 38, 44, 52, 58, 64, 68, 72];
            cashOut = [18, 21, 24, 26, 28, 30, 29, 28];
            netParts = [72, 28];
        } else if (role === 'osa') {
            cashIn = [4, 6, 8, 11, 13, 15, 17, 18];
            cashOut = [2, 2, 3, 4, 5, 5, 6, 6];
            netParts = [75, 25];
        } else if (role === 'qa') {
            cashIn = [70, 78, 84, 92, 98, 105, 112, 118];
            cashOut = [12, 11, 10, 10, 9, 8, 7, 7];
            netParts = [94, 6];
        } else if (role === 'crad_officer' || role === 'research_coordinator' || role === 'research_grant' || role === 'adviser' || role === 'panel' || role === 'research_director') {
            cashIn = [1, 2, 2, 3, 4, 5, 6, 7];
            cashOut = [1, 1, 2, 2, 3, 3, 4, 4];
            netParts = [69, 31];
        }

        cashIn = scaleSeries(cashIn, periodFactor);
        cashOut = scaleSeries(cashOut, periodFactor);

        destroyAll();

        mountChart(document.getElementById('glassDonut'), {
            type: 'doughnut',
            data: {
                labels: ['A', 'B', 'C', 'D', 'E'],
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderColor: doughnutBorder,
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '72%',
                animation: false,
                plugins: { legend: { display: false }, tooltip: { enabled: true } }
            }
        });

        mountChart(document.getElementById('glassTrend'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Trend',
                    data: trendData,
                    borderColor: blue,
                    backgroundColor: 'rgba(59,130,246,0.18)',
                    fill: true,
                    borderWidth: 2.5,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: blue,
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: text, maxRotation: 0, autoSkip: true, maxTicksLimit: 6 }
                    },
                    y: {
                        grid: { color: grid },
                        border: { display: false },
                        ticks: { color: text, maxTicksLimit: 5 }
                    }
                }
            }
        });

        mountChart(document.getElementById('glassCash'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: (role === 'crad_officer' || role === 'research_coordinator' || role === 'research_grant' || role === 'adviser' || role === 'panel' || role === 'research_director') ? 'Approved' : 'Inflow',
                        data: cashIn,
                        borderColor: green,
                        backgroundColor: 'transparent',
                        borderWidth: 2.25,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 4
                    },
                    {
                        label: (role === 'crad_officer' || role === 'research_coordinator' || role === 'research_grant' || role === 'adviser' || role === 'panel' || role === 'research_director') ? 'Pending' : 'Outflow',
                        data: cashOut,
                        borderColor: orange,
                        backgroundColor: 'transparent',
                        borderWidth: 2.25,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { display: false } },
                    y: { grid: { color: grid }, border: { display: false }, ticks: { display: false } }
                }
            }
        });

        mountChart(document.getElementById('glassNet'), {
            type: 'doughnut',
            data: {
                labels: (role === 'crad_officer' || role === 'research_coordinator' || role === 'research_grant' || role === 'adviser' || role === 'panel' || role === 'research_director') ? ['Complete', 'Other'] : ['Complete', 'Remaining'],
                datasets: [{
                    data: netParts,
                    backgroundColor: [green, 'rgba(148,163,184,0.18)'],
                    borderWidth: 0,
                    hoverOffset: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '78%',
                animation: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });
    }

    buildCharts();

    window.addEventListener('sms2:themechange', function () {
        // Rebuild with fresh theme colors — safer than chart.update()
        window.setTimeout(buildCharts, 0);
    });
})();
