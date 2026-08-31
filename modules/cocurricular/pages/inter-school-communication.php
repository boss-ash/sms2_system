<?php
/**
 * SMS 2 - Inter-school Communication
 * Module: Co-Curricular
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Inter-school Communication';
$activeModule = 'cocurricular';
$activePage   = 'inter-school-communication';
$breadcrumbs  = [
    ['label' => 'Co-Curricular', 'url' => BASE_URL . '/modules/cocurricular/index.php'],
    ['label' => 'Inter-school Communication', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
