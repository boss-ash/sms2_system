<?php
/**
 * SMS 2 - Auto Section Assignment
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Auto Section Assignment';
$activeModule = 'enrollment';
$activePage   = 'auto-section-assignment';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Auto Section Assignment', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
