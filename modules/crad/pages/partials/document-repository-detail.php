<?php
/** @var array<string, mixed> $detail */
$app = $detail['application'] ?? [];
$archive = $detail['archive'] ?? null;
$categories = $detail['categories'] ?? [];
$ref = htmlspecialchars((string) ($app['proposal_reference'] ?? 'Proposal'));
$workflowLabel = (string) ($detail['workflow_label'] ?? '');
$workflowClass = (string) ($detail['workflow_class'] ?? 'ready');
$canArchive = !empty($detail['can_archive']);
$isArchived = !empty($detail['is_archived']);
$fileBase = (string) ($detail['file_base_url'] ?? '');
?>
<div class="gdr-detail" data-application-id="<?= (int) ($app['id'] ?? 0) ?>">
    <div class="gdr-panel gdr-grant-card">
        <div class="gdr-grant-head">
            <div>
                <h2 class="gdr-panel-title"><?= $ref ?></h2>
                <p class="gdr-grant-subtitle"><?= htmlspecialchars((string) ($app['research_title'] ?? '—')) ?></p>
            </div>
            <span class="gdr-status-badge <?= htmlspecialchars($workflowClass) ?>" data-gdr-workflow-badge><?= htmlspecialchars($workflowLabel) ?></span>
        </div>
        <div class="gdr-summary-grid">
            <div class="gdr-summary-card">
                <span>Lead Proponent</span>
                <strong><?= htmlspecialchars((string) ($app['applicant_name'] ?? '—')) ?></strong>
            </div>
            <div class="gdr-summary-card">
                <span>Grant Program</span>
                <strong><?= htmlspecialchars((string) ($app['funding_title'] ?? '—')) ?></strong>
            </div>
            <div class="gdr-summary-card">
                <span>Status</span>
                <strong data-gdr-app-status><?= htmlspecialchars(grantApplicationStatusLabel((string) ($app['status'] ?? ''))) ?></strong>
            </div>
            <div class="gdr-summary-card">
                <span>Records to Archive</span>
                <strong data-gdr-item-count><?= (int) ($detail['total_items'] ?? 0) ?></strong>
            </div>
            <?php if ($archive !== null): ?>
            <div class="gdr-summary-card archived">
                <span>Archive Reference</span>
                <strong data-gdr-archive-ref><?= htmlspecialchars((string) ($archive['archive_reference'] ?? '—')) ?></strong>
            </div>
            <?php endif; ?>
        </div>
        <div class="gdr-link-row">
            <?php if ($canArchive): ?>
            <button type="button" class="gdr-btn gdr-btn-primary gdrArchiveBtn">
                <?= smsIcon('archive') ?> Archive to Document Repository
            </button>
            <?php endif; ?>
            <a class="gdr-btn gdr-btn-ghost" href="<?= htmlspecialchars(grantPublicationsIpUrl((int) ($app['id'] ?? 0))) ?>">
                <?= smsIcon('book-open') ?> Publications &amp; IP
            </a>
        </div>
        <?php if ($isArchived && $archive !== null): ?>
        <p class="gdr-muted" style="margin:.75rem 0 0;">
            Archived<?php if (!empty($archive['archived_at'])): ?> on <?= htmlspecialchars(date('M j, Y g:i A', strtotime((string) $archive['archived_at']))) ?><?php endif; ?>
            <?php if (!empty($archive['archived_by_name'])): ?> by <?= htmlspecialchars((string) $archive['archived_by_name']) ?><?php endif; ?>.
        </p>
        <?php elseif ($canArchive): ?>
        <p class="gdr-muted" style="margin:.75rem 0 0;">Review the record manifest below, then archive to store permanent research records.</p>
        <?php endif; ?>
    </div>

    <?php foreach ($categories as $cat): ?>
        <?php
        $items = $cat['items'] ?? [];
        if ($items === []) {
            continue;
        }
        ?>
        <div class="gdr-panel gdr-category-panel">
            <h3 class="gdr-section-title">
                <?= smsIcon('folder', ['class' => 'me-1']) ?>
                <?= htmlspecialchars((string) ($cat['label'] ?? '')) ?>
                <span class="gdr-count-badge"><?= count($items) ?></span>
            </h3>
            <div class="gdr-item-list">
                <?php foreach ($items as $item): ?>
                    <?php
                    $itemType = (string) ($item['item_type'] ?? 'record');
                    $downloadUrl = (string) ($item['download_url'] ?? '');
                    $itemId = (int) ($item['id'] ?? 0);
                    if ($isArchived && $itemId > 0 && $itemType === 'file' && !empty($item['file_path'])) {
                        $downloadUrl = $fileBase . '?item_id=' . $itemId;
                    }
                    ?>
                    <div class="gdr-item">
                        <div class="gdr-item-head">
                            <strong><?= htmlspecialchars((string) ($item['item_label'] ?? 'Record')) ?></strong>
                            <span class="gdr-item-type"><?= $itemType === 'file' ? 'File' : 'Record' ?></span>
                        </div>
                        <?php if (!empty($item['summary_text'])): ?>
                        <pre class="gdr-item-summary"><?= htmlspecialchars((string) $item['summary_text']) ?></pre>
                        <?php endif; ?>
                        <?php if ($downloadUrl !== ''): ?>
                        <a class="gdr-file-link" href="<?= htmlspecialchars($downloadUrl) ?>" target="_blank" rel="noopener">
                            <?= smsIcon($itemType === 'file' ? 'file-download' : 'external-link-alt') ?>
                            <?= $itemType === 'file' ? htmlspecialchars((string) ($item['file_original'] ?? 'Download')) : 'Open' ?>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ((int) ($detail['total_items'] ?? 0) === 0): ?>
    <div class="gdr-panel">
        <div class="gdr-empty compact">
            <?= smsIcon('inbox') ?>
            <p style="margin:0;">No archivable records found for this project yet.</p>
        </div>
    </div>
    <?php endif; ?>
</div>
