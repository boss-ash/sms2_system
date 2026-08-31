<?php
/**
 * Legacy CRAD proposal tracking endpoint.
 * Current submissions are reviewed through the three-tier title-approval flow.
 */
require_once __DIR__ . '/../../../config/config.php';

header('Location: ' . BASE_URL . '/modules/crad/pages/register-proposal.php', true, 302);
exit;
