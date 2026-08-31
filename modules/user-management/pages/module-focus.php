<?php
/**
 * Legacy redirect — Module Security is a single page again.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/authentication.php';
requireAuth();
requireSuperAdmin();

$mod = (string) ($_GET['focus'] ?? $_GET['module'] ?? $_GET['m'] ?? $_GET['sec_mod'] ?? $_GET['mod'] ?? '');
if (in_array(strtolower($mod), ['crud', 'crowd'], true)) {
    $mod = 'crad';
}
if ($mod === 'student-portal') {
    $mod = 'student_portal';
}

$qs = [];
if ($mod !== '') {
    $qs['focus'] = $mod;
} else {
    $qs['picker'] = '1';
}

header('Location: ' . BASE_URL . '/modules/user-management/pages/module-security.php?' . http_build_query($qs));
exit;
