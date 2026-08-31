<?php
/**
 * SMS 2 - CORE SYSTEM · Revisions Requested
 * Researcher view of grant proposals returned by the review committee.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-helpers.php';
require_once __DIR__ . '/../includes/grant-evaluation-helpers.php';
require_once __DIR__ . '/../includes/grant-approval-helpers.php';

requireAuth();
grantRequireViewAccess();

if (!grantUserCanApply()) {
    grantRedirectUnauthorized();
}

$pageTitle             = 'Revisions Requested';
$activeModule          = grantActiveModuleKey();
$activePage            = 'revisions-requested';
$hideModulePageBanner  = true;

$breadcrumbs = [
    ['label' => grantBreadcrumbModuleLabel(), 'url' => grantBreadcrumbModuleUrl()],
    ['label' => 'Proposals & Applications', 'url' => BASE_URL . '/modules/crad/pages/proposals-applications.php'],
    ['label' => 'Revisions Requested', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad      = cradDb();
$revisions = [];
$feedback  = [];
$approvalReturns = [];
$dbError   = '';

if ($crad) {
    try {
        grantEnsureTables($crad);
        grantEnsureEvaluationTables($crad);
        grantEnsureApprovalTables($crad);
        $revisions = grantGetMyRevisionRequiredApplications($crad);
        if ($revisions !== []) {
            $ids = array_map(static fn(array $r): int => (int) ($r['id'] ?? 0), $revisions);
            $feedback = grantGetLatestEvaluationsForApplications($crad, $ids);
            $approvalReturns = grantGetLatestApprovalReturnsForApplications($crad, $ids);
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('revisions-requested: ' . $e->getMessage());
    }
} else {
    $dbError = 'CRAD database connection unavailable.';
}

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="mpl-alert" role="alert" style="background:rgba(239,68,68,.08);color:#b91c1c;margin-bottom:1rem;">
    <?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?>
</div>
<?php endif; ?>

<div class="mpl" data-grant-live="1" data-revisions-page="1">

    <div class="mpl-top">
        <p>Proposals returned for revision — from the review committee or an approval level (Adviser through Finance). View the return reason, then revise and resubmit.</p>
        <div class="mpl-toolbar">
            <a class="mpl-btn mpl-btn-ghost" href="<?= BASE_URL ?>/modules/crad/pages/proposals-applications.php">
                <?= smsIcon('file-alt') ?> All Proposals
            </a>
        </div>
    </div>

    <?php if ($dbError === ''): ?>
    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Revisions Requested</h2>
                <p>Same proposal reference — upload a new version when you are ready to resubmit (loops back to committee review).</p>
            </div>
            <span class="gre-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="revisionsTable">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Grant Program</th>
                        <th>Research Title</th>
                        <th>Returned By</th>
                        <th>Reason</th>
                        <th>Returned</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="revisionsTableBody">
                <?php if (empty($revisions)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2rem;color:var(--sms-text-muted);">
                            No proposals currently require revision.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($revisions as $row):
                        $eval = $feedback[(int) $row['id']] ?? null;
                        $ref  = (string) ($row['proposal_reference'] ?? ('#' . (int) $row['id']));
                        $ver  = max(1, (int) ($row['current_version'] ?? 1));
                    ?>
                    <tr data-app-id="<?= (int) $row['id'] ?>">
                        <td style="font-weight:800;color:var(--sms-primary);"><?= htmlspecialchars($ref) ?></td>
                        <td style="font-weight:600;max-width:180px;"><?= htmlspecialchars((string) $row['funding_title']) ?></td>
                        <td style="max-width:240px;font-size:.86rem;"><?= htmlspecialchars((string) ($row['research_title'] ?? '—')) ?></td>
                        <td>Version <?= $ver ?> — <?= htmlspecialchars(grantVersionLabel($ver)) ?></td>
                        <td style="font-size:.82rem;white-space:nowrap;">
                            <?= $eval
                                ? htmlspecialchars(date('M d, Y g:i A', strtotime((string) $eval['submitted_at'])))
                                : htmlspecialchars(date('M d, Y g:i A', strtotime((string) $row['updated_at']))) ?>
                        </td>
                        <td>
                            <a class="mpl-btn mpl-btn-primary mpl-btn-sm"
                               href="<?= htmlspecialchars(grantReviseProposalUrl((int) $row['id'])) ?>">
                                <?= smsIcon('edit') ?> Revise Proposal
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</div>

<script src="<?= BASE_URL ?>/assets/js/grant-opportunities-live.js?v=2"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
