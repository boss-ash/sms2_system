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

/** @return array<string, string> */
function grantRecommendationOptions(): array
{
    return [
        'disapprove'         => 'Disapprove',
        'require_revisions'  => 'Require Revisions',
        'recommend'          => 'Recommend',
    ];
}

function grantRecommendationLabel(string $recommendation): string
{
    return grantRecommendationOptions()[$recommendation] ?? ucwords(str_replace('_', ' ', $recommendation));
}

function grantStatusForRecommendation(string $recommendation): ?string
{
    return match ($recommendation) {
        'disapprove'        => 'Rejected',
        'require_revisions' => 'Revision Required',
        'recommend'         => 'Under Review',
        default             => null,
    };
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
            recommendation        VARCHAR(40)   DEFAULT NULL,
            revision_reason       TEXT          DEFAULT NULL,
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

    _grantAddColumnIfMissing($crad, 'grant_proposal_evaluations', 'recommendation',
        "VARCHAR(40) DEFAULT NULL COMMENT 'Reviewer decision: disapprove | require_revisions' AFTER required_corrections");
    _grantAddColumnIfMissing($crad, 'grant_proposal_evaluations', 'revision_reason',
        "TEXT DEFAULT NULL COMMENT 'Reason for required revisions' AFTER recommendation");
    _grantAddColumnIfMissing($crad, 'grant_proposal_evaluations', 'proposal_version',
        "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Proposal version evaluated' AFTER grant_application_id");

    _grantEnsureEvaluationVersionIndex($crad);

    $crad->exec("
        CREATE TABLE IF NOT EXISTS grant_proposal_notifications (
            id                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
            event_key           VARCHAR(120)  NOT NULL,
            recipient_user_id   INT UNSIGNED  DEFAULT NULL,
            recipient_role      VARCHAR(40)   NOT NULL DEFAULT '',
            recipient_email     VARCHAR(190)  NOT NULL DEFAULT '',
            grant_application_id INT UNSIGNED NOT NULL,
            type                VARCHAR(40)   NOT NULL DEFAULT '',
            title               VARCHAR(200)  NOT NULL DEFAULT '',
            body                TEXT          NOT NULL,
            url                 VARCHAR(500)  NOT NULL DEFAULT '',
            is_read             TINYINT(1)    NOT NULL DEFAULT 0,
            created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_gpn_event (event_key),
            KEY idx_gpn_recipient_user (recipient_user_id),
            KEY idx_gpn_application (grant_application_id),
            KEY idx_gpn_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function _grantEnsureEvaluationVersionIndex(PDO $crad): void
{
    try {
        $old = $crad->query("SHOW INDEX FROM grant_proposal_evaluations WHERE Key_name = 'uniq_gpe_app_evaluator'")->fetch();
        if ($old) {
            $crad->exec('ALTER TABLE grant_proposal_evaluations DROP INDEX uniq_gpe_app_evaluator');
        }
        $new = $crad->query("SHOW INDEX FROM grant_proposal_evaluations WHERE Key_name = 'uniq_gpe_app_eval_ver'")->fetch();
        if (!$new) {
            $crad->exec('ALTER TABLE grant_proposal_evaluations ADD UNIQUE KEY uniq_gpe_app_eval_ver (grant_application_id, evaluator_user_id, proposal_version)');
        }
    } catch (Throwable $e) {
        error_log('_grantEnsureEvaluationVersionIndex: ' . $e->getMessage());
    }
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
            ga.proposal_reference,
            ga.current_version,
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
              AND ev.proposal_version = COALESCE(NULLIF(ga.current_version, 0), 1)
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

function grantGetEvaluationByApplication(PDO $crad, int $applicationId, ?int $evaluatorUserId = null, ?int $proposalVersion = null): ?array
{
    grantEnsureEvaluationTables($crad);

    $evaluatorUserId = $evaluatorUserId ?? (int) ($_SESSION['user_id'] ?? 0);
    if ($evaluatorUserId <= 0) {
        return null;
    }

    if ($proposalVersion === null) {
        $app = grantGetApplicationForEvaluation($crad, $applicationId);
        $proposalVersion = max(1, (int) ($app['current_version'] ?? 1));
    }

    $stmt = $crad->prepare("
        SELECT *
        FROM grant_proposal_evaluations
        WHERE grant_application_id = ?
          AND evaluator_user_id = ?
          AND proposal_version = ?
        LIMIT 1
    ");
    $stmt->execute([$applicationId, $evaluatorUserId, $proposalVersion]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * Latest committee evaluation per application (for researcher feedback display).
 *
 * @param  array<int, int> $applicationIds
 * @return array<int, array<string, mixed>>
 */
function grantGetLatestEvaluationsForApplications(PDO $crad, array $applicationIds): array
{
    grantEnsureEvaluationTables($crad);

    $applicationIds = array_values(array_unique(array_filter(array_map('intval', $applicationIds))));
    if ($applicationIds === []) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($applicationIds), '?'));
    $stmt = $crad->prepare("
        SELECT e.*
        FROM grant_proposal_evaluations e
        INNER JOIN grant_applications ga ON ga.id = e.grant_application_id
        INNER JOIN (
            SELECT e2.grant_application_id, MAX(e2.id) AS latest_id
            FROM grant_proposal_evaluations e2
            INNER JOIN grant_applications ga2 ON ga2.id = e2.grant_application_id
            WHERE e2.grant_application_id IN ({$placeholders})
              AND e2.proposal_version = COALESCE(NULLIF(ga2.current_version, 0), 1)
              AND (
                    ga2.status NOT IN ('Revision Required', 'Rejected')
                 OR (ga2.status = 'Revision Required' AND e2.recommendation = 'require_revisions')
                 OR (ga2.status = 'Rejected' AND e2.recommendation = 'disapprove')
              )
            GROUP BY e2.grant_application_id
        ) latest ON latest.latest_id = e.id
    ");
    $stmt->execute($applicationIds);

    $map = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
        $map[(int) $row['grant_application_id']] = $row;
    }

    return $map;
}

function grantProposalsApplicationsUrl(): string
{
    return BASE_URL . '/modules/crad/pages/proposals-applications.php';
}

function grantNotifyApplicantEvaluationDecision(
    PDO $crad,
    array $application,
    string $recommendation,
    float $totalScore,
    string $evaluatorName,
    ?string $revisionReason = null
): void {
    grantEnsureEvaluationTables($crad);

    $applicationId = (int) ($application['id'] ?? 0);
    $recipientUserId = (int) ($application['applicant_user_id'] ?? 0);
    if ($applicationId <= 0 || $recipientUserId <= 0) {
        return;
    }

    $title = (string) ($application['research_title'] ?? 'your grant proposal');
    $titleShort = mb_strimwidth($title, 0, 80, '…');

    if ($recommendation === 'disapprove') {
        $type = 'grant_rejected';
        $notifTitle = 'Grant Proposal Rejected';
        $body = sprintf(
            'Your grant proposal "%s" was disapproved by the review committee (score: %s/100). View details in Proposals & Applications.',
            $titleShort,
            number_format($totalScore, 1)
        );
    } elseif ($recommendation === 'require_revisions') {
        $type = 'grant_revision_required';
        $notifTitle = 'Grant Proposal Revisions Required';
        $reasonPreview = trim((string) $revisionReason);
        if ($reasonPreview !== '') {
            $reasonPreview = mb_strimwidth($reasonPreview, 0, 120, '…');
            $body = sprintf(
                'Your grant proposal "%s" requires revisions. Reason: %s',
                $titleShort,
                $reasonPreview
            );
        } else {
            $body = sprintf(
                'Your grant proposal "%s" requires revisions. Please review committee feedback in Proposals & Applications.',
                $titleShort
            );
        }
    } else {
        return;
    }

    $eventKey = 'grant-proposal:' . $type . ':' . $applicationId
        . ':v' . max(1, (int) ($application['current_version'] ?? 1))
        . ':u' . $recipientUserId;
    $stmt = $crad->prepare("
        INSERT INTO grant_proposal_notifications
            (event_key, recipient_user_id, recipient_role, recipient_email,
             grant_application_id, type, title, body, url)
        VALUES
            (?, ?, '', '', ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            body = VALUES(body),
            url = VALUES(url),
            is_read = 0,
            created_at = NOW()
    ");
    $stmt->execute([
        $eventKey,
        $recipientUserId,
        $applicationId,
        $type,
        $notifTitle,
        $body,
        $recommendation === 'require_revisions' ? grantRevisionsRequestedUrl() : grantProposalsApplicationsUrl(),
    ]);
}

/**
 * @param array<string, mixed> $input
 * @return array{ok: bool, id?: int, total_score?: float, recommendation?: string, new_status?: string, error?: string}
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

    $proposalVersion = max(1, (int) ($application['current_version'] ?? 1));

    if (grantGetEvaluationByApplication($crad, $applicationId, $evaluatorUserId, $proposalVersion)) {
        return ['ok' => false, 'error' => 'You have already submitted an evaluation for this proposal version.'];
    }

    $recommendation = strtolower(trim((string) ($input['recommendation'] ?? '')));
    if (!array_key_exists($recommendation, grantRecommendationOptions())) {
        return ['ok' => false, 'error' => 'Please select a recommendation decision.'];
    }

    $newStatus = grantStatusForRecommendation($recommendation);
    if ($newStatus === null) {
        return ['ok' => false, 'error' => 'Invalid recommendation selected.'];
    }

    $currentStatus = (string) ($application['status'] ?? '');
    if ($recommendation === 'recommend') {
        if (in_array($currentStatus, ['Rejected', 'Revision Required'], true)) {
            $newStatus = $currentStatus;
        } elseif ($currentStatus === 'Submitted') {
            $newStatus = 'Under Review';
        } else {
            $newStatus = $currentStatus !== '' ? $currentStatus : 'Under Review';
        }
    }

    $revisionReason = trim((string) ($input['revision_reason'] ?? ''));
    if ($recommendation === 'require_revisions' && $revisionReason === '') {
        return ['ok' => false, 'error' => 'Revision reason is required when selecting Require Revisions.'];
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
    if ($recommendation === 'require_revisions' && $requiredCorrections === '') {
        $requiredCorrections = $revisionReason;
    }

    try {
        $crad->beginTransaction();

        $stmt = $crad->prepare("
            INSERT INTO grant_proposal_evaluations
                (grant_application_id, proposal_version, evaluator_user_id, evaluator_name,
                 score_rationale, score_methodology, score_budget,
                 score_team_capability, score_compliance, total_score,
                 comments, recommendations, required_corrections,
                 recommendation, revision_reason,
                 submitted_at, updated_at)
            VALUES
                (?, ?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 ?, ?,
                 NOW(), NOW())
        ");
        $stmt->execute([
            $applicationId,
            $proposalVersion,
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
            $recommendation,
            $revisionReason !== '' ? $revisionReason : null,
        ]);

        $currentStatus = (string) ($application['status'] ?? '');
        $shouldUpdateStatus = true;
        if ($recommendation === 'recommend') {
            if (in_array($currentStatus, ['Rejected', 'Revision Required', 'Approved', 'Denied', 'Withdrawn'], true)) {
                $newStatus = $currentStatus;
                $shouldUpdateStatus = false;
            } elseif ($currentStatus === 'Submitted') {
                $newStatus = 'Under Review';
            } else {
                $shouldUpdateStatus = false;
            }
        }

        if ($shouldUpdateStatus) {
            $crad->prepare("
                UPDATE grant_applications
                   SET status = ?, updated_at = NOW()
                 WHERE id = ?
            ")->execute([$newStatus, $applicationId]);
        }

        if (in_array($recommendation, ['disapprove', 'require_revisions'], true)) {
            grantNotifyApplicantEvaluationDecision(
                $crad,
                $application,
                $recommendation,
                $total,
                $evaluatorName,
                $revisionReason !== '' ? $revisionReason : null
            );
        }

        $crad->commit();

        return [
            'ok'             => true,
            'id'             => (int) $crad->lastInsertId(),
            'total_score'    => $total,
            'recommendation' => $recommendation,
            'new_status'     => $newStatus,
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

/**
 * Grant proposal notifications for the signed-in researcher.
 *
 * @return array<int, array<string, mixed>>
 */
function grantProposalNotificationsForCurrentUser(int $limit = 8): array
{
    $crad = cradDb();
    if (!$crad) {
        return [];
    }

    grantEnsureEvaluationTables($crad);

    try {
        $table = $crad->query("SHOW TABLES LIKE 'grant_proposal_notifications'")->fetchColumn();
        if (!$table) {
            return [];
        }

        if (!function_exists('smsCurrentUserNotificationWhere')) {
            require_once dirname(__DIR__, 3) . '/includes/notifications.php';
        }

        $where = smsCurrentUserNotificationWhere();
        $stmt = $crad->prepare("
            SELECT id, event_key, type, title, body, url, is_read, created_at
            FROM grant_proposal_notifications
            WHERE {$where['sql']}
            ORDER BY created_at DESC, id DESC
            LIMIT :limit
        ");
        foreach ($where['params'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return array_map(static function (array $row): array {
            $type = (string) ($row['type'] ?? '');
            $icon = $type === 'grant_rejected' ? 'fa-times-circle' : 'fa-edit';
            return [
                'id' => -2000000 - (int) ($row['id'] ?? 0),
                'batch_key' => (string) ($row['event_key'] ?? ''),
                'icon' => $icon,
                'status' => ((int) ($row['is_read'] ?? 0) === 1) ? 'read' : 'unread',
                'title' => (string) ($row['title'] ?? 'Grant Proposal Update'),
                'body' => (string) ($row['body'] ?? ''),
                'url' => (string) ($row['url'] ?? grantProposalsApplicationsUrl()),
                'created_at' => (string) ($row['created_at'] ?? date('Y-m-d H:i:s')),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    } catch (Throwable $e) {
        error_log('grantProposalNotificationsForCurrentUser: ' . $e->getMessage());
        return [];
    }
}

function grantMarkProposalNotificationRead(int $notificationId): void
{
    if ($notificationId >= -2000000) {
        return;
    }

    $grantNotificationId = abs($notificationId) - 2000000;
    if ($grantNotificationId <= 0) {
        return;
    }

    $crad = cradDb();
    if (!$crad) {
        return;
    }

    grantEnsureEvaluationTables($crad);

    try {
        if (!function_exists('smsCurrentUserNotificationWhere')) {
            require_once dirname(__DIR__, 3) . '/includes/notifications.php';
        }
        $where = smsCurrentUserNotificationWhere();
        $stmt = $crad->prepare("
            UPDATE grant_proposal_notifications
               SET is_read = 1
             WHERE id = :notification_id
               AND {$where['sql']}
             LIMIT 1
        ");
        $stmt->bindValue(':notification_id', $grantNotificationId, PDO::PARAM_INT);
        foreach ($where['params'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
    } catch (Throwable $e) {
        error_log('grantMarkProposalNotificationRead: ' . $e->getMessage());
    }
}
