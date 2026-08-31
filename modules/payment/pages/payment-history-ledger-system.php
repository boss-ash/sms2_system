<?php
/**
 * SMS 2 - Payment History & Ledger System
 * Module: Payment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Payment History & Ledger System';
$activeModule = 'payment';
$activePage   = 'payment-history-ledger-system';
$breadcrumbs  = [
    ['label' => 'Payment Management', 'url' => BASE_URL . '/modules/payment/index.php'],
    ['label' => 'Payment History & Ledger System', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
