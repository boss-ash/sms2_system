<?php
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = 'My Schedule';
$activeModule = 'faculty';
$activePage = 'my-schedule';
require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/faculty-account-page.php';
renderFacultyAccountPage($pageTitle, $activePage, 'schedule');
require_once ROOT_PATH . '/includes/layout-end.php';
