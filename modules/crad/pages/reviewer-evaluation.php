<?php
/**
 * SMS 2 - Review Committee · Reviewer Evaluation
 * Score pending grant proposals using the institutional rubric (100 pts).
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-evaluation-helpers.php';

requireAuth();
grantRequireEvaluateAccess();

$pageTitle             = 'Reviewer Evaluation';
$activeModule          = grantEvaluationActiveModuleKey();
$activePage            = 'reviewer-evaluation';
$pageBannerIcon        = 'fa-clipboard-check';
$pageBannerDescription = 'Score research grant proposals submitted for committee review.';
$hideModulePageBanner  = true;

$breadcrumbs = [
    ['label' => 'Research Grant', 'url' => BASE_URL . '/modules/crad/pages/reviewer-evaluation.php'],
    ['label' => 'Reviewer Evaluation', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad    = cradDb();
$queue   = [];
$dbError = '';
$selectedId = (int) ($_GET['id'] ?? 0);
$selected   = null;
$existingEval = null;

$rubric = grantRubricCriteria();

if ($crad) {
    try {
        $queue = grantEvaluationQueue($crad);
        if ($selectedId > 0) {
            $selected = grantGetApplicationForEvaluation($crad, $selectedId);
            if ($selected && !in_array((string) ($selected['status'] ?? ''), ['Submitted', 'Under Review'], true)) {
                $selected = null;
            }
            if ($selected) {
                $existingEval = grantGetEvaluationByApplication($crad, $selectedId);
            }
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('reviewer-evaluation: ' . $e->getMessage());
    }
} else {
    $dbError = 'CRAD database connection unavailable.';
}

$pendingCount = count(array_filter($queue, static fn(array $r): bool => empty($r['my_evaluation_id'])));
$scoredCount  = count(array_filter($queue, static fn(array $r): bool => !empty($r['my_evaluation_id'])));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/grant-reviewer-evaluation.css?v=3" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="mpl gre" data-grant-eval-live="1">

<div class="gre-header">
    <div>
        <h1><?= smsIcon('clipboard-check', ['class' => 'me-2', 'style' => 'color:var(--sms-primary);']) ?>Reviewer Evaluation</h1>
        <p>Proposals with <strong>Pending Evaluation</strong> in Proposals &amp; Applications appear here for rubric scoring.</p>
    </div>
    <div class="gre-stat-row">
        <span class="gre-stat pending"><strong data-eval-pending-count><?= $pendingCount ?></strong> awaiting score</span>
        <span class="gre-stat scored"><strong data-eval-scored-count><?= $scoredCount ?></strong> scored by you</span>
        <span class="gre-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
    </div>
</div>

<?php if ($dbError === ''): ?>

<?php if (!$selected): ?>
<section class="mpl-panel gre-queue-section">
    <div class="mpl-panel-head">
        <div>
            <h2>Assigned Review Queue</h2>
            <p>Click a proposal title to view details, then score using the rubric (100 points).</p>
        </div>
    </div>
    <?php if (empty($queue)): ?>
        <div class="gre-queue-empty">No proposals are waiting for committee evaluation.</div>
    <?php else: ?>
        <div class="gre-queue-list" id="greQueueList">
            <?php foreach ($queue as $row):
                $isScored = !empty($row['my_evaluation_id']);
                $statusLabel = ($row['status'] ?? '') === 'Submitted' ? 'Pending Evaluation' : 'Under Review';
                $title = (string) ($row['research_title'] ?? 'Untitled Proposal');
            ?>
            <details class="gre-queue-card">
                <summary class="gre-queue-summary">
                    <span class="gre-queue-title-text"><?= htmlspecialchars($title) ?></span>
                    <?= smsIcon('chevron-down', ['class' => 'gre-queue-chevron', 'aria-hidden' => 'true']) ?>
                </summary>
                <div class="gre-queue-body">
                    <div class="gre-queue-meta-grid">
                        <div>
                            <span class="gre-queue-label">Reference</span>
                            <strong><?= htmlspecialchars((string) ($row['proposal_reference'] ?? ('#' . (int) $row['id']))) ?></strong>
                            <small>Version <?= max(1, (int) ($row['current_version'] ?? 1)) ?></small>
                        </div>
                        <div>
                            <span class="gre-queue-label">Status</span>
                            <?php if ($isScored): ?>
                                <span class="mpl-status completed">Scored (<?= number_format((float) $row['my_total_score'], 1) ?>/100)</span>
                            <?php else: ?>
                                <span class="mpl-status pending"><?= htmlspecialchars($statusLabel) ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="gre-queue-label">Grant Program</span>
                            <span><?= htmlspecialchars((string) $row['funding_title']) ?></span>
                        </div>
                        <div>
                            <span class="gre-queue-label">Lead Proponent</span>
                            <span><?= htmlspecialchars((string) $row['applicant_name']) ?></span>
                        </div>
                        <div>
                            <span class="gre-queue-label">College / Dept</span>
                            <span><?= htmlspecialchars((string) ($row['college_dept'] ?? '—')) ?></span>
                        </div>
                        <div>
                            <span class="gre-queue-label">Requested Budget</span>
                            <span><?= $row['requested_budget'] !== null ? '₱' . number_format((float) $row['requested_budget'], 0) : '—' ?></span>
                        </div>
                        <div>
                            <span class="gre-queue-label">Submitted</span>
                            <span><?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $row['submitted_at']))) ?></span>
                        </div>
                    </div>
                    <div class="gre-queue-actions">
                        <a class="mpl-btn mpl-btn-primary mpl-btn-sm"
                           href="?id=<?= (int) $row['id'] ?>">
                            <?= $isScored ? smsIcon('eye') . ' View Evaluation' : smsIcon('star-half-alt') . ' Score Proposal' ?>
                        </a>
                    </div>
                </div>
            </details>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php else: ?>
<div class="gre-layout">
    <aside class="gre-info-panel">
        <a class="mpl-btn mpl-btn-ghost mpl-btn-sm mb-2" href="<?= BASE_URL ?>/modules/crad/pages/reviewer-evaluation.php">
            <?= smsIcon('arrow-left') ?> Back to Queue
        </a>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Research Title</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars((string) ($selected['research_title'] ?? 'Research Proposal')) ?></div>
        </details>

        <?php if (!empty($selected['proposal_reference'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Reference</summary>
            <div class="gre-accordion-body">
                <?= htmlspecialchars((string) $selected['proposal_reference']) ?>
                <div class="gre-accordion-sub">Version <?= max(1, (int) ($selected['current_version'] ?? 1)) ?> — <?= htmlspecialchars(grantVersionLabel((int) ($selected['current_version'] ?? 1))) ?></div>
            </div>
        </details>
        <?php endif; ?>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Grant Program</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars((string) $selected['funding_title']) ?></div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Lead Proponent</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars((string) $selected['applicant_name']) ?></div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">College / Dept</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars((string) ($selected['college_dept'] ?? '—')) ?></div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Requested Budget</summary>
            <div class="gre-accordion-body">
                ₱<?= number_format((float) ($selected['requested_budget'] ?? 0), 0) ?>
                <div class="gre-accordion-sub">of ₱<?= number_format((float) ($selected['max_funding_cap'] ?? 0), 0) ?> cap</div>
            </div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Eligibility</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars((string) ($selected['eligibility'] ?? '')) ?></div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Submitted</summary>
            <div class="gre-accordion-body"><?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $selected['submitted_at']))) ?></div>
        </details>

        <?php if (!empty($selected['abstract'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Executive Abstract</summary>
            <div class="gre-accordion-body"><?= nl2br(htmlspecialchars((string) $selected['abstract'])) ?></div>
        </details>
        <?php endif; ?>

        <?php if (!empty($selected['objectives'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Objectives</summary>
            <div class="gre-accordion-body"><?= nl2br(htmlspecialchars((string) $selected['objectives'])) ?></div>
        </details>
        <?php endif; ?>

        <?php if (!empty($selected['proposal_pdf']) || !empty($selected['supporting_docs']) || !empty($selected['ethics_doc'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Documents</summary>
            <div class="gre-accordion-body gre-docs">
                <?php if (!empty($selected['proposal_pdf'])): ?>
                <a class="mpl-btn mpl-btn-soft mpl-btn-sm" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'proposal')) ?>" target="_blank" rel="noopener">
                    <?= smsIcon('file-pdf') ?> Proposal Document
                </a>
                <?php endif; ?>
                <?php if (!empty($selected['supporting_docs'])): ?>
                <a class="mpl-btn mpl-btn-ghost mpl-btn-sm" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'supporting')) ?>" target="_blank" rel="noopener">
                    <?= smsIcon('paperclip') ?> Supporting Docs
                </a>
                <?php endif; ?>
                <?php if (!empty($selected['ethics_doc'])): ?>
                <a class="mpl-btn mpl-btn-ghost mpl-btn-sm" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'ethics')) ?>" target="_blank" rel="noopener">
                    <?= smsIcon('shield-alt') ?> Ethics Clearance
                </a>
                <?php endif; ?>
            </div>
        </details>
        <?php endif; ?>
    </aside>

    <section class="gre-score-panel">
        <?php if ($existingEval): ?>
        <details class="gre-accordion gre-accordion-highlight">
            <summary class="gre-accordion-title">Evaluation Summary</summary>
            <div class="gre-accordion-body">
                <div class="gre-scored-banner gre-scored-banner-inline">
                    <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
                    Submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
                    — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
                </div>
            </div>
        </details>

        <details class="gre-accordion">
            <summary class="gre-accordion-title">Rubric Scores</summary>
            <div class="gre-accordion-body gre-accordion-body-flush">
                <table class="gre-rubric-table gre-rubric-readonly">
                    <thead><tr><th>Criteria</th><th>Maximum</th><th>Your Score</th></tr></thead>
                    <tbody>
                    <?php foreach ($rubric as $key => $max):
                        $col = 'score_' . $key;
                        $label = ucwords(str_replace('_', ' ', $key));
                    ?>
                        <tr><td><?= htmlspecialchars($label) ?></td><td><?= $max ?></td><td><strong><?= number_format((float) ($existingEval[$col] ?? 0), 1) ?></strong></td></tr>
                    <?php endforeach; ?>
                        <tr class="gre-total-row"><td colspan="2"><strong>Total Score</strong></td><td><strong><?= number_format((float) $existingEval['total_score'], 1) ?></strong></td></tr>
                    </tbody>
                </table>
            </div>
        </details>

        <?php if (!empty($existingEval['comments'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Comments</summary>
            <div class="gre-accordion-body"><?= nl2br(htmlspecialchars((string) $existingEval['comments'])) ?></div>
        </details>
        <?php endif; ?>

        <?php if (!empty($existingEval['recommendations'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Recommendations</summary>
            <div class="gre-accordion-body"><?= nl2br(htmlspecialchars((string) $existingEval['recommendations'])) ?></div>
        </details>
        <?php endif; ?>

        <?php if (!empty($existingEval['required_corrections'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Required Corrections</summary>
            <div class="gre-accordion-body"><?= nl2br(htmlspecialchars((string) $existingEval['required_corrections'])) ?></div>
        </details>
        <?php endif; ?>

        <?php if (!empty($existingEval['recommendation'])): ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title">Recommendation Decision</summary>
            <div class="gre-accordion-body">
                <strong><?= htmlspecialchars(grantRecommendationLabel((string) $existingEval['recommendation'])) ?></strong>
                <?php if (!empty($existingEval['revision_reason'])): ?>
                    <div class="gre-accordion-sub mt-2">Revision reason:<br><?= nl2br(htmlspecialchars((string) $existingEval['revision_reason'])) ?></div>
                <?php endif; ?>
            </div>
        </details>
        <?php endif; ?>

        <?php else: ?>
        <details class="gre-accordion">
            <summary class="gre-accordion-title"><?= smsIcon('star-half-alt', ['class' => 'me-1']) ?> Score Proposal Using Rubric</summary>
            <div class="gre-accordion-body">
        <p class="text-muted mb-3" style="font-size:.84rem;">Enter scores for each criterion. Total is computed automatically (max 100).</p>

        <div id="greEvalAlert" class="mpl-alert" style="display:none;" role="alert"></div>

        <form id="greEvalForm" data-no-loader novalidate>
            <input type="hidden" name="grant_application_id" value="<?= (int) $selected['id'] ?>">

            <table class="gre-rubric-table">
                <thead>
                    <tr>
                        <th>Criteria</th>
                        <th style="width:90px;">Maximum</th>
                        <th style="width:140px;">Score</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rubric as $key => $max):
                    $label = ucwords(str_replace('_', ' ', $key));
                    $inputId = 'score_' . $key;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td class="text-center"><?= $max ?></td>
                        <td>
                            <input type="number" class="go-form-input gre-score-input"
                                   id="<?= htmlspecialchars($inputId) ?>"
                                   name="<?= htmlspecialchars($inputId) ?>"
                                   min="0" max="<?= $max ?>" step="0.5" required
                                   data-max="<?= $max ?>"
                                   aria-label="<?= htmlspecialchars($label) ?> score">
                        </td>
                    </tr>
                <?php endforeach; ?>
                    <tr class="gre-total-row">
                        <td colspan="2"><strong>Total Score</strong></td>
                        <td><strong id="greTotalScore">0</strong> / 100</td>
                    </tr>
                </tbody>
            </table>

            <div class="gre-form-group">
                <label for="greComments" class="go-form-label">Comments</label>
                <textarea id="greComments" name="comments" class="go-form-input" rows="3"
                          placeholder="General comments on the proposal…"></textarea>
            </div>
            <div class="gre-form-group">
                <label for="greRecommendations" class="go-form-label">Recommendations</label>
                <textarea id="greRecommendations" name="recommendations" class="go-form-input" rows="3"
                          placeholder="Committee recommendations…"></textarea>
            </div>
            <div class="gre-form-group">
                <label for="greCorrections" class="go-form-label">Required Corrections</label>
                <textarea id="greCorrections" name="required_corrections" class="go-form-input" rows="3"
                          placeholder="List required corrections, if any…"></textarea>
            </div>

            <div class="gre-form-group">
                <span class="go-form-label">Recommendation <span class="text-danger">*</span></span>
                <div class="gre-recommendation-options" role="radiogroup" aria-label="Recommendation decision">
                    <?php foreach (grantRecommendationOptions() as $value => $label): ?>
                    <label class="gre-recommendation-option">
                        <input type="radio" name="recommendation" value="<?= htmlspecialchars($value) ?>" required>
                        <span><?= htmlspecialchars($label) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <p class="gre-recommendation-hint">Disapprove ends the proposal. Require Revisions sends it back to the researcher. Recommend forwards it to the approval workflow.</p>
            </div>

            <div class="gre-form-group" id="greRevisionReasonGroup" style="display:none;">
                <label for="greRevisionReason" class="go-form-label">Revision Reason <span class="text-danger">*</span></label>
                <textarea id="greRevisionReason" name="revision_reason" class="go-form-input" rows="3"
                          placeholder="Explain what must be revised before resubmission…"></textarea>
            </div>

            <button type="submit" class="mpl-btn mpl-btn-primary" id="greSubmitBtn">
                <?= smsIcon('check', ['class' => 'me-1']) ?>Submit Evaluation
            </button>
        </form>
            </div>
        </details>
        <?php endif; ?>
    </section>
</div>
<?php endif; ?>

<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var apiBase = '<?= BASE_URL ?>/modules/crad/api/grant-evaluation.php';
    var inputs = document.querySelectorAll('.gre-score-input');
    var totalEl = document.getElementById('greTotalScore');
    var form = document.getElementById('greEvalForm');
    var revisionGroup = document.getElementById('greRevisionReasonGroup');
    var revisionInput = document.getElementById('greRevisionReason');
    var recommendationInputs = document.querySelectorAll('input[name="recommendation"]');

    function updateRecommendationUi() {
        if (!revisionGroup) return;
        var selected = document.querySelector('input[name="recommendation"]:checked');
        var needsRevision = selected && selected.value === 'require_revisions';
        revisionGroup.style.display = needsRevision ? '' : 'none';
        if (revisionInput) {
            revisionInput.required = !!needsRevision;
            if (!needsRevision) revisionInput.value = '';
        }
    }

    recommendationInputs.forEach(function (inp) {
        inp.addEventListener('change', updateRecommendationUi);
    });
    updateRecommendationUi();

    function updateTotal() {
        if (!totalEl) return;
        var sum = 0;
        inputs.forEach(function (inp) {
            var v = parseFloat(inp.value);
            if (!isNaN(v) && v >= 0) sum += v;
        });
        totalEl.textContent = sum.toFixed(1).replace(/\.0$/, '');
        totalEl.style.color = sum > 100 ? '#b91c1c' : '';
    }

    inputs.forEach(function (inp) {
        inp.addEventListener('input', updateTotal);
    });
    updateTotal();

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.SMS2Loader) window.SMS2Loader.forceHide();

            var alertEl = document.getElementById('greEvalAlert');
            alertEl.style.display = 'none';

            var total = 0;
            var valid = true;
            inputs.forEach(function (inp) {
                var v = parseFloat(inp.value);
                var max = parseFloat(inp.getAttribute('data-max') || '0');
                if (isNaN(v) || v < 0 || v > max) valid = false;
                else total += v;
            });

            if (!valid) {
                alertEl.style.display = '';
                alertEl.style.background = 'rgba(239,68,68,.08)';
                alertEl.style.color = '#b91c1c';
                alertEl.textContent = 'Please enter valid scores within each criterion maximum.';
                return;
            }
            if (total > 100) {
                alertEl.style.display = '';
                alertEl.style.background = 'rgba(239,68,68,.08)';
                alertEl.style.color = '#b91c1c';
                alertEl.textContent = 'Total score cannot exceed 100.';
                return;
            }

            var recommendation = document.querySelector('input[name="recommendation"]:checked');
            if (!recommendation) {
                alertEl.style.display = '';
                alertEl.style.background = 'rgba(239,68,68,.08)';
                alertEl.style.color = '#b91c1c';
                alertEl.textContent = 'Please select a recommendation.';
                return;
            }
            if (recommendation.value === 'require_revisions') {
                var reason = revisionInput ? revisionInput.value.trim() : '';
                if (!reason) {
                    alertEl.style.display = '';
                    alertEl.style.background = 'rgba(239,68,68,.08)';
                    alertEl.style.color = '#b91c1c';
                    alertEl.textContent = 'Revision reason is required when selecting Require Revisions.';
                    if (revisionInput) revisionInput.focus();
                    return;
                }
            }

            var btn = document.getElementById('greSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting…';

            var fd = new FormData(form);
            fd.set('action', 'submit_evaluation');

            fetch(apiBase, { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        window.location.href = '?id=<?= (int) ($selected['id'] ?? 0) ?>&saved=1';
                    } else {
                        alertEl.style.display = '';
                        alertEl.style.background = 'rgba(239,68,68,.08)';
                        alertEl.style.color = '#b91c1c';
                        alertEl.textContent = data.message || 'Failed to submit evaluation.';
                        btn.disabled = false;
                        btn.innerHTML = '<?= smsIcon('check', ['class' => 'me-1']) ?>Submit Evaluation';
                    }
                })
                .catch(function () {
                    alertEl.style.display = '';
                    alertEl.style.background = 'rgba(239,68,68,.08)';
                    alertEl.style.color = '#b91c1c';
                    alertEl.textContent = 'Network error. Please try again.';
                    btn.disabled = false;
                    btn.innerHTML = '<?= smsIcon('check', ['class' => 'me-1']) ?>Submit Evaluation';
                });
        });
    }
});
</script>
<script src="<?= BASE_URL ?>/assets/js/grant-evaluation-live.js?v=1"></script>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
