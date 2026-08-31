<?php
/**
 * SMS 2 – Module Security (Super Admin)
 * Lives only under User Management.
 *
 * Flow:
 *   1) All Modules picker
 *   2) Open a module → that module’s Security Settings
 *   3) Activity Logs / Password Management / Authenticator (in-page)
 *
 * URL:
 *   module-security.php?picker=1
 *   module-security.php?focus=crad
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/module-security-catalog.php';
require_once ROOT_PATH . '/includes/security-workflow.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/totp.php';
require_once ROOT_PATH . '/includes/authenticator-ui.php';
require_once ROOT_PATH . '/includes/passkey.php';
require_once ROOT_PATH . '/includes/security-ui.php';
require_once ROOT_PATH . '/includes/module-controls.php';

requireAuth();
requireSuperAdmin();
smsEnsureSecurityTables();

$catalog = smsModuleSecurityCatalog();
$moduleOptions = [];
foreach (array_keys($MODULES) as $key) {
    if (isset($catalog[$key])) {
        $moduleOptions[$key] = $catalog[$key];
    }
}
foreach ($catalog as $key => $info) {
    if (!isset($moduleOptions[$key])) {
        $moduleOptions[$key] = $info;
    }
}

$self = BASE_URL . '/modules/user-management/pages/module-security.php';

$normalizeMod = static function (string $raw) use ($moduleOptions): string {
    $key = strtolower(trim(rawurldecode($raw)));
    $map = [
        'student-portal' => 'student_portal',
        'crud'           => 'crad',
        'crowd'          => 'crad',
    ];
    if (isset($map[$key])) {
        $key = $map[$key];
    }
    return isset($moduleOptions[$key]) ? $key : '';
};

$pickerUrl = static function () use ($self): string {
    return $self . '?picker=1';
};

/** Canonical URL for a focused module (tabs are client-side — no view= in URL). */
$focusUrl = static function (string $mod) use ($self): string {
    return $self . '?focus=' . rawurlencode($mod);
};

$wantPicker = isset($_GET['picker']) || isset($_GET['home']);

/* ── All Modules picker ─────────────────────────────────────── */
if ($wantPicker && $_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['um_sec_focus']);
    if ((string) ($_GET['picker'] ?? '') !== '1' || isset($_GET['focus']) || isset($_GET['module']) || isset($_GET['m'])) {
        header('Location: ' . $pickerUrl());
        exit;
    }
}

/* ── Resolve focused module (never let empty POST override ?focus=) ─ */
$rawMod = trim((string) ($_POST['module_key'] ?? ''));
if ($rawMod === '') {
    $rawMod = trim((string) (
        $_GET['focus']
        ?? $_GET['module']
        ?? $_GET['m']
        ?? $_GET['sec_mod']
        ?? $_GET['mod']
        ?? ''
    ));
}
// Last resort on POST: parse focus from the request URI (form action)
if ($rawMod === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $reqUri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    if (preg_match('/[?&]focus=([^&]+)/', $reqUri, $m)) {
        $rawMod = rawurldecode($m[1]);
    }
}
$moduleKey = $normalizeMod($rawMod);

// Keep focus across refresh if query was dropped but session still has it
if ($moduleKey === '' && !$wantPicker && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $sessionFocus = $normalizeMod((string) ($_SESSION['um_sec_focus'] ?? ''));
    if ($sessionFocus !== '') {
        header('Location: ' . $focusUrl($sessionFocus));
        exit;
    }
}

$hasModule = $moduleKey !== '';

if ($hasModule) {
    $_SESSION['um_sec_focus'] = $moduleKey;
    // Canonical: ?focus=crad only (ignore legacy view/t/module params)
    if (
        $_SERVER['REQUEST_METHOD'] === 'GET'
        && (
            (string) ($_GET['focus'] ?? '') !== $moduleKey
            || isset($_GET['view'])
            || isset($_GET['t'])
            || isset($_GET['module'])
            || isset($_GET['m'])
            || isset($_GET['home'])
        )
    ) {
        header('Location: ' . $focusUrl($moduleKey));
        exit;
    }
} elseif (!$wantPicker && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // No module in URL/session on GET — send to All Modules picker
    header('Location: ' . $pickerUrl());
    exit;
}

$adminId = (int) getCurrentUserId();

