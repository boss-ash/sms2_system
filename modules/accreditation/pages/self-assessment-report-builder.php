<?php
/**
 * SMS 2 - Self Assessment Report Builder
 * Module: Accreditation Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Self Assessment Report Builder';
$activeModule = 'accreditation';
$activePage   = 'self-assessment-report-builder';
$breadcrumbs  = [
    ['label' => 'Accreditation Management', 'url' => BASE_URL . '/modules/accreditation/index.php'],
    ['label' => 'Self Assessment Report Builder', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
