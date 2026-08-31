<?php
/**
 * SMS 2 - Persona File Database
 * Module: Registrar
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Persona File Database';
$activeModule = 'registrar';
$activePage   = 'persona-file-database';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Persona File Database', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
