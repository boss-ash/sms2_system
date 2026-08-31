<?php
/**
 * SMS 2 - CRAD · Approval Workflows
 * Sequential sign-off: Adviser → Dept. Chair → Dean → Research Office → VPAA → Finance
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-approval-helpers.php';

requireAuth();
grantRequireApprovalAccess();

$pageTitle             = 'Approval Workflows';
$activeModule          = grantApprovalActiveModuleKey();
$activePage            = 'approval-workflows';
$pageBannerIcon        = 'fa-tasks';
$isMonitor             = grantUserCanMonitorApprovalWorkflow();
$pageBannerDescription = $isMonitor
    ? 'Monitor sequential grant proposal approvals across all sign-off levels in real time.'
    : 'Review and sign off grant proposals assigned to your approval level.';

$breadcrumbs = [
    ['label' => grantApprovalBreadcrumbModuleLabel(), 'url' => grantApprovalBreadcrumbModuleUrl()],
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
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/grant-approval-workflows.css?v=1" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="mpl gaw" data-grant-approval-live="1" data-selected-id="<?= (int) $selectedId ?>">

<div class="mpl-toolbar" style="margin-bottom:1rem;">
    <div class="mpl-stat-row">
        <div class="mpl-stat">
            <span class="mpl-stat-label">In Progress</span>
            <strong id="gawStatInProgress"><?= (int) $inProgressCount ?></strong>
        </div>
        <div class="mpl-stat">
            <span class="mpl-stat-label">Completed</span>
            <strong id="gawStatCompleted"><?= (int) $completedCount ?></strong>
        </div>
        <div class="mpl-stat">
            <span class="mpl-stat-label">Total</span>
            <strong id="gawStatTotal"><?= count($workflows) ?></strong>
        </div>
    </div>
    <div class="mpl-toolbar-actions">
        <button type="button" class="mpl-btn mpl-btn-outline" id="gawRefreshBtn">
            <?= smsIcon('refresh') ?> Refresh
        </button>
    </div>
</div>

<div class="gaw-layout">
    <aside class="gaw-queue">
        <div class="gaw-queue-head">
            <h2><?= $isMonitor ? 'All Approval Workflows' : 'Pending Your Sign-off' ?></h2>
        </div>
        <div class="gaw-queue-list" id="gawQueueList">
            <?php if ($workflows === []): ?>
                <div class="gaw-detail-empty" style="padding:2rem 1rem;">
                    <?= smsIcon('inbox', ['style' => 'font-size:1.5rem;color:#94a3b8;margin-bottom:.5rem;']) ?>
                    <p style="margin:0;">No proposals in the approval workflow yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($workflows as $row): ?>
                    <?php
                    $appId = (int) ($row['grant_application_id'] ?? 0);
                    $ref   = htmlspecialchars((string) ($row['proposal_reference'] ?? 'Proposal'));
                    $title = htmlspecialchars((string) ($row['research_title'] ?? 'Untitled'));
                    $wfStatus = (string) ($row['workflow_status'] ?? '');
                    $stepLabel = htmlspecialchars((string) ($row['current_step_label'] ?? ''));
                    $pillClass = match ($wfStatus) {
                        'Completed' => 'completed',
                        'Returned'  => 'returned',
                        default     => 'in-progress',
                    };
                    ?>
                    <button type="button"
                            class="gaw-queue-item <?= $appId === $selectedId ? 'active' : '' ?>"
                            data-app-id="<?= $appId ?>">
                        <div class="gaw-queue-ref"><?= $ref ?> v<?= (int) ($row['current_version'] ?? 1) ?></div>
                        <div class="gaw-queue-title"><?= $title ?></div>
                        <div class="gaw-queue-meta">
                            <span class="gaw-status-pill <?= $pillClass ?>"><?= htmlspecialchars($wfStatus) ?></span>
                            <?php if ($stepLabel !== '' && $wfStatus === 'In Progress'): ?>
                                · <?= $stepLabel ?>
                            <?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>

    <section class="gaw-detail" id="gawDetailPanel">
        <?php if ($detail === null): ?>
            <div class="gaw-detail-empty">
                <?= smsIcon('tasks', ['style' => 'font-size:2rem;color:#94a3b8;margin-bottom:.5rem;']) ?>
                <p style="margin:0;">Select a proposal to view its sign-off sequence.</p>
            </div>
        <?php else: ?>
            <?php
            $wf = $detail['workflow'];
            $steps = $detail['steps'];
            $ref = htmlspecialchars((string) ($wf['proposal_reference'] ?? 'Proposal'));
            $canAct = !empty($detail['can_act']);
            $wfStatus = (string) ($wf['workflow_status'] ?? '');
            $currentStepKey = (string) ($wf['current_step_key'] ?? '');
            ?>
            <h2 class="gaw-detail-title">Sign-off Sequence for <?= $ref ?></h2>

            <div class="gaw-stepper" id="gawStepper">
                <?php foreach ($steps as $step): ?>
                    <?php
                    $display = grantApprovalStepDisplayState($step, $currentStepKey, $wfStatus);
                    $order = (int) ($step['step_order'] ?? 0);
                    ?>
                    <div class="gaw-step <?= htmlspecialchars($display['state']) ?>">
                        <div class="gaw-step-icon">
                            <?php if ($display['state'] === 'approved'): ?>
                                <?= smsIcon('check', ['style' => 'font-size:1rem;']) ?>
                            <?php else: ?>
                                <?= $order ?>
                            <?php endif; ?>
                        </div>
                        <div class="gaw-step-name"><?= htmlspecialchars((string) ($step['step_label'] ?? '')) ?></div>
                        <div class="gaw-step-status"><?= htmlspecialchars($display['label']) ?></div>
                        <?php if ($display['sub'] !== ''): ?>
                            <div class="gaw-step-sub"><?= htmlspecialchars($display['sub']) ?></div>
                        <?php endif; ?>
                        <?php if ($display['date'] !== ''): ?>
                            <div class="gaw-step-date"><?= htmlspecialchars($display['date']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($canAct): ?>
            <div class="gaw-action-panel" id="gawActionPanel">
                <h3>ADMINISTRATIVE SIGN-OFF ACTION PANEL</h3>
                <p>Logged in as: <strong><?= htmlspecialchars($roleLabel) ?></strong>. Signatures are timestamped and logged in the permanent audit trail.</p>
                <div class="gaw-action-buttons">
                    <button type="button" class="mpl-btn gaw-btn-approve" id="gawSignApproveBtn">
                        <?= smsIcon('signature') ?> Sign &amp; Approve Current Level
                    </button>
                    <button type="button" class="mpl-btn gaw-btn-return" id="gawReturnBtn">
                        <?= smsIcon('x') ?> Return to Proponent for Revision
                    </button>
                </div>
            </div>
            <?php elseif ($isMonitor && $wfStatus === 'In Progress'): ?>
            <p class="gaw-monitor-note">
                <?= smsIcon('eye', ['class' => 'me-1']) ?>
                Monitoring mode — current stage: <strong><?= htmlspecialchars((string) ($detail['current_step']['step_label'] ?? '')) ?></strong>
                (<?= htmlspecialchars(grantApprovalRoleLabel((string) ($detail['current_step']['approver_role_key'] ?? ''))) ?>)
            </p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
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
            <button type="button" class="mpl-btn mpl-btn-outline" id="gawSignClearBtn">Clear</button>
            <button type="button" class="mpl-btn mpl-btn-outline" id="gawSignCancelBtn">Cancel</button>
            <button type="button" class="mpl-btn gaw-btn-approve" id="gawSignConfirmBtn">
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
            <button type="button" class="mpl-btn mpl-btn-outline" id="gawReturnCancelBtn">Cancel</button>
            <button type="button" class="mpl-btn gaw-btn-return" id="gawReturnConfirmBtn">
                <?= smsIcon('x') ?> Return to Proponent
            </button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/grant-approval-live.js?v=1"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
