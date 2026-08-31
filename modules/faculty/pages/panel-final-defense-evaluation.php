<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/modules/faculty/includes/final-defense-evaluation.php';

$pageTitle = 'Final Defense Evaluation';
$activeModule = 'faculty';
$activePage = 'panel-final-defense-evaluation';
$breadcrumbs = [
    ['label' => 'Panel Portal', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Final Defense Evaluation', 'url' => null],
];

require_once ROOT_PATH . '/includes/layout-start.php';
finalDefenseRequirePanelMember();
renderBreadcrumbs($breadcrumbs);

$crad = finalDefenseDb();
$message = '';
$error = '';
$selectedId = (int) ($_GET['id'] ?? 0);
$showHistory = (($_GET['history'] ?? '') === '1');

if (!$crad instanceof PDO) {
    $error = 'CRAD database connection is unavailable.';
} else {
    finalDefenseEnsureSchema($crad);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_final_evaluation') {
        if (!csrfVerify()) {
            $error = 'Security check failed. Please refresh and try again.';
        } else {
            $selectedId = (int) ($_POST['schedule_id'] ?? 0);
            $result = finalDefenseSubmitEvaluation($crad, $selectedId, $_POST);
            if (!empty($result['ok'])) {
                $message = (string) ($result['message'] ?? 'Evaluation submitted successfully.');
                $selectedId = 0;
            } else {
                $error = (string) ($result['error'] ?? 'Unable to submit evaluation.');
            }
        }
    }
}

$defense = $crad instanceof PDO && $selectedId > 0 ? finalDefenseAssignedSchedule($crad, $selectedId) : null;
if ($defense && $defense['evaluation_id'] !== null) {
    $defense = null;
    if ($error === '') {
        $error = 'This Final Defense already has your evaluation.';
    }
}
$rows = $crad instanceof PDO ? finalDefenseRows($crad, $showHistory) : [];
?>

<div class="glass-dashboard">
    <div class="glass-board">
        <?php if ($message !== ''): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

        <?php if (!$defense): ?>
            <section class="glass-panel p-4">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <h5 class="mb-0"><?= smsIcon('clipboard-check', ['class' => 'me-2 text-primary']) ?>Final Defenses for Evaluation</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/modules/faculty/pages/panel-final-defense-evaluation.php<?= $showHistory ? '' : '?history=1' ?>">
                            <?= smsIcon('history', ['class' => 'me-1']) ?><?= $showHistory ? 'Pending Evaluations' : 'Evaluation History' ?>
                        </a>
                        <span class="badge text-bg-primary"><?= count($rows) ?> Records</span>
                    </div>
                </div>
                <?php if (!$rows): ?>
                    <div class="text-center text-muted py-5">No Final Defense records found in this view.</div>
                <?php else: ?>
                    <div class="table-responsive"><table class="table align-middle mb-0">
                        <thead><tr><th>Research Group</th><th>Research Title</th><th>Date / Time</th><th>Venue</th><th><?= $showHistory ? 'Result' : 'Action' ?></th></tr></thead>
                        <tbody><?php foreach ($rows as $row): ?>
                            <tr>
                                <td><strong><?= e((string) (($row['research_group'] ?? '') ?: 'Research Group')) ?></strong><div class="small text-muted"><?= e((string) ($row['group_number'] ?? '')) ?></div></td>
                                <td><?= e((string) ($row['research_title'] ?? '')) ?></td>
                                <td><?= e(date('M j, Y h:i A', strtotime((string) $row['defense_datetime']))) ?></td>
                                <td><?= e((string) (($row['venue'] ?? '') ?: 'TBA')) ?></td>
                                <td><?php if ($showHistory): ?>
                                    <span class="badge text-bg-success"><?= e((string) ($row['panel_result'] ?? 'Submitted')) ?></span>
                                    <div class="small text-muted mt-1"><?= e((string) ($row['panel_score'] ?? '')) ?></div>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>/modules/faculty/pages/panel-final-defense-evaluation.php?id=<?= (int) $row['id'] ?>"><?= smsIcon('pen', ['class' => 'me-1']) ?>Evaluate</a>
                                <?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?></tbody>
                    </table></div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="glass-panel p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div><h5 class="mb-1">Final Defense Evaluation</h5><div class="text-muted"><?= e((string) ($defense['research_group'] ?? 'Research Group')) ?></div></div>
                    <span class="badge text-bg-success">Final Defense</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><small class="text-muted">Research Title</small><div class="fw-bold"><?= e((string) ($defense['research_title'] ?? '')) ?></div></div>
                    <div class="col-md-3"><small class="text-muted">Date</small><div><?= e(date('M j, Y', strtotime((string) $defense['defense_datetime']))) ?></div></div>
                    <div class="col-md-3"><small class="text-muted">Panel Member</small><div><?= e(getCurrentUserName()) ?></div></div>
                </div>
                <form method="post">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="submit_final_evaluation">
                    <input type="hidden" name="schedule_id" value="<?= (int) $defense['id'] ?>">
                    <?php foreach (finalDefenseRubric() as $criterion): ?>
                        <div class="mb-3"><label class="form-label"><?= e($criterion['label']) ?> Score</label><input type="number" class="form-control" name="<?= e($criterion['key']) ?>_score" min="0" max="100" step="0.01" required></div>
                    <?php endforeach; ?>
                    <div class="mb-3"><label class="form-label">Remarks</label><textarea class="form-control" name="remarks" rows="4"></textarea></div>
                    <div class="mb-3"><label class="form-label">Result</label><select class="form-select" name="result" required><option value="">Select result...</option><option value="APPROVED">APPROVED</option><option value="APPROVED WITH REVISION">APPROVED WITH REVISION</option><option value="FAILED">FAILED</option></select></div>
                    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary"><?= smsIcon('check', ['class' => 'me-1']) ?>Submit Final Defense Evaluation</button><a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/modules/faculty/pages/panel-final-defense-evaluation.php">Back</a></div>
                </form>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
