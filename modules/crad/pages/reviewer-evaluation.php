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
require_once __DIR__ . '/../includes/grant-approval-helpers.php';

requireAuth();

if (function_exists('getCurrentUserRoleKey') && getCurrentUserRoleKey() === 'crad_officer') {
    header('Location: ' . BASE_URL . '/modules/crad/pages/approval-workflows.php');
    exit;
}

grantRequireEvaluateAccess();

grantRedirectReviewWorkflowShellIfNeeded('reviewer-evaluation');

$pageTitle             = 'Reviewer Evaluation';
$activeModule          = grantEvaluationActiveModuleKey();
$activePage            = 'reviewer-evaluation';
$pageBannerIcon        = 'fa-clipboard-check';
$pageBannerDescription = grantIsAdviserEvaluationViewer()
    ? 'Review committee scores before your administrative sign-off.'
    : (grantIsGrantApproverEvaluationViewer()
        ? 'Review committee and adviser scores before your approval sign-off.'
        : 'Score research grant proposals submitted for committee review.');
$hideModulePageBanner  = true;
$isAdviserView         = grantIsAdviserEvaluationViewer();
$isApproverView        = grantIsGrantApproverEvaluationViewer();
$isMonitorView         = grantIsGrantWorkflowMonitor();

$breadcrumbs = [
    ['label' => grantEvaluationBreadcrumbModuleLabel(), 'url' => grantEvaluationBreadcrumbModuleUrl()],
    ['label' => 'Review & Workflow', 'url' => grantApprovalWorkflowListUrl()],
    ['label' => 'Reviewer Evaluation', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad    = cradDb();
$queue   = [];
$dbError = '';
$selectedId = (int) ($_GET['id'] ?? 0);
$selected   = null;
$existingEval = null;
$committeeEval = null;
$adviserEval = null;
$approvalDetail = null;
$canSignApproval = false;

$rubric = grantRubricCriteria();

if ($crad) {
    try {
        $queue = grantEvaluationQueue($crad);
        if ($selectedId > 0) {
            $selected = grantGetApplicationForEvaluation($crad, $selectedId);
            if ($selected && !grantApplicationOpenForEvaluationViewer($crad, $selectedId)) {
                $selected = null;
            }
            if ($selected && $isAdviserView) {
                require_once __DIR__ . '/../includes/grant-approval-helpers.php';
                $evals = grantGetLatestEvaluationsForApplications($crad, [$selectedId]);
                $committeeEval = $evals[$selectedId] ?? null;
                $existingEval = grantGetAdviserEvaluationByApplication($crad, $selectedId);
                $approvalDetail = grantGetApprovalWorkflowDetail($crad, $selectedId);
                $canSignApproval = !empty($approvalDetail['can_act']);
            } elseif ($selected && $isApproverView) {
                require_once __DIR__ . '/../includes/grant-approval-helpers.php';
                $evals = grantGetLatestEvaluationsForApplications($crad, [$selectedId]);
                $committeeEval = $evals[$selectedId] ?? null;
                $adviserEval = grantGetLatestAdviserEvaluationByApplication($crad, $selectedId);
                $approvalDetail = grantGetApprovalWorkflowDetail($crad, $selectedId);
                $canSignApproval = !empty($approvalDetail['can_act']);
            } elseif ($selected && $isMonitorView) {
                require_once __DIR__ . '/../includes/grant-approval-helpers.php';
                $evals = grantGetLatestEvaluationsForApplications($crad, [$selectedId]);
                $committeeEval = $evals[$selectedId] ?? null;
                $adviserEval = grantGetLatestAdviserEvaluationByApplication($crad, $selectedId);
                $approvalDetail = grantGetApprovalWorkflowDetail($crad, $selectedId);
            } elseif ($selected) {
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

$pendingCount = $isMonitorView && $crad
    ? grantMonitorEvaluationCounts($crad)['pending']
    : (($isAdviserView || $isApproverView)
        ? count($queue)
        : count(array_filter($queue, static fn(array $r): bool => empty($r['my_evaluation_id']))));
$scoredCount  = $isAdviserView
    ? ($crad ? grantAdviserEvaluationScoredCount($crad) : 0)
    : ($isApproverView
        ? ($crad ? grantApproverSignoffCount($crad) : 0)
        : ($isMonitorView
            ? ($crad ? grantMonitorEvaluationCounts($crad)['scored'] : 0)
            : count(array_filter($queue, static fn(array $r): bool => !empty($r['my_evaluation_id'])))));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/grant-reviewer-evaluation.css?v=5" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="mpl gre" data-grant-eval-live="1">

<div class="gre-header">
    <div>
        <h1><?= smsIcon('clipboard-check', ['class' => 'me-2', 'style' => 'color:var(--sms-primary);']) ?>Reviewer Evaluation</h1>
        <?php if ($isAdviserView): ?>
        <p>Score proposals using the rubric first. After you submit your evaluation, you can sign off in <strong>Approval Workflows</strong>.</p>
        <?php elseif ($isApproverView): ?>
        <p>Review committee and adviser scores, then sign off in <strong>Approval Workflows</strong>.</p>
        <?php elseif ($isMonitorView): ?>
        <p>Monitor committee scores and institutional sign-offs across the full approval pipeline.</p>
        <?php else: ?>
        <p>Proposals with <strong>Pending Evaluation</strong> in Proposals &amp; Applications appear here for rubric scoring.</p>
        <?php endif; ?>
    </div>
    <div class="gre-stat-row">
        <span class="gre-stat pending"><strong data-eval-pending-count><?= $pendingCount ?></strong> <?= ($isAdviserView || $isApproverView || $isMonitorView) ? ($isMonitorView ? 'in progress' : 'in review') : 'awaiting score' ?></span>
        <span class="gre-stat scored"><strong data-eval-scored-count><?= $scoredCount ?></strong> <?= $isApproverView ? 'signed off' : ($isMonitorView ? 'completed' : 'scored by you') ?></span>
        <span class="gre-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
    </div>
</div>

<?php if ($dbError === ''): ?>

<?php if (!$selected): ?>
<section class="mpl-panel">
    <div class="mpl-panel-head">
        <div>
            <h2>Evaluation Queue</h2>
            <?php if ($isAdviserView): ?>
            <p>Select a proposal to score before administrative sign-off.</p>
            <?php elseif ($isApproverView): ?>
            <p>Select a proposal to review before your approval sign-off.</p>
            <?php elseif ($isMonitorView): ?>
            <p>Select a proposal to review scores and current approval stage.</p>
            <?php else: ?>
            <p>Select a proposal to score using the review committee rubric (total 100 points).</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="mpl-table-wrap">
        <table class="mpl-table" id="greQueueTable">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Grant Program</th>
                    <th>Lead Proponent</th>
                    <th>Research Title</th>
                    <th>College / Dept</th>
                    <th>Budget</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="greQueueBody">
            <?php if (empty($queue)): ?>
                <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--sms-text-muted);">
                    <?= $isAdviserView
                        ? 'No grant proposals are awaiting Academic Adviser review.'
                        : ($isApproverView
                            ? 'No grant proposals are awaiting your approval review.'
                            : ($isMonitorView
                                ? 'No grant proposals are in the approval workflow yet.'
                                : 'No proposals are waiting for committee evaluation.')) ?>
                </td></tr>
            <?php else: ?>
                <?php foreach ($queue as $row):
                    $isScored = !empty($row['my_evaluation_id']);
                    if ($isAdviserView) {
                        $statusLabel = $isScored ? 'Scored' : 'In Review';
                    } elseif ($isApproverView) {
                        $statusLabel = 'In Review';
                        $isScored = false;
                    } elseif ($isMonitorView) {
                        $wfStatus = (string) ($row['workflow_status'] ?? '');
                        $statusLabel = $wfStatus === 'Completed'
                            ? 'Completed'
                            : ((string) ($row['current_step_label'] ?? 'In Progress'));
                        $isScored = $wfStatus === 'Completed';
                    } else {
                        $statusLabel = ($row['status'] ?? '') === 'Submitted' ? 'Pending Evaluation' : 'Under Review';
                    }
                ?>
                <tr data-app-id="<?= (int) $row['id'] ?>">
                    <td style="font-weight:800;color:var(--sms-primary);white-space:nowrap;">
                        <?= htmlspecialchars((string) ($row['proposal_reference'] ?? ('#' . (int) $row['id']))) ?>
                        <div style="font-size:.7rem;font-weight:500;color:var(--sms-text-muted);">
                            v<?= max(1, (int) ($row['current_version'] ?? 1)) ?>
                        </div>
                    </td>
                    <td style="font-weight:600;max-width:180px;"><?= htmlspecialchars((string) $row['funding_title']) ?></td>
                    <td><?= htmlspecialchars((string) $row['applicant_name']) ?></td>
                    <td style="max-width:220px;font-size:.86rem;"><?= htmlspecialchars((string) ($row['research_title'] ?? '—')) ?></td>
                    <td style="font-size:.84rem;"><?= htmlspecialchars((string) ($row['college_dept'] ?? '—')) ?></td>
                    <td style="font-weight:700;white-space:nowrap;">
                        <?= $row['requested_budget'] !== null ? '₱' . number_format((float) $row['requested_budget'], 0) : '—' ?>
                    </td>
                    <td style="font-size:.82rem;white-space:nowrap;">
                        <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $row['submitted_at']))) ?>
                    </td>
                    <td>
                        <?php if ($isScored && !$isMonitorView): ?>
                            <span class="mpl-status completed">Scored (<?= number_format((float) $row['my_total_score'], 1) ?>/100)</span>
                        <?php elseif ($isMonitorView && $isScored): ?>
                            <span class="mpl-status completed"><?= htmlspecialchars($statusLabel) ?></span>
                        <?php else: ?>
                            <span class="mpl-status pending"><?= htmlspecialchars($statusLabel) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="mpl-btn mpl-btn-primary mpl-btn-sm"
                           href="?id=<?= (int) $row['id'] ?>">
                            <?= $isAdviserView
                                ? ($isScored ? smsIcon('eye') . ' View' : smsIcon('star-half-alt') . ' Score')
                                : ($isApproverView || $isMonitorView
                                    ? smsIcon('eye') . ' Review'
                                    : ($isScored ? smsIcon('eye') . ' View' : smsIcon('star-half-alt') . ' Score')) ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php else: ?>
<div class="gre-layout">
    <aside class="gre-info-panel">
        <a class="mpl-btn mpl-btn-ghost mpl-btn-sm mb-3" href="<?= htmlspecialchars(grantReviewerEvaluationUrl()) ?>">
            <?= smsIcon('arrow-left') ?> Back to Queue
        </a>

        <details class="gre-proposal-toggle">
            <summary class="gre-proposal-summary">
                <span class="gre-proposal-summary-inner">
                    <span class="gre-proposal-summary-label">Grant Program</span>
                    <span class="gre-proposal-summary-value"><?= htmlspecialchars((string) $selected['funding_title']) ?></span>
                </span>
                <?= smsIcon('chevron-down', ['class' => 'gre-proposal-chevron', 'aria-hidden' => 'true']) ?>
            </summary>

            <div class="gre-proposal-details">
                <h2 class="gre-proposal-heading"><?= htmlspecialchars((string) ($selected['research_title'] ?? 'Research Proposal')) ?></h2>

                <dl class="gre-meta">
                    <?php if (!empty($selected['proposal_reference'])): ?>
                    <div><dt>Reference</dt><dd><?= htmlspecialchars((string) $selected['proposal_reference']) ?>
                        <small>Version <?= max(1, (int) ($selected['current_version'] ?? 1)) ?> — <?= htmlspecialchars(grantVersionLabel((int) ($selected['current_version'] ?? 1))) ?></small>
                    </dd></div>
                    <?php endif; ?>
                    <div><dt>Grant Program</dt><dd><?= htmlspecialchars((string) $selected['funding_title']) ?></dd></div>
                    <div><dt>Lead Proponent</dt><dd><?= htmlspecialchars((string) $selected['applicant_name']) ?></dd></div>
                    <div><dt>College / Dept</dt><dd><?= htmlspecialchars((string) ($selected['college_dept'] ?? '—')) ?></dd></div>
                    <div><dt>Requested Budget</dt><dd>₱<?= number_format((float) ($selected['requested_budget'] ?? 0), 0) ?>
                        <small>of ₱<?= number_format((float) ($selected['max_funding_cap'] ?? 0), 0) ?> cap</small></dd></div>
                    <div><dt>Eligibility</dt><dd><?= htmlspecialchars((string) ($selected['eligibility'] ?? '')) ?></dd></div>
                    <div><dt>Submitted</dt><dd><?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $selected['submitted_at']))) ?></dd></div>
                </dl>

                <?php if (!empty($selected['abstract'])): ?>
                <div class="gre-block">
                    <h3>Executive Abstract</h3>
                    <p><?= nl2br(htmlspecialchars((string) $selected['abstract'])) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($selected['objectives'])): ?>
                <div class="gre-block">
                    <h3>Objectives</h3>
                    <p><?= nl2br(htmlspecialchars((string) $selected['objectives'])) ?></p>
                </div>
                <?php endif; ?>

                <div class="gre-docs gre-docs-stack">
                    <?php if (!empty($selected['proposal_pdf'])): ?>
                    <a class="mpl-btn mpl-btn-soft mpl-btn-sm gre-doc-btn" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'proposal')) ?>" target="_blank" rel="noopener">
                        <?= smsIcon('file-pdf') ?> Proposal Document
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($selected['supporting_docs'])): ?>
                    <a class="mpl-btn mpl-btn-ghost mpl-btn-sm gre-doc-btn" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'supporting')) ?>" target="_blank" rel="noopener">
                        <?= smsIcon('paperclip') ?> Supporting Docs
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($selected['ethics_doc'])): ?>
                    <a class="mpl-btn mpl-btn-ghost mpl-btn-sm gre-doc-btn" href="<?= htmlspecialchars(grantProposalFileUrl((int) $selected['id'], 'ethics')) ?>" target="_blank" rel="noopener">
                        <?= smsIcon('shield-alt') ?> Ethics Clearance
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </details>
    </aside>

    <section class="gre-score-panel">
        <?php if ($isAdviserView): ?>
        <?php if ($existingEval): ?>
        <div class="gre-scored-banner">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
            Your evaluation submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
            — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
        </div>
        <h2><?= smsIcon('user-tie', ['class' => 'me-2 text-primary']) ?>Your Adviser Evaluation</h2>
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
        <?php if (!empty($existingEval['comments'])): ?><div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['comments'])) ?></p></div><?php endif; ?>
        <?php if (!empty($existingEval['recommendations'])): ?><div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['recommendations'])) ?></p></div><?php endif; ?>
        <?php if (!empty($existingEval['required_corrections'])): ?><div class="gre-block"><h3>Required Corrections</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['required_corrections'])) ?></p></div><?php endif; ?>

        <div class="gre-adviser-actions">
            <a class="mpl-btn mpl-btn-primary" href="<?= BASE_URL ?>/modules/crad/pages/approval-workflows.php?id=<?= (int) $selected['id'] ?>">
                <?= smsIcon('signature', ['class' => 'me-1']) ?>Go to Approval Workflows
            </a>
            <p class="gre-adviser-hint mb-0">You may now sign and approve this proposal in Approval Workflows.</p>
        </div>

        <?php else: ?>
        <div class="gre-scored-banner gre-adviser-banner">
            <?= smsIcon('hourglass-half', ['class' => 'me-2']) ?>
            <strong>In Review</strong> — Score this proposal before signing off in Approval Workflows.
        </div>

        <?php if ($committeeEval): ?>
        <details class="gre-committee-ref">
            <summary class="gre-committee-ref-summary">
                <?= smsIcon('users', ['class' => 'me-1']) ?>
                Review Committee Evaluation
                <strong><?= number_format((float) $committeeEval['total_score'], 1) ?> / 100</strong>
            </summary>
            <div class="gre-committee-ref-body">
                <p class="text-muted mb-3">Submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $committeeEval['submitted_at']))) ?></p>
                <table class="gre-rubric-table gre-rubric-readonly">
                    <thead><tr><th>Criteria</th><th>Maximum</th><th>Score</th></tr></thead>
                    <tbody>
                    <?php foreach ($rubric as $key => $max):
                        $col = 'score_' . $key;
                        $label = ucwords(str_replace('_', ' ', $key));
                    ?>
                        <tr><td><?= htmlspecialchars($label) ?></td><td><?= $max ?></td><td><strong><?= number_format((float) ($committeeEval[$col] ?? 0), 1) ?></strong></td></tr>
                    <?php endforeach; ?>
                        <tr class="gre-total-row"><td colspan="2"><strong>Total Score</strong></td><td><strong><?= number_format((float) $committeeEval['total_score'], 1) ?></strong></td></tr>
                    </tbody>
                </table>
                <?php if (!empty($committeeEval['comments'])): ?><div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $committeeEval['comments'])) ?></p></div><?php endif; ?>
                <?php if (!empty($committeeEval['recommendations'])): ?><div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $committeeEval['recommendations'])) ?></p></div><?php endif; ?>
            </div>
        </details>
        <?php endif; ?>

        <h2><?= smsIcon('star-half-alt', ['class' => 'me-2 text-primary']) ?>Academic Adviser Evaluation</h2>
        <p class="text-muted mb-3">Enter your scores for each criterion. Total is computed automatically (max 100).</p>

        <div id="greEvalAlert" class="mpl-alert" style="display:none;" role="alert"></div>

        <form id="greEvalForm" data-no-loader novalidate data-adviser-eval="1">
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
                          placeholder="Your comments on the proposal…"></textarea>
            </div>
            <div class="gre-form-group">
                <label for="greRecommendations" class="go-form-label">Recommendations</label>
                <textarea id="greRecommendations" name="recommendations" class="go-form-input" rows="3"
                          placeholder="Your recommendations…"></textarea>
            </div>
            <div class="gre-form-group">
                <label for="greCorrections" class="go-form-label">Required Corrections</label>
                <textarea id="greCorrections" name="required_corrections" class="go-form-input" rows="3"
                          placeholder="List required corrections, if any…"></textarea>
            </div>

            <button type="submit" class="mpl-btn mpl-btn-primary" id="greSubmitBtn">
                <?= smsIcon('check', ['class' => 'me-1']) ?>Submit Adviser Evaluation
            </button>
        </form>
        <?php endif; ?>

        <?php elseif ($isApproverView || $isMonitorView): ?>
        <div class="gre-scored-banner gre-adviser-banner">
            <?= smsIcon($isMonitorView ? 'eye' : 'hourglass-half', ['class' => 'me-2']) ?>
            <?php if ($isMonitorView): ?>
            <strong>Pipeline Monitor</strong> — Review scores and track sign-off progress in Approval Workflows.
            <?php else: ?>
            <strong>In Review</strong> — Review scores below, then sign off in Approval Workflows.
            <?php endif; ?>
        </div>

        <?php if ($committeeEval): ?>
        <details class="gre-committee-ref" open>
            <summary class="gre-committee-ref-summary">
                <?= smsIcon('users', ['class' => 'me-1']) ?>
                Review Committee Evaluation
                <strong><?= number_format((float) $committeeEval['total_score'], 1) ?> / 100</strong>
            </summary>
            <div class="gre-committee-ref-body">
                <p class="text-muted mb-3">Submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $committeeEval['submitted_at']))) ?></p>
                <table class="gre-rubric-table gre-rubric-readonly">
                    <thead><tr><th>Criteria</th><th>Maximum</th><th>Score</th></tr></thead>
                    <tbody>
                    <?php foreach ($rubric as $key => $max):
                        $col = 'score_' . $key;
                        $label = ucwords(str_replace('_', ' ', $key));
                    ?>
                        <tr><td><?= htmlspecialchars($label) ?></td><td><?= $max ?></td><td><strong><?= number_format((float) ($committeeEval[$col] ?? 0), 1) ?></strong></td></tr>
                    <?php endforeach; ?>
                        <tr class="gre-total-row"><td colspan="2"><strong>Total Score</strong></td><td><strong><?= number_format((float) $committeeEval['total_score'], 1) ?></strong></td></tr>
                    </tbody>
                </table>
                <?php if (!empty($committeeEval['comments'])): ?><div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $committeeEval['comments'])) ?></p></div><?php endif; ?>
                <?php if (!empty($committeeEval['recommendations'])): ?><div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $committeeEval['recommendations'])) ?></p></div><?php endif; ?>
            </div>
        </details>
        <?php endif; ?>

        <?php if ($adviserEval): ?>
        <details class="gre-committee-ref" open>
            <summary class="gre-committee-ref-summary">
                <?= smsIcon('user-tie', ['class' => 'me-1']) ?>
                Academic Adviser Evaluation
                <strong><?= number_format((float) $adviserEval['total_score'], 1) ?> / 100</strong>
            </summary>
            <div class="gre-committee-ref-body">
                <p class="text-muted mb-3">Submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $adviserEval['submitted_at']))) ?></p>
                <table class="gre-rubric-table gre-rubric-readonly">
                    <thead><tr><th>Criteria</th><th>Maximum</th><th>Score</th></tr></thead>
                    <tbody>
                    <?php foreach ($rubric as $key => $max):
                        $col = 'score_' . $key;
                        $label = ucwords(str_replace('_', ' ', $key));
                    ?>
                        <tr><td><?= htmlspecialchars($label) ?></td><td><?= $max ?></td><td><strong><?= number_format((float) ($adviserEval[$col] ?? 0), 1) ?></strong></td></tr>
                    <?php endforeach; ?>
                        <tr class="gre-total-row"><td colspan="2"><strong>Total Score</strong></td><td><strong><?= number_format((float) $adviserEval['total_score'], 1) ?></strong></td></tr>
                    </tbody>
                </table>
                <?php if (!empty($adviserEval['comments'])): ?><div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $adviserEval['comments'])) ?></p></div><?php endif; ?>
                <?php if (!empty($adviserEval['recommendations'])): ?><div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $adviserEval['recommendations'])) ?></p></div><?php endif; ?>
            </div>
        </details>
        <?php endif; ?>

        <div class="gre-adviser-actions">
            <a class="mpl-btn mpl-btn-primary" href="<?= BASE_URL ?>/modules/crad/pages/approval-workflows.php?id=<?= (int) $selected['id'] ?>">
                <?= smsIcon('signature', ['class' => 'me-1']) ?>Go to Approval Workflows
            </a>
            <p class="gre-adviser-hint mb-0">
                <?php if ($isMonitorView): ?>
                View the full signature trail and current approval stage in Approval Workflows.
                <?php elseif ($canSignApproval): ?>
                You may sign and approve this proposal in Approval Workflows.
                <?php else: ?>
                This proposal is not yet ready for your sign-off.
                <?php endif; ?>
            </p>
        </div>

        <?php elseif ($existingEval): ?>
        <div class="gre-scored-banner">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
            Evaluation submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
            — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
        </div>
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
        <?php if (!empty($existingEval['comments'])): ?><div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['comments'])) ?></p></div><?php endif; ?>
        <?php if (!empty($existingEval['recommendations'])): ?><div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['recommendations'])) ?></p></div><?php endif; ?>
        <?php if (!empty($existingEval['required_corrections'])): ?><div class="gre-block"><h3>Required Corrections</h3><p><?= nl2br(htmlspecialchars((string) $existingEval['required_corrections'])) ?></p></div><?php endif; ?>
        <?php if (!empty($existingEval['recommendation'])): ?>
        <div class="gre-block">
            <h3>Recommendation Decision</h3>
            <p><strong><?= htmlspecialchars(grantRecommendationLabel((string) $existingEval['recommendation'])) ?></strong></p>
            <?php if (!empty($existingEval['revision_reason'])): ?>
                <p class="mb-0"><span style="font-size:.75rem;font-weight:700;color:var(--sms-text-muted);">Revision reason:</span><br><?= nl2br(htmlspecialchars((string) $existingEval['revision_reason'])) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <h2><?= smsIcon('star-half-alt', ['class' => 'me-2 text-primary']) ?>Score Proposal Using Rubric</h2>
        <p class="text-muted mb-3">Enter scores for each criterion. Total is computed automatically (max 100).</p>

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
    var isAdviserEval = form && form.getAttribute('data-adviser-eval') === '1';
    var revisionGroup = document.getElementById('greRevisionReasonGroup');
    var revisionInput = document.getElementById('greRevisionReason');
    var recommendationInputs = document.querySelectorAll('input[name="recommendation"]');

    function updateRecommendationUi() {
        if (!revisionGroup || isAdviserEval) return;
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
    if (!isAdviserEval) {
        updateRecommendationUi();
    }

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
            if (!isAdviserEval) {
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
<script src="<?= BASE_URL ?>/assets/js/grant-evaluation-live.js?v=2"></script>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
