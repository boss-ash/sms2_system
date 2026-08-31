<?php
/**
 * Faculty Adviser - Final Defense Revision Monitoring
 * Tracks post-final-defense revisions until adviser compliance confirmation.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once ROOT_PATH . '/modules/crad/includes/final-phase-helpers.php';

requireAuth();
if (getCurrentUserRoleKey() !== 'adviser') {
    http_response_code(403);
    exit('Forbidden');
}

$crad = cradDb();
$adviserUserId = (int) ($_SESSION['user_id'] ?? 0);
$adviserEmail = strtolower(trim((string) ($_SESSION['user_email'] ?? '')));
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $groupId = (int) ($_POST['group_id'] ?? 0);
        $status = trim((string) ($_POST['revision_status'] ?? ''));
        if (fpSetFinalDefenseRevisionStatus($crad, $groupId, $adviserUserId, $adviserEmail, $status)) {
            $message = 'Final Defense revision compliance status updated.';
        } else {
            $error = 'Unable to update this revision cycle. Confirm that the group is assigned to you and all panel evaluations are available.';
        }
    }
}

$groups = fpGetFinalDefenseRevisionGroups($crad, $adviserUserId, $adviserEmail);
$selectedGroupId = (int) ($_GET['group'] ?? $_POST['group_id'] ?? 0);
$selected = $selectedGroupId > 0
    ? fpGetFinalDefenseRevisionDetail($crad, $selectedGroupId, $adviserUserId, $adviserEmail)
    : null;

$pageTitle = 'Final Defense Revision Monitoring';
$activeModule = 'faculty';
$activePage = 'final-defense-revision-monitoring';
$breadcrumbs = [
    ['label' => 'Faculty', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Research Monitoring', 'url' => null],
    ['label' => 'Final Defense Revision Monitoring', 'url' => null],
];

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>

<div class="glass-dashboard">
    <div class="glass-board">
        <?php if ($message !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="glass-panel mb-4">
            <div class="glass-panel-body">
                <h5 class="glass-panel-title"><?= smsIcon('redo', ['class' => 'me-2']) ?>Final Defense Revision Monitoring</h5>
                <p class="text-muted mb-0">Groups appear here when every assigned Final Defense panel member returns APPROVED WITH REVISION. Review their remarks and confirm compliance after the researcher submits the required revisions.</p>
            </div>
        </div>

        <?php if (!$groups): ?>
            <div class="glass-panel"><div class="glass-panel-body text-center py-5">
                <?= smsIcon('clipboard-check', ['class' => 'fa-2x text-muted mb-3']) ?>
                <h6>No Final Defense revision cases</h6>
                <p class="text-muted mb-0">No assigned group currently has a complete panel consensus requiring post-defense revision.</p>
            </div></div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($groups as $group): ?>
                    <?php
                    $groupId = (int) $group['research_group_id'];
                    $status = (string) ($group['revision_status'] ?: 'Needs Revision');
                    $detail = fpGetFinalDefenseRevisionDetail($crad, $groupId, $adviserUserId, $adviserEmail);
                    ?>
                    <div class="col-xl-6">
                        <div class="glass-panel h-100"><div class="glass-panel-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-primary"><?= htmlspecialchars((string) $group['group_number']) ?></span>
                                    <h6 class="mt-2 mb-1"><?= htmlspecialchars((string) ($group['group_name'] ?: 'Research Group')) ?></h6>
                                    <div class="text-muted small"><?= htmlspecialchars((string) $group['research_title']) ?></div>
                                </div>
                                <span class="badge <?= $status === 'Compliant' ? 'bg-success' : ($status === 'Under Review' ? 'bg-warning text-dark' : 'bg-danger') ?>"><?= htmlspecialchars($status) ?></span>
                            </div>
                            <div class="small text-muted mb-3">
                                <?= smsIcon('gavel', ['class' => 'me-1']) ?> Final Defense: <?= !empty($group['defense_datetime']) ? htmlspecialchars(date('M j, Y g:i A', strtotime((string) $group['defense_datetime']))) : 'Not recorded' ?>
                                &middot; Panel: <?= (int) $group['awr_count'] ?>/<?= (int) $group['assigned_panel_count'] ?> revisions
                            </div>
                            <div class="alert <?= !empty($group['revision_submitted']) ? 'alert-info' : 'alert-warning' ?> py-2 small">
                                <i class="fas <?= !empty($group['revision_submitted']) ? 'fa-file-check' : 'fa-hourglass-half' ?> me-1"></i>
                                <?php if (!empty($group['revision_submitted'])): ?>
                                    Revision submitted: <?= htmlspecialchars((string) ($group['revision_update_title'] ?: 'Progress update')) ?>
                                    <?php if (!empty($group['revision_submitted_at'])): ?> on <?= htmlspecialchars(date('M j, Y g:i A', strtotime((string) $group['revision_submitted_at']))) ?><?php endif; ?>.
                                <?php else: ?>
                                    Waiting for the researcher to submit a revision progress update.
                                <?php endif; ?>
                            </div>
                            <h6 class="small fw-bold">Panel Remarks</h6>
                            <?php foreach (($detail['panel_evaluations'] ?? []) as $panel): ?>
                                <div class="border rounded p-2 mb-2 small">
                                    <strong><?= htmlspecialchars((string) ($panel['panel_name'] ?: 'Panel Member')) ?></strong>
                                    <span class="text-muted ms-1">(<?= htmlspecialchars((string) $panel['result']) ?>)</span>
                                    <div class="mt-1"><?= nl2br(htmlspecialchars((string) ($panel['remarks'] ?: 'No remarks provided.'))) ?></div>
                                </div>
                            <?php endforeach; ?>
                            <form method="post" class="d-flex align-items-center gap-2 flex-wrap mt-3">
                                <?= csrfField() ?>
                                <input type="hidden" name="group_id" value="<?= $groupId ?>">
                                <select class="form-select form-select-sm" name="revision_status" style="max-width:220px" aria-label="Revision compliance status">
                                    <?php foreach (['Needs Revision', 'Under Review', 'Compliant'] as $option): ?>
                                        <option value="<?= htmlspecialchars($option) ?>" <?= $status === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary btn-sm" type="submit"><?= smsIcon('save', ['class' => 'me-1']) ?>Save Compliance Status</button>
                            </form>
                            <?php if ($status === 'Compliant'): ?>
                                <div class="alert alert-success mt-3 mb-0 py-2 small"><?= smsIcon('check-circle', ['class' => 'me-1']) ?>Eligible for Final Manuscript Approval checks.</div>
                            <?php endif; ?>
                        </div></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
