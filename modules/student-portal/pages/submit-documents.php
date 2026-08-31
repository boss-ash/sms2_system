<?php
/**
 * Legacy document-packet submission endpoint.
 * Title proposals now use title_approvals and research-proposal-submission.php.
 */
require_once __DIR__ . '/../../../config/config.php';

$query = !empty($_GET['view']) && $_GET['view'] === 'drafts'
    ? '?view=drafts'
    : '';
header('Location: ' . BASE_URL . '/modules/student-portal/pages/research-proposal-submission.php' . $query, true, 302);
exit;
