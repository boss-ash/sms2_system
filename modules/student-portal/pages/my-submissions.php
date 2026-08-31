<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';
requireAuth();
$pageTitle = 'My Submissions';
$activeModule = 'student_portal';
$activePage = 'my-submissions';
$pageBannerIcon = 'fa-folder-open';
$pageBannerDescription = 'View and manage your research chapter submissions.';
$breadcrumbs = [['label' => 'Student Portal', 'url' => BASE_URL . '/modules/student-portal/pages/dashboard.php'], ['label' => 'My Submissions', 'url' => null]];
$crad = chapterDb();
$group = chapterRegisteredStudentGroup($crad);
$rows = $group ? chapterLatestSubmissionsForGroup($crad, (int) $group['id']) : [];
require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<div class="glass-dashboard" data-chapter-live="student" data-live-endpoint="<?= BASE_URL ?>/modules/crad/api/chapter-live.php?mode=student" data-document-base="<?= BASE_URL ?>/modules/crad/api/chapter-document.php?id=" data-registry-available="<?= $group ? '1' : '0' ?>">
    <?php if (!$group): ?>
        <?php chapterRenderUnavailableNotice(); ?>
    <?php else: ?>
    <section class="glass-panel p-4">
        <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0"><?= smsIcon('folder-open', ['class' => 'me-2 text-primary']) ?>My Submissions</h5><small class="text-muted" data-live-stamp>Live</small></div>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Chapter</th><th>Current Version</th><th>Submitted</th><th>Status</th><th>Evaluator</th><th>Last Updated</th><th>Actions</th></tr></thead><tbody data-student-submission-rows>
            <?php if (!$rows): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No chapter submissions yet.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr><td><?= e(chapterLabel((int) $row['chapter_number'])) ?></td><td>Version <?= (int) $row['version_number'] ?></td><td><?= e(chapterFormatDate((string) $row['submitted_at'])) ?></td><td><span class="badge text-bg-<?= e(chapterStatusClass((string) $row['status'])) ?>"><?= e($row['status']) ?></span></td><td><?= e($row['evaluator_name'] ?: '-') ?></td><td><?= e(chapterFormatDate((string) $row['updated_at'])) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= e(chapterDocumentUrl((int) $row['id'])) ?>" target="_blank"><?= smsIcon('eye', ['class' => 'me-1']) ?>View</a> <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/modules/student-portal/pages/submission-status.php"><?= smsIcon('chart-line', ['class' => 'me-1']) ?>Status</a></td></tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody></table></div>
    </section>
    <?php endif; ?>
</div>
<script src="<?= BASE_URL ?>/assets/js/chapter-evaluation-live.js"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
