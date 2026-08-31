(function () {
    var root = document.querySelector('[data-panel-live]');
    if (!root) return;

    function updateOverall(form) {
        var inputs = form.querySelectorAll('input[type="number"]');
        var total = 0;
        var count = 0;
        inputs.forEach(function (input) {
            var value = parseFloat(input.value);
            if (!Number.isNaN(value)) {
                total += value;
                count += 1;
            }
        });
        var output = form.querySelector('[data-panel-overall]');
        if (output) output.value = count ? (total / count).toFixed(2) : '0.00';
    }

    document.addEventListener('input', function (event) {
        var form = event.target.closest('[data-panel-evaluation-form]');
        if (form) updateOverall(form);
    });

    document.addEventListener('click', function (event) {
        var open = event.target.closest('[data-panel-open-confirm]');
        if (open) {
            var form = open.closest('form');
            if (!form || !form.reportValidity()) return;
            var modalEl = document.getElementById('panelSubmitConfirmModal');
            if (modalEl && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                form.submit();
            }
        }

        var confirm = event.target.closest('[data-panel-confirm-submit]');
        if (confirm) {
            var form = document.querySelector('[data-panel-evaluation-form]');
            if (!form || !form.reportValidity()) return;
            confirm.disabled = true;
            form.submit();
        }
    });

    function poll() {
        var endpoint = root.getAttribute('data-endpoint');
        var mode = root.getAttribute('data-panel-live');
        if (!endpoint || mode === 'scoring') return;
        var current = new URL(window.location.href);
        var url = endpoint + '?mode=' + encodeURIComponent(mode === 'history' ? 'history' : (mode === 'details' ? 'details' : 'assigned'));
        if (mode === 'details' && current.searchParams.get('id')) {
            url += '&id=' + encodeURIComponent(current.searchParams.get('id'));
        }
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload || !payload.ok) return;
                var content = root.querySelector('[data-panel-content]');
                var count = root.querySelector('[data-panel-count]');
                if (content && typeof payload.html === 'string') content.innerHTML = payload.html;
                if (count) count.textContent = payload.count + ' Record' + (payload.count === 1 ? '' : 's');
            })
            .catch(function () {});
    }

    window.setInterval(poll, 10000);
})();
