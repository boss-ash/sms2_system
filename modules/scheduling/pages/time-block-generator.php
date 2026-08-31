<?php
/**
 * SMS 2 - Time Block Generator
 * Module: Class Scheduling
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Time Block Generator';
$activeModule = 'scheduling';
$activePage   = 'time-block-generator';
$breadcrumbs  = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Time Block Generator', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
