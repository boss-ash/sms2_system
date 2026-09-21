<?php
/**
 * One-off SMTP test — delete after use.
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/mail.php';

header('Content-Type: text/plain; charset=utf-8');

$to = $argv[1] ?? 'j14677365@gmail.com';
echo "Host: " . smsSetting('smtp_host') . "\n";
echo "Port: " . smsSetting('smtp_port') . "\n";
echo "Enc: " . smsSetting('smtp_encryption') . "\n";
echo "User: " . smsSetting('smtp_username') . "\n";
echo "From: " . smsSetting('mail_from_email') . "\n";
echo "Pass set: " . (smsSetting('smtp_password') !== '' ? 'yes' : 'no') . "\n";
echo "To: {$to}\n\n";

$result = smsSendMail(
    $to,
    'SMS2 SMTP test',
    '<p>Test email from <strong>SMS2 PHPMailer</strong>.</p>',
    "Test email from SMS2 PHPMailer.\n"
);

echo "ok=" . (!empty($result['ok']) ? '1' : '0') . "\n";
echo "error=" . ($result['error'] ?? '') . "\n";
