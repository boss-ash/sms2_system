<?php
/**
 * SMS 2 - Scripts
 */
?>
<!-- Bootstrap JS (local) -->
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sms-icons.js?v=1"></script>
<!-- SMS 2 App -->
<?php if (empty($omitThemeJs)): ?>
<script src="<?= BASE_URL ?>/assets/js/theme.js"></script>
<?php endif; ?>
<?php if (empty($omitAppChromeJs)): ?>
<script src="<?= BASE_URL ?>/assets/js/ph-clock.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sidebar.js?v=collapsible-all-1"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sms-confirm.js?v=1"></script>
<script src="<?= BASE_URL ?>/assets/js/sms-security-ui.js?v=6"></script>
<!-- Global Search -->
<script src="<?= BASE_URL ?>/assets/js/search.js"></script>
<?php endif; ?>

<?php 
// Research Progress Live Updates (READ-ONLY Polling) - Only for student-portal and faculty modules
$loadResearchProgressLive = false;

// Check if current page is in student-portal or faculty modules
if (isset($activeModule)) {
    if ($activeModule === 'student-portal' || $activeModule === 'faculty') {
        $loadResearchProgressLive = true;
    }
}

// Alternative check: Check URL path if $activeModule not set
if (!$loadResearchProgressLive) {
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($currentPath, '/student-portal/') !== false || strpos($currentPath, '/faculty/') !== false) {
        $loadResearchProgressLive = true;
    }
}

if ($loadResearchProgressLive): 
?>
<!-- Research Progress Live Updates (READ-ONLY Polling) -->
<script src="<?= BASE_URL ?>/assets/js/research-progress-live.js?v=2"></script>
<script>
// Auto-detect role and initialize
(function() {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/student-portal/')) {
        document.body.classList.add('student-portal');
    } else if (currentPath.includes('/faculty/')) {
        document.body.classList.add('faculty-portal');
    }
})();
</script>
<?php endif; ?>
</body>
</html>
