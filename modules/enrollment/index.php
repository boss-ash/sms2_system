<?php
/**
 * SMS 2 - Enrollment Management - Overview
 */
$pageTitle    = 'Enrollment Management';
$activeModule = 'enrollment';
$activePage   = '';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => null],
];

$moduleIntro = 'Open a submodule to manage real enrollment records — applications, documents, validation, IDs, waitlist, sections, and notices.';

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
require_once __DIR__ . '/../../includes/module-index-grid.php';
require_once __DIR__ . '/../../includes/layout-end.php';