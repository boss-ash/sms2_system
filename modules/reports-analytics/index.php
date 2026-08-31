<?php
/**
 * SMS 2 - Reports & Analytics - Overview
 */
$pageTitle    = 'Reports & Analytics';
$activeModule = 'reports-analytics';
$activePage   = '';
$breadcrumbs  = [
    ['label' => 'Reports & Analytics', 'url' => null],
];
$moduleIntro = 'Role-based Reports & Analytics dashboards — charts and summary tables scoped only to modules your office can access.';

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
require_once __DIR__ . '/../../includes/module-index-grid.php';
require_once __DIR__ . '/../../includes/layout-end.php';
