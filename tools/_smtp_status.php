<?php
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/mail.php';

header('Content-Type: text/plain; charset=utf-8');

echo "host=" . smsSetting('smtp_host') . "\n";
echo "user=" . smsSetting('smtp_username') . "\n";
echo "from=" . smsSetting('mail_from_email') . "\n";
echo "pass_len=" . strlen(smsSmtpPassword()) . "\n";
echo "file=" . (is_readable(ROOT_PATH . '/storage/keys/smtp_app_password') ? 'yes' : 'no') . "\n";
echo "local_pass=" . (defined('SMS2_SMTP_PASSWORD') && SMS2_SMTP_PASSWORD !== '' ? 'yes' : 'no') . "\n\n";

$pdo = db();
$stmt = $pdo->query(
    "SELECT id, email, username, role_key, status
     FROM users
     WHERE role_key IN ('superadmin','super_admin','admin')
        OR LOWER(email) IN ('kennethabejuela0308@gmail.com','j14677365@gmail.com')
     ORDER BY id
     LIMIT 30"
);
foreach ($stmt as $row) {
    echo implode("\t", [
        $row['id'],
        $row['email'],
        $row['username'] ?? '',
        $row['role_key'],
        $row['status'],
    ]) . "\n";
}
