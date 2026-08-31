<?php
/**
 * SMS 2 - Outputs & Records · Publications & IP
 * Researcher: Submit Final Output/Publication
 * CRAD Staff: Verify & Record to Publications & IP Repository
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once __DIR__ . '/../includes/grant-final-output-helpers.php';
require_once __DIR__ . '/../includes/grant-funded-research-helpers.php';

requireAuth();
grantRequirePublicationsIpAccess();

$pageTitle            = 'Publications & IP';
$activeModule         = grantActiveModuleKey();
$activePage           = 'publications-ip';
$hideModulePageBanner = true;
$canSubmit            = grantUserCanSubmitFinalOutput();
$canVerify            = grantUserCanVerifyPublicationsIp();

$breadcrumbs = [
    ['label' => grantBreadcrumbModuleLabel(), 'url' => grantBreadcrumbModuleUrl()],
    ['label' => 'Outputs & Records', 'url' => grantPublicationsIpUrl()],
    ['label' => 'Publications & IP', 'url' => null],
];

require_once ROOT_PATH . '/includes/breadcrumbs.php';

$crad       = cradDb();
$overview   = [];
$selectedId = (int) ($_GET['id'] ?? 0);
$detail     = null;
$dbError    = '';

if ($crad) {
    try {
        $overview = grantGetFinalOutputOverview($crad);
        if ($selectedId <= 0 && $overview !== []) {
            $selectedId = (int) ($overview[0]['grant_application_id'] ?? 0);
        }
        if ($selectedId > 0) {
            $detail = grantGetFinalOutputDetail($crad, $selectedId);
        }
    } catch (Throwable $e) {
        $dbError = htmlspecialchars($e->getMessage());
        error_log('publications-ip: ' . $e->getMessage());
    }
} else {
    $dbError = 'CRAD database connection unavailable.';
}

$pendingVerifyCount = count(array_filter($overview, static fn(array $r): bool => !empty($r['needs_verification'])));

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link href="<?= BASE_URL ?>/assets/css/grant-publications-ip.css?v=1" rel="stylesheet">

<?php if ($dbError !== ''): ?>
<div class="gpip-alert" role="alert"><?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?><?= $dbError ?></div>
<?php endif; ?>

<div class="gpip"
     data-grant-publications-ip-live="1"
     data-selected-id="<?= (int) $selectedId ?>"
     data-can-submit="<?= ($canSubmit && !empty($detail['can_submit'])) ? '1' : '0' ?>"
     data-can-verify="<?= ($canVerify && !empty($detail['can_verify'])) ? '1' : '0' ?>">

    <header class="gpip-page-header">
        <div class="gpip-page-header-main">
            <h1>
                <?php if ($canVerify): ?>
                    Verify &amp; Record Publications &amp; IP
                <?php else: ?>
                    Submit Final Output / Publication
                <?php endif; ?>
                <span class="gpip-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
            </h1>
            <p>
                <?php if ($canVerify): ?>
                    Verify final research output, authors, journal/conference, DOI, publication proof, copyright, patent, and other IP records — then record to the <strong>Publications &amp; IP Repository</strong>.
                <?php else: ?>
                    Submit your final research output and publication details for CRAD verification under <strong>Outputs &amp; Records → Publications &amp; IP</strong>.
                <?php endif; ?>
            </p>
        </div>
        <?php if ($overview !== []): ?>
        <div class="gpip-stat-row">
            <span class="gpip-stat"><strong data-gpip-project-count><?= count($overview) ?></strong> funded project<?= count($overview) === 1 ? '' : 's' ?></span>
            <?php if ($canVerify): ?>
            <span class="gpip-stat warn"><strong data-gpip-pending-count><?= $pendingVerifyCount ?></strong> pending verification</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </header>

    <?php if ($overview === []): ?>
        <div class="gpip-panel">
            <div class="gpip-empty">
                <?= smsIcon('book-open') ?>
                <p style="margin:0;font-weight:700;">No eligible funded projects yet.</p>
                <p style="margin:.35rem 0 0;font-size:.84rem;color:#64748b;">
                    <?php if ($canVerify): ?>
                        Funded grant projects will appear here when researchers are ready to submit final output.
                    <?php else: ?>
                        Your approved &amp; funded grant will appear here after Finance completes the final sign-off.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="gpip-project-bar">
            <label for="gpipProjectSelect">Select Project:</label>
            <select id="gpipProjectSelect" class="gpip-project-select" aria-label="Select funded project">
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

        <div id="gpipDetailPanel">
            <?php if ($detail === null): ?>
                <div class="gpip-panel">
                    <div class="gpip-empty">
                        <?= smsIcon('file-alt') ?>
                        <p style="margin:0;">Select a project to view details.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php include __DIR__ . '/partials/publications-ip-detail.php'; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($canSubmit): ?>
    <div class="gpip-dialog" id="gpipSubmitDialog" role="dialog" aria-modal="true" aria-labelledby="gpipSubmitTitle">
        <div class="gpip-dialog-box">
            <div class="gpip-dialog-header">
                <h3 id="gpipSubmitTitle"><?= smsIcon('file-upload') ?> Submit Final Output</h3>
                <p class="gpip-dialog-lead">Upload your final research PDF and publication details for CRAD verification.</p>
            </div>
            <form id="gpipSubmitForm" class="gpip-form gpip-dialog-form" method="post" data-no-loader enctype="multipart/form-data">
                <input type="hidden" id="gpipSubmitApplicationId" name="grant_application_id" value="<?= (int) $selectedId ?>">
                <div class="gpip-dialog-body">
                    <label for="gpipFinalTitle">Final Research Title</label>
                    <input type="text" id="gpipFinalTitle" name="final_research_title" required maxlength="500">

                    <label for="gpipAuthors">Authors</label>
                    <input type="text" id="gpipAuthors" name="authors" required maxlength="500" placeholder="Lead author and co-authors">

                    <label for="gpipAbstract">Abstract</label>
                    <textarea id="gpipAbstract" name="abstract" rows="4" required placeholder="Research abstract…"></textarea>

                    <label for="gpipPublicationType">Publication Type</label>
                    <select id="gpipPublicationType" name="publication_type" required>
                        <?php foreach (grantFinalOutputPublicationTypes() as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="gpipJournal">Journal / Conference</label>
                    <input type="text" id="gpipJournal" name="journal_conference" required maxlength="255">

                    <div class="gpip-form-grid-2">
                        <div>
                            <label for="gpipDoi">DOI</label>
                            <input type="text" id="gpipDoi" name="doi" maxlength="120" placeholder="Optional">
                        </div>
                        <div>
                            <label for="gpipPubUrl">Publication URL</label>
                            <input type="url" id="gpipPubUrl" name="publication_url" maxlength="500" placeholder="https://">
                        </div>
                    </div>

                    <label for="gpipIpInfo">IP Information</label>
                    <textarea id="gpipIpInfo" name="ip_information" rows="2" placeholder="Copyright, patent, or other IP details…"></textarea>

                    <div class="gpip-form-grid-2">
                        <div>
                            <label for="gpipCopyright">Copyright</label>
                            <input type="text" id="gpipCopyright" name="copyright_info" maxlength="255" placeholder="Optional">
                        </div>
                        <div>
                            <label for="gpipPatent">Patent</label>
                            <input type="text" id="gpipPatent" name="patent_info" maxlength="255" placeholder="Optional">
                        </div>
                    </div>

                    <label for="gpipOtherIp">Other IP Records</label>
                    <input type="text" id="gpipOtherIp" name="other_ip_info" maxlength="255" placeholder="Optional">

                    <label for="gpipFinalPdf">Final Research PDF</label>
                    <input type="file" id="gpipFinalPdf" name="final_pdf" accept=".pdf" required>
                    <p class="gpip-field-hint">Required. PDF only (max 15 MB).</p>

                    <label for="gpipSupporting">Supporting Files</label>
                    <input type="file" id="gpipSupporting" name="supporting_file" accept=".pdf,.doc,.docx,.zip">
                    <p class="gpip-field-hint">Optional. PDF, Word, or ZIP (max 15 MB).</p>
                </div>
                <div class="gpip-dialog-actions">
                    <button type="button" class="gpip-btn gpip-btn-ghost" id="gpipSubmitCancelBtn">Cancel</button>
                    <button type="submit" class="gpip-btn gpip-btn-primary" id="gpipSubmitBtn">
                        <?= smsIcon('upload') ?> Submit Final Output
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($canVerify): ?>
    <div class="gpip-dialog" id="gpipReturnDialog" role="dialog" aria-modal="true" aria-labelledby="gpipReturnTitle">
        <div class="gpip-dialog-box">
            <div class="gpip-dialog-header">
                <h3 id="gpipReturnTitle"><?= smsIcon('undo') ?> Return for Correction</h3>
                <p class="gpip-dialog-lead">Provide feedback so the researcher can revise and resubmit.</p>
            </div>
            <form id="gpipReturnForm" class="gpip-form gpip-dialog-form" method="post" data-no-loader>
                <input type="hidden" id="gpipReturnApplicationId" name="grant_application_id" value="<?= (int) $selectedId ?>">
                <div class="gpip-dialog-body">
                    <label for="gpipReturnReason">Return Reason</label>
                    <textarea id="gpipReturnReason" name="return_reason" rows="4" required placeholder="Describe what needs to be corrected…"></textarea>
                </div>
                <div class="gpip-dialog-actions">
                    <button type="button" class="gpip-btn gpip-btn-ghost" id="gpipReturnCancelBtn">Cancel</button>
                    <button type="submit" class="gpip-btn gpip-btn-danger" id="gpipReturnBtn">
                        <?= smsIcon('undo') ?> Return for Correction
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="<?= BASE_URL ?>/assets/js/grant-publications-ip-live.js?v=1"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
