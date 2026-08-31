<?php
/**
 * SMS 2 - Welcome Page
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/module-controls.php';

if (smsNeedsSetup()) {
    header('Location: ' . BASE_URL . '/setup/index.php');
    exit;
}

if (smsIsSystemInMaintenance()) {
    if (isAuthenticated() && smsCanBypassSystemControls()) {
        header('Location: ' . BASE_URL . '/modules/user-management/pages/system-settings.php');
        exit;
    }
    header('Location: ' . BASE_URL . '/account/maintenance.php');
    exit;
}

if (isAuthenticated()) {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

$pageTitle = 'Welcome';
$bodyClass = 'welcome-page';
$forceTheme = 'light';
$omitThemeJs = true;
$omitAppChromeJs = true;
$welcomeHeroUrl = smsWelcomeHeroImageUrl();

require_once ROOT_PATH . '/includes/header.php';
?>

<div class="sms-welcome-bg" aria-hidden="true"></div>

<main class="sms-welcome-main">
    <section class="sms-welcome-shell" aria-label="Welcome">
        <header class="sms-welcome-shell-head">
            <a class="sms-welcome-brand" href="<?= BASE_URL ?>/welcome/index.php">
                <img src="<?= e(smsBrandLogoUrl()) ?>?v=crest3" alt="<?= e(APP_SHORT_NAME) ?> logo" width="44" height="44">
                <span>
                    <strong><?= e(APP_SHORT_NAME) ?></strong>
                    <small>Student Management System</small>
                </span>
            </a>
            <span class="sms-welcome-secure">
                <?= smsIcon('shield', ['aria-hidden' => 'true']) ?>
                Secure portal
            </span>
        </header>

        <div class="sms-welcome-shell-body">
            <aside class="sms-welcome-showcase">
                <div class="sms-welcome-hero" aria-hidden="true">
                    <img
                        class="sms-welcome-hero-img"
                        src="<?= e($welcomeHeroUrl) ?>?v=1"
                        alt=""
                        width="800"
                        height="600"
                        decoding="async"
                        fetchpriority="high"
                    >
                </div>
                <div class="sms-welcome-showcase-copy">
                    <span class="sms-welcome-tag">
                        <?= smsIcon('building', ['aria-hidden' => 'true']) ?>
                        Official portal
                    </span>
                    <h1>Enrollment, academics, and campus services in one place.</h1>
                    <p><?= e(INSTITUTION) ?> — secure, organized, and built for daily school operations.</p>
                </div>
            </aside>

            <div class="sms-welcome-panel">
                <div class="sms-welcome-panel-head">
                    <span class="sms-welcome-version">
                        <?= smsIcon('circle-check', ['aria-hidden' => 'true']) ?>
                        SMS 2 · v<?= e(APP_VERSION) ?>
                    </span>
                    <h2>Welcome to <?= e(APP_SHORT_NAME) ?></h2>
                    <p class="sms-welcome-lead">Sign in to open your assigned modules and manage campus records.</p>
                </div>

                <ul class="sms-welcome-modules">
                    <li>
                        <span class="sms-welcome-mod-icon sms-welcome-mod-icon--enroll">
                            <?= smsIcon('user-plus', ['aria-hidden' => 'true']) ?>
                        </span>
                        <span class="sms-welcome-mod-text">
                            <strong>Enrollment</strong>
                            <em>Admissions and registration</em>
                        </span>
                        <?= smsIcon('chevron-right', ['class' => 'sms-welcome-mod-arrow', 'aria-hidden' => 'true']) ?>
                    </li>
                    <li>
                        <span class="sms-welcome-mod-icon sms-welcome-mod-icon--acad">
                            <?= smsIcon('book', ['aria-hidden' => 'true']) ?>
                        </span>
                        <span class="sms-welcome-mod-text">
                            <strong>Academics</strong>
                            <em>Records, grades, and scheduling</em>
                        </span>
                        <?= smsIcon('chevron-right', ['class' => 'sms-welcome-mod-arrow', 'aria-hidden' => 'true']) ?>
                    </li>
                    <li>
                        <span class="sms-welcome-mod-icon sms-welcome-mod-icon--faculty">
                            <?= smsIcon('chalkboard', ['aria-hidden' => 'true']) ?>
                        </span>
                        <span class="sms-welcome-mod-text">
                            <strong>Faculty</strong>
                            <em>Teaching and research tools</em>
                        </span>
                        <?= smsIcon('chevron-right', ['class' => 'sms-welcome-mod-arrow', 'aria-hidden' => 'true']) ?>
                    </li>
                    <li>
                        <span class="sms-welcome-mod-icon sms-welcome-mod-icon--services">
                            <?= smsIcon('wallet', ['aria-hidden' => 'true']) ?>
                        </span>
                        <span class="sms-welcome-mod-text">
                            <strong>Student services</strong>
                            <em>Payments and student portal</em>
                        </span>
                        <?= smsIcon('chevron-right', ['class' => 'sms-welcome-mod-arrow', 'aria-hidden' => 'true']) ?>
                    </li>
                </ul>

                <div class="sms-welcome-actions">
                    <a href="<?= BASE_URL ?>/login/login.php" class="sms-welcome-btn sms-welcome-btn-primary" data-auth-transition data-auth-direction="left">
                        <?= smsIcon('login', ['aria-hidden' => 'true']) ?>
                        Sign in to system
                    </a>
                    <a href="<?= BASE_URL ?>/login/student-admission.php" class="sms-welcome-btn sms-welcome-btn-secondary" data-auth-transition data-auth-direction="left">
                        <?= smsIcon('id', ['aria-hidden' => 'true']) ?>
                        Student admission
                    </a>
                </div>
            </div>
        </div>

        <footer class="sms-welcome-shell-foot">
            <p>&copy; <?= date('Y') ?> <?= e(INSTITUTION) ?>. All rights reserved.</p>
        </footer>
    </section>
</main>

<?php require_once ROOT_PATH . '/includes/scripts.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/auth-transition.js?v=8"></script>
