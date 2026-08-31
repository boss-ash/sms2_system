<?php
/**
 * SMS 2 - RFID/QR Code Integration
 * Module: Registrar
 */
require_once __DIR__ . '/../../../config/config.php';

$pageTitle    = 'RFID/QR Code Integration';
$activeModule = 'registrar';
$activePage   = 'rfid-qr-code-integration';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'RFID/QR Code Integration', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php require_once ROOT_PATH . '/includes/submodule-process.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
