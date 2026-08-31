<?php
/**
 * SMS 2 - Lesson Material Upload
 * Module: Online Learning & LMS
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Lesson Material Upload';
$activeModule = 'lms';
$activePage   = 'lesson-material-upload';
$breadcrumbs  = [
    ['label' => 'Online Learning & LMS', 'url' => BASE_URL . '/modules/lms/index.php'],
    ['label' => 'Lesson Material Upload', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
