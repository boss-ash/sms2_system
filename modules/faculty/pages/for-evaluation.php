<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';

$pageTitle    = 'For Evaluation';
$activeModule = 'faculty';
$activePage   = 'for-evaluation';
$pageBannerIcon = 'fa-clipboard-check';
$pageBannerDescription = 'Review research chapter submissions awaiting evaluation.';
$breadcrumbs  = [
    ['label' => 'Grammarian Portal', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'For Evaluation', 'url' => null],
];

requireAuth();
if (!chapterIsEvaluator()) {
    http_response_code(403);
    exit('Forbidden');
}
$crad = chapterDb();
$rows = chapterEvaluatorQueue($crad, false);

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="glass-dashboard" data-chapter-live="evaluator" data-live-endpoint="<?= BASE_URL ?>/modules/crad/api/chapter-live.php?mode=evaluator" data-document-base="<?= BASE_URL ?>/modules/crad/api/chapter-document.php?id=">
    <section class="glass-panel p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><?= smsIcon('clipboard-check', ['class' => 'me-2 text-primary']) ?>For Evaluation</h5>
            <small class="text-muted"><span data-evaluator-pending-count><?= count($rows) ?></span> pending · Live</small>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Research Group</th><th>Research Title</th><th>Chapter</th><th>Version</th><th>Submitted By</th><th>Submitted</th><th>Status</th><th>Action</th></tr></thead>
                <tbody data-evaluator-queue-rows>
                    <?php if (!$rows): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4"><strong>No Submissions for Evaluation</strong><br>There are currently no valid Chapter 1-3 submissions awaiting evaluation.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><strong><?= e($row['group_name']) ?></strong><div class="small text-muted"><?= e($row['group_number']) ?></div></td>
                                <td><?= e($row['research_title']) ?></td>
                                <td><?= e(chapterLabel((int) $row['chapter_number'])) ?></td>
                                <td>Version <?= (int) $row['version_number'] ?></td>
                                <td><?= e($row['submitted_by_name']) ?></td>
                                <td><?= e(chapterFormatDate((string) $row['submitted_at'])) ?></td>
                                <td><span class="badge text-bg-<?= e(chapterStatusClass((string) $row['status'])) ?>"><?= e($row['status']) ?></span></td>
                                <td><a class="btn btn-sm btn-sms-primary" href="<?= BASE_URL ?>/modules/faculty/pages/evaluation-scoring.php?id=<?= (int) $row['id'] ?>"><?= smsIcon('pen', ['class' => 'me-1']) ?>Review</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<script src="<?= BASE_URL ?>/assets/js/chapter-evaluation-live.js"></script>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