/* ── POST actions — always return to the SAME focused module ─ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postModule = $moduleKey;
    if ($postModule === '') {
        $postModule = $normalizeMod(trim((string) ($_POST['module_key'] ?? '')));
    }
    if ($postModule === '') {
        $postModule = $normalizeMod((string) ($_SESSION['um_sec_focus'] ?? ''));
    }
    if ($postModule === '') {
        $_SESSION['flash_sec_error'] = 'Open a module first.';
        header('Location: ' . $pickerUrl());
        exit;
    }

    $_SESSION['um_sec_focus'] = $postModule;
    $returnUrl = $focusUrl($postModule);

    if (!csrfVerify()) {
        $_SESSION['flash_sec_error'] = 'Security check failed. Please try again.';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    $action = (string) ($_POST['action'] ?? '');
    $panelForAction = static function (string $act): string {
        if (str_starts_with($act, 'admin_auth_') || $act === 'admin_passkey_clear' || $act === 'admin_passkey_remove') {
            return 'authenticator';
        }
        if (in_array($act, [
            'approve_request', 'reject_request', 'admin_reset_user',
        ], true)) {
            return 'passwords';
        }
        if (in_array($act, ['module_maintenance_on', 'module_maintenance_off', 'module_force_logout'], true)) {
            return 'module';
        }
        return 'logs';
    };
    $_SESSION['um_sec_panel'] = $panelForAction($action);
    if ($_SESSION['um_sec_panel'] === 'authenticator') {
        $returnUrl .= '#panel-authenticator';
    } elseif ($_SESSION['um_sec_panel'] === 'passwords') {
        $returnUrl .= '#panel-passwords';
    } elseif ($_SESSION['um_sec_panel'] === 'module') {
        $returnUrl .= '#panel-module';
    }

    if ($action === 'approve_request') {
        if ((string) ($_POST['confirm_approve'] ?? '') !== '1') {
            $_SESSION['flash_sec_error'] = 'Approval cancelled — confirmation required.';
        } else {
            $result = smsApprovePasswordRequest((int) ($_POST['request_id'] ?? 0), $adminId);
            if ($result['ok']) {
                $_SESSION['flash_sec_success'] = 'Approved. The user’s chosen password is now active.';
            } else {
                $_SESSION['flash_sec_error'] = $result['error'] ?? 'Approve failed.';
            }
        }
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'reject_request') {
        $note = trim((string) ($_POST['admin_note'] ?? ''));
        if (smsRejectPasswordRequest((int) ($_POST['request_id'] ?? 0), $adminId, $note)) {
            $_SESSION['flash_sec_success'] = $note !== ''
                ? 'Request rejected. Your note was saved for the user.'
                : 'Request rejected.';
        } else {
            $_SESSION['flash_sec_error'] = 'Could not reject request.';
        }
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_reset_user') {
        $targetId = (int) ($_POST['target_user_id'] ?? 0);
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['new_password_confirm'] ?? '');
        $forceChange = !empty($_POST['force_change']);
        $allowedIds = array_map(static fn($u) => (int) $u['id'], smsUsersForModuleReset($postModule));
        $label = $moduleOptions[$postModule]['label'] ?? $postModule;
        $strength = smsValidatePasswordStrength($newPassword);

        // Single module user → no selector needed (auto-target)
        if ($targetId <= 0 && count($allowedIds) === 1) {
            $targetId = (int) $allowedIds[0];
        }

        if ($targetId <= 0) {
            $_SESSION['flash_sec_error'] = 'Select a user.';
        } elseif ($targetId === $adminId) {
            $_SESSION['flash_sec_error'] = 'You cannot reset your own password on this screen.';
        } elseif (!in_array($targetId, $allowedIds, true)) {
            $_SESSION['flash_sec_error'] = 'That user is not part of ' . $label . '.';
        } elseif (!$strength['ok']) {
            $_SESSION['flash_sec_error'] = $strength['message'];
        } elseif ($newPassword !== $confirmPassword) {
            $_SESSION['flash_sec_error'] = 'New password and confirmation do not match.';
        } elseif (!smsSetUserPassword($targetId, $newPassword, $forceChange)) {
            $_SESSION['flash_sec_error'] = 'Could not reset password.';
        } else {
            logActivity(
                'password_reset',
                'Reset password for ' . smsUserLogLabel($targetId)
                    . ($forceChange ? ' (must change on next login)' : ''),
                $postModule
            );
            $_SESSION['flash_sec_success'] = $forceChange
                ? 'Password reset. The user must change it on next login.'
                : 'Password reset successfully.';
            $_SESSION['um_sec_panel'] = 'passwords';
        }
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'module_maintenance_on' || $action === 'module_maintenance_off') {
        $enabled = $action === 'module_maintenance_on';
        $msg = trim((string) ($_POST['maintenance_message'] ?? ''));
        if (smsSetModuleMaintenance($postModule, $enabled, $msg)) {
            $label = $moduleOptions[$postModule]['label'] ?? $postModule;
            logActivity(
                'update',
                ($enabled ? 'Enabled' : 'Disabled') . ' maintenance for ' . $label,
                $postModule
            );
            $_SESSION['flash_sec_success'] = $enabled
                ? $label . ' is now in maintenance. Module users will see a maintenance page (they can still sign in).'
                : $label . ' maintenance is off. Module is available again.';
        } else {
            $_SESSION['flash_sec_error'] = 'Could not update maintenance setting.';
        }
        $_SESSION['um_sec_panel'] = 'module';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'module_force_logout') {
        $label = $moduleOptions[$postModule]['label'] ?? $postModule;
        $scope = strtolower(trim((string) ($_POST['logout_scope'] ?? 'all')));
        $allowedUsers = smsUsersForModuleReset($postModule);
        $allowedIds = array_map(static fn($u) => (int) $u['id'], $allowedUsers);

        if ($scope === 'selected') {
            $rawIds = $_POST['logout_user_ids'] ?? [];
            if (!is_array($rawIds)) {
                $rawIds = [];
            }
            $picked = [];
            foreach ($rawIds as $rawId) {
                $id = (int) $rawId;
                if ($id > 0 && in_array($id, $allowedIds, true)) {
                    $picked[] = $id;
                }
            }
            $picked = array_values(array_unique($picked));
            if ($picked === []) {
                $_SESSION['flash_sec_error'] = 'Select at least one ' . $label . ' user to log out.';
            } else {
                $n = smsForceLogoutUsers($picked);
                if ($n > 0) {
                    $names = [];
                    foreach ($allowedUsers as $u) {
                        if (in_array((int) $u['id'], $picked, true)) {
                            $names[] = (string) ($u['full_name'] ?: $u['username']);
                        }
                    }
                    $who = count($names) <= 3
                        ? implode(', ', $names)
                        : implode(', ', array_slice($names, 0, 3)) . ' (+' . (count($names) - 3) . ' more)';
                    logActivity(
                        'update',
                        'Forced logout for ' . count($picked) . ' ' . $label . ' user(s): ' . $who,
                        $postModule
                    );
                    $_SESSION['flash_sec_success'] = 'Force logout sent for '
                        . count($picked)
                        . ' selected '
                        . $label
                        . ' user'
                        . (count($picked) === 1 ? '' : 's')
                        . '. They will be signed out on their next request.';
                } else {
                    $_SESSION['flash_sec_error'] = 'Could not force logout selected users.';
                }
            }
        } else {
            $epoch = smsForceLogoutModuleUsers($postModule);
            if ($epoch > 0) {
                logActivity('update', 'Forced logout for all ' . $label . ' users', $postModule);
                $_SESSION['flash_sec_success'] = 'Force logout sent. All active '
                    . $label
                    . ' users will be signed out on their next request.';
            } else {
                $_SESSION['flash_sec_error'] = 'Could not force logout.';
            }
        }
        $_SESSION['um_sec_panel'] = 'module';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_auth_disable') {
        $targetId = (int) ($_POST['target_user_id'] ?? 0);
        $allowedIds = array_map(static fn($u) => (int) $u['id'], smsUsersForModuleReset($postModule));
        $label = $moduleOptions[$postModule]['label'] ?? $postModule;
        if ($targetId <= 0 || !in_array($targetId, $allowedIds, true)) {
            $_SESSION['flash_sec_error'] = 'Select a valid ' . $label . ' user.';
        } elseif (!smsAuthenticatorIsEnabled($targetId)) {
            $_SESSION['flash_sec_error'] = 'Authenticator is already off for that user.';
        } else {
            smsAuthenticatorDisable($targetId);
            unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
            logActivity(
                'update',
                'Turned off Authenticator for ' . smsUserLogLabel($targetId),
                $postModule
            );
            $_SESSION['flash_sec_success'] = 'Authenticator turned off for that user.';
        }
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_auth_turn_on') {
        $targetId = (int) ($_POST['target_user_id'] ?? 0);
        $allowedIds = array_map(static fn($u) => (int) $u['id'], smsUsersForModuleReset($postModule));
        $label = $moduleOptions[$postModule]['label'] ?? $postModule;
        if ($targetId <= 0 || !in_array($targetId, $allowedIds, true)) {
            $_SESSION['flash_sec_error'] = 'Select a valid ' . $label . ' user.';
        } elseif (smsAuthenticatorIsEnabled($targetId)) {
            $_SESSION['flash_sec_error'] = 'Authenticator is already on for that user.';
        } else {
            $secret = smsAuthenticatorBeginSetup($targetId);
            if (!$secret) {
                $_SESSION['flash_sec_error'] = 'Could not start Authenticator setup.';
            } else {
                $_SESSION['admin_auth_setup_for'] = $targetId;
                $_SESSION['admin_auth_setup_at'] = time();
                logActivity(
                    'update',
                    'Started Authenticator setup for ' . smsUserLogLabel($targetId),
                    $postModule
                );
                $_SESSION['flash_sec_success'] = 'Scan the QR (or enter the key), then enter the app code to turn Authenticator on.';
            }
        }
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_auth_confirm') {
        $targetId = (int) ($_SESSION['admin_auth_setup_for'] ?? 0);
        $code = trim((string) ($_POST['totp_code'] ?? ''));
        $allowedIds = array_map(static fn($u) => (int) $u['id'], smsUsersForModuleReset($postModule));
        if (
            $targetId <= 0
            || !in_array($targetId, $allowedIds, true)
            || empty($_SESSION['admin_auth_setup_at'])
            || ((int) $_SESSION['admin_auth_setup_at'] + 900) < time()
        ) {
            unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
            $_SESSION['flash_sec_error'] = 'Authenticator setup expired. Turn On again.';
        } elseif (!smsAuthenticatorConfirmEnable($targetId, $code)) {
            $_SESSION['flash_sec_error'] = 'Invalid Authenticator code. Try again.';
        } else {
            unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
            logActivity(
                'update',
                'Turned on Authenticator for ' . smsUserLogLabel($targetId),
                $postModule
            );
            $_SESSION['flash_sec_success'] = 'Authenticator is Active for that user.';
        }
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_auth_cancel') {
        $targetId = (int) ($_SESSION['admin_auth_setup_for'] ?? 0);
        if ($targetId > 0) {
            smsAuthenticatorDisable($targetId);
            logActivity(
                'update',
                'Cancelled Authenticator setup for ' . smsUserLogLabel($targetId),
                $postModule
            );
        }
        unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
        $_SESSION['flash_sec_success'] = 'Authenticator setup cancelled.';
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    if ($action === 'admin_passkey_remove') {
        $targetId = (int) ($_POST['target_user_id'] ?? 0);
        $passkeyId = (int) ($_POST['passkey_id'] ?? 0);
        $allowedIds = array_map(static fn($u) => (int) $u['id'], smsUsersForModuleReset($postModule));
        $label = $moduleOptions[$postModule]['label'] ?? $postModule;
        if ($targetId <= 0 || !in_array($targetId, $allowedIds, true)) {
            $_SESSION['flash_sec_error'] = 'Select a valid ' . $label . ' user.';
        } elseif ($passkeyId <= 0) {
            $_SESSION['flash_sec_error'] = 'Select which passkey to remove.';
        } elseif (!smsPasskeyDelete($targetId, $passkeyId)) {
            $_SESSION['flash_sec_error'] = 'Could not remove that passkey (not found for this user).';
        } else {
            logActivity(
                'update',
                'Removed passkey #' . $passkeyId . ' for ' . smsUserLogLabel($targetId),
                $postModule
            );
            $_SESSION['flash_sec_success'] = 'Selected passkey removed.';
        }
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    // Legacy "clear all" disabled — Super Admin must pick a specific passkey.
    if ($action === 'admin_passkey_clear') {
        $_SESSION['flash_sec_error'] = 'Choose a specific passkey to remove. Clearing all at once is not allowed.';
        $_SESSION['um_sec_panel'] = 'authenticator';
        header('Location: ' . $returnUrl, true, 303);
        exit;
    }

    header('Location: ' . $returnUrl, true, 303);
    exit;
}

/* ── Page data ─────────────────────────────────────────────── */
$allPending = [];
$pendingByModule = [];
$logs = [];
$pendingRequests = [];
$resetUsers = [];
$authUsers = [];
$moduleLabel = '';
$pendingCount = 0;
$adminAuthSetupId = 0;
$adminAuthSetupUser = null;
$adminAuthPendingSecret = null;
$initialPanel = 'logs';

