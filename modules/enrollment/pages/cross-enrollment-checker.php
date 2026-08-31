<?php
/**
 * SMS 2 - Cross-enrollment Checker
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Cross-enrollment Checker';
$activeModule = 'enrollment';
$activePage   = 'cross-enrollment-checker';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Cross-enrollment Checker', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
