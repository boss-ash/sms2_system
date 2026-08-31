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
require_once __DIR__ . '/../includes/grant-evaluation-rubric-ui.php';
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
$pipelineEvals = [];
$approverRoleLabel = '';
$currentApproverEvalType = null;
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
                $pipelineEvals = grantGetPipelineEvaluationsForApplication($crad, $selectedId);
                $evals = grantGetLatestEvaluationsForApplications($crad, [$selectedId]);
                $committeeEval = $evals[$selectedId] ?? null;
                $existingEval = grantGetAdviserEvaluationByApplication($crad, $selectedId);
                $approvalDetail = grantGetApprovalWorkflowDetail($crad, $selectedId);
                $canSignApproval = !empty($approvalDetail['can_act']);
            } elseif ($selected && $isApproverView) {
                require_once __DIR__ . '/../includes/grant-approval-helpers.php';
                $approverRoleLabel = grantApprovalRoleLabel(getCurrentUserRoleKey());
                $currentApproverEvalType = grantEvaluationTypeForApproverRole();
                $pipelineEvals = grantGetPipelineEvaluationsForApplication($crad, $selectedId);
                $evals = grantGetLatestEvaluationsForApplications($crad, [$selectedId]);
                $committeeEval = $evals[$selectedId] ?? null;
                $adviserEval = grantGetLatestAdviserEvaluationByApplication($crad, $selectedId);
                $existingEval = grantGetApproverEvaluationByApplication($crad, $selectedId);
                $approvalDetail = grantGetApprovalWorkflowDetail($crad, $selectedId);
                $canSignApproval = !empty($approvalDetail['can_act']);
            } elseif ($selected && $isMonitorView) {
                require_once __DIR__ . '/../includes/grant-approval-helpers.php';
                $pipelineEvals = grantGetPipelineEvaluationsForApplication($crad, $selectedId);
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
        ? count(array_filter($queue, static fn(array $r): bool => empty($r['my_evaluation_id'])))
        : count(array_filter($queue, static fn(array $r): bool => empty($r['my_evaluation_id']))));
$scoredCount  = $isAdviserView
    ? ($crad ? grantAdviserEvaluationScoredCount($crad) : 0)
    : ($isApproverView
        ? count(array_filter($queue, static fn(array $r): bool => !empty($r['my_evaluation_id'])))
        : ($isMonitorView
            ? ($crad ? grantMonitorEvaluationCounts($crad)['scored'] : 0)
            : count(array_filter($queue, static fn(array $r): bool => !empty($r['my_evaluation_id'])))));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/grant-reviewer-evaluation.css?v=8" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="mpl gre" data-grant-eval-live="1"<?= $selectedId > 0 ? ' data-grant-eval-app-id="' . (int) $selectedId . '"' : '' ?>>

