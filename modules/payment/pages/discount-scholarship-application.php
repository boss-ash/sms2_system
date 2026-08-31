<?php
/**
 * SMS 2 - Discount & Scholarship Application
 * Module: Payment Management
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'Discount & Scholarship Application';
$activeModule = 'payment';
$activePage   = 'discount-scholarship-application';
$breadcrumbs  = [
    ['label' => 'Payment Management', 'url' => BASE_URL . '/modules/payment/index.php'],
    ['label' => 'Discount & Scholarship Application', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
