<?php
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/crypto.php';

$cipher = 'sms2enc1.BnNN43RIftF9bLKe7buHTa6/qaxuGXWg5XruC7mKh67ZYei7aPH7AeOKNpdXJ7A=';
$dec = smsSecretDecrypt($cipher);
echo 'dump_decrypt_len=' . strlen($dec) . "\n";
echo 'key_mtime=' . (file_exists(smsCryptoKeyPath()) ? date('c', filemtime(smsCryptoKeyPath())) : 'missing') . "\n";
