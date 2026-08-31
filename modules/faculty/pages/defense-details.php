<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';

$pageTitle = 'Defense Details';
$activeModule = 'faculty';
$activePage = 'defense-details';
$breadcrumbs = [
    ['label' => 'Panel Portal', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Defense Details', 'url' => null],
];

require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/panel-defense-page.php';
renderBreadcrumbs($breadcrumbs);
renderPanelDefensePage('details');
require_once ROOT_PATH . '/includes/layout-end.php';
