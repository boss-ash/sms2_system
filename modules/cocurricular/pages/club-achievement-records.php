<?php
/**
 * SMS 2 - Club Achievement Records
 * Module: Co-Curricular
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Club Achievement Records';
$activeModule = 'cocurricular';
$activePage   = 'club-achievement-records';
$breadcrumbs  = [
    ['label' => 'Co-Curricular', 'url' => BASE_URL . '/modules/cocurricular/index.php'],
    ['label' => 'Club Achievement Records', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
