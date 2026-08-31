<?php
/**
 * SMS 2 - Substitute Assignment Tracker
 * Module: Class Scheduling
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Substitute Assignment Tracker';
$activeModule = 'scheduling';
$activePage   = 'substitute-assignment-tracker';
$breadcrumbs  = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Substitute Assignment Tracker', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
