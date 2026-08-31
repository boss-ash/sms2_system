<?php
/**
 * SMS 2 - Health Record Log
 * Module: Registrar
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Health Record Log';
$activeModule = 'registrar';
$activePage   = 'health-record-log';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Health Record Log', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
