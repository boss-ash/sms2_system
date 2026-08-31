<?php
/**
 * SMS 2 – Session archive store for module process lists (demo / UI separation).
 * Archived records leave the active list and live under ?view=archive.
 */
declare(strict_types=1);

if (!function_exists('smsMplArchiveKey')) {
    function smsMplArchiveKey(string $moduleKey, string $pageSlug): string
    {
        return strtolower(trim($moduleKey)) . '|' . strtolower(trim($pageSlug));
    }
}

if (!function_exists('smsMplArchiveBucket')) {
    /**
     * @return array<string, true>
     */
    function smsMplArchiveBucket(string $moduleKey, string $pageSlug): array
    {
        if (!isset($_SESSION['mpl_archive']) || !is_array($_SESSION['mpl_archive'])) {
            $_SESSION['mpl_archive'] = [];
        }
        $key = smsMplArchiveKey($moduleKey, $pageSlug);
        if (!isset($_SESSION['mpl_archive'][$key]) || !is_array($_SESSION['mpl_archive'][$key])) {
            $_SESSION['mpl_archive'][$key] = [];
        }
        return $_SESSION['mpl_archive'][$key];
    }
}

if (!function_exists('smsMplArchiveAdd')) {
    function smsMplArchiveAdd(string $moduleKey, string $pageSlug, string $ref): void
    {
        $ref = trim($ref);
        if ($ref === '') {
            return;
        }
        $bucket = smsMplArchiveBucket($moduleKey, $pageSlug);
        $bucket[$ref] = true;
        $_SESSION['mpl_archive'][smsMplArchiveKey($moduleKey, $pageSlug)] = $bucket;
    }
}

if (!function_exists('smsMplArchiveRemove')) {
    function smsMplArchiveRemove(string $moduleKey, string $pageSlug, string $ref): void
    {
        $ref = trim($ref);
        if ($ref === '') {
            return;
        }
        $bucket = smsMplArchiveBucket($moduleKey, $pageSlug);
        unset($bucket[$ref]);
        $_SESSION['mpl_archive'][smsMplArchiveKey($moduleKey, $pageSlug)] = $bucket;
    }
}

if (!function_exists('smsMplArchiveHas')) {
    function smsMplArchiveHas(string $moduleKey, string $pageSlug, string $ref): bool
    {
        $bucket = smsMplArchiveBucket($moduleKey, $pageSlug);
        return isset($bucket[trim($ref)]);
    }
}

if (!function_exists('smsMplArchiveCount')) {
    function smsMplArchiveCount(string $moduleKey, string $pageSlug): int
    {
        return count(smsMplArchiveBucket($moduleKey, $pageSlug));
    }
}
