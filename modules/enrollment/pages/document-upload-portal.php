<?php
/**
 * SMS 2 - Document Upload Portal
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Document Upload Portal';
$activeModule = 'enrollment';
$activePage   = 'document-upload-portal';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Document Upload Portal', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