if (!$hasModule) {
    $allPending = smsPendingPasswordRequests();
    foreach ($allPending as $req) {
        $mk = strtolower((string) $req['module_key']);
        $pendingByModule[$mk] = ($pendingByModule[$mk] ?? 0) + 1;
    }
} else {
    $moduleLabel = $moduleOptions[$moduleKey]['label'] ?? smsModuleLabel($moduleKey);
    $viewKey = 'um_sec_view_' . $moduleKey;
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_SESSION[$viewKey])) {
        logActivity('view', 'Opened ' . $moduleLabel . ' Module Security', $moduleKey);
        $_SESSION[$viewKey] = time();
    }
    $logs = smsModuleActivityLogs($moduleKey, 300);
    $pendingRequests = smsPendingPasswordRequests($moduleKey);
    $resetUsers = smsUsersForModuleReset($moduleKey);
    $pendingCount = count($pendingRequests);
    $moduleMaintenanceOn = smsIsModuleInMaintenance($moduleKey);
    $moduleMaintenanceMsg = smsSetting(smsModuleMaintenanceMsgKey($moduleKey), '');
    $minLen = (int) smsSetting('min_password_length', '8');
    $singleResetUser = count($resetUsers) === 1 ? $resetUsers[0] : null;
    $authUsers = [];
    foreach ($resetUsers as $u) {
        $uid = (int) $u['id'];
        $auth = smsAuthenticatorGet($uid);
        $u['authenticator_enabled'] = $auth && !empty($auth['enabled']);
        $u['passkey_count'] = smsPasskeyCount($uid);
        $u['passkeys'] = smsPasskeysForUser($uid);
        $authUsers[] = $u;
    }
    $adminAuthSetupId = (int) ($_SESSION['admin_auth_setup_for'] ?? 0);
    if (
        $adminAuthSetupId > 0
        && !empty($_SESSION['admin_auth_setup_at'])
        && ((int) $_SESSION['admin_auth_setup_at'] + 900) >= time()
    ) {
        foreach ($authUsers as $u) {
            if ((int) $u['id'] === $adminAuthSetupId) {
                $adminAuthSetupUser = $u;
                break;
            }
        }
        if ($adminAuthSetupUser) {
            $row = smsAuthenticatorGet($adminAuthSetupId);
            $adminAuthPendingSecret = $row['pending_secret'] ?? ($row['secret'] ?? null);
            if (!$adminAuthPendingSecret) {
                $adminAuthPendingSecret = smsAuthenticatorBeginSetup($adminAuthSetupId);
            }
        } else {
            unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
            $adminAuthSetupId = 0;
        }
    } else {
        unset($_SESSION['admin_auth_setup_for'], $_SESSION['admin_auth_setup_at']);
        $adminAuthSetupId = 0;
    }
    if (!empty($_SESSION['um_sec_panel'])) {
        $initialPanel = (string) $_SESSION['um_sec_panel'];
        unset($_SESSION['um_sec_panel']);
    }
}

