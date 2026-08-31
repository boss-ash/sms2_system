<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once ROOT_PATH . '/modules/crad/includes/grant-evaluation-helpers.php';

$crad = cradDb();
if (!$crad) {
    echo "No crad db\n";
    exit(1);
}

$main = db();
$user = $main->query("SELECT id, username, full_name FROM users WHERE username = 's230000001' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "Student not found\n";
    exit(1);
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_role_key'] = 'student';

grantEnsureTables($crad);
grantBackfillApplicantDecisionNotifications($crad, (int) $user['id']);

$rows = grantProposalNotificationsForCurrentUser(10);
echo "Notifications for student:\n";
foreach ($rows as $row) {
    echo '- ' . ($row['title'] ?? '') . ' | ' . ($row['url'] ?? '') . ' | ' . ($row['status'] ?? '') . "\n";
}
