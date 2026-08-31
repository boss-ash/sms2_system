<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';
require_once ROOT_PATH . '/modules/faculty/includes/panel-defense-page.php';

requireAuth();

$crad = chapterDb();
$id = (int) ($_GET['id'] ?? 0);
$submission = $id > 0 ? chapterGetSubmission($crad, $id) : null;
if (!$submission || (!chapterEvaluatorCanAccess($submission) && !chapterStudentCanAccess($submission) && !chapterPanelCanAccessSubmission($crad, $submission))) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$subdir = trim((string) ($submission['stored_subdir'] ?? ''), '/');
$stored = basename((string) ($submission['stored_name'] ?? ''));
$path = smsUploadRoot() . '/' . $subdir . '/' . $stored;
if ($stored === '' || !is_file($path)) {
    http_response_code(404);
    echo 'Document not found.';
    exit;
}

$download = (($_GET['download'] ?? '') === '1');
$name = preg_replace('/[^a-zA-Z0-9._ -]/', '_', (string) ($submission['original_name'] ?? 'chapter-document'));
header('Content-Type: ' . ((string) ($submission['file_mime'] ?? '') ?: 'application/octet-stream'));
header('Content-Length: ' . filesize($path));
header('Content-Disposition: ' . ($download ? 'attachment' : 'inline') . '; filename="' . addslashes($name) . '"');
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
