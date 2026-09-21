<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/modules/crad/includes/research-services-clearance.php';

requireAuth();
if (!rscCanManageAsCrad()) {
    http_response_code(403);
    exit('Forbidden');
}

$pageTitle = 'Approve Signed Clearance';
$activeModule = 'crad';
$activePage = 'research-clearance';
$pageBannerIcon = 'fa-stamp';
$pageBannerDescription = 'Review student-uploaded signed clearance images. Approve if qualified, or reject so the student can re-upload.';
$breadcrumbs = [
    ['label' => 'CRAD', 'url' => BASE_URL . '/modules/crad/index.php'],
    ['label' => 'Approve Signed Clearance', 'url' => null],
];

$crad = rscDb();
rscEnsureSchema($crad);
$rows = rscListForCrad($crad);
$selectedId = (int) ($_GET['id'] ?? 0);
$current = $selectedId > 0 ? rscRefreshExisting($crad, rscFindById($crad, $selectedId)) : ($rows[0] ?? null);
$public = $current ? rscPublicRow($current) : null;

require_once ROOT_PATH . '/includes/layout-start.php';
renderBreadcrumbs($breadcrumbs);
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/modules/crad/assets/css/research-clearance.css?v=rsc-flow-6">

<div class="glass-dashboard rsc-print-root"
     data-rsc-live
     data-rsc-role="<?= e(getCurrentUserRoleKey()) ?>"
     data-rsc-endpoint="<?= e(BASE_URL . '/modules/crad/api/research-clearance.php') ?>"
     data-rsc-csrf="<?= e(csrfToken()) ?>"
     data-rsc-id="<?= $public ? (int) $public['id'] : '' ?>">
    <div class="rsc-empty" data-rsc-empty <?= $rows ? 'hidden' : '' ?>>Waiting for a student to upload a signed Research Services Clearance.</div>

    <div data-rsc-detail <?= $public ? '' : 'hidden' ?>>
        <div class="rsc-toolbar">
            <div>
                <div class="rsc-status" data-rsc-status><?= e($public['status_label'] ?? '') ?></div>
                <small class="text-muted" data-rsc-sync></small>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if (count($rows) > 1): ?>
                    <select class="form-select form-select-sm" style="max-width:280px;" data-rsc-group>
                        <?php foreach ($rows as $item): $itemPublic = rscPublicRow($item); ?>
                            <option value="<?= (int) $itemPublic['id'] ?>"<?= $public && (int) $public['id'] === (int) $itemPublic['id'] ? ' selected' : '' ?>>
                                <?= e(($itemPublic['stage_label'] ?? '') . ' · ' . ($itemPublic['leader_group_no'] ?: ('#' . $itemPublic['id']))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <button type="button" class="btn btn-success" data-rsc-approve <?= ($public && !empty($public['can_crad_sign'])) ? '' : 'hidden' ?>><?= smsIcon('check', ['class' => 'me-1']) ?>Approve</button>
                <button type="button" class="btn btn-outline-danger" data-rsc-reject <?= ($public && !empty($public['can_crad_sign'])) ? '' : 'hidden' ?>><?= smsIcon('times', ['class' => 'me-1']) ?>Reject</button>
            </div>
        </div>

        <div class="alert alert-warning" data-rsc-upload-gate <?= ($public && !empty($public['has_upload'])) ? 'hidden' : '' ?>>
            <?= smsIcon('upload', ['class' => 'me-2']) ?>
            Waiting for the student to upload their <strong>signed clearance</strong> image.
        </div>

        <div class="alert alert-info" data-rsc-mis-aa-note <?= ($public && !empty($public['has_upload']) && ($public['status'] ?? '') !== 'clearance_done') ? '' : 'hidden' ?>>
            <?= smsIcon('info-circle', ['class' => 'me-2']) ?>
            Review the uploaded signed form. <strong>Approve</strong> if qualified, or <strong>Reject</strong> so the student can re-upload.
        </div>

        <div class="rsc-wrap" data-rsc-form <?= ($public && !empty($public['has_upload'])) ? '' : 'hidden' ?>></div>
    </div>
</div>
<script src="<?= BASE_URL ?>/modules/crad/assets/js/research-clearance-live.js?v=rsc-flow-7"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
