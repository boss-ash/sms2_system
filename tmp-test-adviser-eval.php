<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/modules/crad/includes/grant-evaluation-helpers.php';

$_SESSION['user_id'] = 54;
$_SESSION['role_key'] = 'adviser';

if (!function_exists('getCurrentUserRoleKey')) {
    function getCurrentUserRoleKey(): string
    {
        return 'adviser';
    }
}

$crad = cradDb();
$q = grantAdviserEvaluationQueue($crad);
echo 'Queue count: ' . count($q) . PHP_EOL;
if ($q !== []) {
    $row = $q[0];
    echo ($row['proposal_reference'] ?? '') . ' | ' . ($row['research_title'] ?? '') . PHP_EOL;
    echo 'committee score: ' . ($row['committee_total_score'] ?? 'n/a') . PHP_EOL;
}
