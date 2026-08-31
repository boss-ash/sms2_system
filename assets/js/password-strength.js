/**
 * Live password strength checklist (pairs with smsPasswordStrengthMarkup).
 */
(function () {
    'use strict';

    function evaluate(password, minLen) {
        return {
            length: password.length >= minLen,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
    }

    function strengthMeta(checks) {
        var passed = Object.keys(checks).filter(function (key) { return checks[key]; }).length;
        if (passed <= 1) return { level: 'weak', label: 'Weak', pct: 20, className: 'is-weak' };
        if (passed <= 3) return { level: 'fair', label: 'Fair', pct: 45, className: 'is-fair' };
        if (passed === 4) return { level: 'good', label: 'Good', pct: 72, className: 'is-good' };
        return { level: 'strong', label: 'Strong', pct: 100, className: 'is-strong' };
    }

    function paint(box, checks, password) {
        box.querySelectorAll('.pw-rule').forEach(function (li) {
            var rule = li.getAttribute('data-rule');
            var ok = !!(checks && checks[rule]);
            li.classList.toggle('is-ok', ok);
            li.classList.toggle('is-bad', !ok && password.length > 0);
            var icon = li.querySelector('.pw-rule-icon');
            if (icon) {
                icon.className = 'ti pw-rule-icon ' + (ok ? 'ti-circle-check' : (password.length > 0 ? 'ti-circle-x' : 'ti-circle'));
            }
        });

        var meta = strengthMeta(checks);
        var scoreEl = box.querySelector('[data-pw-score]');
        if (scoreEl) {
            scoreEl.textContent = password.length ? meta.label : '—';
            scoreEl.className = 'pw-strength-score' + (password.length ? ' ' + meta.className : '');
        }

        var fill = box.querySelector('.pw-strength-bar-fill');
        if (fill) {
            fill.style.width = password.length ? meta.pct + '%' : '0';
            fill.className = 'pw-strength-bar-fill' + (password.length ? ' ' + meta.className : '');
        }
    }

    function bind(box) {
        var inputId = box.getAttribute('data-pw-input');
        var minLen = parseInt(box.getAttribute('data-pw-min') || '8', 10) || 8;
        var input = document.getElementById(inputId);
        if (!input) return;

        var run = function () {
            var value = input.value || '';
            paint(box, evaluate(value, minLen), value);
        };
        input.addEventListener('input', run);
        input.addEventListener('keyup', run);
        run();
    }

    function initAll(root) {
        (root || document).querySelectorAll('.pw-strength').forEach(bind);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
    });

    window.smsInitPasswordStrength = initAll;
})();
