<?php
/**
 * Legacy User Archive URL — kept as a redirect into User Accounts archive view.
 * Archive is not a separate sidebar module.
 */
require_once __DIR__ . '/../../../config/config.php';

$qs = $_GET;
$qs['view'] = 'archive';
$query = http_build_query($qs);
header('Location: ' . BASE_URL . '/modules/user-management/pages/user-accounts.php?' . $query, true, 302);
exit;
