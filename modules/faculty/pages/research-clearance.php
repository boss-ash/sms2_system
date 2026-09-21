<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/modules/crad/includes/research-services-clearance.php';

requireAuth();
if (getCurrentUserRoleKey() !== 'adviser') {
    http_response_code(403);
    exit('Forbidden');
}

$pageTitle = 'Research Services Clearance';
$activeModule = 'faculty';
$activePage = 'research-clearance';
$pageBannerIcon = 'fa-stamp';
$pageBannerDescription = 'Adviser digital signing is no longer required. Students upload signed clearances for CRAD approval.';
$breadcrumbs = [
    ['label' => 'Faculty', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Research Services Clearance', 'url' => null],
];

$crad = rscDb();
rscEnsureSchema($crad);
$rows = [];
$public = null;

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/modules/crad/assets/css/research-clearance.css?v=rsc-flow-3">

<div class="glass-dashboard">
    <div class="alert alert-info mb-0">
        <?= smsIcon('info-circle', ['class' => 'me-2']) ?>
        Research Services Clearance no longer needs adviser digital signing.
        Students print the form, upload the signed image, and <strong>CRAD</strong> approves it.
    </div>
</div>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
                <button type="button" class="btn btn-outline-secondary" data-rsc-download <?= $public ? '' : 'hidden' ?>><?= smsIcon('download', ['class' => 'me-1']) ?>Download Image</button>
                <button type="button" class="btn btn-success" data-rsc-sign <?= ($public && $public['status'] === 'sent_to_adviser') ? '' : 'hidden' ?>><?= smsIcon('signature', ['class' => 'me-1']) ?>Sign Clearance</button>
            </div>
        </div>
        <div class="rsc-wrap" data-rsc-form><?= $public['form_html'] ?? '' ?></div>
    </div>
</div>
<?php require __DIR__ . '/../../crad/includes/research-clearance-sig-modal.php'; ?>
<script src="<?= BASE_URL ?>/modules/crad/assets/js/research-clearance-live.js?v=rsc-stage-2"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
