<?php
/**
 * SMS 2 - Module File Generator
 * Run once to scaffold all module directories and placeholder pages.
 */

require_once __DIR__ . '/config/config.php';

$moduleIndexTemplate = <<<'PHP'
<?php
/**
 * SMS 2 - {MODULE_LABEL} - Overview
 */
$pageTitle    = '{MODULE_LABEL}';
$activeModule = '{MODULE_KEY}';
$activePage   = '';
$breadcrumbs  = [
    ['label' => '{MODULE_LABEL}', 'url' => null],
];

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="page-header">
    <h1><i class="fas {MODULE_ICON} text-sms-primary me-2"></i>{MODULE_LABEL}</h1>
    <p>Select a submodule below to get started.</p>
</div>

<div class="row g-3">
{MODULE_CARDS}
</div>

<?php require_once __DIR__ . '/../../includes/layout-end.php'; ?>

PHP;

$pageTemplate = <<<'PHP'
<?php
/**
 * SMS 2 - {PAGE_TITLE}
 * Module: {MODULE_LABEL}
 */
$pageTitle    = '{PAGE_TITLE}';
$activeModule = '{MODULE_KEY}';
$activePage   = '{PAGE_SLUG}';
$breadcrumbs  = [
    ['label' => '{MODULE_LABEL}', 'url' => BASE_URL . '/modules/{MODULE_KEY}/index.php'],
    ['label' => '{PAGE_TITLE}', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1>{PAGE_TITLE}</h1>
        <p>{MODULE_LABEL} submodule — placeholder page.</p>
    </div>
    <span class="placeholder-badge"><?= smsIcon('code', ['class' => 'me-1']) ?>Phase 1 Placeholder</span>
</div>

<div class="card">
    <div class="card-body p-4">
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas {MODULE_ICON} fa-3x text-sms-primary opacity-50"></i>
            </div>
            <h4 class="fw-semibold mb-2">{PAGE_TITLE}</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                This page is a placeholder for the <strong>{PAGE_TITLE}</strong> feature
                under <strong>{MODULE_LABEL}</strong>. Business logic, database integration,
                and CRUD operations will be implemented in a future development phase.
            </p>
            <a href="<?= BASE_URL ?>/modules/{MODULE_KEY}/index.php" class="btn btn-outline-primary rounded-3">
                <?= smsIcon('arrow-left', ['class' => 'me-2']) ?>Back to {MODULE_LABEL}
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>

PHP;

$created = 0;

foreach ($MODULES as $moduleKey => $module) {
    $baseDir = ROOT_PATH . '/modules/' . $moduleKey;

    // Create directories
    $dirs = [
        $baseDir,
        $baseDir . '/pages',
        $baseDir . '/assets/css',
        $baseDir . '/assets/js',
        $baseDir . '/includes',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // Placeholder files in asset/includes dirs
    file_put_contents($baseDir . '/assets/css/.gitkeep', '');
    file_put_contents($baseDir . '/assets/js/.gitkeep', '');
    file_put_contents($baseDir . '/includes/.gitkeep', '');

    // Generate module index cards
    $cards = '';
    foreach ($module['pages'] as $page) {
        $cards .= '    <div class="col-md-6 col-lg-4">' . "\n";
        $cards .= '        <a href="<?= BASE_URL ?>/modules/' . $moduleKey . '/pages/' . $page['slug'] . '.php" class="text-decoration-none">' . "\n";
        $cards .= '            <div class="card module-card hover-card h-100">' . "\n";
        $cards .= '                <div class="card-body d-flex align-items-center gap-3">' . "\n";
        $cards .= '                    <div class="card-icon"><?= smsIcon(\'square\') ?></div>' . "\n";
        $cards .= '                    <div>' . "\n";
        $cards .= '                        <h6 class="mb-0 fw-semibold">' . htmlspecialchars($page['title']) . '</h6>' . "\n";
        $cards .= '                        <small class="text-muted">Open submodule</small>' . "\n";
        $cards .= '                    </div>' . "\n";
        $cards .= '                </div>' . "\n";
        $cards .= '            </div>' . "\n";
        $cards .= '        </a>' . "\n";
        $cards .= '    </div>' . "\n";
    }

    // Write module index.php
    $indexContent = str_replace(
        ['{MODULE_LABEL}', '{MODULE_KEY}', '{MODULE_ICON}', '{MODULE_CARDS}'],
        [$module['label'], $moduleKey, $module['icon'], $cards],
        $moduleIndexTemplate
    );
    file_put_contents($baseDir . '/index.php', $indexContent);
    $created++;

    // Write each page placeholder
    foreach ($module['pages'] as $page) {
        $pageContent = str_replace(
            ['{PAGE_TITLE}', '{MODULE_LABEL}', '{MODULE_KEY}', '{MODULE_ICON}', '{PAGE_SLUG}'],
            [$page['title'], $module['label'], $moduleKey, $module['icon'], $page['slug']],
            $pageTemplate
        );
        file_put_contents($baseDir . '/pages/' . $page['slug'] . '.php', $pageContent);
        $created++;
    }
}

echo "Generated {$created} module files successfully.\n";
