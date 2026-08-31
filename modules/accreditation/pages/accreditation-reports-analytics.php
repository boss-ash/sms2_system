<?php
/**
 * SMS 2 - Accreditation Reports & Analytics
 * Module: Accreditation Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Accreditation Reports & Analytics';
$activeModule = 'accreditation';
$activePage   = 'accreditation-reports-analytics';
$breadcrumbs  = [
    ['label' => 'Accreditation Management', 'url' => BASE_URL . '/modules/accreditation/index.php'],
    ['label' => 'Accreditation Reports & Analytics', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
