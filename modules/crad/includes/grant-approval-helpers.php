<?php
/**
 * CRAD Grant Proposal — Sequential approval workflow helpers.
 *
 * Sign-off sequence: Adviser → Dept. Chair → Dean → Research Office → VPAA → Finance
 */
declare(strict_types=1);

require_once __DIR__ . '/grant-helpers.php';
require_once __DIR__ . '/grant-evaluation-helpers.php';

/** @return list<array{key: string, order: int, label: string, short: string, role: string}> */
function grantApprovalStepDefinitions(): array
{
    return [
        ['key' => 'adviser',           'order' => 1, 'label' => 'Academic Adviser',    'short' => 'Adviser',          'role' => 'adviser'],
        ['key' => 'department_chair',  'order' => 2, 'label' => 'Dept. Chair',         'short' => 'Dept. Chair',      'role' => 'research_coordinator'],
        ['key' => 'dean',              'order' => 3, 'label' => 'College Dean',        'short' => 'Dean',             'role' => 'hr'],
        ['key' => 'research_office',   'order' => 4, 'label' => 'Research Office',     'short' => 'Research Office',  'role' => 'crad_officer'],
        ['key' => 'vpaa',              'order' => 5, 'label' => 'VPAA Sign-off',       'short' => 'VPAA',             'role' => 'qa'],
        ['key' => 'finance',           'order' => 6, 'label' => 'Finance Allocation',  'short' => 'Finance',          'role' => 'finance'],
    ];
}

/** @return array<string, array{key: string, order: int, label: string, short: string, role: string}> */
function grantApprovalStepMap(): array
{
    $map = [];
    foreach (grantApprovalStepDefinitions() as $step) {
        $map[$step['key']] = $step;
    }

    return $map;
}

/** @return list<string> */
function grantApprovalApproverRoleKeys(): array
{
    return array_values(array_unique(array_map(
        static fn(array $step): string => (string) $step['role'],
        grantApprovalStepDefinitions()
    )));
}

function grantUserCanViewApprovalWorkflow(): bool
{
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';

    if (in_array($roleKey, array_merge(grantApprovalApproverRoleKeys(), ['superadmin']), true)) {
        return true;
    }

    if (!function_exists('smsHasGrantedModuleAdminAccess')) {
        require_once dirname(__DIR__, 3) . '/includes/authentication.php';
    }

    return smsHasGrantedModuleAdminAccess('crad')
        || smsHasGrantedModuleAdminAccess('crad_grant');
}

function grantUserCanMonitorApprovalWorkflow(): bool
{
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';

    return in_array($roleKey, ['crad_officer', 'superadmin'], true)
        || (function_exists('smsHasGrantedModuleAdminAccess') && smsHasGrantedModuleAdminAccess('crad'));
}

function grantRequireApprovalAccess(): void
{
    if (grantUserCanViewApprovalWorkflow()) {
        return;
    }

    grantRedirectUnauthorized();
}

function grantApprovalActiveModuleKey(): string
{
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';

    return match ($roleKey) {
        'adviser'                => 'faculty',
        'research_coordinator'   => 'crad',
        'hr'                     => 'faculty',
        'finance'                => 'payment',
        'qa'                     => 'accreditation',
        default                  => 'crad',
    };
}

function grantApprovalBreadcrumbModuleLabel(): string
{
    return match (grantApprovalActiveModuleKey()) {
        'faculty'        => 'Faculty',
        'payment'        => 'Finance',
        'accreditation'  => 'QA Office',
        default          => 'CRAD',
    };
}

function grantApprovalBreadcrumbModuleUrl(): string
{
    return match (grantApprovalActiveModuleKey()) {
        'faculty'        => BASE_URL . '/modules/faculty/pages/approved-research.php',
        'payment'        => BASE_URL . '/modules/payment/index.php',
        'accreditation'  => BASE_URL . '/modules/accreditation/index.php',
        default          => BASE_URL . '/modules/crad/index.php',
    };
}

function grantApprovalRoleLabel(string $roleKey): string
{
    return match ($roleKey) {
        'adviser'              => 'Academic Adviser',
        'research_coordinator' => 'Department Chair',
        'hr'                   => 'College Dean',
        'crad_officer'         => 'Research Office',
        'qa'                   => 'VPAA',
        'finance'              => 'Finance Officer',
        'superadmin'           => 'Administrator',
        default                => ucwords(str_replace('_', ' ', $roleKey)),
    };
}

