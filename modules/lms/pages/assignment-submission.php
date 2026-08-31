<?php
/**
 * SMS 2 - Assignment Submission
 * Module: Online Learning & LMS
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Assignment Submission';
$activeModule = 'lms';
$activePage   = 'assignment-submission';
$breadcrumbs  = [
    ['label' => 'Online Learning & LMS', 'url' => BASE_URL . '/modules/lms/index.php'],
    ['label' => 'Assignment Submission', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
