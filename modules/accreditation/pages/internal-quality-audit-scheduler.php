<?php
/**
 * SMS 2 - Internal Quality Audit Scheduler
 * Module: Accreditation Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Internal Quality Audit Scheduler';
$activeModule = 'accreditation';
$activePage   = 'internal-quality-audit-scheduler';
$breadcrumbs  = [
    ['label' => 'Accreditation Management', 'url' => BASE_URL . '/modules/accreditation/index.php'],
    ['label' => 'Internal Quality Audit Scheduler', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
