<?php
/**
 * SMS 2 – Create encrypted database backup (CLI)
 *
 * Usage:
 *   C:\xampp\php\php.exe database/backup-encrypted.php "YourStrongPassword"
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run from CLI only.\n";
    exit(1);
}

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/backup.php';

$password = (string) ($argv[1] ?? '');
if ($password === '') {
    fwrite(STDERR, "Usage: php database/backup-encrypted.php \"YourStrongPassword\"\n");
    exit(1);
}

$result = smsCreateEncryptedBackup($password);
if (empty($result['ok'])) {
    fwrite(STDERR, 'Backup failed: ' . ($result['error'] ?: 'unknown') . PHP_EOL);
    exit(1);
}

echo 'Encrypted backup created:' . PHP_EOL;
echo $result['path'] . PHP_EOL;
echo 'Keep the password safe. File is blocked from web access (storage/backups).' . PHP_EOL;
exit(0);
