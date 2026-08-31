<?php
/**
 * SMS 2 - CRAD · Approval Workflows
 * Multi-Level Approval Pipeline — sequential institutional sign-offs.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-approval-helpers.php';
require_once __DIR__ . '/../includes/grant-evaluation-helpers.php';

requireAuth();
grantRequireApprovalAccess();

grantRedirectReviewWorkflowShellIfNeeded('approval-workflows');

$pageTitle             = 'Approval Workflows';
$activeModule          = defined('SMS2_GRANT_APPROVAL_SHELL_MODULE')
    ? (string) SMS2_GRANT_APPROVAL_SHELL_MODULE
    : grantApprovalActiveModuleKey();
$activePage            = 'approval-workflows';
$pageBannerIcon        = 'fa-tasks';
$hideModulePageBanner  = true;
$isMonitor             = grantUserCanMonitorApprovalWorkflow();

$breadcrumbs = [
    ['label' => grantApprovalBreadcrumbModuleLabel(), 'url' => grantApprovalBreadcrumbModuleUrl()],
    ['label' => 'Review & Workflow', 'url' => grantApprovalWorkflowListUrl()],
    ['label' => 'Approval Workflows', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad       = cradDb();
$workflows  = [];
$selectedId = (int) ($_GET['id'] ?? 0);
$detail     = null;
$dbError    = '';
$roleKey    = getCurrentUserRoleKey();
$roleLabel  = grantApprovalRoleLabel($roleKey);

if ($crad) {
    try {
        grantBackfillApprovalWorkflows($crad);
        $workflows = grantApprovalWorkflowList($crad);
        if ($selectedId <= 0 && $workflows !== []) {
            $selectedId = (int) ($workflows[0]['grant_application_id'] ?? 0);
        }
        if ($selectedId > 0) {
            $detail = grantGetApprovalWorkflowDetail($crad, $selectedId);
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('approval-workflows: ' . $e->getMessage());
    }
} else {
    $dbError = 'CRAD database connection unavailable.';
}

$inProgressCount = count(array_filter(
    $workflows,
    static fn(array $r): bool => (string) ($r['workflow_status'] ?? '') === 'In Progress'
));
$completedCount = count(array_filter(
    $workflows,
    static fn(array $r): bool => (string) ($r['workflow_status'] ?? '') === 'Completed'
));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/grant-approval-workflows.css?v=4" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="gaw-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;padding:.75rem 1rem;border-radius:8px;font-size:.88rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="gaw" data-grant-approval-live="1" data-selected-id="<?= (int) $selectedId ?>" data-monitor="<?= $isMonitor ? '1' : '0' ?>">

    <header class="gaw-page-header">
        <div class="gaw-page-header-main">
            <h1>
                Multi-Level Approval Pipeline
                <span class="gaw-live-badge">Live</span>
            </h1>
            <p>Track sequential institutional sign-offs: <?= htmlspecialchars(grantApprovalPipelineLabel()) ?></p>
        </div>
        <?php if ($isMonitor): ?>
        <div class="gaw-stat-row" id="gawMonitorStats">
            <span class="gaw-stat pending"><strong data-gaw-in-progress><?= $inProgressCount ?></strong> in progress</span>
            <span class="gaw-stat completed"><strong data-gaw-completed><?= $completedCount ?></strong> completed</span>
        </div>
        <?php endif; ?>
    </header>

    <?php if ($workflows === []): ?>
        <div class="gaw-pipeline-card">
            <div class="gaw-detail-empty">
                <?= smsIcon('inbox') ?>
                <?php if ($roleKey === 'finance'): ?>
                <p style="margin:0;font-weight:600;">No proposals pending Finance approval.</p>
                <p style="margin:.35rem 0 0;font-size:.84rem;">Proposals appear here in real time after VPAA signs off. Only items at the Finance step (Level 6) are listed.</p>
                <?php else: ?>
                <p style="margin:0;font-weight:600;">No proposals in the approval workflow yet.</p>
                <p style="margin:.35rem 0 0;font-size:.84rem;">Proposals appear here after the Review Committee recommends them for approval.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="gaw-project-bar">
            <label for="gawProjectSelect">Select Project to View Signature Trail:</label>
            <select id="gawProjectSelect" class="gaw-project-select" aria-label="Select project">
                <?php foreach ($workflows as $row): ?>
                    <?php
                    $appId = (int) ($row['grant_application_id'] ?? 0);
                    $ref   = (string) ($row['proposal_reference'] ?? 'Proposal');
                    $title = (string) ($row['research_title'] ?? 'Untitled');
                    $label = $ref . ': ' . $title;
                    ?>
                    <option value="<?= $appId ?>" <?= $appId === $selectedId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="gaw-pipeline-card" id="gawDetailPanel">
            <?php if ($detail === null): ?>
                <div class="gaw-detail-empty">
                    <?= smsIcon('tasks') ?>
                    <p style="margin:0;">Select a project to view its sign-off sequence.</p>
                </div>
            <?php else: ?>
                <?php
                $wf = $detail['workflow'];
                $steps = $detail['steps'];
                $ref = htmlspecialchars((string) ($wf['proposal_reference'] ?? 'Proposal'));
                $canAct = !empty($detail['can_act']);
                $wfStatus = (string) ($wf['workflow_status'] ?? '');
                $currentStepKey = (string) ($wf['current_step_key'] ?? '');
                $adviserEvalComplete = !empty($detail['adviser_eval_complete']);
                $needsAdviserScore = $roleKey === 'adviser'
                    && $currentStepKey === 'adviser'
                    && !$adviserEvalComplete
                    && $wfStatus === 'In Progress';
                ?>
                <h2 class="gaw-pipeline-title">Sign-off Sequence for <?= $ref ?></h2>

                <div class="gaw-stepper" id="gawStepper">
                    <?php foreach ($steps as $step): ?>
                        <?php
                        $display = grantApprovalStepDisplayState($step, $currentStepKey, $wfStatus);
                        $order = (int) ($step['step_order'] ?? 0);
                        ?>
                        <div class="gaw-step <?= htmlspecialchars($display['state']) ?>">
                            <div class="gaw-step-icon">
                                <?php if ($display['state'] === 'approved'): ?>
                                    <?= smsIcon('check') ?>
                                <?php else: ?>
                                    <?= $order ?>
                                <?php endif; ?>
                            </div>
                            <div class="gaw-step-name"><?= htmlspecialchars((string) ($step['step_label'] ?? '')) ?></div>
                            <div class="gaw-step-status"><?= htmlspecialchars($display['label']) ?></div>
                            <?php if ($display['date'] !== ''): ?>
                                <div class="gaw-step-date"><?= htmlspecialchars($display['date']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($needsAdviserScore): ?>
                <div class="gaw-action-panel gaw-action-panel-warn" id="gawAdviserScorePanel">
                    <h3><?= smsIcon('clipboard-check', ['class' => 'me-1']) ?>Adviser Evaluation Required</h3>
                    <p>Score this proposal in <strong>Reviewer Evaluation</strong> before you can sign and approve here.</p>
                    <a class="gaw-btn-approve" href="<?= htmlspecialchars(grantReviewerEvaluationUrl((int) $selectedId)) ?>" style="display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;">
                        <?= smsIcon('star-half-alt') ?> Go to Reviewer Evaluation
                    </a>
                </div>
                <?php elseif ($canAct): ?>
                <div class="gaw-action-panel" id="gawActionPanel">
                    <h3>Administrative Sign-off Action Panel</h3>
                    <p>Logged in as: <strong><?= htmlspecialchars($roleLabel) ?></strong>. Signatures are timestamped and logged in the permanent audit trail.</p>
                    <div class="gaw-action-buttons">
                        <button type="button" class="gaw-btn-approve" id="gawSignApproveBtn">
                            <?= smsIcon('signature') ?> Sign &amp; Approve Current Level
                        </button>
                        <button type="button" class="gaw-btn-return" id="gawReturnBtn">
                            <?= smsIcon('x') ?> Return to Proponent for Revision
                        </button>
                    </div>
                </div>
                <?php elseif ($isMonitor): ?>
                <div class="gaw-monitor-panel">
                    <?php if (!empty($detail['committee_eval']) || !empty($detail['adviser_eval'])): ?>
                    <div class="gaw-monitor-scores">
                        <?php if (!empty($detail['committee_eval'])): ?>
                        <span class="gaw-monitor-score-pill">
                            <?= smsIcon('users', ['class' => 'me-1']) ?>Committee:
                            <strong><?= number_format((float) ($detail['committee_eval']['total_score'] ?? 0), 1) ?>/100</strong>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($detail['adviser_eval'])): ?>
                        <span class="gaw-monitor-score-pill">
                            <?= smsIcon('user-tie', ['class' => 'me-1']) ?>Adviser:
                            <strong><?= number_format((float) ($detail['adviser_eval']['total_score'] ?? 0), 1) ?>/100</strong>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="gaw-monitor-note">
                        <?= smsIcon($wfStatus === 'Completed' ? 'check' : 'eye', ['class' => 'me-1']) ?>
                        <?php if ($detail['monitor_stage_hint'] !== ''): ?>
                            <?= htmlspecialchars((string) $detail['monitor_stage_hint']) ?>
                        <?php else: ?>
                            Monitoring mode — current stage:
                            <strong><?= htmlspecialchars((string) ($detail['current_step']['step_label'] ?? '')) ?></strong>
                            (<?= htmlspecialchars(grantApprovalRoleLabel((string) ($detail['current_step']['approver_role_key'] ?? ''))) ?>)
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div class="gaw-signature-dialog" id="gawSignDialog" role="dialog" aria-modal="true" aria-labelledby="gawSignTitle">
    <div class="gaw-signature-box">
        <h3 id="gawSignTitle" style="margin:0 0 .35rem;font-size:1rem;font-weight:800;">
            <?= smsIcon('signature') ?> Draw Your Signature
        </h3>
        <p style="margin:0;color:#64748b;font-size:.86rem;">Sign in the box below to approve the current level.</p>
        <div class="gaw-signature-canvas-wrap">
            <canvas id="gawSignCanvas" width="460" height="140"></canvas>
        </div>
        <div style="display:flex;gap:.5rem;justify-content:flex-end;flex-wrap:wrap;">
            <button type="button" class="gaw-btn-return gaw-btn-return-outline" id="gawSignClearBtn">Clear</button>
            <button type="button" class="gaw-btn-return gaw-btn-return-outline" id="gawSignCancelBtn">Cancel</button>
            <button type="button" class="gaw-btn-approve" id="gawSignConfirmBtn">
                <?= smsIcon('check') ?> Confirm Sign-off
            </button>
        </div>
    </div>
</div>

<div class="gaw-signature-dialog gaw-return-dialog" id="gawReturnDialog" role="dialog" aria-modal="true" aria-labelledby="gawReturnTitle">
    <div class="gaw-signature-box">
        <h3 id="gawReturnTitle" style="margin:0 0 .35rem;font-size:1rem;font-weight:800;color:#b91c1c;">
            <?= smsIcon('x') ?> Return for Revision
        </h3>
        <p style="margin:0 0 .75rem;color:#64748b;font-size:.86rem;">Provide remarks for the proponent. This will set the proposal status to Revision Required.</p>
        <textarea id="gawReturnRemarks" placeholder="Enter revision instructions…" required></textarea>
        <div style="display:flex;gap:.5rem;justify-content:flex-end;flex-wrap:wrap;margin-top:.75rem;">
            <button type="button" class="gaw-btn-return gaw-btn-return-outline" id="gawReturnCancelBtn">Cancel</button>
            <button type="button" class="gaw-btn-return" id="gawReturnConfirmBtn">
                <?= smsIcon('x') ?> Return to Proponent
            </button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/grant-approval-live.js?v=7"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
