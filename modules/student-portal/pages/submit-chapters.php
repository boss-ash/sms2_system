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
                            <label class="form-label" for="chapter-document">Document</label>
                            <div class="sms-file-picker" data-sms-file-picker>
                                <input
                                    type="file"
                                    id="chapter-document"
                                    class="sms-file-picker__input"
                                    name="document"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    required>
                                <label class="sms-file-picker__zone" for="chapter-document">
                                    <span class="sms-file-picker__icon" aria-hidden="true"><?= smsIcon('upload') ?></span>
                                    <span class="sms-file-picker__title">Choose a file or drag it here</span>
                                    <span class="sms-file-picker__hint">PDF, DOC, DOCX, JPG, PNG · Max 10 MB</span>
                                    <span class="sms-file-picker__name" data-file-name>No file selected</span>
                                    <span class="sms-file-picker__btn">Browse files</span>
                                </label>
                            </div>
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

<style>
.sms-file-picker {
    position: relative;
}

.sms-file-picker__input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.sms-file-picker__zone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    min-height: 168px;
    padding: 1.25rem 1rem;
    border: 2px dashed var(--sms-primary-light, #3b82f6);
    border-radius: 14px;
    background: var(--sms-primary-xlight, rgba(219, 234, 254, 0.55));
    color: var(--sms-text, #334155);
    text-align: center;
    cursor: pointer;
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
}

.sms-file-picker__zone:hover,
.sms-file-picker__zone:focus-within {
    border-color: var(--sms-primary, #1e40af);
    background: rgba(219, 234, 254, 0.85);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
}

.sms-file-picker.is-dragover .sms-file-picker__zone {
    border-color: var(--sms-primary, #1e40af);
    background: rgba(219, 234, 254, 0.95);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.18);
}

.sms-file-picker.is-selected .sms-file-picker__zone {
    border-style: solid;
    border-color: var(--sms-success, #16a34a);
    background: rgba(22, 163, 74, 0.08);
}

.sms-file-picker__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(30, 64, 175, 0.12);
    color: var(--sms-primary, #1e40af);
}

.sms-file-picker__icon .ti {
    font-size: 1.45rem;
}

.sms-file-picker__title {
    color: var(--sms-heading, #0f172a);
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.3;
}

.sms-file-picker__hint {
    color: var(--sms-text-muted, #64748b);
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1.35;
}

.sms-file-picker__name {
    display: block;
    max-width: 100%;
    margin-top: 0.15rem;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.72);
    color: var(--sms-heading, #0f172a);
    font-size: 0.8rem;
    font-weight: 700;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sms-file-picker.is-selected .sms-file-picker__name {
    background: rgba(22, 163, 74, 0.14);
    color: var(--sms-success, #16a34a);
}

.sms-file-picker__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    margin-top: 0.2rem;
    padding: 0.55rem 1.1rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af, #2563eb);
    color: #fff;
    font-size: 0.86rem;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(30, 64, 175, 0.22);
}

.sms-file-picker__zone:hover .sms-file-picker__btn,
.sms-file-picker__zone:focus-within .sms-file-picker__btn {
    background: linear-gradient(135deg, #1e3a8a, #1e40af);
}

[data-theme="dark"] .sms-file-picker__zone {
    border-color: rgba(96, 165, 250, 0.55);
    background: rgba(30, 58, 138, 0.22);
}

[data-theme="dark"] .sms-file-picker__zone:hover,
[data-theme="dark"] .sms-file-picker__zone:focus-within,
[data-theme="dark"] .sms-file-picker.is-dragover .sms-file-picker__zone {
    border-color: #60a5fa;
    background: rgba(30, 58, 138, 0.34);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

[data-theme="dark"] .sms-file-picker.is-selected .sms-file-picker__zone {
    border-color: #34d399;
    background: rgba(16, 185, 129, 0.12);
}

[data-theme="dark"] .sms-file-picker__icon {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
}

[data-theme="dark"] .sms-file-picker__title {
    color: var(--sms-text-strong, #f1f5f9);
}

[data-theme="dark"] .sms-file-picker__name {
    background: rgba(15, 23, 42, 0.55);
    color: #e2e8f0;
}

[data-theme="dark"] .sms-file-picker.is-selected .sms-file-picker__name {
    background: rgba(16, 185, 129, 0.18);
    color: #6ee7b7;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-sms-file-picker]').forEach(function (picker) {
    var input = picker.querySelector('.sms-file-picker__input');
    var nameEl = picker.querySelector('[data-file-name]');
    if (!input || !nameEl) return;

    function updateName() {
      var file = input.files && input.files[0];
      if (!file) {
        picker.classList.remove('is-selected');
        nameEl.textContent = 'No file selected';
        return;
      }
      picker.classList.add('is-selected');
      nameEl.textContent = file.name;
    }

    input.addEventListener('change', updateName);

    ['dragenter', 'dragover'].forEach(function (eventName) {
      picker.addEventListener(eventName, function (event) {
        event.preventDefault();
        picker.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
      picker.addEventListener(eventName, function (event) {
        event.preventDefault();
        picker.classList.remove('is-dragover');
      });
    });

    picker.addEventListener('drop', function (event) {
      var files = event.dataTransfer && event.dataTransfer.files;
      if (!files || !files.length) return;
      if (typeof DataTransfer !== 'undefined') {
        var transfer = new DataTransfer();
        transfer.items.add(files[0]);
        input.files = transfer.files;
      }
      updateName();
    });
  });
});
</script>

<script src="<?= BASE_URL ?>/assets/js/chapter-evaluation-live.js"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
