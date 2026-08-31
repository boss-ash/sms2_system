<?php
/**
 * SMS 2 - Fleet-style process list UI (SMS2 content only)
 * Expects $mpl array.
 */
$mpl = $mpl ?? [];
$description = $mpl['description'] ?? '';
$addLabel = $mpl['add_label'] ?? '+ Add Record';
$addProcess = $mpl['add_process'] ?? 'new';
$alert = $mpl['alert'] ?? '';
$stats = $mpl['stats'] ?? [];
$searchPlaceholder = $mpl['search_placeholder'] ?? 'Search by reference, name, or detail.';
$statuses = $mpl['statuses'] ?? ['All Status', 'Pending', 'In Progress', 'Completed', 'Cancelled'];
$types = $mpl['types'] ?? ['All Types'];
$listTitle = $mpl['list_title'] ?? 'Record List';
$listSubtitle = $mpl['list_subtitle'] ?? 'View and manage all records.';
$showAddButton = array_key_exists('show_add_button', $mpl) ? (bool) $mpl['show_add_button'] : true;
$showArchiveButton = array_key_exists('show_archive_button', $mpl) ? (bool) $mpl['show_archive_button'] : true;
$showPager = array_key_exists('show_pager', $mpl) ? (bool) $mpl['show_pager'] : true;
$columns = $mpl['columns'] ?? [
    'ref' => 'Reference No.',
    'subject' => 'Subject',
    'owner' => 'Assigned To',
    'detail' => 'Detail',
    'schedule' => 'Schedule',
];
$rows = $mpl['rows'] ?? [];

require_once __DIR__ . '/mpl-archive.php';
$moduleKey = (string) ($mpl['module_key'] ?? ($activeModule ?? ''));
$pageSlug = (string) ($mpl['page_slug'] ?? ($activePage ?? ''));
$isArchiveView = isset($_GET['view']) && (string) $_GET['view'] === 'archive';

$activeRows = [];
$archivedRows = [];
foreach ($rows as $row) {
    $ref = trim((string) ($row['reference'] ?? ''));
    if ($ref !== '' && smsMplArchiveHas($moduleKey, $pageSlug, $ref)) {
        $archivedRows[] = $row;
    } else {
        $activeRows[] = $row;
    }
}

$rows = $isArchiveView ? $archivedRows : $activeRows;
$archiveCount = count($archivedRows);
$rowCount = count($rows);
$showingEnd = $rowCount > 0 ? $rowCount : 0;

// Clean add label (catalog often prefixes "+ "; icon already shows plus)
$addLabelDisplay = ltrim((string) $addLabel);
$addLabelDisplay = preg_replace('/^\+\s*/', '', $addLabelDisplay) ?? $addLabelDisplay;
if ($addLabelDisplay === '') {
    $addLabelDisplay = 'Add Record';
}

if ($isArchiveView) {
    $listTitle = ($mpl['list_title'] ?? 'Record List') . ' — Archive';
    $listSubtitle = 'Archived records for this page. Restore to send them back to the active list.';
}

if (!function_exists('smsMplInitials')) {
    function smsMplInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }
        return $letters !== '' ? $letters : 'SM';
    }
}

