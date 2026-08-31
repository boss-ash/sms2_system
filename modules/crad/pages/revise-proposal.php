<?php
/**
 * SMS 2 - CORE SYSTEM · Revise Grant Proposal
 * Upload revised PDF and resubmit the same proposal reference.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-helpers.php';
require_once __DIR__ . '/../includes/grant-evaluation-helpers.php';

requireAuth();
grantRequireViewAccess();

if (!grantUserCanApply()) {
    grantRedirectUnauthorized();
}

$applicationId = (int) ($_GET['id'] ?? 0);
$crad          = cradDb();
$application   = null;
$evaluation    = null;
$versions      = [];
$dbError       = '';

if ($crad && $applicationId > 0) {
    try {
        grantEnsureTables($crad);
        grantEnsureEvaluationTables($crad);
        $application = grantGetApplicationForResearcher($crad, $applicationId);
        if ($application && (string) ($application['status'] ?? '') !== 'Revision Required') {
            $application = null;
            $dbError = 'This proposal is not awaiting revision.';
        } elseif ($application) {
            $evaluation = grantGetLatestEvaluationsForApplications($crad, [$applicationId])[$applicationId] ?? null;
            $versions   = grantGetProposalVersions($crad, $applicationId);
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('revise-proposal: ' . $e->getMessage());
    }
} elseif ($applicationId <= 0) {
    $dbError = 'Invalid proposal selected.';
} else {
    $dbError = 'CRAD database connection unavailable.';
}

$pageTitle            = 'Revise Proposal';
$activeModule         = grantActiveModuleKey();
$activePage           = 'revisions-requested';
$hideModulePageBanner = true;

$breadcrumbs = [
    ['label' => grantBreadcrumbModuleLabel(), 'url' => grantBreadcrumbModuleUrl()],
    ['label' => 'Revisions Requested', 'url' => grantRevisionsRequestedUrl()],
    ['label' => 'Revise Proposal', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);

$ref         = (string) ($application['proposal_reference'] ?? '');
$nextVersion = max(1, (int) ($application['current_version'] ?? 1)) + 1;
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<?php if ($dbError !== '' || !$application): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= htmlspecialchars($dbError ?: 'Proposal not found.') ?>
</div>
<p><a href="<?= htmlspecialchars(grantRevisionsRequestedUrl()) ?>"><?= smsIcon('arrow-left') ?> Back to Revisions Requested</a></p>
<?php else: ?>

<div class="mpl gre-layout" style="display:block;max-width:920px;">
    <a class="mpl-btn mpl-btn-ghost mpl-btn-sm mb-3" href="<?= htmlspecialchars(grantRevisionsRequestedUrl()) ?>">
        <?= smsIcon('arrow-left') ?> Back to Revisions Requested
    </a>

    <section class="mpl-panel mb-3">
        <div class="mpl-panel-head">
            <div>
                <h2><?= htmlspecialchars($ref !== '' ? $ref : 'Grant Proposal') ?></h2>
                <p><?= htmlspecialchars((string) ($application['research_title'] ?? '')) ?></p>
            </div>
            <span class="mpl-status processing">REVISION REQUIRED</span>
        </div>

        <dl class="gre-meta" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;padding:0 1rem 1rem;">
            <div><dt>Grant Program</dt><dd><?= htmlspecialchars((string) $application['funding_title']) ?></dd></div>
            <div><dt>Current Version</dt><dd>Version <?= (int) ($application['current_version'] ?? 1) ?> — <?= htmlspecialchars(grantVersionLabel((int) ($application['current_version'] ?? 1))) ?></dd></div>
            <div><dt>Next Version</dt><dd>Version <?= $nextVersion ?> — <?= htmlspecialchars(grantVersionLabel($nextVersion)) ?></dd></div>
        </dl>

        <?php if ($evaluation): ?>
        <div style="margin:0 1rem 1rem;padding:1rem;background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:10px;">
            <div style="font-size:.68rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:#b45309;margin-bottom:.5rem;">Committee Feedback</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.5rem .75rem;font-size:.85rem;margin-bottom:.75rem;">
                <div><strong>Reviewer:</strong> <?= htmlspecialchars((string) ($evaluation['evaluator_name'] ?? 'Review Committee')) ?></div>
                <div><strong>Score:</strong> <?= number_format((float) ($evaluation['total_score'] ?? 0), 1) ?> / 100</div>
                <div><strong>Reviewed:</strong> <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) ($evaluation['submitted_at'] ?? 'now')))) ?></div>
            </div>
            <?php if (!empty($evaluation['comments'])): ?>
            <div style="margin-bottom:.5rem;"><strong>Comments</strong><p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars((string) $evaluation['comments']) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($evaluation['revision_reason'])): ?>
            <div style="margin-bottom:.5rem;"><strong>Revision Reason</strong><p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars((string) $evaluation['revision_reason']) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($evaluation['required_corrections'])): ?>
            <div><strong>Required Corrections</strong><p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars((string) $evaluation['required_corrections']) ?></p></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($versions !== []): ?>
        <div style="padding:0 1rem 1rem;">
            <div style="font-size:.68rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--sms-text-muted);margin-bottom:.45rem;">Version History</div>
            <ul style="margin:0;padding-left:1.1rem;font-size:.84rem;">
                <?php foreach ($versions as $ver): ?>
                <li>Version <?= (int) $ver['version_number'] ?> — <?= htmlspecialchars((string) ($ver['version_label'] ?: grantVersionLabel((int) $ver['version_number']))) ?>
                    <?php if (!empty($ver['proposal_pdf_original'])): ?>
                        (<?= htmlspecialchars((string) $ver['proposal_pdf_original']) ?>)
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </section>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2><?= smsIcon('upload', ['class' => 'me-1']) ?>Upload Revised Proposal</h2>
                <p>Same reference <strong><?= htmlspecialchars($ref) ?></strong> — a new version will be saved. No new proposal record is created.</p>
            </div>
        </div>

        <div id="reviseAlert" class="mpl-alert" style="display:none;" role="alert"></div>

        <form id="reviseProposalForm" class="p-3" data-no-loader enctype="multipart/form-data" novalidate>
            <input type="hidden" name="grant_application_id" value="<?= (int) $application['id'] ?>">

            <div class="gre-form-group">
                <label for="proposal_pdf" class="go-form-label">Revised Proposal PDF <span class="text-danger">*</span></label>
                <input type="file" id="proposal_pdf" name="proposal_pdf" class="go-form-input" accept=".pdf,.doc,.docx" required>
            </div>

            <div class="gre-form-group">
                <label for="supporting_docs" class="go-form-label">Supporting Documents (optional)</label>
                <input type="file" id="supporting_docs" name="supporting_docs" class="go-form-input" accept=".pdf,.doc,.docx,.zip">
            </div>

            <div class="gre-form-group">
                <label for="ethics_doc" class="go-form-label">Ethics Clearance (optional)</label>
                <input type="file" id="ethics_doc" name="ethics_doc" class="go-form-input" accept=".pdf,.doc,.docx">
            </div>

            <div class="gre-form-group">
                <label for="researcher_notes" class="go-form-label">Notes to Review Committee (optional)</label>
                <textarea id="researcher_notes" name="researcher_notes" class="go-form-input" rows="3"
                          placeholder="Summarize what you changed in this revision…"></textarea>
            </div>

            <button type="submit" class="mpl-btn mpl-btn-primary" id="reviseSubmitBtn">
                <?= smsIcon('paper-plane', ['class' => 'me-1']) ?>Resubmit Proposal
            </button>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var form = document.getElementById('reviseProposalForm');
    if (!form) return;

    var apiBase = '<?= BASE_URL ?>/modules/crad/api/grant-management.php';
    var alertEl = document.getElementById('reviseAlert');
    var btn = document.getElementById('reviseSubmitBtn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (window.SMS2Loader) window.SMS2Loader.forceHide();
        alertEl.style.display = 'none';

        var fileInput = document.getElementById('proposal_pdf');
        if (!fileInput || !fileInput.files || !fileInput.files.length) {
            alertEl.style.display = '';
            alertEl.style.background = 'rgba(239,68,68,.08)';
            alertEl.style.color = '#b91c1c';
            alertEl.textContent = 'Please upload your revised proposal PDF.';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Resubmitting…';

        var fd = new FormData(form);
        fd.set('action', 'resubmit_proposal');

        fetch(apiBase, { method: 'POST', credentials: 'same-origin', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.href = '<?= BASE_URL ?>/modules/crad/pages/proposals-applications.php?resubmitted=1';
                } else {
                    alertEl.style.display = '';
                    alertEl.style.background = 'rgba(239,68,68,.08)';
                    alertEl.style.color = '#b91c1c';
                    alertEl.textContent = data.message || 'Failed to resubmit proposal.';
                    btn.disabled = false;
                    btn.innerHTML = '<?= smsIcon('paper-plane', ['class' => 'me-1']) ?>Resubmit Proposal';
                }
            })
            .catch(function () {
                alertEl.style.display = '';
                alertEl.style.background = 'rgba(239,68,68,.08)';
                alertEl.style.color = '#b91c1c';
                alertEl.textContent = 'Network error. Please try again.';
                btn.disabled = false;
                btn.innerHTML = '<?= smsIcon('paper-plane', ['class' => 'me-1']) ?>Resubmit Proposal';
            });
    });
});
</script>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
