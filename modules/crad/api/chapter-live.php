<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/modules/crad/includes/chapter-evaluation-workflow.php';

requireAuth();
header('Content-Type: application/json');

try {
    $crad = getCradDatabaseConnection();
    $mode = (string) ($_GET['mode'] ?? '');
    $payload = ['ok' => true, 'server_time' => date('c')];

    if ($mode === 'student') {
        if (getCurrentUserRoleKey() !== 'student') {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            exit;
        }
        $group = chapterRegisteredStudentGroup($crad);
        $rows = $group ? chapterLatestSubmissionsForGroup($crad, (int) $group['id']) : [];
        $history = $group ? chapterSubmissionHistoryForGroup($crad, (int) $group['id']) : [];
        $eligibility = $group ? chapterSubmissionEligibility($crad, (int) $group['id']) : [];
        $payload['registry_available'] = (bool) $group;
        $payload['group'] = $group ? [
            'id' => (int) $group['id'],
            'group_number' => (string) $group['group_number'],
            'group_name' => (string) $group['group_name'],
            'research_title' => (string) $group['research_title'],
            'academic_year' => (string) $group['academic_year'],
        ] : null;
        $payload['submissions'] = array_map('chapterLiveSubmissionRow', $rows);
        $payload['eligibility'] = array_values($eligibility);
        $payload['history_count'] = count($history);
        $payload['latest_update'] = $rows ? max(array_map(static fn($r) => (string) ($r['updated_at'] ?? ''), $rows)) : '';
        $payload['eligibility_update'] = $eligibility ? max(array_map(static fn($r) => (string) ($r['approval']['approved_at'] ?? ''), $eligibility)) : '';
        echo json_encode($payload);
        exit;
    }

    if ($mode === 'evaluator') {
        if (!chapterIsEvaluator()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            exit;
        }
        $queue = chapterEvaluatorQueue($crad, false);
        $payload['queue'] = array_map('chapterLiveSubmissionRow', $queue);
        $payload['pending_count'] = count($queue);
        $payload['latest_update'] = $queue ? max(array_map(static fn($r) => (string) ($r['updated_at'] ?? ''), $queue)) : '';
        echo json_encode($payload);
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid mode']);
} catch (Throwable $e) {
    error_log('Chapter live endpoint failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Unable to load live updates.']);
}

function chapterLiveSubmissionRow(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'group' => (string) $row['group_name'],
        'group_number' => (string) $row['group_number'],
        'title' => (string) $row['research_title'],
        'chapter' => chapterLabel((int) $row['chapter_number']),
        'version' => (int) $row['version_number'],
        'status' => (string) $row['status'],
        'status_class' => chapterStatusClass((string) $row['status']),
        'submitted_by' => (string) $row['submitted_by_name'],
        'submitted_at' => chapterFormatDate((string) $row['submitted_at']),
        'updated_at' => chapterFormatDate((string) $row['updated_at']),
        'result' => (string) ($row['result'] ?? ''),
        'evaluator' => (string) ($row['evaluator_name'] ?? ''),
        'scoring_url' => chapterLiveAppBaseUrl() . '/modules/faculty/pages/evaluation-scoring.php?id=' . (int) $row['id'],
    ];
}

function chapterLiveAppBaseUrl(): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $modulePos = strpos($scriptName, '/modules/');
    if ($modulePos !== false) {
        return rtrim(substr($scriptName, 0, $modulePos), '/');
    }

    $base = '/' . ltrim((string) BASE_URL, '/');
    return rtrim($base, '/');
}
