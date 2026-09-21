<?php
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/session.php';
$_SESSION['user_id'] = 1;
$_SESSION['user_role_key'] = 'superadmin';
$_SESSION['user_role'] = 'Super Admin';
$_SESSION['user_name'] = 'Test';
$_SESSION['last_activity'] = time();

define('RD_AI_OPTIMIZER_TEST', true);
require ROOT_PATH . '/modules/crad/config/config.php';
require ROOT_PATH . '/includes/authentication.php';
require ROOT_PATH . '/modules/faculty/includes/research-director-panel-assignment.php';
require ROOT_PATH . '/modules/crad/includes/research-services-clearance.php';
require ROOT_PATH . '/modules/faculty/pages/research-director.php';

$crad = cradDb();
$r = rdScheduleGenerateOptimizedSlots($crad, 75, CRAD_DEFENSE_TYPE_FINAL, '2026-09-21', '2026-10-21', 15);
$out = [
    'ok' => $r['ok'] ?? null,
    'message' => $r['message'] ?? null,
    'summary' => $r['summary'] ?? null,
    'slots' => $r['slots'] ?? [],
];
echo json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL;
