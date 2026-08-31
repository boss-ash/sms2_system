<?php
/**
 * SMS 2 - Club Officer Elections
 * Module: Co-Curricular
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Club Officer Elections';
$activeModule = 'cocurricular';
$activePage   = 'club-officer-elections';
$breadcrumbs  = [
    ['label' => 'Co-Curricular', 'url' => BASE_URL . '/modules/cocurricular/index.php'],
    ['label' => 'Club Officer Elections', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