function grantUserCanActOnApprovalStep(string $stepRoleKey): bool
{
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';

    if ($roleKey === 'superadmin') {
        return true;
    }

    return $roleKey === $stepRoleKey;
}

function grantEnsureApprovalTables(PDO $crad): void
{
    grantEnsureTables($crad);

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $crad->exec("
        CREATE TABLE IF NOT EXISTS grant_proposal_approval_workflows (
            id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
            grant_application_id  INT UNSIGNED NOT NULL,
            current_step_key      VARCHAR(40)  NOT NULL DEFAULT 'adviser',
            workflow_status       ENUM('In Progress','Completed','Returned') NOT NULL DEFAULT 'In Progress',
            started_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            completed_at          DATETIME     DEFAULT NULL,
            updated_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_gpaw_application (grant_application_id),
            KEY idx_gpaw_status (workflow_status),
            KEY idx_gpaw_current_step (current_step_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $crad->exec("
        CREATE TABLE IF NOT EXISTS grant_proposal_approval_steps (
            id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
            workflow_id           INT UNSIGNED NOT NULL,
            grant_application_id  INT UNSIGNED NOT NULL,
            step_key              VARCHAR(40)  NOT NULL,
            step_order            TINYINT UNSIGNED NOT NULL,
            step_label            VARCHAR(80)  NOT NULL,
            approver_role_key     VARCHAR(40)  NOT NULL,
            status                ENUM('Queued','Pending','Approved','Returned') NOT NULL DEFAULT 'Queued',
            approver_user_id      INT UNSIGNED DEFAULT NULL,
            approver_name         VARCHAR(150) DEFAULT NULL,
            remarks               TEXT         DEFAULT NULL,
            signature_data        MEDIUMTEXT   DEFAULT NULL,
            acted_at              DATETIME     DEFAULT NULL,
            created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_gpas_workflow_step (workflow_id, step_key),
            KEY idx_gpas_application (grant_application_id),
            KEY idx_gpas_status (status),
            KEY idx_gpas_role (approver_role_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function grantApprovalWorkflowUrl(int $applicationId = 0): string
{
    $url = str_replace('\\', '/', rtrim(BASE_URL, '/') . '/modules/crad/pages/approval-workflows.php');
    if ($applicationId > 0) {
        $url .= '?id=' . $applicationId;
    }

    return $url;
}

/**
 * @return array{ok: bool, workflow_id?: int, error?: string}
 */
function grantStartApprovalWorkflow(PDO $crad, int $applicationId): array
{
    grantEnsureApprovalTables($crad);

    if ($applicationId <= 0) {
        return ['ok' => false, 'error' => 'Invalid application.'];
    }

    $existing = grantGetApprovalWorkflowByApplicationId($crad, $applicationId);
    if ($existing !== null) {
        return ['ok' => true, 'workflow_id' => (int) ($existing['id'] ?? 0)];
    }

    $app = grantGetApplicationForEvaluation($crad, $applicationId);
    if ($app === null) {
        return ['ok' => false, 'error' => 'Proposal not found.'];
    }

    try {
        $crad->beginTransaction();

        $crad->prepare("
            INSERT INTO grant_proposal_approval_workflows
                (grant_application_id, current_step_key, workflow_status, started_at, updated_at)
            VALUES (?, 'adviser', 'In Progress', NOW(), NOW())
        ")->execute([$applicationId]);
        $workflowId = (int) $crad->lastInsertId();

        $insertStep = $crad->prepare("
            INSERT INTO grant_proposal_approval_steps
                (workflow_id, grant_application_id, step_key, step_order, step_label,
                 approver_role_key, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        foreach (grantApprovalStepDefinitions() as $step) {
            $status = $step['order'] === 1 ? 'Pending' : 'Queued';
            $insertStep->execute([
                $workflowId,
                $applicationId,
                $step['key'],
                $step['order'],
                $step['label'],
                $step['role'],
                $status,
            ]);
        }

        $crad->prepare("
            UPDATE grant_applications
               SET status = 'Under Review', updated_at = NOW()
             WHERE id = ?
        ")->execute([$applicationId]);

        $crad->commit();

        return ['ok' => true, 'workflow_id' => $workflowId];
    } catch (Throwable $e) {
        if ($crad->inTransaction()) {
            $crad->rollBack();
        }
        error_log('grantStartApprovalWorkflow: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'Failed to start approval workflow.'];
    }
}

function grantBackfillApprovalWorkflows(PDO $crad): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    grantEnsureApprovalTables($crad);
    grantEnsureEvaluationTables($crad);

    try {
        $rows = $crad->query("
            SELECT DISTINCT ga.id
              FROM grant_applications ga
              JOIN grant_proposal_evaluations e
                ON e.grant_application_id = ga.id
               AND e.recommendation = 'recommend'
              LEFT JOIN grant_proposal_approval_workflows w
                ON w.grant_application_id = ga.id
             WHERE w.id IS NULL
               AND ga.status NOT IN ('Rejected', 'Revision Required', 'Denied', 'Withdrawn')
        ")->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($rows as $appId) {
            grantStartApprovalWorkflow($crad, (int) $appId);
        }
    } catch (Throwable $e) {
        error_log('grantBackfillApprovalWorkflows: ' . $e->getMessage());
    }
}

function grantGetApprovalWorkflowByApplicationId(PDO $crad, int $applicationId): ?array
{
    grantEnsureApprovalTables($crad);

    $stmt = $crad->prepare("
        SELECT w.*,
               ga.proposal_reference,
               ga.research_title,
               ga.applicant_name,
               ga.college_dept,
               ga.requested_budget,
               ga.status AS application_status,
               ga.current_version,
               go.funding_title
          FROM grant_proposal_approval_workflows w
          JOIN grant_applications ga ON ga.id = w.grant_application_id
          LEFT JOIN grant_opportunities go ON go.id = ga.grant_opportunity_id
         WHERE w.grant_application_id = ?
         LIMIT 1
    ");
    $stmt->execute([$applicationId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * @return list<array<string, mixed>>
 */
function grantGetApprovalSteps(PDO $crad, int $workflowId): array
{
    grantEnsureApprovalTables($crad);

    $stmt = $crad->prepare("
        SELECT *
          FROM grant_proposal_approval_steps
         WHERE workflow_id = ?
         ORDER BY step_order ASC
    ");
    $stmt->execute([$workflowId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * @return array<string, mixed>|null
 */
function grantGetApprovalWorkflowDetail(PDO $crad, int $applicationId): ?array
{
    $workflow = grantGetApprovalWorkflowByApplicationId($crad, $applicationId);
    if ($workflow === null) {
        return null;
    }

    $steps = grantGetApprovalSteps($crad, (int) ($workflow['id'] ?? 0));
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';
    $currentStepKey = (string) ($workflow['current_step_key'] ?? '');
    $canAct = false;
    $currentStep = null;

    foreach ($steps as $step) {
        if ((string) ($step['step_key'] ?? '') === $currentStepKey) {
            $currentStep = $step;
            $canAct = ((string) ($workflow['workflow_status'] ?? '') === 'In Progress')
                && in_array((string) ($step['status'] ?? ''), ['Pending', 'Queued'], true)
                && grantUserCanActOnApprovalStep((string) ($step['approver_role_key'] ?? ''));
            break;
        }
    }

    return [
        'workflow'     => $workflow,
        'steps'        => $steps,
        'current_step' => $currentStep,
        'can_act'      => $canAct,
        'role_label'   => grantApprovalRoleLabel($roleKey),
        'is_monitor'   => grantUserCanMonitorApprovalWorkflow(),
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function grantApprovalWorkflowList(PDO $crad): array
{
    grantEnsureApprovalTables($crad);
    grantBackfillApprovalWorkflows($crad);

    $roleKey   = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';
    $isMonitor = grantUserCanMonitorApprovalWorkflow();

    $sql = "
        SELECT w.*,
               ga.proposal_reference,
               ga.research_title,
               ga.applicant_name,
               ga.college_dept,
               ga.requested_budget,
               ga.status AS application_status,
               ga.current_version,
               go.funding_title,
               cs.step_label AS current_step_label,
               cs.approver_role_key AS current_approver_role
          FROM grant_proposal_approval_workflows w
          JOIN grant_applications ga ON ga.id = w.grant_application_id
          LEFT JOIN grant_opportunities go ON go.id = ga.grant_opportunity_id
          LEFT JOIN grant_proposal_approval_steps cs
            ON cs.workflow_id = w.id AND cs.step_key = w.current_step_key
    ";

    $params = [];
    if (!$isMonitor) {
        $sql .= " WHERE cs.approver_role_key = ? AND w.workflow_status = 'In Progress'";
        $params[] = $roleKey;
    } else {
        $sql .= " WHERE 1=1";
    }

    $sql .= " ORDER BY w.updated_at DESC, w.id DESC";

    $stmt = $crad->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function grantApprovalWorkflowFingerprint(array $workflows): string
{
    $parts = [];
    foreach ($workflows as $row) {
        $parts[] = implode(':', [
            $row['id'] ?? '',
            $row['grant_application_id'] ?? '',
            $row['current_step_key'] ?? '',
            $row['workflow_status'] ?? '',
            $row['updated_at'] ?? '',
            $row['application_status'] ?? '',
        ]);
    }

    return md5(implode('|', $parts));
}

/**
 * @return array{ok: bool, error?: string, completed?: bool}
 */
function grantSubmitApprovalSignoff(
    PDO $crad,
    int $applicationId,
    int $userId,
    string $userName,
    ?string $signatureData = null,
    ?string $remarks = null
): array {
    grantEnsureApprovalTables($crad);

    $detail = grantGetApprovalWorkflowDetail($crad, $applicationId);
    if ($detail === null) {
        return ['ok' => false, 'error' => 'Approval workflow not found.'];
    }

    if (empty($detail['can_act'])) {
        return ['ok' => false, 'error' => 'You are not authorized to sign off at this stage.'];
    }

    $workflow    = $detail['workflow'];
    $currentStep = $detail['current_step'];
    if ($currentStep === null) {
        return ['ok' => false, 'error' => 'No active approval step.'];
    }

    if ($signatureData !== null && $signatureData !== '' && !str_starts_with($signatureData, 'data:image/png;base64,')) {
        return ['ok' => false, 'error' => 'Invalid signature format.'];
    }

    $workflowId     = (int) ($workflow['id'] ?? 0);
    $stepKey        = (string) ($currentStep['step_key'] ?? '');
    $stepOrder      = (int) ($currentStep['step_order'] ?? 0);
    $definitions    = grantApprovalStepDefinitions();
    $nextStep       = null;
    foreach ($definitions as $def) {
        if ($def['order'] === $stepOrder + 1) {
            $nextStep = $def;
            break;
        }
    }

    try {
        $crad->beginTransaction();

        $crad->prepare("
            UPDATE grant_proposal_approval_steps
               SET status = 'Approved',
                   approver_user_id = ?,
                   approver_name = ?,
                   remarks = ?,
                   signature_data = ?,
                   acted_at = NOW(),
                   updated_at = NOW()
             WHERE workflow_id = ? AND step_key = ?
        ")->execute([
            $userId > 0 ? $userId : null,
            $userName,
            $remarks !== '' ? $remarks : null,
            $signatureData,
            $workflowId,
            $stepKey,
        ]);

        if ($nextStep === null) {
            $crad->prepare("
                UPDATE grant_proposal_approval_workflows
                   SET workflow_status = 'Completed',
                       completed_at = NOW(),
                       updated_at = NOW()
                 WHERE id = ?
            ")->execute([$workflowId]);

            $crad->prepare("
                UPDATE grant_applications
                   SET status = 'Approved', updated_at = NOW()
                 WHERE id = ?
            ")->execute([$applicationId]);

            $crad->commit();

            return ['ok' => true, 'completed' => true];
        }

        $crad->prepare("
            UPDATE grant_proposal_approval_workflows
               SET current_step_key = ?, updated_at = NOW()
             WHERE id = ?
        ")->execute([$nextStep['key'], $workflowId]);

        $crad->prepare("
            UPDATE grant_proposal_approval_steps
               SET status = 'Pending', updated_at = NOW()
             WHERE workflow_id = ? AND step_key = ?
        ")->execute([$workflowId, $nextStep['key']]);

        $crad->commit();

        return ['ok' => true, 'completed' => false, 'next_step' => $nextStep['key']];
    } catch (Throwable $e) {
        if ($crad->inTransaction()) {
            $crad->rollBack();
        }
        error_log('grantSubmitApprovalSignoff: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'Failed to record approval sign-off.'];
    }
}

/**
 * @return array{ok: bool, error?: string}
 */
function grantReturnProposalFromApproval(
    PDO $crad,
    int $applicationId,
    int $userId,
    string $userName,
    string $remarks
): array {
    grantEnsureApprovalTables($crad);

    $remarks = trim($remarks);
    if ($remarks === '') {
        return ['ok' => false, 'error' => 'Remarks are required when returning a proposal for revision.'];
    }

    $detail = grantGetApprovalWorkflowDetail($crad, $applicationId);
    if ($detail === null) {
        return ['ok' => false, 'error' => 'Approval workflow not found.'];
    }

    if (empty($detail['can_act'])) {
        return ['ok' => false, 'error' => 'You are not authorized to return this proposal.'];
    }

    $workflow    = $detail['workflow'];
    $currentStep = $detail['current_step'];
    if ($currentStep === null) {
        return ['ok' => false, 'error' => 'No active approval step.'];
    }

    $workflowId = (int) ($workflow['id'] ?? 0);
    $stepKey    = (string) ($currentStep['step_key'] ?? '');

    try {
        $crad->beginTransaction();

        $crad->prepare("
            UPDATE grant_proposal_approval_steps
               SET status = 'Returned',
                   approver_user_id = ?,
                   approver_name = ?,
                   remarks = ?,
                   acted_at = NOW(),
                   updated_at = NOW()
             WHERE workflow_id = ? AND step_key = ?
        ")->execute([$userId > 0 ? $userId : null, $userName, $remarks, $workflowId, $stepKey]);

        $crad->prepare("
            UPDATE grant_proposal_approval_workflows
               SET workflow_status = 'Returned', updated_at = NOW()
             WHERE id = ?
        ")->execute([$workflowId]);

        $crad->prepare("
            UPDATE grant_applications
               SET status = 'Revision Required', updated_at = NOW()
             WHERE id = ?
        ")->execute([$applicationId]);

        $app = grantGetApplicationForEvaluation($crad, $applicationId);
        if ($app !== null && function_exists('grantNotifyApplicantEvaluationDecision')) {
            grantNotifyApplicantEvaluationDecision(
                $crad,
                $app,
                'require_revisions',
                0.0,
                $userName,
                $remarks
            );
        }

        $crad->commit();

        return ['ok' => true];
    } catch (Throwable $e) {
        if ($crad->inTransaction()) {
            $crad->rollBack();
        }
        error_log('grantReturnProposalFromApproval: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'Failed to return proposal for revision.'];
    }
}

/**
 * UI helper: map step row to display state for the stepper.
 *
 * @return array{state: string, label: string, sub: string, date: string}
 */
function grantApprovalStepDisplayState(array $step, string $currentStepKey, string $workflowStatus): array
{
    $status  = (string) ($step['status'] ?? 'Queued');
    $stepKey = (string) ($step['step_key'] ?? '');
    $actedAt = (string) ($step['acted_at'] ?? '');

    if ($status === 'Approved') {
        $date = $actedAt !== '' ? date('M j, Y', strtotime($actedAt)) : '';

        return ['state' => 'approved', 'label' => 'Approved', 'sub' => '', 'date' => $date];
    }

    if ($status === 'Returned') {
        return ['state' => 'returned', 'label' => 'Returned', 'sub' => '', 'date' => ''];
    }

    if ($workflowStatus !== 'In Progress') {
        return ['state' => 'queued', 'label' => 'Queued', 'sub' => '', 'date' => ''];
    }

    if ($stepKey === $currentStepKey && in_array($status, ['Pending', 'Queued'], true)) {
        return ['state' => 'active', 'label' => 'In Review', 'sub' => 'Pending', 'date' => ''];
    }

    return ['state' => 'queued', 'label' => 'Queued', 'sub' => '', 'date' => ''];
}
