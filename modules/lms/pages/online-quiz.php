<?php
/**
 * SMS 2 - Online Quiz
 * Module: Online Learning & LMS
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Online Quiz';
$activeModule = 'lms';
$activePage   = 'online-quiz';
$breadcrumbs  = [
    ['label' => 'Online Learning & LMS', 'url' => BASE_URL . '/modules/lms/index.php'],
    ['label' => 'Online Quiz', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
