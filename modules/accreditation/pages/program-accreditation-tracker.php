<?php
/**
 * SMS 2 - Program Accreditation Tracker
 * Module: Accreditation Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Program Accreditation Tracker';
$activeModule = 'accreditation';
$activePage   = 'program-accreditation-tracker';
$breadcrumbs  = [
    ['label' => 'Accreditation Management', 'url' => BASE_URL . '/modules/accreditation/index.php'],
    ['label' => 'Program Accreditation Tracker', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
