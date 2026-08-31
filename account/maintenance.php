<?php
/**
 * SMS 2 – Global system maintenance page
 * Calm crest + soft “under maintenance” cue. No app chrome.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/module-controls.php';

// When maintenance ends, send people back into the system
if (!smsIsSystemInMaintenance()) {
    if (isAuthenticated()) {
        if (getCurrentUserRoleKey() === 'student') {
            header('Location: ' . BASE_URL . '/modules/student-portal/pages/my-profile.php');
        } else {
            header('Location: ' . BASE_URL . '/dashboard/index.php');
        }
    } else {
        header('Location: ' . BASE_URL . '/login/login.php');
    }
    exit;
}

// Admins should manage settings, not sit on the lockout screen
if (isAuthenticated() && smsCanBypassSystemControls()) {
    header('Location: ' . BASE_URL . '/modules/user-management/pages/system-settings.php');
    exit;
}

// Ensure any leftover non-admin session is cleared (auto-outlet)
if (isAuthenticated()) {
    logout();
}

$message = smsSystemMaintenanceMessage();
$pageTitle = 'Maintenance';
$bodyClass = 'login-page maintenance-lock-page';
$omitThemeJs = true;
$omitAppChromeJs = true;
$forceTheme = 'light';

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

require_once ROOT_PATH . '/includes/header.php';
?>
<style>
body.maintenance-lock-page {
    min-height: 100vh;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(ellipse 80% 55% at 50% 0%, rgba(37, 99, 235, 0.18), transparent 55%),
        linear-gradient(165deg, #071c48 0%, #0b2a6b 45%, #0f3d8c 100%);
    color-scheme: light;
    overflow: hidden;
}

.maint-stage {
    width: min(100% - 2rem, 420px);
    text-align: center;
    padding: 2rem 0 2.5rem;
}

.maint-logo {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
}

.maint-logo__plate {
    width: 100%;
    height: 100%;
    border-radius: 28px;
    background: linear-gradient(145deg, #ffffff, #e8eef7);
    box-shadow:
        0 1px 0 rgba(255, 255, 255, 0.85) inset,
        0 14px 28px rgba(0, 0, 0, 0.28),
        8px 12px 0 rgba(11, 42, 107, 0.16);
    display: grid;
    place-items: center;
    transform: rotateX(8deg) rotateY(-8deg);
    transform-style: preserve-3d;
    animation: maintSoftFloat 5.5s ease-in-out infinite;
}

.maint-logo__plate img {
    width: 72px;
    height: 72px;
    object-fit: contain;
    filter: drop-shadow(0 4px 8px rgba(11, 42, 107, 0.22));
}

.maint-logo__badge {
    position: absolute;
    right: -10px;
    bottom: -6px;
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 1.15rem;
    line-height: 1;
    background: #fff;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
    border: 2px solid rgba(255, 255, 255, 0.95);
    animation: maintBadgePulse 3.8s ease-in-out infinite;
}

.maint-stage h1 {
    margin: 0 0 0.55rem;
    color: #f8fafc;
    font-size: clamp(1.35rem, 4vw, 1.65rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.maint-stage p {
    margin: 0 auto 1.5rem;
    max-width: 34ch;
    color: rgba(226, 232, 240, 0.88);
    font-size: 0.95rem;
    line-height: 1.55;
}

.maint-admin-link {
    display: inline-block;
    margin-top: 0.85rem;
    color: rgba(191, 219, 254, 0.8);
    font-size: 0.8rem;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: color 0.15s ease, border-color 0.15s ease;
}

.maint-admin-link:hover,
.maint-admin-link:focus {
    color: #fff;
    border-bottom-color: rgba(255, 255, 255, 0.45);
}

@keyframes maintSoftFloat {
    0%, 100% { transform: rotateX(8deg) rotateY(-8deg) translateY(0); }
    50% { transform: rotateX(8deg) rotateY(-8deg) translateY(-5px); }
}

@keyframes maintBadgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.06); }
}

@media (prefers-reduced-motion: reduce) {
    .maint-logo__plate,
    .maint-logo__badge {
        animation: none !important;
    }
}
</style>

<main class="maint-stage" role="main" aria-labelledby="maintTitle">
    <div class="maint-logo" aria-hidden="true">
        <div class="maint-logo__plate">
            <img src="<?= e(smsBrandLogoUrl()) ?>?v=crest-maint" alt="" width="72" height="72">
        </div>
        <span class="maint-logo__badge" title="Under maintenance">🛠️</span>
    </div>

    <h1 id="maintTitle">Under maintenance</h1>
    <p><?= e($message) ?></p>
    <a class="maint-admin-link" href="<?= e(BASE_URL) ?>/login/login.php?access=admin">Administrator sign-in</a>
</main>
<script>
(function () {
    var url = location.href.split('#')[0];
    try {
        history.replaceState({ smsMaint: 1 }, '', url);
        history.pushState({ smsMaint: 1 }, '', url);
    } catch (e) {}
    window.addEventListener('popstate', function () {
        try { history.pushState({ smsMaint: 1 }, '', url); } catch (e) {}
    });
    window.addEventListener('pageshow', function (ev) {
        if (ev.persisted) {
            location.replace(url);
        }
    });
})();
</script>
<?php
require_once ROOT_PATH . '/includes/scripts.php';
