<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once ROOT_PATH . '/modules/crad/includes/grant-helpers.php';

$crad = cradDb();
$main = db();

$user = $main->query("SELECT id, username, full_name FROM users WHERE username = 's230000001'")->fetch(PDO::FETCH_ASSOC);
echo "User: " . json_encode($user) . "\n";

grantEnsureTables($crad);

$apps = $crad->query("SELECT id, applicant_name, applicant_user_id, status, proposal_reference FROM grant_applications")->fetchAll(PDO::FETCH_ASSOC);
echo "Apps:\n" . json_encode($apps, JSON_PRETTY_PRINT) . "\n";

$table = $crad->query("SHOW TABLES LIKE 'grant_proposal_notifications'")->fetchColumn();
echo "Notif table: " . ($table ?: 'missing') . "\n";

if ($table) {
    $n = $crad->query("SELECT * FROM grant_proposal_notifications")->fetchAll(PDO::FETCH_ASSOC);
    echo "Notifications:\n" . json_encode($n, JSON_PRETTY_PRINT) . "\n";
}

$evals = $crad->query("SELECT id, grant_application_id, recommendation, revision_reason FROM grant_proposal_evaluations")->fetchAll(PDO::FETCH_ASSOC);
echo "Evals:\n" . json_encode($evals, JSON_PRETTY_PRINT) . "\n";
