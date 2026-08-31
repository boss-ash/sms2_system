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

    function paint(box, checks) {
        box.querySelectorAll('.pw-rule').forEach(function (li) {
            var rule = li.getAttribute('data-rule');
            var ok = !!(checks && checks[rule]);
            li.classList.toggle('is-ok', ok);
            li.classList.toggle('is-bad', !ok);
            var icon = li.querySelector('.pw-rule-icon');
            if (icon) {
                icon.className = 'fas pw-rule-icon ' + (ok ? 'fa-check-circle' : 'fa-times-circle');
            }
        });
    }

    function bind(box) {
        var inputId = box.getAttribute('data-pw-input');
        var minLen = parseInt(box.getAttribute('data-pw-min') || '8', 10) || 8;
        var input = document.getElementById(inputId);
        if (!input) return;

        var run = function () {
            paint(box, evaluate(input.value || '', minLen));
        };
        input.addEventListener('input', run);
        input.addEventListener('keyup', run);
        run();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.pw-strength').forEach(bind);
    });
})();
