<?php
/**
 * SMS 2 - Special Class Scheduler
 * Module: Class Scheduling
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Special Class Scheduler';
$activeModule = 'scheduling';
$activePage   = 'special-class-scheduler';
$breadcrumbs  = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Special Class Scheduler', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
