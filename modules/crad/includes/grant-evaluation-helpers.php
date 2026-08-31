<?php
/**
 * CRAD Grant Proposal — Review Committee evaluation helpers.
 */
declare(strict_types=1);

require_once __DIR__ . '/grant-helpers.php';

/** @var array<string, int> */
function grantRubricCriteria(): array
{
    return [
        'rationale'        => 25,
        'methodology'      => 30,
        'budget'           => 20,
        'team_capability'  => 15,
        'compliance'       => 10,
    ];
}

function grantRubricMaxTotal(): int
{
    return 100;
}

function grantUserCanEvaluate(): bool
{
    $roleKey = function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '';

    if ($roleKey === 'review_committee') {
        return true;
    }

    if (!function_exists('smsRoleAllowedForModule')) {
        require_once dirname(__DIR__, 3) . '/includes/authentication.php';
    }

    return smsHasGrantedModuleAdminAccess('crad_grant');
}

function grantRequireEvaluateAccess(): void
{
    if (grantUserCanEvaluate()) {
        return;
    }

    grantRedirectUnauthorized();
}

function grantEvaluationActiveModuleKey(): string
{
    return 'crad_grant';
}

function grantEnsureEvaluationTables(PDO $crad): void
{
    grantEnsureTables($crad);

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $crad->exec("
        CREATE TABLE IF NOT EXISTS grant_proposal_evaluations (
            id                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
            grant_application_id  INT UNSIGNED  NOT NULL,
            evaluator_user_id     INT UNSIGNED  NOT NULL,
            evaluator_name        VARCHAR(150)  NOT NULL DEFAULT '',
            score_rationale       DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            score_methodology     DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            score_budget          DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            score_team_capability DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            score_compliance      DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            total_score           DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
            comments              TEXT          DEFAULT NULL,
            recommendations       TEXT          DEFAULT NULL,
            required_corrections  TEXT          DEFAULT NULL,
            submitted_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                 ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_gpe_app_evaluator (grant_application_id, evaluator_user_id),
            KEY idx_gpe_application (grant_application_id),
            KEY idx_gpe_evaluator (evaluator_user_id),
            KEY idx_gpe_submitted (submitted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

/**
 * Proposals awaiting committee review (Submitted / Under Review, not yet scored by current evaluator).
 *
 * @return array<int, array<string, mixed>>
 */
function grantEvaluationQueue(PDO $crad, ?int $evaluatorUserId = null): array
{
    grantEnsureEvaluationTables($crad);

    $evaluatorUserId = $evaluatorUserId ?? (int) ($_SESSION['user_id'] ?? 0);
    if ($evaluatorUserId <= 0) {
        return [];
    }

    $stmt = $crad->prepare("
        SELECT
            ga.id,
            ga.grant_opportunity_id,
            ga.applicant_name,
            ga.applicant_user_id,
            ga.college_dept,
            ga.requested_budget,
            ga.research_title,
            ga.abstract,
            ga.objectives,
            ga.proposal_pdf,
            ga.proposal_pdf_original,
            ga.supporting_docs,
            ga.supporting_docs_original,
            ga.ethics_doc,
            ga.ethics_doc_original,
            ga.status,
            ga.submitted_at,
            ga.updated_at,
            go.funding_title,
            go.max_funding_cap,
            go.eligibility,
            go.application_deadline,
            ev.id AS my_evaluation_id,
            ev.total_score AS my_total_score,
            ev.submitted_at AS my_evaluated_at
        FROM grant_applications ga
        INNER JOIN grant_opportunities go ON go.id = ga.grant_opportunity_id
        LEFT JOIN grant_proposal_evaluations ev
               ON ev.grant_application_id = ga.id
              AND ev.evaluator_user_id = ?
        WHERE ga.status IN ('Submitted', 'Under Review')
        ORDER BY ga.submitted_at ASC, ga.id ASC
    ");
    $stmt->execute([$evaluatorUserId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function grantGetApplicationForEvaluation(PDO $crad, int $applicationId): ?array
{
    grantEnsureEvaluationTables($crad);

    $stmt = $crad->prepare("
        SELECT
            ga.*,
            go.funding_title,
            go.max_funding_cap,
            go.eligibility,
            go.application_deadline
        FROM grant_applications ga
        INNER JOIN grant_opportunities go ON go.id = ga.grant_opportunity_id
        WHERE ga.id = ?
        LIMIT 1
    ");
    $stmt->execute([$applicationId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function grantGetEvaluationByApplication(PDO $crad, int $applicationId, ?int $evaluatorUserId = null): ?array
{
    grantEnsureEvaluationTables($crad);

    $evaluatorUserId = $evaluatorUserId ?? (int) ($_SESSION['user_id'] ?? 0);
    if ($evaluatorUserId <= 0) {
        return null;
    }

    $stmt = $crad->prepare("
        SELECT *
        FROM grant_proposal_evaluations
        WHERE grant_application_id = ?
          AND evaluator_user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$applicationId, $evaluatorUserId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * @param array<string, mixed> $input
 * @return array{ok: bool, id?: int, total_score?: float, error?: string}
 */
function grantSubmitProposalEvaluation(PDO $crad, int $applicationId, array $input): array
{
    grantEnsureEvaluationTables($crad);

    $evaluatorUserId = (int) ($_SESSION['user_id'] ?? 0);
    $evaluatorName   = trim((string) ($_SESSION['full_name'] ?? $_SESSION['user_name'] ?? $_SESSION['username'] ?? ''));

    if ($evaluatorUserId <= 0) {
        return ['ok' => false, 'error' => 'Invalid evaluator session.'];
    }

    $application = grantGetApplicationForEvaluation($crad, $applicationId);
    if (!$application) {
        return ['ok' => false, 'error' => 'Proposal not found.'];
    }

    if (!in_array((string) ($application['status'] ?? ''), ['Submitted', 'Under Review'], true)) {
        return ['ok' => false, 'error' => 'This proposal is no longer open for committee evaluation.'];
    }

    if (grantGetEvaluationByApplication($crad, $applicationId, $evaluatorUserId)) {
        return ['ok' => false, 'error' => 'You have already submitted an evaluation for this proposal.'];
    }

    $criteria = grantRubricCriteria();
    $scores   = [];
    $total    = 0.0;

    foreach ($criteria as $key => $max) {
        $field = 'score_' . $key;
        if (!array_key_exists($field, $input) && !array_key_exists($key, $input)) {
            return ['ok' => false, 'error' => 'All rubric criteria scores are required.'];
        }
        $raw = (float) ($input[$field] ?? $input[$key] ?? -1);
        if ($raw < 0 || $raw > $max) {
            $label = ucwords(str_replace('_', ' ', $key));
            return ['ok' => false, 'error' => "{$label} score must be between 0 and {$max}."];
        }
        $scores[$field] = round($raw, 2);
        $total += $scores[$field];
    }

    $total = round($total, 2);
    if ($total > grantRubricMaxTotal()) {
        return ['ok' => false, 'error' => 'Total score cannot exceed 100.'];
    }

    $comments            = trim((string) ($input['comments'] ?? ''));
    $recommendations     = trim((string) ($input['recommendations'] ?? ''));
    $requiredCorrections = trim((string) ($input['required_corrections'] ?? ''));

    try {
        $crad->beginTransaction();

        $stmt = $crad->prepare("
            INSERT INTO grant_proposal_evaluations
                (grant_application_id, evaluator_user_id, evaluator_name,
                 score_rationale, score_methodology, score_budget,
                 score_team_capability, score_compliance, total_score,
                 comments, recommendations, required_corrections,
                 submitted_at, updated_at)
            VALUES
                (?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 NOW(), NOW())
        ");
        $stmt->execute([
            $applicationId,
            $evaluatorUserId,
            $evaluatorName,
            $scores['score_rationale'],
            $scores['score_methodology'],
            $scores['score_budget'],
            $scores['score_team_capability'],
            $scores['score_compliance'],
            $total,
            $comments !== '' ? $comments : null,
            $recommendations !== '' ? $recommendations : null,
            $requiredCorrections !== '' ? $requiredCorrections : null,
        ]);

        if ((string) ($application['status'] ?? '') === 'Submitted') {
            $crad->prepare("
                UPDATE grant_applications
                   SET status = 'Under Review', updated_at = NOW()
                 WHERE id = ?
            ")->execute([$applicationId]);
        }

        $crad->commit();

        return [
            'ok'          => true,
            'id'          => (int) $crad->lastInsertId(),
            'total_score' => $total,
        ];
    } catch (Throwable $e) {
        if ($crad->inTransaction()) {
            $crad->rollBack();
        }
        error_log('grantSubmitProposalEvaluation: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'Failed to save evaluation. Please try again.'];
    }
}

function grantProposalFileUrl(int $applicationId, string $field = 'proposal'): string
{
    return BASE_URL . '/modules/crad/grant-proposal-file.php?id=' . $applicationId . '&field=' . rawurlencode($field);
}
