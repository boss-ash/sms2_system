<?php
/**
 * SMS 2 - Footer
 */
?>
<footer class="sms-footer mt-auto">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small>&copy; <?= date('Y') ?> <?= htmlspecialchars(INSTITUTION) ?>. All rights reserved.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small><?= htmlspecialchars(APP_NAME) ?> v<?= APP_VERSION ?></small>
            </div>
        </div>
    </div>
</footer>
