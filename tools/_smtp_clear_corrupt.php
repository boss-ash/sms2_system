<?php
/**
 * Clear SMTP password ciphertext that cannot be decrypted with current app.key.
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/crypto.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = db();
if (!$pdo) {
    fwrite(STDERR, "DB unavailable\n");
    exit(1);
}

$stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'smtp_password' LIMIT 1");
$stmt->execute();
$raw = (string) ($stmt->fetchColumn() ?: '');

if ($raw === '') {
    echo "smtp_password already empty\n";
    exit(0);
}

if (!smsCryptoIsEncrypted($raw)) {
    echo "smtp_password is plaintext — leaving as-is\n";
    exit(0);
}

$dec = smsSecretDecrypt($raw);
if ($dec !== '') {
    echo "smtp_password decrypts OK — not clearing\n";
    exit(0);
}

$upd = $pdo->prepare("UPDATE system_settings SET setting_value = '' WHERE setting_key = 'smtp_password'");
$upd->execute();
echo "cleared corrupt smtp_password ciphertext\n";
