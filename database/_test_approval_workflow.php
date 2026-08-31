<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once ROOT_PATH . '/modules/crad/includes/grant-approval-helpers.php';

$crad = getCradDatabaseConnection();
grantBackfillApprovalWorkflows($crad);
$list = grantApprovalWorkflowList($crad);
echo 'Workflows: ' . count($list) . PHP_EOL;
foreach ($list as $row) {
    echo ($row['proposal_reference'] ?? '?') . ' | ' . ($row['workflow_status'] ?? '') . ' | step=' . ($row['current_step_key'] ?? '') . PHP_EOL;
}
