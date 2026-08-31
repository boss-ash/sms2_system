<?php
/**
 * SMS 2 - Academic History
 * Module: Registrar
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Academic History';
$activeModule = 'registrar';
$activePage   = 'academic-history';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Academic History', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
