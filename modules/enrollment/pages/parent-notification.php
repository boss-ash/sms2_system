<?php
/**
 * SMS 2 - Parent Notification
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Parent Notification';
$activeModule = 'enrollment';
$activePage   = 'parent-notification';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Parent Notification', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