$success = '';
$error = '';
$otpDevCode = '';
if (!empty($_SESSION['flash_sec_success'])) {
    $success = (string) $_SESSION['flash_sec_success'];
    unset($_SESSION['flash_sec_success']);
}
if (!empty($_SESSION['flash_sec_error'])) {
    $error = (string) $_SESSION['flash_sec_error'];
    unset($_SESSION['flash_sec_error']);
}
if (!empty($_SESSION['flash_sec_otp'])) {
    $otpDevCode = (string) $_SESSION['flash_sec_otp'];
    unset($_SESSION['flash_sec_otp']);
}

$pageTitle    = $hasModule ? ($moduleLabel . ' Security Settings') : 'Module Security';
$activeModule = 'user-management';
$activePage   = 'module-security';
$breadcrumbs  = [
    ['label' => 'User Management', 'url' => BASE_URL . '/modules/user-management/index.php'],
    [
        'label' => 'Module Security',
        'url'   => $hasModule ? $pickerUrl() : null,
    ],
];
if ($hasModule) {
    $breadcrumbs[] = ['label' => $moduleLabel . ' Security Settings', 'url' => null];
}

require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/layout-start.php';

// Sidebar foreach must not overwrite the focused Module Security key (e.g. crad).
if ($hasModule) {
    $moduleKey = $normalizeMod((string) ($_SESSION['um_sec_focus'] ?? $moduleKey));
    if ($moduleKey === '') {
        $moduleKey = $normalizeMod((string) ($_GET['focus'] ?? ''));
    }
}
?>
<link href="<?= BASE_URL ?>/modules/user-management/assets/css/user-management.css" rel="stylesheet">
<style>
.sms-sec-pick-grid .sms-sec-pick {
    display: block;
    height: 100%;
    text-decoration: none;
    color: inherit;
}
.sms-sec-pick .sms-sec-card {
    height: 100%;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}
.sms-sec-pick:hover .sms-sec-card {
    border-color: rgba(41, 78, 203, 0.35);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
}
.sms-sec-pick .sms-sec-card-head {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: 0;
}
</style>

