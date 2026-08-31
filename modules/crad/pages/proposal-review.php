<?php
/**
 * Legacy CRAD proposal review endpoint.
 * CRAD review is now handled by the title-approval inbox in register-proposal.php.
 */
require_once __DIR__ . '/../../../config/config.php';

header('Location: ' . BASE_URL . '/modules/crad/pages/register-proposal.php', true, 302);
exit;
