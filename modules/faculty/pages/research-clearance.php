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
