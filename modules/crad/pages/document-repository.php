<?php
/**
 * SMS 2 - Outputs & Records · Document Repository
 * CRAD Staff — Archive permanent grant research records.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-document-repository-helpers.php';

requireAuth();
grantRequireDocumentRepositoryAccess();

$pageTitle            = 'Document Repository';
$activeModule         = grantActiveModuleKey();
$activePage           = 'document-repository';
$hideModulePageBanner = true;

$breadcrumbs = [
    ['label' => grantBreadcrumbModuleLabel(), 'url' => grantBreadcrumbModuleUrl()],
    ['label' => 'Outputs & Records', 'url' => grantDocumentRepositoryUrl()],
    ['label' => 'Document Repository', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad       = cradDb();
$overview   = [];
$selectedId = (int) ($_GET['id'] ?? 0);
$detail     = null;
$dbError    = '';

if ($crad) {
    try {
        $overview = grantGetDocumentRepositoryOverview($crad);
        if ($selectedId <= 0 && $overview !== []) {
            $selectedId = (int) ($overview[0]['grant_application_id'] ?? 0);
        }
        if ($selectedId > 0) {
            $detail = grantGetDocumentRepositoryDetail($crad, $selectedId);
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('document-repository: ' . $e->getMessage());
    }
} else {
    $dbError = 'CRAD database connection unavailable.';
}

$pendingArchiveCount = count(array_filter($overview, static fn(array $r): bool => !empty($r['needs_archive'])));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/grant-document-repository.css?v=1" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="gdr-alert" role="alert"><?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?></div>
<?php endif; ?>

<div class="gdr"
     data-grant-document-repository-live="1"
     data-selected-id="<?= (int) $selectedId ?>"
     data-can-archive="<?= !empty($detail['can_archive']) ? '1' : '0' ?>">

    <header class="gdr-page-header">
        <div class="gdr-page-header-main">
            <h1>
                Archive to Publications &amp; IP — Document Repository
                <span class="gdr-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
            </h1>
            <p>Permanently archive grant research records — proposal, revisions, reviewer scores, approval history, approved budget, project progress, final output, publication, and IP documentation.</p>
        </div>
        <?php if ($overview !== []): ?>
        <div class="gdr-stat-row">
            <span class="gdr-stat"><strong data-gdr-project-count><?= count($overview) ?></strong> project<?= count($overview) === 1 ? '' : 's' ?></span>
            <span class="gdr-stat warn"><strong data-gdr-pending-count><?= $pendingArchiveCount ?></strong> ready to archive</span>
        </div>
        <?php endif; ?>
    </header>

    <?php if ($overview === []): ?>
        <div class="gdr-panel">
            <div class="gdr-empty">
                <?= smsIcon('archive') ?>
                <p style="margin:0;font-weight:700;">No projects ready for archiving yet.</p>
                <p style="margin:.35rem 0 0;font-size:.84rem;color:#64748b;">Projects appear here after final output is verified (<strong>OUTPUT_VERIFIED</strong>) under Publications &amp; IP.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="gdr-project-bar">
            <label for="gdrProjectSelect">Select Project:</label>
            <select id="gdrProjectSelect" class="gdr-project-select" aria-label="Select project">
                <?php foreach ($overview as $row): ?>
                    <?php
                    $appId = (int) ($row['grant_application_id'] ?? 0);
                    $ref   = (string) ($row['proposal_reference'] ?? 'Proposal');
                    $title = (string) ($row['research_title'] ?? 'Untitled');
                    $label = (string) ($row['workflow_label'] ?? '');
                    ?>
                    <option value="<?= $appId ?>"<?= $appId === $selectedId ? ' selected' : '' ?>>
                        <?= htmlspecialchars($ref . ': ' . $title . ' — ' . $label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="gdrDetailPanel">
            <?php if ($detail === null): ?>
                <div class="gdr-panel">
                    <div class="gdr-empty">
                        <?= smsIcon('folder-open') ?>
                        <p style="margin:0;">Select a project to preview or view archived records.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php include __DIR__ . '/partials/document-repository-detail.php'; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?= BASE_URL ?>/assets/js/grant-document-repository-live.js?v=1"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
