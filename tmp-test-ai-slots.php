<?php
declare(strict_types=1);
define('RD_AI_OPTIMIZER_TEST', true);
require __DIR__ . '/config/config.php';
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
    'slots' => [],
];
foreach (($r['slots'] ?? []) as $s) {
    $out['slots'][] = [
        'date' => $s['defense_date'] ?? $s['date'] ?? null,
        'start' => $s['start_time'] ?? ($s['defense_datetime'] ?? null),
        'end' => $s['end_time'] ?? ($s['defense_end_datetime'] ?? null),
        'venue' => $s['venue_name'] ?? ($s['venue'] ?? null),
        'keys' => array_keys($s),
    ];
}
echo json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL;
