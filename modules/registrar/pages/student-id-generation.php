<?php
/**
 * SMS 2 - Student ID Generation
 * Module: Registrar
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Student ID Generation';
$activeModule = 'registrar';
$activePage   = 'student-id-generation';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student ID Generation', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
