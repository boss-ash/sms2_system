<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';

$pageTitle = 'Evaluation History';
$activeModule = 'faculty';
$activePage = 'panel-evaluation-history';
$breadcrumbs = [
    ['label' => 'Panel Portal', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Evaluation History', 'url' => null],
];

require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/panel-defense-page.php';
renderBreadcrumbs($breadcrumbs);
renderPanelDefensePage('history');
require_once ROOT_PATH . '/includes/layout-end.php';
