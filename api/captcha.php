<?php
/**
 * SMS 2 – One-click CAPTCHA verify (local checkbox)
 * POST JSON: { token: "..." }
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/session.php';
require_once ROOT_PATH . '/includes/captcha.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$raw = file_get_contents('php://input');
$body = [];
if (is_string($raw) && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $body = $decoded;
    }
}

$token = trim((string) ($body['token'] ?? $_POST['token'] ?? ''));
if ($token === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing token']);
    exit;
}

// Small delay gate: client should wait ~0.6s animation; still enforce 1s from issue
$stored = $_SESSION['sms_captcha_local'] ?? null;
if (is_array($stored) && !empty($stored['at']) && (time() - (int) $stored['at']) < 1) {
    usleep(350000);
}

if (!smsCaptchaLocalMarkVerified($token)) {
    // Retry once after short wait if issued this second
    usleep(700000);
    if (!smsCaptchaLocalMarkVerified($token)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'CAPTCHA expired. Refresh and try again.']);
        exit;
    }
}

echo json_encode(['ok' => true]);
