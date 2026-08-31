<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/modules/faculty/includes/panel-defense-page.php';

header('Content-Type: application/json');
panelRequirePanelMember();

$mode = strtolower(trim((string) ($_GET['mode'] ?? 'assigned')));
$id = (int) ($_GET['id'] ?? 0);
$count = 0;

ob_start();
if ($mode === 'history') {
    $rows = panelDefenseRows(true);
    $count = count($rows);
    panelRenderHistoryRows($rows);
} elseif ($mode === 'details') {
    $defense = $id > 0 ? panelDefenseById($id, true) : null;
    $count = $defense ? 1 : 0;
    panelRenderDefenseDetails($defense);
} elseif ($mode === 'scoring') {
    $defense = $id > 0 ? panelDefenseById($id, false) : null;
    $count = $defense ? 1 : count(panelDefenseRows(false));
    panelRenderScoring($defense);
} else {
    $rows = panelDefenseRows(false);
    $count = count($rows);
    panelRenderAssignedRows($rows);
}
$html = trim((string) ob_get_clean());

echo json_encode(['ok' => true, 'count' => $count, 'html' => $html]);
