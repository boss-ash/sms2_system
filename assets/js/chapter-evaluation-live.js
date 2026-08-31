(function () {
    var root = document.querySelector('[data-chapter-live]');
    if (!root) return;

    function escapeHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'})[ch];
        });
    }

    function bindOnceForms(scope) {
        (scope || document).querySelectorAll('[data-once-form]').forEach(function (form) {
            if (form.dataset.onceBound === '1') return;
            form.dataset.onceBound = '1';
            form.addEventListener('submit', function () {
                var btn = form.querySelector('[data-submit-once]');
                if (btn) {
                    btn.disabled = true;
                    btn.dataset.originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                }
            });
        });
    }

    bindOnceForms(document);

    function studentRows(rows) {
        if (!rows.length) {
            return '<tr><td colspan="7" class="text-center text-muted py-4">No chapter submissions yet.</td></tr>';
        }
        return rows.map(function (r) {
            return '<tr>' +
                '<td>' + escapeHtml(r.chapter) + '</td>' +
                '<td>Version ' + escapeHtml(r.version) + '</td>' +
                '<td>' + escapeHtml(r.submitted_at) + '</td>' +
                '<td><span class="badge text-bg-' + escapeHtml(r.status_class) + '">' + escapeHtml(r.status) + '</span></td>' +
                '<td>' + escapeHtml(r.evaluator || '-') + '</td>' +
                '<td>' + escapeHtml(r.updated_at) + '</td>' +
                '<td><a class="btn btn-sm btn-outline-primary" target="_blank" href="' + escapeHtml((root.getAttribute('data-document-base') || '') + encodeURIComponent(r.id)) + '"><i class="fas fa-eye me-1"></i>View</a></td>' +
                '</tr>';
        }).join('');
    }

    function evaluatorRows(rows) {
        if (!rows.length) {
            return '<tr><td colspan="8" class="text-center text-muted py-4"><strong>No Submissions for Evaluation</strong><br>There are currently no valid Chapter 1-3 submissions awaiting evaluation.</td></tr>';
        }
        return rows.map(function (r) {
            return '<tr>' +
                '<td><strong>' + escapeHtml(r.group) + '</strong><div class="small text-muted">' + escapeHtml(r.group_number) + '</div></td>' +
                '<td>' + escapeHtml(r.title) + '</td>' +
                '<td>' + escapeHtml(r.chapter) + '</td>' +
                '<td>Version ' + escapeHtml(r.version) + '</td>' +
                '<td>' + escapeHtml(r.submitted_by) + '</td>' +
                '<td>' + escapeHtml(r.submitted_at) + '</td>' +
                '<td><span class="badge text-bg-' + escapeHtml(r.status_class) + '">' + escapeHtml(r.status) + '</span></td>' +
                '<td><a class="btn btn-sm btn-sms-primary" href="' + escapeHtml(r.scoring_url) + '"><i class="fas fa-pen me-1"></i>Review</a></td>' +
                '</tr>';
        }).join('');
    }

    var mode = root.getAttribute('data-chapter-live');
    var endpoint = root.getAttribute('data-live-endpoint') || '';
    if (!endpoint) return;
    function refreshPageFragment() {
        return fetch(window.location.href, {cache: 'no-store', credentials: 'same-origin'})
            .then(function (res) { return res.ok ? res.text() : ''; })
            .then(function (html) {
                if (!html) return false;
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.querySelector('[data-chapter-live="student"]');
                if (!fresh) return false;
                root.innerHTML = fresh.innerHTML;
                ['data-registry-available', 'data-latest-update', 'data-eligibility-update', 'data-document-base'].forEach(function (attr) {
                    if (fresh.hasAttribute(attr)) {
                        root.setAttribute(attr, fresh.getAttribute(attr) || '');
                    }
                });
                bindOnceForms(root);
                return true;
            });
    }

    function refresh() {
        fetch(endpoint, {cache: 'no-store', credentials: 'same-origin'})
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data || !data.ok) return;
                if (mode === 'student' && Object.prototype.hasOwnProperty.call(data, 'registry_available')) {
                    var knownAvailability = root.getAttribute('data-registry-available') || '0';
                    var liveAvailability = data.registry_available ? '1' : '0';
                    if (knownAvailability !== liveAvailability) {
                        refreshPageFragment();
                        return;
                    }
                    root.setAttribute('data-registry-available', liveAvailability);
                }
                var stamp = document.querySelector('[data-live-stamp]');
                if (stamp) stamp.textContent = 'Updated just now';
                if (mode === 'student') {
                    var studentBody = document.querySelector('[data-student-submission-rows]');
                    if (studentBody) studentBody.innerHTML = studentRows(data.submissions || []);
                    var latest = data.latest_update || '';
                    var known = root.getAttribute('data-latest-update') || '';
                    var eligibilityLatest = data.eligibility_update || '';
                    var knownEligibility = root.getAttribute('data-eligibility-update') || '';
                    if (eligibilityLatest !== knownEligibility) {
                        refreshPageFragment();
                        return;
                    }
                    if (!studentBody && known && latest && known !== latest) {
                        window.location.reload();
                    }
                    if (latest) root.setAttribute('data-latest-update', latest);
                    root.setAttribute('data-eligibility-update', eligibilityLatest);
                }
                if (mode === 'evaluator') {
                    var evaluatorBody = document.querySelector('[data-evaluator-queue-rows]');
                    if (evaluatorBody) evaluatorBody.innerHTML = evaluatorRows(data.queue || []);
                    var count = document.querySelector('[data-evaluator-pending-count]');
                    if (count) count.textContent = data.pending_count || 0;
                }
            })
            .catch(function () {});
    }
    window.setInterval(refresh, 5000);
})();
