<?php
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/breadcrumbs.php';

$pageTitle = 'Evaluation & Scoring';
$activeModule = 'faculty';
$activePage = 'panel-evaluation-scoring';
$breadcrumbs = [
    ['label' => 'Panel Portal', 'url' => BASE_URL . '/modules/faculty/index.php'],
    ['label' => 'Evaluation & Scoring', 'url' => null],
];

require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/modules/faculty/includes/panel-defense-page.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } elseif (($_POST['action'] ?? '') === 'submit_evaluation') {
        $result = panelSubmitEvaluation((int) ($_POST['schedule_id'] ?? 0), $_POST);
        if (!empty($result['ok'])) {
            $message = (string) ($result['message'] ?? 'Evaluation submitted successfully.');
            $_GET['id'] = '0';
        } else {
            $error = (string) ($result['error'] ?? 'Unable to submit evaluation.');
        }
    }
}

renderBreadcrumbs($breadcrumbs);
renderPanelDefensePage('scoring', $message, $error);
require_once ROOT_PATH . '/includes/layout-end.php';
