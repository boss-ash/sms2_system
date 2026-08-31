<?php
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = 'Expertise';
$activeModule = 'faculty';
$activePage = 'expertise';
require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/faculty-account-page.php';
renderFacultyAccountPage($pageTitle, $activePage, 'profile-expertise');
require_once ROOT_PATH . '/includes/layout-end.php';
