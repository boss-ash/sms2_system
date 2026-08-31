<?php
/**
 * SMS 2 - Faculty Management - Overview
 */
$pageTitle    = 'Faculty Management';
$activeModule = 'faculty';
$activePage   = '';
$breadcrumbs  = [
    ['label' => 'Faculty Management', 'url' => null],
];

require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
if (getCurrentUserRoleKey() === 'research_director') {
    header('Location: ' . BASE_URL . '/modules/faculty/pages/research-director.php');
    exit;
}
if (getCurrentUserRoleKey() === 'grammarian') {
    header('Location: ' . BASE_URL . '/modules/faculty/pages/for-evaluation.php');
    exit;
}
if (getCurrentUserRoleKey() === 'panel') {
    header('Location: ' . BASE_URL . '/modules/faculty/pages/assigned-defenses.php');
    exit;
}

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
if (getCurrentUserRoleKey() === 'adviser') {
    require_once __DIR__ . '/includes/faculty-account-page.php';
    renderFacultyAccountPage('Overview', '', 'overview');
} else {
    require_once __DIR__ . '/../../includes/module-index-grid.php';
}
require_once __DIR__ . '/../../includes/layout-end.php';
