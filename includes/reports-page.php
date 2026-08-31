<?php
/**
 * Shared Reports & Analytics page renderer
 * Overview-style chart dashboard (role / module accurate).
 * Expects: $reportSlug
 */
require_once ROOT_PATH . '/includes/authentication.php';
requireAuth();

require_once ROOT_PATH . '/includes/reports-catalog.php';
require_once ROOT_PATH . '/includes/reports-dashboard-data.php';

$reportSlug = trim((string) ($reportSlug ?? ($activePage ?? '')));
if ($reportSlug === '' || !smsUserCanAccessReport($reportSlug)) {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

$reportMeta = null;
foreach (smsReportsCatalog() as $item) {
    if ($item['slug'] === $reportSlug) {
        $reportMeta = $item;
        break;
    }
}

$pageTitle = $reportMeta['title'] ?? 'Report';
$sourceModule = $reportMeta['module'] ?? 'shared';
$activeModule = 'reports-analytics';
$activePage = $reportSlug;
$breadcrumbs = [
    ['label' => 'Reports & Analytics', 'url' => BASE_URL . '/modules/reports-analytics/index.php'],
    ['label' => $pageTitle, 'url' => null],
];

$roleKey = getCurrentUserRoleKey();
$roleLabel = getCurrentUserRole();
$dashboard = smsReportDashboardData($reportSlug, $roleKey);

$flash = '';
if (($_GET['process'] ?? '') === 'export') {
    $flash = $pageTitle . ' export package prepared for ' . $roleLabel . '.';
}

$moduleLabels = [
    'shared' => 'Office Overview',
    'enrollment' => 'Enrollment Management',
    'registrar' => 'Registrar',
    'curriculum' => 'Curriculum & Subject Management',
    'scheduling' => 'Class Schedule',
    'crad' => 'CRAD',
    'payment' => 'Payment Management',
    'faculty' => 'Faculty Management',
    'lms' => 'Online Learning & LMS',
    'cocurricular' => 'Co-Curricular',
    'accreditation' => 'Accreditation Management',
];
$moduleLabel = $moduleLabels[$sourceModule] ?? ucfirst((string) $sourceModule);

$displayTitle = $pageTitle;
$donut = $dashboard['donut'] ?? [];
$donutLabels = $donut['labels'] ?? [];
$donutValues = $donut['values'] ?? [];
$donutColors = $donut['colors'] ?? ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7', '#94a3b8'];

require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/layout-start.php';
?>

<link href="<?= BASE_URL ?>/assets/css/reports-analytics.css" rel="stylesheet">

<?php renderBreadcrumbs($breadcrumbs); ?>

<?php if ($flash !== ''): ?>
    <div class="alert alert-success mb-3" role="alert">
        <?= smsIcon('check-circle', ['class' => 'me-2']) ?><?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<section
    class="ra-dash"
    id="raDash"
    data-dashboard="<?= htmlspecialchars(json_encode($dashboard, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
>
    <header class="ra-dash-head">
        <div>
            <h1><?= htmlspecialchars($displayTitle) ?></h1>
            <p><?= htmlspecialchars($dashboard['subtitle'] ?? 'Analytics for the selected report type and filters.') ?></p>
        </div>
        <a class="ra-export-btn" href="?process=export">
            Export <?= smsIcon('chevron-down', ['style' => 'font-size:0.65rem;']) ?>
        </a>
    </header>

    <div class="ra-filter-bar" aria-label="Active filters">
        <?php foreach (($dashboard['filters'] ?? []) as $filter): ?>
            <?php
            $parts = explode(':', (string) $filter, 2);
            $fkey = trim($parts[0] ?? '');
            $fval = trim($parts[1] ?? '');
            ?>
            <span><?= htmlspecialchars($fkey) ?>: <strong><?= htmlspecialchars($fval) ?></strong></span>
        <?php endforeach; ?>
        <span>Module: <strong><?= htmlspecialchars($moduleLabel) ?></strong></span>
        <span>Office: <strong><?= htmlspecialchars($roleLabel) ?></strong></span>
    </div>

    <div class="ra-chart-grid">
        <article class="ra-card">
            <h2><?= htmlspecialchars($donut['title'] ?? 'Status Distribution') ?></h2>
            <div class="ra-donut-wrap">
                <div class="ra-chart-box">
                    <canvas id="raDonutChart" aria-label="Status distribution chart"></canvas>
                </div>
                <ul class="ra-legend">
                    <?php foreach ($donutLabels as $i => $label): ?>
                        <li>
                            <span class="label">
                                <span class="dot" style="background:<?= htmlspecialchars($donutColors[$i % count($donutColors)]) ?>"></span>
                                <?= htmlspecialchars((string) $label) ?>
                            </span>
                            <strong><?= htmlspecialchars((string) ($donutValues[$i] ?? 0)) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>

        <article class="ra-card">
            <h2><?= htmlspecialchars($dashboard['bar']['title'] ?? 'Monthly Activity') ?></h2>
            <div class="ra-chart-box">
                <canvas id="raBarChart" aria-label="Monthly activity chart"></canvas>
            </div>
        </article>

        <article class="ra-card">
            <h2><?= htmlspecialchars($dashboard['grouped']['title'] ?? 'Comparison Trend') ?></h2>
            <div class="ra-chart-box">
                <canvas id="raGroupedChart" aria-label="Comparison chart"></canvas>
            </div>
        </article>

        <article class="ra-card">
            <h2><?= htmlspecialchars($dashboard['horizontal']['title'] ?? 'Top Ranking') ?></h2>
            <div class="ra-chart-box">
                <canvas id="raHorizontalChart" aria-label="Ranking chart"></canvas>
            </div>
        </article>
    </div>

    <section class="ra-summary" aria-label="Summary report">
        <h2>Summary Report</h2>
        <p>Tabular detail for the active report type (search, sort, and paginate independently of global analytics).</p>

        <div class="ra-summary-tools">
            <label class="ra-search">
                <?= smsIcon('search') ?>
                <input type="search" id="raSummarySearch" placeholder="Search this report table." aria-label="Search summary table">
            </label>
            <label class="ra-rows">
                Rows
                <select id="raSummaryRows" aria-label="Rows per page">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
            </label>
        </div>

        <div class="ra-table-wrap">
            <table class="ra-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="raSummaryBody"></tbody>
            </table>
        </div>
        <div class="ra-table-foot">
            <span id="raSummaryMeta" class="meta">Showing 0-0 of 0 rows</span>
            <div id="raSummaryPager" class="ra-pager" aria-label="Summary pagination"></div>
        </div>
    </section>
</section>

<script src="<?= BASE_URL ?>/assets/js/reports-analytics.js"></script>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
