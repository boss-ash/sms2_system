<?php
/**
 * SMS 2 – Per-module controls (maintenance + force logout)
 * + Global system maintenance mode
 */
declare(strict_types=1);

require_once __DIR__ . '/security.php';

/* ── Global system maintenance ─────────────────────────────────── */

function smsIsSystemInMaintenance(): bool
{
    return smsSetting('system_maintenance', '0') === '1';
}

function smsSystemMaintenanceMessage(): string
{
    $custom = trim(smsSetting('system_maintenance_msg', ''));
    if ($custom !== '') {
        return $custom;
    }
    return 'The system is temporarily unavailable for maintenance. Please try again later.';
}

function smsSystemMaintenanceKickEpoch(): int
{
    return (int) smsSetting('system_maintenance_kick_epoch', '0');
}

/**
 * Enable / disable global maintenance. When enabling, bump kick epoch so
 * non-admin sessions are ended on their next request (auto-logout).
 */
function smsSetSystemMaintenance(bool $enabled, string $message = ''): bool
{
    $ok = smsSetSetting('system_maintenance', $enabled ? '1' : '0');
    if ($message !== '') {
        smsSetSetting('system_maintenance_msg', mb_substr(trim($message), 0, 500));
    }
    if ($enabled) {
        smsSetSetting('system_maintenance_kick_epoch', (string) time());
    }
    return $ok;
}

/**
 * Auto-logout non-admin users when global maintenance is on.
 * Admins keep full access so they can turn it off.
 */
function smsEnforceSystemMaintenance(): void
{
    if (!smsIsSystemInMaintenance()) {
        return;
    }

    $scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $allowedSuffixes = [
        '/account/maintenance.php',
        '/login/logout.php',
        '/login/login.php',
        '/login/verify-2fa.php',
    ];
    foreach ($allowedSuffixes as $suffix) {
        if (str_ends_with($scriptPath, $suffix)) {
            return;
        }
    }

    $role = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';
    if (!function_exists('smsCanBypassSystemControls')) {
        require_once __DIR__ . '/authentication.php';
    }
    if ($role !== '' && smsCanBypassSystemControls($role)) {
        return;
    }

    // End session (auto-logout) then show maintenance immediately
    if (!empty($_SESSION['user_id'])) {
        require_once __DIR__ . '/authentication.php';
        if (function_exists('logout')) {
            logout();
        } else {
            $_SESSION = [];
        }
    }

    if (!headers_sent()) {
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . BASE_URL . '/account/maintenance.php');
        exit;
    }
}

function smsNormalizeModuleControlKey(string $moduleKey): string
{
    $moduleKey = strtolower(trim($moduleKey));
    if ($moduleKey === 'student-portal') {
        $moduleKey = 'student_portal';
    }
    if (in_array($moduleKey, ['crud', 'crowd'], true)) {
        $moduleKey = 'crad';
    }
    return preg_replace('/[^a-z0-9_\-]/', '', $moduleKey) ?? '';
}

function smsModuleMaintenanceSettingKey(string $moduleKey): string
{
    return 'module_maintenance_' . smsNormalizeModuleControlKey($moduleKey);
}

function smsModuleMaintenanceMsgKey(string $moduleKey): string
{
    return 'module_maintenance_msg_' . smsNormalizeModuleControlKey($moduleKey);
}

function smsModuleKickEpochKey(string $moduleKey): string
{
    return 'module_kick_epoch_' . smsNormalizeModuleControlKey($moduleKey);
}

function smsIsModuleInMaintenance(string $moduleKey): bool
{
    $key = smsNormalizeModuleControlKey($moduleKey);
    if ($key === '' || $key === 'user-management' || $key === 'dashboard') {
        return false;
    }
    return smsSetting(smsModuleMaintenanceSettingKey($key), '0') === '1';
}

function smsModuleMaintenanceMessage(string $moduleKey): string
{
    $key = smsNormalizeModuleControlKey($moduleKey);
    $custom = trim(smsSetting(smsModuleMaintenanceMsgKey($key), ''));
    if ($custom !== '') {
        return $custom;
    }
    $label = function_exists('smsModuleLabel') ? smsModuleLabel($key) : $key;
    return $label . ' is temporarily unavailable for maintenance. Please try again later.';
}

function smsSetModuleMaintenance(string $moduleKey, bool $enabled, string $message = ''): bool
{
    $key = smsNormalizeModuleControlKey($moduleKey);
    if ($key === '') {
        return false;
    }
    $ok = smsSetSetting(smsModuleMaintenanceSettingKey($key), $enabled ? '1' : '0');
    if ($message !== '') {
        smsSetSetting(smsModuleMaintenanceMsgKey($key), mb_substr(trim($message), 0, 500));
    }
    return $ok;
}

function smsModuleKickEpoch(string $moduleKey): int
{
    return (int) smsSetting(smsModuleKickEpochKey($moduleKey), '0');
}

function smsUserKickEpochKey(int $userId): string
{
    return 'user_kick_epoch_' . max(0, $userId);
}

function smsUserKickEpoch(int $userId): int
{
    if ($userId <= 0) {
        return 0;
    }
    return (int) smsSetting(smsUserKickEpochKey($userId), '0');
}

