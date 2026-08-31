/**
 * SMS 2 – One-click CAPTCHA (Cloudflare / Discord style)
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        var widget = document.getElementById('smsCaptchaWidget');
        if (!widget) return;

        var box = document.getElementById('smsCaptchaBox');
        var spinner = widget.querySelector('.sms-cf-spinner');
        var check = widget.querySelector('.sms-cf-check');
        var label = widget.querySelector('.sms-cf-label');
        var brand = widget.querySelector('.sms-cf-brand');
        var okField = document.getElementById('smsCaptchaOk');
        var tokenField = document.getElementById('smsCaptchaToken');
        var api = widget.getAttribute('data-captcha-api') || '';
        var busy = false;
        var done = false;

        function setDone() {
            done = true;
            widget.classList.add('is-verified');
            widget.classList.remove('is-loading');
            widget.setAttribute('aria-pressed', 'true');
            widget.setAttribute('aria-label', 'Verified successfully');
            if (spinner) spinner.hidden = true;
            if (check) check.hidden = false;
            if (label) label.textContent = 'Success';
            if (brand) {
                brand.innerHTML = '<i class="fas fa-check-circle" aria-hidden="true"></i>Verified';
            }
            if (okField) okField.value = '1';
            widget.dispatchEvent(new Event('sms-captcha-ok', { bubbles: true }));
        }

        function setError() {
            busy = false;
            widget.classList.remove('is-loading');
            widget.classList.add('is-error');
            if (spinner) spinner.hidden = true;
            if (check) check.hidden = true;
            setTimeout(function () { widget.classList.remove('is-error'); }, 1200);
        }

        async function verifyClick() {
            if (busy || done) return;
            busy = true;
            widget.classList.add('is-loading');
            widget.classList.remove('is-error');
            if (spinner) spinner.hidden = false;
            if (check) check.hidden = true;

            var token = tokenField ? tokenField.value : '';
            try {
                // Brief animation like Turnstile / Discord
                await new Promise(function (r) { setTimeout(r, 650); });
                var res = await fetch(api, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ token: token })
                });
                var data = await res.json().catch(function () { return {}; });
                if (!res.ok || !data.ok) {
                    setError();
                    return;
                }
                setDone();
            } catch (e) {
                setError();
            }
            busy = false;
        }

        widget.addEventListener('click', function (e) {
            e.preventDefault();
            verifyClick();
        });
        widget.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                verifyClick();
            }
        });
    });
})();
