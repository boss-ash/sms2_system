<?php
/**
 * SMS 2 - Section Assignment Tool
 * Module: Class Scheduling
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Section Assignment Tool';
$activeModule = 'scheduling';
$activePage   = 'section-assignment-tool';
$breadcrumbs  = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Section Assignment Tool', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
