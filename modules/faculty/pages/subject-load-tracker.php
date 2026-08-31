<?php
/**
 * SMS 2 - Subject Load Tracker
 * Module: Faculty Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Subject Load Tracker';
$activeModule = 'faculty';
$activePage   = 'subject-load-tracker';
$breadcrumbs  = [
    ['label' => 'Faculty Management', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Subject Load Tracker', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
