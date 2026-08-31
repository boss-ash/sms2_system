<?php
/**
 * SMS 2 – Database dump + encrypted backup helpers
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/crypto.php';

function smsBackupDir(): string
{
    $dir = ROOT_PATH . '/storage/backups';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    $deny = $dir . '/.htaccess';
    if (!is_file($deny)) {
        @file_put_contents(
            $deny,
            "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n"
            . "<IfModule !mod_authz_core.c>\n    Deny from all\n</IfModule>\n"
        );
    }
    return $dir;
}

/**
 * Export current schema+data to a .sql file (PHP-based; no mysqldump required).
 *
 * @return array{ok:bool,error:string,path:?string}
 */
function smsCreateSqlDump(): array
{
    $pdo = db();
    if (!$pdo) {
        return ['ok' => false, 'error' => 'Database unavailable.', 'path' => null];
    }

    try {
        $dbName = '';
        if (defined('DB_NAME')) {
            $dbName = (string) DB_NAME;
        } else {
            $dbName = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
        }
        if ($dbName === '') {
            return ['ok' => false, 'error' => 'Could not detect database name.', 'path' => null];
        }

        $lines = [];
        $lines[] = '-- SMS 2 encrypted-backup source dump';
        $lines[] = '-- Generated: ' . date('c');
        $lines[] = 'SET NAMES utf8mb4;';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $lines[] = '';

        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $table = (string) $table;
            $createRow = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`')->fetch(PDO::FETCH_NUM);
            if (!$createRow || empty($createRow[1])) {
                continue;
            }
            $lines[] = 'DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`;';
            $lines[] = $createRow[1] . ';';
            $lines[] = '';

            $stmt = $pdo->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cols = [];
                $vals = [];
                foreach ($row as $col => $val) {
                    $cols[] = '`' . str_replace('`', '``', (string) $col) . '`';
                    if ($val === null) {
                        $vals[] = 'NULL';
                    } else {
                        $vals[] = $pdo->quote((string) $val);
                    }
                }
                $lines[] = 'INSERT INTO `' . str_replace('`', '``', $table) . '` ('
                    . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ');';
            }
            $lines[] = '';
        }
        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        $dir = smsBackupDir();
        $sqlPath = $dir . '/sms2_dump_' . date('Ymd_His') . '.sql';
        if (@file_put_contents($sqlPath, implode("\n", $lines), LOCK_EX) === false) {
            return ['ok' => false, 'error' => 'Could not write SQL dump.', 'path' => null];
        }
        @chmod($sqlPath, 0600);
        return ['ok' => true, 'error' => '', 'path' => $sqlPath];
    } catch (Throwable $e) {
        error_log('SMS2 dump failed: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'Dump failed.', 'path' => null];
    }
}

/**
 * Create encrypted backup (.sms2bak). Deletes the intermediate plaintext dump.
 *
 * @return array{ok:bool,error:string,path:?string}
 */
function smsCreateEncryptedBackup(string $password): array
{
    $dump = smsCreateSqlDump();
    if (empty($dump['ok']) || empty($dump['path'])) {
        return ['ok' => false, 'error' => $dump['error'] ?: 'Dump failed.', 'path' => null];
    }

    $encPath = preg_replace('/\.sql$/', '.sms2bak', (string) $dump['path']) ?: ((string) $dump['path'] . '.sms2bak');
    $enc = smsEncryptFileTo((string) $dump['path'], $encPath, $password);
    @unlink((string) $dump['path']);

    if (empty($enc['ok'])) {
        return ['ok' => false, 'error' => $enc['error'], 'path' => null];
    }
    return ['ok' => true, 'error' => '', 'path' => $encPath];
}
