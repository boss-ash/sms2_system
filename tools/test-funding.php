<?php
require __DIR__ . '/../modules/crad/config/config.php';
require __DIR__ . '/../modules/crad/includes/grant-funding-helpers.php';

$crad = cradDb();
grantBackfillFundingDisbursementPlans($crad);
$overview = grantGetFundedDisbursementOverview($crad);
echo 'count=' . count($overview) . PHP_EOL;
if ($overview !== []) {
    $id = (int) ($overview[0]['grant_application_id'] ?? 0);
    $detail = grantGetFundingDisbursementDetail($crad, $id);
    echo 'tranches=' . count($detail['tranches'] ?? []) . PHP_EOL;
    echo 'approved=' . ($detail['approved_budget'] ?? 0) . PHP_EOL;
}
