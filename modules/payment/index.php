<?php
/**
 * SMS 2 - Payment Management - Overview
 */
require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

requireAuth();

if (getCurrentUserRoleKey() === 'finance') {
    header('Location: ' . BASE_URL . '/modules/payment/pages/approval-workflows.php');
    exit;
}

$pageTitle    = 'Payment Management';
$activeModule = 'payment';
$activePage   = '';
$breadcrumbs  = [
    ['label' => 'Payment Management', 'url' => null],
];

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
require_once __DIR__ . '/../../includes/module-index-grid.php';
require_once __DIR__ . '/../../includes/layout-end.php';