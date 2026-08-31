<?php
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = 'Research Details';
$activeModule = 'faculty';
$activePage = 'research-details';
require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/faculty-account-page.php';
renderFacultyAccountPage($pageTitle, $activePage, 'research');
require_once ROOT_PATH . '/includes/layout-end.php';