/** Bump epoch so module users with older sessions must sign in again. */
function smsForceLogoutModuleUsers(string $moduleKey): int
{
    $key = smsNormalizeModuleControlKey($moduleKey);
    if ($key === '') {
        return 0;
    }
    $epoch = time();
    smsSetSetting(smsModuleKickEpochKey($key), (string) $epoch);
    return $epoch;
}

/**
 * Force-logout specific users only (picked logout).
 * Returns how many user kick epochs were written.
 *
 * @param list<int> $userIds
 */
function smsForceLogoutUsers(array $userIds): int
{
    $epoch = time();
    $count = 0;
    foreach ($userIds as $rawId) {
        $userId = (int) $rawId;
        if ($userId <= 0) {
            continue;
        }
        if (smsSetSetting(smsUserKickEpochKey($userId), (string) $epoch)) {
            $count++;
        }
    }
    return $count;
}

/**
 * Whether this role should be kicked when a module force-logout fires.
 */
function smsRoleTiedToModule(string $roleKey, string $moduleKey): bool
{
    $roleKey = strtolower(trim($roleKey));
    $moduleKey = smsNormalizeModuleControlKey($moduleKey);
    if ($roleKey === '' || $moduleKey === '') {
        return false;
    }
    if (!function_exists('smsIsGrantedAdminRole')) {
        require_once __DIR__ . '/authentication.php';
    }
    if (smsIsGrantedAdminRole($roleKey)) {
        return false;
    }
    if (function_exists('smsPrimaryModuleForRole') && smsPrimaryModuleForRole($roleKey) === $moduleKey) {
        return true;
    }
    if (!function_exists('smsRolesForModule')) {
        return false;
    }
    $roles = smsRolesForModule($moduleKey);
    return in_array($roleKey, $roles, true);
}

/**
 * End session if Super Admin forced logout for this user (picked) or their module (all).
 */
function smsEnforceModuleForceLogout(): void
{
    if (empty($_SESSION['user_id'])) {
        return;
    }
    $roleKey = (string) ($_SESSION['user_role_key'] ?? '');
    if (!function_exists('smsCanBypassSystemControls')) {
        require_once __DIR__ . '/authentication.php';
    }
    if ($roleKey !== '' && smsCanBypassSystemControls($roleKey)) {
        return;
    }

    $loginAt = (int) ($_SESSION['login_at'] ?? 0);
    // Legacy sessions without login_at must still respect kicks (do not stamp "now")

    $userId = (int) $_SESSION['user_id'];
    $shouldKick = false;

    // Picked logout — only this account
    $userEpoch = smsUserKickEpoch($userId);
    if ($userEpoch > 0 && $userEpoch > $loginAt) {
        $shouldKick = true;
    }

    // Module-wide logout — all roles tied to the module
    if (!$shouldKick) {
        $modules = [];
        if (function_exists('smsPrimaryModuleForRole')) {
            $modules[] = smsPrimaryModuleForRole($roleKey);
        }
        if (function_exists('getAllowedModuleKeys')) {
            foreach (getAllowedModuleKeys() as $m) {
                $modules[] = (string) $m;
            }
        }
        $modules = array_values(array_unique(array_filter($modules)));

        foreach ($modules as $mod) {
            if (!smsRoleTiedToModule($roleKey, $mod)) {
                continue;
            }
            $epoch = smsModuleKickEpoch($mod);
            if ($epoch > 0 && $epoch > $loginAt) {
                $shouldKick = true;
                break;
            }
        }
    }

    if (!$shouldKick) {
        // Legacy sessions: stamp login_at only after confirming no kick is pending
        if ($loginAt <= 0) {
            $_SESSION['login_at'] = time();
        }
        return;
    }

    require_once __DIR__ . '/authentication.php';
    if (function_exists('logout')) {
        logout();
    } else {
        $_SESSION = [];
    }
    if (!headers_sent()) {
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . BASE_URL . '/login/login.php?forced=1');
        exit;
    }
}

/**
 * If this user’s primary module is in maintenance, park them on the
 * maintenance page until it is turned off (no dashboard / module escape).
 */
function smsEnforcePrimaryModuleMaintenance(): void
{
    if (empty($_SESSION['user_id'])) {
        return;
    }
    $roleKey = (string) ($_SESSION['user_role_key'] ?? '');
    if (!function_exists('smsCanBypassSystemControls')) {
        require_once __DIR__ . '/authentication.php';
    }
    if ($roleKey === '' || smsCanBypassSystemControls($roleKey)) {
        return;
    }

    if (!function_exists('smsPrimaryModuleForRole')) {
        require_once __DIR__ . '/security-workflow.php';
    }
    $primary = function_exists('smsPrimaryModuleForRole')
        ? smsNormalizeModuleControlKey((string) smsPrimaryModuleForRole($roleKey))
        : '';
    if ($primary === '' || $primary === 'user-management' || $primary === 'system') {
        return;
    }
    if (!smsIsModuleInMaintenance($primary)) {
        return;
    }

    $scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (
        str_ends_with($scriptPath, '/account/module-unavailable.php')
        || str_ends_with($scriptPath, '/login/logout.php')
        || str_ends_with($scriptPath, '/account/maintenance.php')
    ) {
        return;
    }

    if (!headers_sent()) {
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . BASE_URL . '/account/module-unavailable.php?module=' . rawurlencode($primary));
        exit;
    }
}
