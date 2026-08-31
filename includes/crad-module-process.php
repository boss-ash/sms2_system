<?php
/**
 * SMS 2 - CRAD / custom process adapter → fleet-style real page UI
 */
$processAction = $_GET['process'] ?? '';
$pageName = $pageTitle ?? 'Process';
$activeModuleKey = $activeModule ?? 'crad';
$activePageKey = $activePage ?? '';

require_once ROOT_PATH . '/includes/module-page-ui-catalog.php';
$catalogUi = smsModulePageUi((string) $activeModuleKey, (string) $activePageKey, (string) $pageName);

$actionMessages = [
    'new' => $pageName . ' draft has been created.',
    'validate' => $pageName . ' validation completed.',
    'approve' => $pageName . ' approval recorded.',
    'report' => $pageName . ' report generated.',
    'view' => $pageName . ' opened for viewing.',
    'edit' => $pageName . ' opened for editing.',
    'archive' => $pageName . ' moved to Archive.',
    'restore' => $pageName . ' restored to the active list.',
    'delete' => $pageName . ' moved to Archive.',
    'submit' => $pageName . ' entry saved.',
];
$processMessage = $actionMessages[$processAction] ?? '';

require_once ROOT_PATH . '/includes/mpl-archive.php';
$archiveRef = trim((string) ($_GET['ref'] ?? ''));
if ($processAction === 'archive' && $archiveRef !== '') {
    smsMplArchiveAdd($activeModuleKey, $activePageKey, $archiveRef);
}
if ($processAction === 'restore' && $archiveRef !== '') {
    smsMplArchiveRemove($activeModuleKey, $activePageKey, $archiveRef);
}
if ($processAction === 'delete' && $archiveRef !== '') {
    smsMplArchiveAdd($activeModuleKey, $activePageKey, $archiveRef);
}

if (is_array($catalogUi)) {
    $mpl = $catalogUi;
    $mpl['alert'] = $processMessage;
    $mpl['module_key'] = $activeModuleKey;
    $mpl['page_slug'] = $activePageKey;
    require ROOT_PATH . '/includes/module-process-list-view.php';
    return;
}

// Fallback: map legacy $cradProcess if catalog has no page yet.
$cradProcess = $cradProcess ?? [];
$description = $cradProcess['description'] ?? ('Manage ' . $pageName . ' records.');
$metrics = $cradProcess['metrics'] ?? [];
$records = $cradProcess['records'] ?? [];
$actions = $cradProcess['actions'] ?? [];
$fields = $cradProcess['fields'] ?? ['reference', 'title', 'owner', 'status', 'updated'];
$columns = $cradProcess['columns'] ?? ['Reference', 'Item', 'Assigned To', 'Status', 'Updated'];

$addAction = $actions[0] ?? ['label' => 'Add Record', 'process' => 'new'];
$addLabel = (string) ($addAction['label'] ?? 'Add Record');
if ($addLabel !== '' && $addLabel[0] !== '+') {
    $addLabel = '+ ' . $addLabel;
}

$stats = [];
foreach (array_slice($metrics, 0, 4) as $metric) {
    $stats[] = [
        'label' => $metric['label'] ?? 'Metric',
        'value' => (string) ($metric['value'] ?? '0'),
        'icon' => $metric['icon'] ?? 'fa-circle',
        'tone' => $metric['tone'] ?? 'blue',
    ];
}

$statusOptions = ['All Status'];
$mplRows = [];
foreach ($records as $record) {
    $status = (string) ($record['status'] ?? 'Pending');
    if (!in_array($status, $statusOptions, true)) {
        $statusOptions[] = $status;
    }
    $mplRows[] = [
        'reference' => (string) ($record['reference'] ?? ''),
        'subject' => (string) ($record[$fields[1] ?? 'title'] ?? $record['title'] ?? ''),
        'subtitle' => $pageName,
        'owner' => (string) ($record['owner'] ?? ''),
        'detail' => (string) ($record['detail'] ?? 'CRAD Desk'),
        'schedule' => (string) ($record['updated'] ?? ''),
        'status' => $status,
        'status_class' => (string) ($record['status_class'] ?? strtolower($status)),
        'type' => $pageName,
    ];
}

$mpl = [
    'description' => $description,
    'add_label' => $addLabel,
    'add_process' => $addAction['process'] ?? 'new',
    'alert' => $processMessage,
    'stats' => $stats,
    'search_placeholder' => 'Search by reference, title, or detail.',
    'statuses' => $statusOptions,
    'types' => ['All Types', $pageName],
    'list_title' => $pageName . ' List',
    'list_subtitle' => 'View and manage all ' . strtolower($pageName) . ' records.',
    'show_add_button' => array_key_exists('show_add_button', $cradProcess) ? (bool) $cradProcess['show_add_button'] : true,
    'show_pager' => array_key_exists('show_pager', $cradProcess) ? (bool) $cradProcess['show_pager'] : true,
    'columns' => [
        'ref' => 'Reference No.',
        'subject' => $columns[1] ?? 'Subject',
        'owner' => $columns[2] ?? 'Assigned To',
        'detail' => 'Office / Detail',
        'schedule' => $columns[4] ?? 'Schedule',
    ],
    'rows' => $mplRows,
];

require ROOT_PATH . '/includes/module-process-list-view.php';
