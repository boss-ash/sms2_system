<?php
/**
 * SMS 2 - Feedback & Comments
 * Module: Online Learning & LMS
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Feedback & Comments';
$activeModule = 'lms';
$activePage   = 'feedback-comments';
$breadcrumbs  = [
    ['label' => 'Online Learning & LMS', 'url' => BASE_URL . '/modules/lms/index.php'],
    ['label' => 'Feedback & Comments', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
