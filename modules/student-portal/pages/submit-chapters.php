<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';

requireAuth();
if (getCurrentUserRoleKey() !== 'student') {
    http_response_code(403);
    exit('Forbidden');
}

$pageTitle = 'Submit Chapter 1-3';
$activeModule = 'student_portal';
$activePage = 'submit-chapters';
$pageBannerIcon = 'fa-file-upload';
$pageBannerDescription = 'Submit your Chapter 1, Chapter 2, and Chapter 3 research documents for evaluation.';
$breadcrumbs = [
    ['label' => 'Student Portal', 'url' => BASE_URL . '/modules/student-portal/pages/dashboard.php'],
    ['label' => 'Submit Chapter 1-3', 'url' => null],
];

$crad = chapterDb();
$group = chapterRegisteredStudentGroup($crad);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } elseif (!$group) {
        $error = chapterSubmissionUnavailableMessage();
    } else {
        $result = chapterSubmitDocument(
            $crad,
            $group,
            (int) ($_POST['chapter_number'] ?? 0),
            is_array($_FILES['document'] ?? null) ? $_FILES['document'] : [],
            trim((string) ($_POST['submission_notes'] ?? '')),
            trim((string) ($_POST['submission_token'] ?? ''))
        );
        if (!empty($result['ok'])) {
            $message = 'Chapter submitted successfully. Version ' . (int) $result['version'] . ' is now waiting for evaluation.';
        } else {
            $error = (string) ($result['error'] ?? 'Unable to submit document.');
        }
    }
}

$latest = $group ? chapterLatestSubmissionsForGroup($crad, (int) $group['id']) : [];
$latestByChapter = [];
foreach ($latest as $row) {
    $latestByChapter[(int) $row['chapter_number']] = $row;
}
$chapterEligibility = $group ? chapterSubmissionEligibility($crad, (int) $group['id']) : [];
$token = bin2hex(random_bytes(32));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>

<div class="glass-dashboard" data-chapter-live="student" data-live-endpoint="<?= BASE_URL ?>/modules/crad/api/chapter-live.php?mode=student" data-document-base="<?= BASE_URL ?>/modules/crad/api/chapter-document.php?id=" data-latest-update="<?= e($latest ? max(array_map(static fn($r) => (string) ($r['updated_at'] ?? ''), $latest)) : '') ?>" data-eligibility-update="<?= e($chapterEligibility ? max(array_map(static fn($r) => (string) ($r['approval']['approved_at'] ?? ''), $chapterEligibility)) : '') ?>" data-registry-available="<?= $group ? '1' : '0' ?>">
    <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

    <?php if (!$group): ?>
        <?php chapterRenderUnavailableNotice(); ?>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="glass-panel p-3 h-100"><small class="text-muted">Group Number</small><div class="fw-bold"><?= e($group['group_number']) ?></div></div></div>
            <div class="col-md-3"><div class="glass-panel p-3 h-100"><small class="text-muted">Group Name</small><div class="fw-bold"><?= e($group['group_name']) ?></div></div></div>
            <div class="col-md-4"><div class="glass-panel p-3 h-100"><small class="text-muted">Research Title</small><div class="fw-bold"><?= e($group['research_title']) ?></div></div></div>
            <div class="col-md-2"><div class="glass-panel p-3 h-100"><small class="text-muted">Academic Year</small><div class="fw-bold"><?= e($group['academic_year']) ?></div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <section class="glass-panel p-4">
                    <h5 class="mb-3"><?= smsIcon('upload', ['class' => 'me-2 text-primary']) ?>Submission Form</h5>
                    <form method="post" enctype="multipart/form-data" data-once-form>
                        <?= csrfField() ?>
                        <input type="hidden" name="submission_token" value="<?= e($token) ?>">
                        <div class="mb-3">
                            <label class="form-label">Chapter</label>
                            <select class="form-select" name="chapter_number" required>
                                <?php foreach (chapterAllowedChapters() as $num => $label):
                                    $current = $latestByChapter[$num] ?? null;
                                    $canRevise = $current && (string) $current['status'] === 'Needs Revision';
                                    $eligible = !empty($chapterEligibility[$num]['eligible']);
                                    $disabled = !$eligible || ($current && !$canRevise);
                                    $eligibilityLabel = $eligible ? 'Ready for Submission' : 'Adviser Approval Required';
                                ?>
                                    <option value="<?= $num ?>" <?= $disabled ? 'disabled' : '' ?>>
                                        <?= e($label) ?> - <?= e($eligibilityLabel) ?><?= $current ? ' - Latest: V' . (int) $current['version_number'] . ' ' . (string) $current['status'] : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Only chapters approved by your adviser are available for Grammarian submission.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Document</label>
                            <input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <div class="form-text">Allowed: PDF, DOC, DOCX, JPG, PNG. Max 10 MB.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Submission Notes</label>
                            <textarea class="form-control" name="submission_notes" rows="4" placeholder="Optional notes for the evaluator"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sms-primary w-100" data-submit-once>
                            <?= smsIcon('paper-plane', ['class' => 'me-2']) ?>Submit Chapter
                        </button>
                    </form>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="glass-panel p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><?= smsIcon('list-check', ['class' => 'me-2 text-primary']) ?>Current Versions</h5>
                        <small class="text-muted" data-live-stamp>Live</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Chapter</th><th>Version</th><th>Status</th><th>Updated</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach (chapterAllowedChapters() as $num => $label):
                                    $row = $latestByChapter[$num] ?? null; ?>
                                    <tr>
                                        <td><?= e($label) ?></td>
                                        <td><?= $row ? 'Version ' . (int) $row['version_number'] : '-' ?></td>
                                        <td><?= $row ? '<span class="badge text-bg-' . e(chapterStatusClass((string) $row['status'])) . '">' . e($row['status']) . '</span>' : '<span class="text-muted">No submission yet</span>' ?></td>
                                        <td><?= $row ? e(chapterFormatDate((string) $row['updated_at'])) : '-' ?></td>
                                        <td><?= $row ? '<a class="btn btn-sm btn-outline-primary" href="' . e(chapterDocumentUrl((int) $row['id'])) . '" target="_blank">' . smsIcon('eye') . '</a>' : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= BASE_URL ?>/assets/js/chapter-evaluation-live.js"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
