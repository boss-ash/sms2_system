<?php
/**
 * SMS 2 - CORE SYSTEM · Approved & Funded
 * Proposals that completed all six institutional approval levels (Finance final sign-off).
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-helpers.php';
require_once __DIR__ . '/../includes/grant-approval-helpers.php';
require_once __DIR__ . '/../includes/grant-funding-helpers.php';

requireAuth();
grantRequireViewAccess();

$pageTitle            = 'Approved & Funded';
$activeModule         = grantActiveModuleKey();
$activePage           = 'approved-funded';
$hideModulePageBanner = true;
$canManage            = grantUserCanManage();

$breadcrumbs = [
    ['label' => grantBreadcrumbModuleLabel(), 'url' => grantBreadcrumbModuleUrl()],
    ['label' => 'Proposals & Applications', 'url' => BASE_URL . '/modules/crad/pages/proposals-applications.php'],
    ['label' => 'Approved & Funded', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad    = cradDb();
$records = [];
$dbError = '';

if ($crad) {
    try {
        grantEnsureApprovalTables($crad);
        $records = grantGetApprovedFundedApplications($crad);
        if (!$canManage) {
            $userId = (int) ($_SESSION['user_id'] ?? 0);
            $records = array_values(array_filter(
                $records,
                static fn(array $row): bool => (int) ($row['applicant_user_id'] ?? 0) === $userId
            ));
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('approved-funded: ' . $e->getMessage());
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

<div class="mpl">
    <div class="mpl-top">
        <p>Grant proposals with status <strong>APPROVED &amp; FUNDED</strong> after Adviser → Department Chair → Dean → Research Office → VPAA → Finance sign-offs.</p>
        <div class="mpl-toolbar">
            <a class="mpl-btn mpl-btn-ghost" href="<?= BASE_URL ?>/modules/crad/pages/proposals-applications.php">
                <?= smsIcon('file-alt') ?> All Proposals
            </a>
            <?php if ($canManage): ?>
            <a class="mpl-btn mpl-btn-primary" href="<?= htmlspecialchars(grantBudgetDisbursementUrl()) ?>">
                <?= smsIcon('money-bill-wave') ?> Budget &amp; Disbursement
            </a>
            <a class="mpl-btn mpl-btn-ghost" href="<?= BASE_URL ?>/modules/crad/pages/approval-workflows.php">
                <?= smsIcon('tasks') ?> Approval Workflows
            </a>
            <?php endif; ?>
        </div>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Approved &amp; Funded</h2>
                <p>Final funding clearance recorded by Finance Office (Approval Level 6).</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Grant Program</th>
                        <th>Lead Proponent</th>
                        <th>Research Title</th>
                        <th>Budget</th>
                        <th>Funded On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($records === []): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2rem;color:var(--sms-text-muted);">
                            No proposals are approved and funded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td style="font-weight:800;color:var(--sms-primary);white-space:nowrap;">
                            <?= htmlspecialchars((string) ($row['proposal_reference'] ?? ('#' . (int) $row['id']))) ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($row['funding_title'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars((string) ($row['applicant_name'] ?? '—')) ?></td>
                        <td style="max-width:240px;font-size:.86rem;"><?= htmlspecialchars((string) ($row['research_title'] ?? '—')) ?></td>
                        <td style="font-weight:700;white-space:nowrap;">
                            <?= $row['requested_budget'] !== null ? '₱' . number_format((float) $row['requested_budget'], 0) : '—' ?>
                        </td>
                        <td style="font-size:.82rem;white-space:nowrap;">
                            <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) ($row['updated_at'] ?? 'now')))) ?>
                        </td>
                        <td><span class="mpl-status completed">APPROVED &amp; FUNDED</span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