<?php
$pageBannerIcon        = 'fa-shield-alt';
$pageBannerDescription = 'Choose a module to open its Security Settings — activity logs, password requests, and authenticator controls.';
renderBreadcrumbs($breadcrumbs);
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= smsIcon('check-circle', ['class' => 'me-2']) ?><?= e($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= smsIcon('exclamation-circle', ['class' => 'me-2']) ?><?= e($error) ?></div>
<?php endif; ?>
<?php if ($otpDevCode !== ''): ?>
    <div class="alert alert-warning">
        <strong>Local OTP (email not delivered):</strong>
        <code class="fs-5 ms-1"><?= e($otpDevCode) ?></code>
    </div>
<?php endif; ?>

<?php if (!$hasModule): ?>

    <div class="row g-3 sms-sec-pick-grid mb-4">
        <?php foreach ($moduleOptions as $optKey => $info): ?>
            <?php $pending = (int) ($pendingByModule[$optKey] ?? 0); ?>
            <div class="col-md-6">
                <a href="<?= e($focusUrl($optKey)) ?>" class="sms-sec-pick">
                    <section class="card sms-sec-card mb-0">
                        <div class="card-body">
                            <div class="sms-sec-card-head">
                                <div class="sms-sec-card-title">
                                    <span class="sms-sec-icon"><?= smsIcon($info['icon'] ?? 'shield', ['aria-hidden' => 'true']) ?></span>
                                    <div>
                                        <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2 flex-wrap">
                                            <?= e($info['label']) ?>
                                            <?php if ($pending > 0): ?>
                                                <span class="badge bg-danger"><?= $pending ?> pending</span>
                                            <?php endif; ?>
                                        </h2>
                                        <p class="sms-sec-lead mb-0 mt-1">Activity Logs · Passwords · Authenticator</p>
                                    </div>
                                </div>
                                <?= smsIcon('chevron-right', ['class' => 'text-sms-primary', 'aria-hidden' => 'true', 'style' => 'opacity:.55;font-size:.75rem;']) ?>
                            </div>
                        </div>
                    </section>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div id="secModuleRoot" class="sms-sec-root" data-focus-module="<?= e($moduleKey) ?>" data-initial-panel="<?= e($initialPanel) ?>" data-url-mode="admin">
        <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h1>
                    <i class="fas <?= e($moduleOptions[$moduleKey]['icon'] ?? 'fa-shield-alt') ?> text-sms-primary me-2"></i>
                    <?= e($moduleLabel) ?> Security Settings
                </h1>
                <p class="mb-0">
                    Security tools for <strong><?= e($moduleLabel) ?></strong> only.
                    Switch modules anytime with <strong>All modules</strong>.
                </p>
            </div>
            <a class="btn btn-outline-secondary btn-sm" href="<?= e($pickerUrl()) ?>">
                <?= smsIcon('th-large', ['class' => 'me-1']) ?>All modules
            </a>
        </div>

        <ul class="nav nav-tabs sms-sec-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" data-sec-tab="logs" data-sec-tab-card="logs" role="tab" aria-controls="panel-logs">
                    <?= smsIcon('history', ['class' => 'me-1']) ?>Activity Logs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" data-sec-tab="passwords" data-sec-tab-card="passwords" role="tab" aria-controls="panel-passwords">
                    <?= smsIcon('key', ['class' => 'me-1']) ?>Password Management
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger ms-1"><?= (int) $pendingCount ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" data-sec-tab="authenticator" data-sec-tab-card="authenticator" role="tab" aria-controls="panel-authenticator">
                    <?= smsIcon('fingerprint', ['class' => 'me-1']) ?>Authenticator &amp; Passkey
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" data-sec-tab="module" data-sec-tab-card="module" role="tab" aria-controls="panel-module">
                    <?= smsIcon('cogs', ['class' => 'me-1']) ?>Module Management
                    <?php if (!empty($moduleMaintenanceOn)): ?>
                        <span class="badge text-bg-warning ms-1">Maint</span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <!-- Activity Logs panel -->
        <div id="panel-logs" class="sec-panel sms-sec-panel" role="tabpanel" data-sec-panel="logs">
            <?php
            $logActions = [];
            foreach ($logs as $log) {
                $a = (string) ($log['action'] ?? '');
                if ($a !== '') {
                    $logActions[$a] = true;
                }
            }
            ksort($logActions);
            ?>
            <section class="card sms-sec-card">
                <div class="card-body">
                    <div class="sms-sec-card-head">
                        <div class="sms-sec-card-title">
                            <span class="sms-sec-icon"><?= smsIcon('history', ['aria-hidden' => 'true']) ?></span>
                            <div>
                                <h2 class="h5 fw-bold mb-0"><?= e($moduleLabel) ?> — Activity Logs</h2>
                                <p class="sms-sec-lead mb-0 mt-1">All <?= e($moduleLabel) ?> security events — including Super Admin actions for this module.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small text-muted" id="secLogCount"><?= count($logs) ?> shown</span>
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-sms-export-csv="#secLogTable"
                                    data-sms-export-rows="tbody tr.sec-log-row"
                                    data-sms-export-filename="<?= e($moduleKey) ?>-activity-logs.csv">
                                <?= smsIcon('file-export', ['class' => 'me-1']) ?>Export CSV
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="secLogClear">Clear filters</button>
                        </div>
                    </div>
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small mb-1" for="secLogUser">User</label>
                            <input type="text" id="secLogUser" class="form-control form-control-sm" placeholder="Name…">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1" for="secLogAction">Action type</label>
                            <select id="secLogAction" class="form-select form-select-sm">
                                <option value="">All actions</option>
                                <?php foreach (array_keys($logActions) as $actionName): ?>
                                    <option value="<?= e($actionName) ?>"><?= e(ucfirst(str_replace('_', ' ', $actionName))) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-1" for="secLogDateFrom">Date from</label>
                            <input type="date" id="secLogDateFrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small mb-1" for="secLogDateTo">Date to</label>
                            <input type="date" id="secLogDateTo" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="sec-log-scroll table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0" id="secLogTable">
                            <thead class="sec-log-thead">
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Detail</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$logs): ?>
                                    <tr class="sec-log-empty-static">
                                        <td colspan="5" class="text-center text-muted py-4">No activity logs for this module yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="sec-log-row"
                                            data-action="<?= e((string) ($log['action'] ?? '')) ?>"
                                            data-user="<?= e(strtolower((string) ($log['user_name'] ?? ''))) ?>"
                                            data-date="<?= e((string) ($log['log_date'] ?? '')) ?>">
                                            <td class="small text-nowrap"><?= e($log['time']) ?></td>
                                            <td>
                                                <div class="fw-semibold"><?= e($log['user_name'] ?? 'System') ?></div>
                                                <div class="small text-muted"><?= e($log['role_key'] ?? '') ?></div>
                                            </td>
                                            <td><span class="badge text-bg-light"><?= e($log['action']) ?></span></td>
                                            <td class="small"><?= e($log['detail']) ?></td>
                                            <td class="small text-muted"><?= e($log['ip_address'] ?? '—') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="sec-log-empty-filter" hidden>
                                        <td colspan="5" class="text-center text-muted py-4">No logs match the selected filters.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <!-- Password Management panel -->
        <div id="panel-passwords" class="sec-panel sms-sec-panel" role="tabpanel" data-sec-panel="passwords" hidden>
            <section class="card sms-sec-card mb-4">
                <div class="card-body">
                    <div class="sms-sec-card-head">
                        <div class="sms-sec-card-title">
                            <span class="sms-sec-icon"><?= smsIcon('key', ['aria-hidden' => 'true']) ?></span>
                            <div>
                                <h2 class="h5 fw-bold mb-0"><?= e($moduleLabel) ?> — Password Management</h2>
                                <p class="sms-sec-lead mb-0 mt-1">Approve staff requests or reset a <?= e($moduleLabel) ?> user password.</p>
                            </div>
                        </div>
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-danger"><?= $pendingCount ?> pending</span>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 sms-sec-pw-split">
                        <div class="col-lg-7">
                            <div class="sms-sec-pw-box h-100">
                                <h3 class="h6 fw-bold mb-3">
                                    <?= smsIcon('inbox', ['class' => 'text-sms-primary me-1', 'aria-hidden' => 'true']) ?>Password requests
                                </h3>
                                <div class="table-responsive sms-sec-list mb-0">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Reason</th>
                                                <th>Requested</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$pendingRequests): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        No pending requests for <?= e($moduleLabel) ?>.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($pendingRequests as $req): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold"><?= e($req['full_name']) ?></div>
                                                            <div class="small text-muted"><?= e($req['email']) ?> · <?= e($req['role_key']) ?></div>
                                                        </td>
                                                        <td class="small"><?= e($req['reason'] ?: '—') ?></td>
                                                        <td class="small text-nowrap"><?= e(date('M j, Y g:i A', strtotime((string) $req['created_at']))) ?></td>
                                                        <td class="text-end text-nowrap">
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                    data-bs-toggle="modal" data-bs-target="#approveModal"
                                                                    data-request-id="<?= (int) $req['id'] ?>"
                                                                    data-user-name="<?= e($req['full_name']) ?>">Approve</button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                                    data-request-id="<?= (int) $req['id'] ?>"
                                                                    data-user-name="<?= e($req['full_name']) ?>">Reject</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="sms-sec-pw-box h-100">
                                <h3 class="h6 fw-bold mb-3">
                                    <?= smsIcon('user-cog', ['class' => 'text-sms-primary me-1', 'aria-hidden' => 'true']) ?>Reset staff password
                                    <?php if ($singleResetUser): ?>
                                        <span class="fw-normal text-muted">· one user</span>
                                    <?php endif; ?>
                                </h3>
                                <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" class="row g-3"
                                      data-sms-confirm-submit="Are you sure you want to reset this user’s password? They will need the new password to sign in."
                                      data-sms-confirm-title="Reset password?"
                                      data-sms-confirm-ok="Yes, reset password"
                                      id="adminResetPasswordForm">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                                    <input type="hidden" name="action" value="admin_reset_user">
                                    <?php if ($singleResetUser): ?>
                                        <input type="hidden" name="target_user_id" value="<?= (int) $singleResetUser['id'] ?>">
                                        <div class="col-12">
                                            <div class="border rounded-3 px-3 py-2 bg-transparent">
                                                <strong><?= e((string) $singleResetUser['full_name']) ?></strong>
                                                <span class="text-muted"> · <?= e((string) ($singleResetUser['email'] ?: $singleResetUser['username'])) ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold" for="target_user_id">User</label>
                                            <select class="form-select" id="target_user_id" name="target_user_id" required <?= !$resetUsers ? 'disabled' : '' ?>>
                                                <option value="">Select <?= e($moduleLabel) ?> user…</option>
                                                <?php if (!$resetUsers): ?>
                                                    <option value="" disabled>No users found for this module</option>
                                                <?php else: ?>
                                                    <?php foreach ($resetUsers as $u): ?>
                                                        <option value="<?= (int) $u['id'] ?>">
                                                            <?= e($u['full_name']) ?> — <?= e($u['email'] ?: $u['username']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold" for="new_password">New password</label>
                                        <?= smsPasswordInput([
                                            'id' => 'new_password',
                                            'name' => 'new_password',
                                            'required' => true,
                                            'autocomplete' => 'new-password',
                                            'minlength' => $minLen,
                                        ]) ?>
                                        <?= smsPasswordStrengthMarkup('new_password') ?>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold" for="new_password_confirm">Confirm</label>
                                        <?= smsPasswordInput([
                                            'id' => 'new_password_confirm',
                                            'name' => 'new_password_confirm',
                                            'required' => true,
                                            'autocomplete' => 'new-password',
                                            'minlength' => $minLen,
                                        ]) ?>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="force_change" name="force_change" checked>
                                            <label class="form-check-label" for="force_change">
                                                Require change on next login
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sms-primary" <?= !$resetUsers ? 'disabled' : '' ?>>
                                            <?= smsIcon('key', ['class' => 'me-1']) ?>Reset password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade sms-confirm-modal" id="approveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered sms-confirm-dialog">
                    <div class="modal-content sms-confirm-content">
                        <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                            <input type="hidden" name="action" value="approve_request">
                            <input type="hidden" name="confirm_approve" value="1">
                            <input type="hidden" name="request_id" id="approveRequestId" value="">
                            <div class="sms-confirm-header">
                                <div class="sms-confirm-header-text">
                                    <span class="sms-confirm-kicker">Password request</span>
                                    <h5 class="modal-title sms-confirm-title">Confirm approval</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="sms-confirm-body">
                                <div class="sms-confirm-icon sms-confirm-icon--primary" aria-hidden="true">
                                    <?= smsIcon('check-circle') ?>
                                </div>
                                <p class="sms-confirm-msg mb-1">Approve password request for <strong id="approveUserName">this user</strong>?</p>
                                <p class="small text-muted mb-0">Their chosen password will become active immediately.</p>
                            </div>
                            <div class="sms-confirm-footer">
                                <button type="button" class="btn btn-outline-secondary sms-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn sms-confirm-ok sms-confirm-ok--primary">Yes, approve</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade sms-confirm-modal" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered sms-confirm-dialog sms-confirm-dialog--wide">
                    <div class="modal-content sms-confirm-content">
                        <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                            <input type="hidden" name="action" value="reject_request">
                            <input type="hidden" name="request_id" id="rejectRequestId" value="">
                            <div class="sms-confirm-header">
                                <div class="sms-confirm-header-text">
                                    <span class="sms-confirm-kicker">Password request</span>
                                    <h5 class="modal-title sms-confirm-title">Reject request</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="sms-confirm-body sms-confirm-body--form">
                                <div class="sms-confirm-icon sms-confirm-icon--danger" aria-hidden="true">
                                    <?= smsIcon('times-circle') ?>
                                </div>
                                <p class="sms-confirm-msg mb-0">Reject request for <strong id="rejectUserName">this user</strong>.</p>
                                <div class="w-100 text-start">
                                    <label class="form-label fw-semibold" for="admin_note">
                                        Note to user <span class="text-muted fw-normal">(optional)</span>
                                    </label>
                                    <textarea class="form-control" id="admin_note" name="admin_note" rows="3" maxlength="500"
                                              placeholder="Optional message the user will see…"></textarea>
                                </div>
                            </div>
                            <div class="sms-confirm-footer">
                                <button type="button" class="btn btn-outline-secondary sms-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn sms-confirm-ok sms-confirm-ok--danger">Reject request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authenticator panel -->
        <div id="panel-authenticator" class="sec-panel sms-sec-panel" role="tabpanel" data-sec-panel="authenticator" hidden>
            <?php if ($adminAuthSetupUser && $adminAuthPendingSecret): ?>
                <?php
                $setupLabel = (string) ($adminAuthSetupUser['email'] ?: $adminAuthSetupUser['full_name']);
                $setupUri = smsTotpOtpAuthUri($adminAuthPendingSecret, $setupLabel);
                ?>
                <section class="card sms-sec-card mb-4">
                    <div class="card-body">
                        <div class="sms-sec-card-head">
                            <div class="sms-sec-card-title">
                                <span class="sms-sec-icon"><?= smsIcon('qrcode', ['aria-hidden' => 'true']) ?></span>
                                <h2 class="h5 fw-bold mb-0">Turn On — <?= e($adminAuthSetupUser['full_name']) ?></h2>
                            </div>
                        </div>
                        <p class="sms-sec-lead">Have the user scan with Google Authenticator, then enter the app code to activate.</p>
                        <div class="sms-sec-setup">
                            <div><?= smsTotpQrMarkup($setupUri, 280) ?></div>
                            <div>
                                <p class="small mb-2">Or enter this key manually:</p>
                                <code class="d-inline-block px-2 py-1 bg-light rounded user-select-all"><?= e(trim(chunk_split($adminAuthPendingSecret, 4, ' '))) ?></code>
                            </div>
                        </div>
                        <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" style="max-width:420px;">
                            <?= csrfField() ?>
                            <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                            <input type="hidden" name="action" value="admin_auth_confirm">
                            <div class="mb-3">
                                <?= smsOtpInput('totp_code', ['id' => 'admin_totp_code', 'required' => true, 'autofocus' => true, 'label' => 'App code']) ?>
                            </div>
                            <button type="submit" class="btn btn-sms-primary">Activate</button>
                            <button type="submit" name="action" value="admin_auth_cancel" class="btn btn-outline-secondary ms-1" formnovalidate>Cancel</button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>

            <section class="card sms-sec-card">
                <div class="card-body p-0">
                    <div class="px-3 pt-3">
                        <div class="sms-sec-card-head border-0 pb-2 mb-0">
                            <div class="sms-sec-card-title">
                                <span class="sms-sec-icon"><?= smsIcon('fingerprint', ['aria-hidden' => 'true']) ?></span>
                                <h2 class="h5 fw-bold mb-0"><?= e($moduleLabel) ?> — Authenticator &amp; Passkey</h2>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Auth</th>
                                    <th style="min-width:14rem;">Passkeys</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$authUsers): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No users found for this module.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($authUsers as $u): ?>
                                        <?php
                                        $on = !empty($u['authenticator_enabled']);
                                        $userKeys = is_array($u['passkeys'] ?? null) ? $u['passkeys'] : [];
                                        $pkCount = count($userKeys);
                                        $displayName = (string) $u['full_name'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= e($displayName) ?></div>
                                                <div class="small text-muted"><?= e($u['email'] ?: $u['username']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge <?= $on ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                                    <?= $on ? 'On' : 'Off' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($pkCount === 0): ?>
                                                    <span class="badge text-bg-secondary">None</span>
                                                <?php else: ?>
                                                    <ul class="list-unstyled mb-0 sms-sec-pk-list">
                                                        <?php foreach ($userKeys as $pk): ?>
                                                            <?php
                                                            $pkId = (int) ($pk['id'] ?? 0);
                                                            $pkName = (string) ($pk['device_name'] ?? 'Passkey');
                                                            $pkAdded = !empty($pk['created_at'])
                                                                ? date('M j, Y', strtotime((string) $pk['created_at']))
                                                                : '';
                                                            ?>
                                                            <li class="sms-sec-pk-item">
                                                                <div class="sms-sec-pk-meta">
                                                                    <span class="fw-semibold"><?= e($pkName) ?></span>
                                                                    <?php if ($pkAdded !== ''): ?>
                                                                        <span class="small text-muted"> · <?= e($pkAdded) ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" class="d-inline">
                                                                    <?= csrfField() ?>
                                                                    <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                                                                    <input type="hidden" name="action" value="admin_passkey_remove">
                                                                    <input type="hidden" name="target_user_id" value="<?= (int) $u['id'] ?>">
                                                                    <input type="hidden" name="passkey_id" value="<?= $pkId ?>">
                                                                    <button type="submit"
                                                                            class="sms-sec-btn sms-sec-btn-danger"
                                                                            data-sms-confirm="Are you sure you want to remove “<?= e($pkName) ?>” for <?= e($displayName) ?>? Only this passkey will be removed; any others stay."
                                                                            data-sms-confirm-title="Remove this passkey?"
                                                                            data-sms-confirm-ok="Yes, remove">
                                                                        <?= smsIcon('trash-alt', ['aria-hidden' => 'true']) ?>Remove
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <?php if ($on): ?>
                                                    <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" class="d-inline-block">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                                                        <input type="hidden" name="action" value="admin_auth_disable">
                                                        <input type="hidden" name="target_user_id" value="<?= (int) $u['id'] ?>">
                                                        <button type="submit"
                                                                class="sms-sec-btn sms-sec-btn-danger"
                                                                data-sms-confirm="Are you sure you want to turn off Authenticator for <?= e($displayName) ?>? They will no longer need an app code at login."
                                                                data-sms-confirm-title="Turn off Authenticator?"
                                                                data-sms-confirm-ok="Yes, turn off">
                                                            <?= smsIcon('power-off', ['aria-hidden' => 'true']) ?>Turn Off
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" class="d-inline-block">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                                                        <input type="hidden" name="action" value="admin_auth_turn_on">
                                                        <input type="hidden" name="target_user_id" value="<?= (int) $u['id'] ?>">
                                                        <button type="submit"
                                                                class="sms-sec-btn sms-sec-btn-primary"
                                                            <?= ($adminAuthSetupId > 0 && $adminAuthSetupId !== (int) $u['id']) ? 'disabled' : '' ?>>
                                                            <?= smsIcon('power-off', ['aria-hidden' => 'true']) ?>Turn On
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <!-- Module Management: maintenance + force logout -->
        <div id="panel-module" class="sec-panel sms-sec-panel" role="tabpanel" data-sec-panel="module" hidden>
            <section class="card sms-sec-card mb-4">
                <div class="card-body">
                    <div class="sms-sec-card-head">
                        <div class="sms-sec-card-title">
                            <span class="sms-sec-icon"><?= smsIcon('tools', ['aria-hidden' => 'true']) ?></span>
                            <div>
                                <h2 class="h5 fw-bold mb-0"><?= e($moduleLabel) ?> — Maintenance</h2>
                                <p class="sms-sec-lead mb-0 mt-1">
                                    Users can still sign in, but opening this module shows a maintenance note and Sign out only.
                                    Super Admin can still open the module.
                                </p>
                            </div>
                        </div>
                        <?php if ($moduleMaintenanceOn): ?>
                            <span class="badge text-bg-warning">On</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Off</span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" class="row g-3" style="max-width:560px;">
                        <?= csrfField() ?>
                        <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="maintenance_message">Message (optional)</label>
                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="2"
                                      maxlength="500" placeholder="e.g. CRAD is under scheduled maintenance until 5 PM."><?= e($moduleMaintenanceMsg) ?></textarea>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <?php if ($moduleMaintenanceOn): ?>
                                <button type="submit" name="action" value="module_maintenance_off" class="btn btn-sms-primary">
                                    <?= smsIcon('check', ['class' => 'me-1']) ?>Turn maintenance off
                                </button>
                            <?php else: ?>
                                <button type="submit" name="action" value="module_maintenance_on" class="btn btn-warning"
                                        data-sms-confirm="Put <?= e($moduleLabel) ?> in maintenance? Module users will only see the note and Sign out."
                                        data-sms-confirm-title="Enable module maintenance?"
                                        data-sms-confirm-ok="Yes, enable">
                                    <?= smsIcon('tools', ['class' => 'me-1']) ?>Enable maintenance
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </section>

            <section class="card sms-sec-card mb-4">
                <div class="card-body">
                    <div class="sms-sec-card-head">
                        <div class="sms-sec-card-title">
                            <span class="sms-sec-icon"><?= smsIcon('sign-out-alt', ['aria-hidden' => 'true']) ?></span>
                            <div>
                                <h2 class="h5 fw-bold mb-0">Force logout — <?= e($moduleLabel) ?></h2>
                                <p class="sms-sec-lead mb-0 mt-1">
                                    Sign out everyone on this module, or pick specific accounts.
                                    Super Admin sessions are not affected.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="<?= e($focusUrl($moduleKey)) ?>" id="moduleForceLogoutForm"
                          data-sms-confirm-submit="Force logout the selected <?= e($moduleLabel) ?> users now?"
                          data-sms-confirm-title="Force logout?"
                          data-sms-confirm-ok="Yes, force logout">
                        <?= csrfField() ?>
                        <input type="hidden" name="module_key" value="<?= e($moduleKey) ?>">
                        <input type="hidden" name="action" value="module_force_logout">

                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <label class="sms-sec-radio">
                                <input type="radio" name="logout_scope" value="all" checked
                                       data-logout-scope="all">
                                <span>Logout all <?= e($moduleLabel) ?> users</span>
                            </label>
                            <label class="sms-sec-radio">
                                <input type="radio" name="logout_scope" value="selected"
                                       data-logout-scope="selected">
                                <span>Pick who to logout</span>
                            </label>
                        </div>

                        <div id="forceLogoutPicker" class="sms-force-logout-picker mb-3" hidden>
                            <?php if ($resetUsers === []): ?>
                                <p class="text-muted mb-0 small">No <?= e($moduleLabel) ?> users found to pick from.</p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <span class="small text-muted">
                                        <?= count($resetUsers) ?> <?= e($moduleLabel) ?> account<?= count($resetUsers) === 1 ? '' : 's' ?>
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-logout-select="all">Select all</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-logout-select="none">Clear</button>
                                    </div>
                                </div>
                                <div class="table-responsive sms-force-logout-table">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:2.5rem;"></th>
                                                <th>Name</th>
                                                <th>Email / Username</th>
                                                <th style="width:6.5rem;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resetUsers as $u): ?>
                                                <?php
                                                $uid = (int) $u['id'];
                                                $isOnline = function_exists('smsUserIsOnline')
                                                    && smsUserIsOnline(isset($u['last_seen_at']) ? (string) $u['last_seen_at'] : null);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               name="logout_user_ids[]"
                                                               value="<?= $uid ?>"
                                                               data-logout-user
                                                               id="logout_user_<?= $uid ?>">
                                                    </td>
                                                    <td>
                                                        <label class="mb-0" for="logout_user_<?= $uid ?>">
                                                            <?= e((string) ($u['full_name'] ?: $u['username'])) ?>
                                                        </label>
                                                    </td>
                                                    <td class="small text-muted">
                                                        <?= e((string) ($u['email'] !== '' ? $u['email'] : $u['username'])) ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($isOnline): ?>
                                                            <span class="sms-presence sms-presence--online">
                                                                <span class="sms-presence-dot" aria-hidden="true"></span>Online
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="sms-presence sms-presence--offline">
                                                                <span class="sms-presence-dot" aria-hidden="true"></span>Offline
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-outline-danger" id="moduleForceLogoutBtn">
                            <?= smsIcon('sign-out-alt', ['class' => 'me-1']) ?>Force logout
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <style>
    .sms-sec-radio {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--sms-text);
        cursor: pointer;
        user-select: none;
    }
    .sms-sec-radio input {
        margin: 0;
    }
    .sms-force-logout-picker {
        border: 1px solid var(--sms-border-soft, rgba(15, 23, 42, 0.1));
        border-radius: 0.75rem;
        padding: 0.85rem 1rem;
        background: var(--sms-surface-soft, rgba(15, 23, 42, 0.03));
        max-width: 720px;
    }
    .sms-force-logout-table {
        max-height: 260px;
        overflow: auto;
    }
    .sms-force-logout-table table {
        font-size: 0.86rem;
    }
    .sms-presence {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }
    .sms-presence-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .sms-presence--online {
        color: #15803d;
    }
    .sms-presence--online .sms-presence-dot {
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }
    .sms-presence--offline {
        color: #64748b;
    }
    .sms-presence--offline .sms-presence-dot {
        background: #94a3b8;
    }
    </style>
    <script>
    (function () {
        var form = document.getElementById('moduleForceLogoutForm');
        if (!form) return;
        var picker = document.getElementById('forceLogoutPicker');
        var btn = document.getElementById('moduleForceLogoutBtn');
        var scopes = form.querySelectorAll('[data-logout-scope]');

        function selectedCount() {
            return form.querySelectorAll('[data-logout-user]:checked').length;
        }

        function syncScope() {
            var selected = form.querySelector('input[name="logout_scope"]:checked');
            var isPick = selected && selected.value === 'selected';
            if (picker) picker.hidden = !isPick;
            if (btn) {
                btn.innerHTML = isPick
                    ? '<?= smsIcon('user-slash', ['class' => 'me-1']) ?>Force logout selected'
                    : '<?= smsIcon('sign-out-alt', ['class' => 'me-1']) ?>Force logout all';
            }
            form.setAttribute(
                'data-sms-confirm-submit',
                isPick
                    ? 'Force logout the selected <?= e($moduleLabel) ?> users now?'
                    : 'Force logout ALL active <?= e($moduleLabel) ?> users now?'
            );
        }

        scopes.forEach(function (el) {
            el.addEventListener('change', syncScope);
        });

        form.querySelectorAll('[data-logout-select]').forEach(function (el) {
            el.addEventListener('click', function () {
                var mode = el.getAttribute('data-logout-select');
                form.querySelectorAll('[data-logout-user]').forEach(function (cb) {
                    cb.checked = mode === 'all';
                });
            });
        });

        form.addEventListener('submit', function (e) {
            var selected = form.querySelector('input[name="logout_scope"]:checked');
            if (selected && selected.value === 'selected' && selectedCount() === 0) {
                e.preventDefault();
                e.stopPropagation();
                if (window.smsShowToast) {
                    window.smsShowToast('Select at least one user to log out.', 'warning');
                } else {
                    alert('Select at least one user to log out.');
                }
            }
        }, true);

        syncScope();
    })();
    </script>

    <link href="<?= BASE_URL ?>/assets/css/password-strength.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>/assets/js/password-strength.js"></script>
    <script src="<?= BASE_URL ?>/modules/user-management/assets/js/module-security.js?v=20260723h"></script>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
