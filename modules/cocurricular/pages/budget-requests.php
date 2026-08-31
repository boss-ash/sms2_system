<?php
/**
 * SMS 2 - Budget Requests
 * Module: Co-Curricular
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Budget Requests';
$activeModule = 'cocurricular';
$activePage   = 'budget-requests';
$breadcrumbs  = [
    ['label' => 'Co-Curricular', 'url' => BASE_URL . '/modules/cocurricular/index.php'],
    ['label' => 'Budget Requests', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
