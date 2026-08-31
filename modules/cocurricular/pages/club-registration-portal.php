<?php
/**
 * SMS 2 - Club Registration Portal
 * Module: Co-Curricular
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Club Registration Portal';
$activeModule = 'cocurricular';
$activePage   = 'club-registration-portal';
$breadcrumbs  = [
    ['label' => 'Co-Curricular', 'url' => BASE_URL . '/modules/cocurricular/index.php'],
    ['label' => 'Club Registration Portal', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
