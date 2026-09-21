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
require ROOT_PATH . '/modules/faculty/pages/research-director.php';

$crad = cradDb();
$r = rdScheduleGenerateOptimizedSlots($crad, 75, CRAD_DEFENSE_TYPE_FINAL, '2026-09-21', '2026-10-21', 15);
$slots = [];
foreach (($r['slots'] ?? []) as $s) {
    $slots[] = [
        'start' => $s['defense_datetime'] ?? $s['start_at'] ?? $s['start_time'] ?? null,
        'end' => $s['defense_end_datetime'] ?? $s['end_at'] ?? $s['end_time'] ?? null,
        'venue' => is_array($s['venue'] ?? null) ? ($s['venue']['venue_name'] ?? '') : ($s['venue_name'] ?? $s['venue'] ?? ''),
        'date' => $s['defense_date'] ?? null,
    ];
}
echo json_encode([
    'ok' => $r['ok'] ?? null,
    'message' => $r['message'] ?? null,
    'summary' => $r['summary'] ?? null,
    'slot_count' => count($slots),
    'slots' => $slots,
    'sample_keys' => array_keys(($r['slots'][0] ?? [])),
], JSON_PRETTY_PRINT) . PHP_EOL;
