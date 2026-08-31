<?php
/**
 * Grant dashboard metrics grid partial.
 *
 * @var array<string, int|float|string> $grantDashboardMetrics
 */
$grantDashboardMetrics = $grantDashboardMetrics ?? grantDashboardMetricsDefaults();
$grantDashboardAnalyticsUrl = BASE_URL . '/modules/crad/pages/dashboard-analytics.php';
?>
<link href="<?= BASE_URL ?>/assets/css/grant-dashboard-metrics.css?v=1" rel="stylesheet">

<section class="glass-panel gdm-panel" data-grant-dashboard-metrics="1" aria-label="Grant dashboard metrics">
    <div class="glass-panel-body">
        <div class="gdm-head">
            <div>
                <h2>
                    Grant Dashboard Metrics
                    <span class="gdm-live-badge"><?= smsIcon('sync-alt') ?> Live</span>
                </h2>
                <p>Automatically recalculated from grant calls, proposals, funding, research output, publications, and IP records.</p>
            </div>
            <div style="text-align:right;">
                <a class="gdm-link" href="<?= htmlspecialchars($grantDashboardAnalyticsUrl) ?>">
                    <?= smsIcon('chart-pie') ?> Dashboard &amp; Analytics
                </a>
                <div class="gdm-updated" data-gdm-updated>
                    <?php if (!empty($grantDashboardMetrics['updated_at'])): ?>
                        Updated <?= htmlspecialchars(date('g:i:s A', strtotime((string) $grantDashboardMetrics['updated_at']))) ?>
                    <?php else: ?>
                        Loading…
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="gdm-grid">
            <?php foreach (grantDashboardMetricDefinitions() as $def): ?>
                <?php
                $key = (string) ($def['key'] ?? '');
                $tone = (string) ($def['tone'] ?? 'blue');
                ?>
                <article class="gdm-card">
                    <div class="gdm-icon <?= htmlspecialchars($tone) ?>">
                        <?= smsIcon((string) ($def['icon'] ?? 'fa-chart-bar')) ?>
                    </div>
                    <div>
                        <span><?= htmlspecialchars((string) ($def['label'] ?? '')) ?></span>
                        <strong data-gdm-value="<?= htmlspecialchars($key) ?>">
                            <?= htmlspecialchars(grantFormatDashboardMetricValue($key, $grantDashboardMetrics)) ?>
                        </strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>/assets/js/grant-dashboard-metrics-live.js?v=1"></script>
