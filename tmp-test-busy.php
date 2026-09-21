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
$blocks = rdScheduleAiBusyBlocks($crad, 75, '2026-09-21', '2026-10-21', CRAD_DEFENSE_TYPE_FINAL);
$hit = [];
foreach ($blocks as $b) {
    if (str_starts_with((string) $b['start_at'], '2026-09-24') || str_starts_with((string) $b['start_at'], '2026-09-28')) {
        $hit[] = $b;
    }
}
echo 'total_blocks=' . count($blocks) . PHP_EOL;
echo json_encode($hit, JSON_PRETTY_PRINT) . PHP_EOL;
$conflict = rdScheduleAiHasConflict($blocks, 75, 2, '2026-09-24 13:00:00', '2026-09-24 15:00:00');
echo 'conflict_room1_sep24=' . ($conflict ? 'yes' : 'no') . PHP_EOL;
$msgs = rdScheduleConflictMessages($crad, 75, 2, '2026-09-24 13:00:00', '2026-09-24 15:00:00', 0, CRAD_DEFENSE_TYPE_FINAL);
echo 'save_msgs=' . json_encode($msgs) . PHP_EOL;