$statusClass = static function (string $class, string $status): string {
    $c = strtolower(trim($class !== '' ? $class : $status));
    if (str_contains($c, 'complete') || str_contains($c, 'approved') || str_contains($c, 'assigned') || str_contains($c, 'posted') || str_contains($c, 'released')) {
        return 'completed';
    }
    if (str_contains($c, 'cancel') || str_contains($c, 'denied') || str_contains($c, 'overdue')) {
        return 'cancelled';
    }
    if (str_contains($c, 'progress') || str_contains($c, 'pending') || str_contains($c, 'review') || str_contains($c, 'evaluation') || str_contains($c, 'assignment')) {
        return 'pending';
    }
    if (str_contains($c, 'schedul') || str_contains($c, 'process') || str_contains($c, 'active') || str_contains($c, 'open') || str_contains($c, 'panel')) {
        return 'scheduled';
    }
    return 'processing';
};
?>
<link href="<?= BASE_URL ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<div class="mpl" data-mpl>
    <div class="mpl-top">
        <p><?= htmlspecialchars($description) ?></p>
        <div class="mpl-toolbar">
            <?php if ($isArchiveView): ?>
                <a class="mpl-btn mpl-btn-ghost" href="?">
                    <?= smsIcon('arrow-left', ['aria-hidden' => 'true']) ?>Active list
                </a>
            <?php else: ?>
                <?php if ($showArchiveButton): ?>
                    <a class="mpl-btn mpl-btn-soft" href="?view=archive">
                        <?= smsIcon('archive', ['aria-hidden' => 'true']) ?>Archive
                        <?php if ($archiveCount > 0): ?>
                            <span class="mpl-btn-count"><?= (int) $archiveCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($showAddButton): ?>
                    <a class="mpl-btn mpl-btn-primary" href="?process=<?= htmlspecialchars(urlencode($addProcess)) ?>">
                        <?= smsIcon('plus', ['aria-hidden' => 'true']) ?><?= htmlspecialchars($addLabelDisplay) ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($alert !== ''): ?>
        <div class="mpl-alert" role="alert">
            <?= smsIcon('check-circle', ['class' => 'me-1']) ?><?= htmlspecialchars($alert) ?>
        </div>
    <?php endif; ?>

    <?php if ($stats): ?>
        <section class="mpl-stats" aria-label="Process summary">
            <?php foreach ($stats as $stat): ?>
                <?php
                $tone = (string) ($stat['tone'] ?? 'blue');
                $toneClass = in_array($tone, ['warning', 'amber'], true) ? 'amber'
                    : (in_array($tone, ['success', 'green'], true) ? 'green'
                    : (in_array($tone, ['info', 'purple'], true) ? 'purple' : 'blue'));
                ?>
                <article class="mpl-stat">
                    <div class="mpl-stat-icon <?= htmlspecialchars($toneClass) ?>">
                        <i class="fas <?= htmlspecialchars($stat['icon'] ?? 'fa-circle') ?>"></i>
                    </div>
                    <div>
                        <span><?= htmlspecialchars($stat['label'] ?? '') ?></span>
                        <strong><?= htmlspecialchars((string) ($stat['value'] ?? '0')) ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <div class="mpl-filters">
        <label class="mpl-search">
            <?= smsIcon('search') ?>
            <input type="search" id="mplSearch" placeholder="<?= htmlspecialchars($searchPlaceholder) ?>" aria-label="Search records">
        </label>
        <select id="mplStatus" aria-label="Filter by status">
            <?php foreach ($statuses as $statusOption): ?>
                <option value="<?= htmlspecialchars($statusOption) ?>"><?= htmlspecialchars($statusOption) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="mplType" aria-label="Filter by type">
            <?php foreach ($types as $typeOption): ?>
                <option value="<?= htmlspecialchars($typeOption) ?>"><?= htmlspecialchars($typeOption) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" id="mplDate" aria-label="Filter by date">
        <a class="mpl-btn mpl-btn-ghost mpl-btn-sm" href="?"><?= smsIcon('sync-alt', ['aria-hidden' => 'true']) ?> Refresh</a>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2><?= htmlspecialchars($listTitle) ?></h2>
                <p><?= htmlspecialchars($listSubtitle) ?></p>
            </div>
            <a class="mpl-btn mpl-btn-ghost mpl-btn-sm" href="?process=report"><?= smsIcon('file-export', ['aria-hidden' => 'true']) ?> Export</a>
        </div>

        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th style="width:2.5rem"><input type="checkbox" aria-label="Select all"></th>
                        <th><?= htmlspecialchars($columns['ref']) ?></th>
                        <th><?= htmlspecialchars($columns['subject']) ?></th>
                        <th><?= htmlspecialchars($columns['owner']) ?></th>
                        <th><?= htmlspecialchars($columns['detail']) ?></th>
                        <th><?= htmlspecialchars($columns['schedule']) ?></th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mplRows">
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                                <?= $isArchiveView
                                    ? 'Archive is empty for this page.'
                                    : 'No records found.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $subject = (string) ($row['subject'] ?? '');
                            $status = (string) ($row['status'] ?? '');
                            $class = $statusClass((string) ($row['status_class'] ?? ''), $status);
                            $type = (string) ($row['type'] ?? ($row['subtitle'] ?? 'General'));
                            $search = strtolower(trim(
                                ($row['search'] ?? '') . ' ' .
                                ($row['reference'] ?? '') . ' ' .
                                $subject . ' ' .
                                ($row['owner'] ?? '') . ' ' .
                                ($row['detail'] ?? '') . ' ' .
                                $status . ' ' . $type
                            ));
                            ?>
                            <?php
                            $refKey = trim((string) ($row['reference'] ?? ''));
                            $refQ = rawurlencode($refKey);
                            ?>
                            <tr data-search="<?= htmlspecialchars($search) ?>" data-status="<?= htmlspecialchars(strtolower($status)) ?>" data-type="<?= htmlspecialchars(strtolower($type)) ?>">
                                <td><input type="checkbox" aria-label="Select <?= htmlspecialchars((string) ($row['reference'] ?? '')) ?>"></td>
                                <td class="ref"><?= htmlspecialchars((string) ($row['reference'] ?? '')) ?></td>
                                <td>
                                    <div class="mpl-person">
                                        <span class="mpl-avatar"><?= htmlspecialchars(smsMplInitials($subject)) ?></span>
                                        <div>
                                            <strong><?= htmlspecialchars($subject) ?></strong>
                                            <?php if (!empty($row['subtitle'])): ?>
                                                <small><?= htmlspecialchars((string) $row['subtitle']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars((string) ($row['owner'] ?? '—')) ?></td>
                                <td><?= htmlspecialchars((string) ($row['detail'] ?? '—')) ?></td>
                                <td><?= htmlspecialchars((string) ($row['schedule'] ?? '—')) ?></td>
                                <td>
                                    <?php if ($isArchiveView): ?>
                                        <span class="mpl-status cancelled">Archived</span>
                                    <?php else: ?>
                                        <span class="mpl-status <?= htmlspecialchars($class) ?>"><?= htmlspecialchars($status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="mpl-actions">
                                        <a href="?process=view&ref=<?= $refQ ?><?= $isArchiveView ? '&view=archive' : '' ?>" title="View" aria-label="View"><?= smsIcon('eye') ?></a>
                                        <?php if ($isArchiveView): ?>
                                            <a class="warning" href="?process=restore&ref=<?= $refQ ?>&view=archive" title="Restore to active list" aria-label="Restore"><?= smsIcon('undo') ?></a>
                                        <?php else: ?>
                                            <a href="?process=edit&ref=<?= $refQ ?>" title="Edit" aria-label="Edit"><?= smsIcon('pen') ?></a>
                                            <a class="warning" href="?process=archive&ref=<?= $refQ ?>" title="Move to Archive" aria-label="Archive"><?= smsIcon('archive') ?></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mpl-foot">
            <span class="meta">Showing <?= $showingEnd > 0 ? '1' : '0' ?>-<?= $showingEnd ?> of <?= $rowCount ?> records</span>
            <?php if ($showPager): ?>
                <div class="mpl-pager" aria-label="Pagination">
                    <span class="active">1</span>
                    <span>2</span>
                    <span>3</span>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
(function () {
    var root = document.querySelector('[data-mpl]');
    if (!root) return;
    var search = root.querySelector('#mplSearch');
    var status = root.querySelector('#mplStatus');
    var type = root.querySelector('#mplType');
    var rows = root.querySelectorAll('#mplRows tr[data-search]');
    function applyFilters() {
        var q = (search && search.value ? search.value : '').toLowerCase().trim();
        var st = (status && status.value ? status.value : 'All Status').toLowerCase();
        var tp = (type && type.value ? type.value : 'All Types').toLowerCase();
        rows.forEach(function (row) {
            var hay = row.getAttribute('data-search') || '';
            var rowStatus = row.getAttribute('data-status') || '';
            var rowType = row.getAttribute('data-type') || '';
            var matchQ = !q || hay.indexOf(q) !== -1;
            var matchS = !st || st === 'all status' || rowStatus === st || rowStatus.indexOf(st) !== -1;
            var matchT = !tp || tp === 'all types' || rowType === tp || rowType.indexOf(tp) !== -1;
            row.style.display = matchQ && matchS && matchT ? '' : 'none';
        });
    }
    if (search) search.addEventListener('input', applyFilters);
    if (status) status.addEventListener('change', applyFilters);
    if (type) type.addEventListener('change', applyFilters);
})();
</script>
