<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modules/crad/config/config.php';
require_once __DIR__ . '/../modules/crad/includes/grant-helpers.php';

$crad = cradDb();
if (!$crad) {
    echo "NO DB\n";
    exit(1);
}

$t0 = microtime(true);
grantEnsureTables($crad);
echo "ensureTables: " . round((microtime(true) - $t0) * 1000) . "ms\n";

$t1 = microtime(true);
$opps = grantGetOpportunities($crad);
echo "getOpportunities: " . round((microtime(true) - $t1) * 1000) . "ms count=" . count($opps) . "\n";

if (!empty($opps)) {
    print_r($opps[0]);
}
