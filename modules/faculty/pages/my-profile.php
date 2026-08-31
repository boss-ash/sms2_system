<?php
require_once __DIR__ . '/../../../config/config.php';
$pageTitle = 'My Profile';
$activeModule = 'faculty';
$activePage = 'my-profile';
require_once ROOT_PATH . '/includes/layout-start.php';
if (getCurrentUserRoleKey() === 'panel') {
    require_once ROOT_PATH . '/modules/faculty/includes/panel-profile-page.php';
    renderPanelProfilePage('profile');
    require_once ROOT_PATH . '/includes/layout-end.php';
    exit;
}
require_once ROOT_PATH . '/modules/faculty/includes/faculty-account-page.php';
renderFacultyAccountPage($pageTitle, $activePage, 'profile');
require_once ROOT_PATH . '/includes/layout-end.php';
