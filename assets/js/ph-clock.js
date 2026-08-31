/**
 * SMS 2 - Live Philippine Standard Time (Asia/Manila) clock
 * Fixed-width format (HH:MM:SS AM/PM) so the navbar slot stays steady.
 */
(function () {
    'use strict';

    var el = document.getElementById('navbarPhClock');
    if (!el) return;

    var serverMs = parseInt(el.getAttribute('data-server-ms') || '', 10);
    if (!Number.isFinite(serverMs)) {
        serverMs = Date.now();
    }
    var clientOrigin = Date.now();

    var partsFmt = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Asia/Manila',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
    });

    function nowPh() {
        return new Date(serverMs + (Date.now() - clientOrigin));
    }

    function formatSteady(date) {
        var parts = partsFmt.formatToParts(date);
        var map = {};
        parts.forEach(function (p) {
            if (p.type !== 'literal') map[p.type] = p.value;
        });
        var hour = String(map.hour || '00').padStart(2, '0');
        var minute = String(map.minute || '00').padStart(2, '0');
        var second = String(map.second || '00').padStart(2, '0');
        var dayPeriod = String(map.dayPeriod || 'AM').toUpperCase().replace(/\./g, '');
        // Always: "10:37:37 PM" (11 chars + spaces handled by fixed CSS width)
        return hour + ':' + minute + ':' + second + ' ' + dayPeriod;
    }

    function tick() {
        var current = nowPh();
        el.textContent = formatSteady(current);
        try {
            el.setAttribute(
                'datetime',
                current.toLocaleString('sv-SE', { timeZone: 'Asia/Manila' }).replace(' ', 'T') + '+08:00'
            );
        } catch (e) { /* ignore */ }
    }

    tick();
    setInterval(tick, 1000);
})();
