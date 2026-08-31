<?php
/**
 * SMS 2 – Reset password with token
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security-workflow.php';
require_once ROOT_PATH . '/includes/captcha.php';
require_once ROOT_PATH . '/includes/security-ui.php';
require_once ROOT_PATH . '/includes/module-controls.php';

if (smsIsSystemInMaintenance()) {
    header('Location: ' . BASE_URL . '/account/maintenance.php');
    exit;
}

if (isAuthenticated()) {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$minLen = (int) smsSetting('min_password_length', '8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $captcha = smsCaptchaVerifyRequest();
        if (empty($captcha['ok'])) {
            $error = $captcha['error'] !== ''
                ? $captcha['error']
                : 'Please complete the CAPTCHA before continuing.';
        } else {
            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['password_confirm'] ?? '');

            if ($token === '') {
                $error = 'Invalid or missing reset token.';
            } else {
                $strength = smsValidatePasswordStrength($password);
                if (!$strength['ok']) {
                    $error = $strength['message'];
                } elseif ($password !== $confirm) {
                    $error = 'Passwords do not match.';
                } elseif (!smsResetPasswordWithToken($token, $password)) {
                    $error = 'This reset link is invalid or has expired.';
                } else {
                    header('Location: ' . BASE_URL . '/login/login.php?reset=1');
                    exit;
                }
            }
        }
    }
}

$pageTitle = 'Reset Password';
$bodyClass = 'login-page';
require_once ROOT_PATH . '/includes/header.php';
?>
<link href="<?= BASE_URL ?>/assets/css/auth-pages.css" rel="stylesheet">
<style>
body.login-page {
    background: #071c48 !important;
    background-image: none !important;
    position: relative;
}

.reset-video-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
    background:
        radial-gradient(ellipse 90% 70% at 15% 10%, rgba(96, 165, 250, 0.22) 0%, transparent 55%),
        linear-gradient(145deg, #051637 0%, #0b2a6b 42%, #1a6fc4 100%);
}

.reset-video-bg::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 75% 60% at 50% 42%, rgba(4, 16, 42, 0.12) 0%, rgba(4, 16, 42, 0.42) 75%, rgba(2, 8, 24, 0.62) 100%),
        linear-gradient(180deg, rgba(4, 16, 42, 0.28) 0%, rgba(4, 16, 42, 0.18) 45%, rgba(2, 8, 24, 0.55) 100%);
}

.auth-stage {
    position: relative;
    z-index: 1;
}

.auth-footer {
    position: relative;
    z-index: 1;
}

@media (prefers-reduced-motion: reduce) {
    .reset-video-bg {
        background: #071c48;
    }
}
</style>

<div class="reset-video-bg" aria-hidden="true"></div>

<main class="auth-stage">
    <section class="auth-card" aria-label="Reset password">
        <div class="auth-badge">
            <?= smsIcon('shield-alt') ?>
            <span>Secure Password Reset</span>
        </div>
        <h1>Set new password</h1>
        <p class="auth-lead">Choose a strong password for your BCP account.</p>

        <?php if ($token === ''): ?>
            <div class="alert alert-danger">Missing reset token. Request a new link from the forgot-password page.</div>
            <div class="auth-links">
                <a href="<?= BASE_URL ?>/login/forgot-password.php">Request reset link</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="POST" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="mb-3">
                    <label class="form-label" for="password">New password</label>
                    <?= smsPasswordInput(['id' => 'password', 'name' => 'password', 'required' => true, 'minlength' => $minLen, 'autocomplete' => 'new-password']) ?>
                    <?= smsPasswordStrengthMarkup('password') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirm">Confirm password</label>
                    <?= smsPasswordInput(['id' => 'password_confirm', 'name' => 'password_confirm', 'required' => true, 'minlength' => $minLen, 'autocomplete' => 'new-password']) ?>
                </div>
                <?= smsCaptchaMarkup() ?>
                <button type="submit" class="btn btn-auth-primary w-100">
                    <?= smsIcon('check', ['class' => 'me-2']) ?>Update password
                </button>
                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/login/login.php">Back to sign in</a>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<footer class="auth-footer">
    <p class="mb-0">&copy; 2026 Bestlink College of the Philippines. All rights reserved.</p>
</footer>
<?php require_once ROOT_PATH . '/includes/scripts.php'; ?>
