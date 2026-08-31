<?php
/**
 * SMS 2 - Continuous Improvement Action Planning
 * Module: Accreditation Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Continuous Improvement Action Planning';
$activeModule = 'accreditation';
$activePage   = 'continuous-improvement-action-planning';
$breadcrumbs  = [
    ['label' => 'Accreditation Management', 'url' => BASE_URL . '/modules/accreditation/index.php'],
    ['label' => 'Continuous Improvement Action Planning', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
