<?php
/**
 * SMS 2 deployment database migration runner.
 *
 * CLI:
 *   php database/migrate.php
 *   php database/migrate.php --fresh
 *
 * Environment:
 *   HostForge/Laravel-style: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_CHARSET
 *   Project-specific: SMS2_DB_HOST, SMS2_DB_PORT, SMS2_DB_NAME, SMS2_DB_USER, SMS2_DB_PASS, SMS2_DB_CHARSET
 *   Optional CRAD override: CRAD_DB_HOST, CRAD_DB_PORT, CRAD_DB_NAME, CRAD_DB_USER, CRAD_DB_PASS, CRAD_DB_CHARSET
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modules/crad/config/config.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run this migration from CLI only.\n";
    exit(1);
}

$options = [
    'fresh' => in_array('--fresh', $argv ?? [], true),
    'force' => in_array('--force', $argv ?? [], true),
];

function migrateOut(string $message): void
{
    echo $message . PHP_EOL;
}

function migrateQuoteIdentifier(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function migrateConnectServer(string $host, string $port, string $user, string $pass, string $charset): PDO
{
    return new PDO(
        'mysql:host=' . $host . ';port=' . $port . ';charset=' . $charset,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

function migrateSqlCreateTables(string $sqlFile): array
{
    if (!is_readable($sqlFile)) {
        return [];
    }

    preg_match_all('/CREATE\s+TABLE\s+`([^`]+)`/i', (string) file_get_contents($sqlFile), $matches);

    return array_values(array_unique($matches[1] ?? []));
}

function migrateExistingTargetTableCount(PDO $pdo, array $tables): int
{
    if (!$tables) {
        return 0;
    }

    $stmt = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME IN (' . implode(',', array_fill(0, count($tables), '?')) . ')'
    );
    $stmt->execute($tables);

    return (int) $stmt->fetchColumn();
}

function migrateEnsureDatabase(PDO $pdo, string $database): void
{
    $quotedDatabase = migrateQuoteIdentifier($database);

    try {
        $pdo->exec(
            'CREATE DATABASE IF NOT EXISTS ' . $quotedDatabase .
            ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        return;
    } catch (PDOException $createError) {
        try {
            $pdo->exec('USE ' . $quotedDatabase);
            migrateOut('Create database skipped by host permissions; existing database is accessible.');
            return;
        } catch (PDOException) {
            throw $createError;
        }
    }
}

function migrateSplitSql(string $sql): array
{
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    $delimiter = ';';
    $statement = '';
    $statements = [];

    foreach (explode("\n", $sql) as $line) {
        $trimmed = trim($line);

        if (preg_match('/^DELIMITER\s+(.+)$/i', $trimmed, $matches)) {
            $delimiter = $matches[1];
            continue;
        }

        if ($delimiter !== ';') {
            if (str_ends_with($trimmed, $delimiter)) {
                $statement .= substr($line, 0, strrpos($line, $delimiter)) . "\n";
                $candidate = trim($statement);
                if ($candidate !== '') {
                    $statements[] = $candidate;
                }
                $statement = '';
                continue;
            }

            $statement .= $line . "\n";
            continue;
        }

        $statement .= $line . "\n";
        if (str_ends_with($trimmed, ';')) {
            $candidate = trim(substr($statement, 0, strrpos($statement, ';')));
            if ($candidate !== '') {
                $statements[] = $candidate;
            }
            $statement = '';
        }
    }

    $candidate = trim($statement);
    if ($candidate !== '') {
        $statements[] = $candidate;
    }

    return $statements;
}

function migrateApplySqlFile(PDO $pdo, string $sqlFile): int
{
    if (!is_readable($sqlFile)) {
        throw new RuntimeException('SQL file not readable: ' . $sqlFile);
    }

    $sql = (string) file_get_contents($sqlFile);
    $statements = migrateSplitSql($sql);
    $applied = 0;

    foreach ($statements as $statement) {
        $pdo->exec($statement);
        $applied++;
    }

    return $applied;
}

function migrateEnsureTrackingTable(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration_key` varchar(120) NOT NULL,
            `source_file` varchar(255) NOT NULL,
            `source_sha256` char(64) NOT NULL,
            `applied_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_migration_key` (`migration_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function migrateWasRecorded(PDO $pdo, string $migrationKey): bool
{
    migrateEnsureTrackingTable($pdo);

    $stmt = $pdo->prepare(
        'SELECT 1 FROM `schema_migrations` WHERE `migration_key` = ? LIMIT 1'
    );
    $stmt->execute([$migrationKey]);

    return (bool) $stmt->fetchColumn();
}

function migrateRecord(PDO $pdo, string $migrationKey, string $sourceFile): void
{
    migrateEnsureTrackingTable($pdo);

    $stmt = $pdo->prepare(
        'INSERT INTO `schema_migrations` (`migration_key`, `source_file`, `source_sha256`)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE
             `source_file` = VALUES(`source_file`),
             `source_sha256` = VALUES(`source_sha256`),
             `applied_at` = current_timestamp()'
    );
    $stmt->execute([
        $migrationKey,
        str_replace('\\', '/', $sourceFile),
        hash_file('sha256', $sourceFile),
    ]);
}

function migrateOneDatabase(array $target, array $options): void
{
    $label = $target['label'];
    $database = $target['database'];
    $quotedDatabase = migrateQuoteIdentifier($database);

    migrateOut('');
    migrateOut('== ' . $label . ' ==');
    migrateOut('SQL: ' . $target['sql_file']);
    migrateOut('DB : ' . $target['host'] . '/' . $database);

    $pdo = migrateConnectServer(
        $target['host'],
        $target['port'],
        $target['user'],
        $target['pass'],
        $target['charset']
    );

    if ($options['fresh']) {
        migrateOut('Dropping existing database because --fresh was provided...');
        $pdo->exec('DROP DATABASE IF EXISTS ' . $quotedDatabase);
    }

    migrateEnsureDatabase($pdo, $database);
    $pdo->exec('USE ' . $quotedDatabase);

    if (!$options['fresh'] && !$options['force']) {
        if (migrateWasRecorded($pdo, $target['migration_key'])) {
            migrateOut('Skipped: migration was already recorded. Use --force to re-apply.');
            return;
        }

        $targetTables = migrateSqlCreateTables($target['sql_file']);
        $existingTargetTables = migrateExistingTargetTableCount($pdo, $targetTables);
        if ($targetTables && $existingTargetTables === count($targetTables)) {
            migrateRecord($pdo, $target['migration_key'], $target['sql_file']);
            migrateOut('Skipped: target tables already exist; recorded this migration as applied.');
            return;
        }

        if ($existingTargetTables > 0) {
            throw new RuntimeException(
                'Partial schema detected for ' . $label .
                '. Use --fresh to rebuild, or fix the existing tables before migrating.'
            );
        }
    }

    $applied = migrateApplySqlFile($pdo, $target['sql_file']);
    migrateRecord($pdo, $target['migration_key'], $target['sql_file']);

    migrateOut('Applied ' . $applied . ' SQL statement(s).');
}

$targets = [
    [
        'label' => 'SMS2 main database',
        'migration_key' => '2026_08_28_sms2_db_dump',
        'host' => DB_HOST,
        'port' => DB_PORT,
        'database' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => DB_CHARSET,
        'sql_file' => __DIR__ . '/sms2_db.sql',
    ],
    [
        'label' => 'CRAD module database',
        'migration_key' => '2026_08_28_crad_db_dump',
        'host' => CRAD_DB_HOST,
        'port' => CRAD_DB_PORT,
        'database' => CRAD_DB_NAME,
        'user' => CRAD_DB_USER,
        'pass' => CRAD_DB_PASS,
        'charset' => CRAD_DB_CHARSET,
        'sql_file' => dirname(__DIR__) . '/modules/crad/database/crad_db.sql',
    ],
];

try {
    $connection = strtolower((string) sms2_env_first(['SMS2_DB_CONNECTION', 'DB_CONNECTION'], 'mysql'));
    if (!in_array($connection, ['mysql', 'mariadb'], true)) {
        throw new RuntimeException(
            'Unsupported DB_CONNECTION "' . $connection . '". Select MySQL/MariaDB on HostForge; this project ships MySQL dumps.'
        );
    }

    migrateOut('SMS 2 deployment migration started.');
    foreach ($targets as $target) {
        migrateOneDatabase($target, $options);
    }
    migrateOut('');
    migrateOut('Migration complete.');
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
