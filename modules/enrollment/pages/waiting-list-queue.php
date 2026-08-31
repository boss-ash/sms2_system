<?php
/**
 * SMS 2 - Waiting List Queue
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Waiting List Queue';
$activeModule = 'enrollment';
$activePage   = 'waiting-list-queue';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Waiting List Queue', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
