<?php
/**
 * SMS 2 - Calendar Integration
 * Module: Class Scheduling
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Calendar Integration';
$activeModule = 'scheduling';
$activePage   = 'calendar-integration';
$breadcrumbs  = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Calendar Integration', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
