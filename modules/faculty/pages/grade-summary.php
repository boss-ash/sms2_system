<?php
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = 'Grade Summary';
$activeModule = 'faculty';
$activePage = 'grade-summary';
require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/faculty-account-page.php';
renderFacultyAccountPage($pageTitle, $activePage, 'grades');
require_once ROOT_PATH . '/includes/layout-end.php';