<div class="gre-header">
    <div>
        <h1><?= smsIcon('clipboard-check', ['class' => 'me-2', 'style' => 'color:var(--sms-primary);']) ?>Reviewer Evaluation</h1>
        <?php if ($isAdviserView): ?>
        <p>Score proposals using the rubric first. After you submit your evaluation, you can sign off in <strong>Approval Workflows</strong>.</p>
        <?php elseif ($isApproverView): ?>
        <p>Score proposals using the rubric first, then sign off in <strong>Approval Workflows</strong>.</p>
        <?php elseif ($isMonitorView): ?>
        <p>Monitor committee scores and institutional sign-offs across the full approval pipeline.</p>
        <?php else: ?>
        <p>Proposals with <strong>Pending Evaluation</strong> in Proposals &amp; Applications appear here for rubric scoring.</p>
        <?php endif; ?>
    </div>
    <div class="gre-stat-row">
        <span class="gre-stat pending"><strong data-eval-pending-count><?= $pendingCount ?></strong> <?= ($isAdviserView || $isApproverView || $isMonitorView) ? ($isMonitorView ? 'in progress' : 'in review') : 'awaiting score' ?></span>
        <span class="gre-stat scored"><strong data-eval-scored-count><?= $scoredCount ?></strong> <?= $isApproverView ? 'scored by you' : ($isMonitorView ? 'completed' : 'scored by you') ?></span>
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
                        $statusLabel = $isScored ? 'Scored' : 'Awaiting Score';
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
                            <?= $isAdviserView || $isApproverView
                                ? ($isScored ? smsIcon('eye') . ' View' : smsIcon('star-half-alt') . ' Score')
                                : ($isMonitorView
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
        <?php if (!empty($pipelineEvals) && ($isAdviserView || $isApproverView || $isMonitorView)): ?>
        <?php grantRenderPipelineScorePillsFromEvals($pipelineEvals, 'gaw-monitor-scores gre-detail-scores'); ?>
        <?php endif; ?>

        <?php if ($isAdviserView): ?>
        <?php if ($existingEval): ?>
        <div class="gre-scored-banner">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
            Your evaluation submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
            — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
        </div>
        <?php grantRenderRubricReadonlyCard($existingEval, 'Your Academic Adviser Evaluation', $rubric, 'user-tie'); ?>

        <div class="gre-adviser-actions">
            <a class="mpl-btn mpl-btn-primary" href="<?= htmlspecialchars(grantApprovalWorkflowListUrl() . '?id=' . (int) $selected['id']) ?>">
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
        <?php grantRenderRubricReadonlyCard($committeeEval, 'Review Committee Evaluation', $rubric, 'users', false); ?>
        <?php endif; ?>

        <?php grantRenderRubricEvaluationForm([
            'application_id' => (int) $selected['id'],
            'rubric' => $rubric,
            'title' => 'Academic Adviser Evaluation',
            'submit_label' => 'Submit Adviser Evaluation',
            'comments_placeholder' => 'General comments on the proposal…',
            'recommendations_placeholder' => 'Your recommendations…',
            'recommendation_hint' => 'Disapprove ends the proposal. Require Revisions sends it back to the researcher. Recommend lets you proceed to Approval Workflows sign-off.',
        ]); ?>
        <?php endif; ?>

        <?php elseif ($isApproverView): ?>
        <?php if ($existingEval): ?>
        <div class="gre-scored-banner">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
            Your evaluation submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
            — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
        </div>
        <?php grantRenderRubricReadonlyCard($existingEval, 'Your ' . $approverRoleLabel . ' Evaluation', $rubric, grantPipelineScorePillIcon((string) $currentApproverEvalType)); ?>

        <div class="gre-adviser-actions">
            <a class="mpl-btn mpl-btn-primary" href="<?= htmlspecialchars(grantApprovalWorkflowListUrl() . '?id=' . (int) $selected['id']) ?>">
                <?= smsIcon('signature', ['class' => 'me-1']) ?>Go to Approval Workflows
            </a>
            <p class="gre-adviser-hint mb-0">You may now sign and approve this proposal in Approval Workflows.</p>
        </div>

        <?php else: ?>
        <div class="gre-scored-banner gre-adviser-banner">
            <?= smsIcon('hourglass-half', ['class' => 'me-2']) ?>
            <strong>In Review</strong> — Score this proposal before signing off in Approval Workflows.
        </div>

        <?php grantRenderRubricEvaluationForm([
            'application_id' => (int) $selected['id'],
            'rubric' => $rubric,
            'title' => $approverRoleLabel . ' Evaluation',
            'description' => 'Enter scores for each criterion. Total is computed automatically (max 100).',
            'submit_label' => 'Submit ' . $approverRoleLabel . ' Evaluation',
            'comments_placeholder' => 'General comments on the proposal…',
            'recommendations_placeholder' => 'Your recommendations…',
            'recommendation_hint' => 'Disapprove ends the proposal. Require Revisions sends it back to the researcher. Recommend lets you proceed to Approval Workflows sign-off.',
        ]); ?>

        <?php
        $hasPriorEvals = false;
        foreach (grantPipelineEvaluationTypes() as $pipelineType) {
            if ($pipelineType === $currentApproverEvalType) {
                break;
            }
            if (!empty($pipelineEvals[$pipelineType])) {
                $hasPriorEvals = true;
                break;
            }
        }
        if ($hasPriorEvals):
        ?>
        <div class="gre-prior-eval-section">
            <h3 class="gre-prior-eval-heading"><?= smsIcon('history', ['class' => 'me-1']) ?>Prior Evaluation Scores</h3>
            <p class="text-muted gre-prior-eval-note">Reference scores from earlier review stages.</p>
        <?php
        foreach (grantPipelineEvaluationTypes() as $pipelineType) {
            if ($pipelineType === $currentApproverEvalType) {
                break;
            }
            $priorEval = $pipelineEvals[$pipelineType] ?? null;
            if (!$priorEval) {
                continue;
            }
            grantRenderRubricReadonlyCard(
                $priorEval,
                grantEvaluationStepLabel($pipelineType) . ' Evaluation',
                $rubric,
                grantPipelineScorePillIcon($pipelineType),
                false
            );
        }
        ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php elseif ($isMonitorView): ?>
        <div class="gre-scored-banner gre-adviser-banner">
            <?= smsIcon('eye', ['class' => 'me-2']) ?>
            <strong>Pipeline Monitor</strong> — Review scores and track sign-off progress in Approval Workflows.
        </div>

        <?php foreach (grantPipelineEvaluationTypes() as $pipelineType):
            $pipelineEval = $pipelineEvals[$pipelineType] ?? null;
            if (!$pipelineEval) {
                continue;
            }
            grantRenderRubricReadonlyCard(
                $pipelineEval,
                grantEvaluationStepLabel($pipelineType) . ' Evaluation',
                $rubric,
                grantPipelineScorePillIcon($pipelineType)
            );
        endforeach; ?>

        <div class="gre-adviser-actions">
            <a class="mpl-btn mpl-btn-primary" href="<?= htmlspecialchars(grantApprovalWorkflowListUrl() . '?id=' . (int) $selected['id']) ?>">
                <?= smsIcon('signature', ['class' => 'me-1']) ?>Go to Approval Workflows
            </a>
            <p class="gre-adviser-hint mb-0">View the full signature trail and current approval stage in Approval Workflows.</p>
        </div>

        <?php elseif ($existingEval): ?>
        <div class="gre-scored-banner">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?>
            Evaluation submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $existingEval['submitted_at']))) ?>
            — Total: <strong><?= number_format((float) $existingEval['total_score'], 1) ?> / 100</strong>
        </div>
        <?php grantRenderRubricReadonlyCard($existingEval, 'Your Review Committee Evaluation', $rubric, 'users'); ?>

        <?php else: ?>
        <?php grantRenderRubricEvaluationForm([
            'application_id' => (int) $selected['id'],
            'rubric' => $rubric,
            'title' => 'Review Committee Evaluation',
            'submit_label' => 'Submit Evaluation',
            'comments_placeholder' => 'General comments on the proposal…',
            'recommendations_placeholder' => 'Committee recommendations…',
        ]); ?>
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
<script src="<?= BASE_URL ?>/assets/js/grant-evaluation-live.js?v=5"></script>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
