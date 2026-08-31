<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';
requireAuth();
$pageTitle = 'Submission History';
$activeModule = 'student_portal';
$activePage = 'submission-history';
$pageBannerIcon = 'fa-history';
$pageBannerDescription = 'View the complete submission and revision history of your research chapters.';
$breadcrumbs = [['label' => 'Student Portal', 'url' => BASE_URL . '/modules/student-portal/pages/dashboard.php'], ['label' => 'Submission History', 'url' => null]];
$crad = chapterDb();
$group = chapterRegisteredStudentGroup($crad);
$history = $group ? chapterSubmissionHistoryForGroup($crad, (int) $group['id']) : [];
require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<div class="glass-dashboard" data-chapter-live="student" data-live-endpoint="<?= BASE_URL ?>/modules/crad/api/chapter-live.php?mode=student" data-registry-available="<?= $group ? '1' : '0' ?>">
    <?php if (!$group): ?>
        <?php chapterRenderUnavailableNotice(); ?>
    <?php else: ?>
    <section class="glass-panel p-4">
        <h5 class="mb-3"><?= smsIcon('history', ['class' => 'me-2 text-primary']) ?>Submission History</h5>
        <?php if (!$history): ?><div class="text-center text-muted py-5">No chapter submission history yet.</div>
        <?php else: ?><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Time</th><th>Chapter</th><th>Version</th><th>Status</th><th>Action</th><th>Actor</th><th>Detail</th></tr></thead><tbody>
            <?php foreach ($history as $event): ?><tr><td><?= e(chapterFormatDate((string) $event['created_at'])) ?></td><td><?= e(chapterLabel((int) $event['chapter_number'])) ?></td><td>Version <?= (int) $event['version_number'] ?></td><td><span class="badge text-bg-<?= e(chapterStatusClass((string) $event['status'])) ?>"><?= e($event['status']) ?></span></td><td><?= e(ucwords(str_replace('_', ' ', (string) $event['event_type']))) ?></td><td><?= e($event['actor_name'] ?: '-') ?></td><td><?= e($event['detail'] ?: '-') ?></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </section>
    <?php endif; ?>
</div>
<script src="<?= BASE_URL ?>/assets/js/chapter-evaluation-live.js"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
