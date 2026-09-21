<?php
/**
 * Diagnose SMTP secret decrypt without printing the password.
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/crypto.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = db();
$stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'smtp_password' LIMIT 1");
$stmt->execute();
$raw = (string) ($stmt->fetchColumn() ?: '');

echo 'raw_len=' . strlen($raw) . "\n";
echo 'is_encrypted=' . (smsCryptoIsEncrypted($raw) ? '1' : '0') . "\n";
echo 'key_path=' . smsCryptoKeyPath() . "\n";
echo 'key_exists=' . (is_readable(smsCryptoKeyPath()) ? '1' : '0') . "\n";
$keyFile = is_readable(smsCryptoKeyPath()) ? (string) file_get_contents(smsCryptoKeyPath()) : '';
echo 'key_file_len=' . strlen($keyFile) . "\n";

$dec = smsSecretDecrypt($raw);
echo 'decrypt_len=' . strlen($dec) . "\n";
echo 'decrypt_ok=' . ($dec !== '' ? '1' : '0') . "\n";

$viaSetting = smsSetting('smtp_password', '');
echo 'setting_len=' . strlen($viaSetting) . "\n";
echo 'user=' . smsSetting('smtp_username', '') . "\n";
echo 'host=' . smsSetting('smtp_host', '') . "\n";
