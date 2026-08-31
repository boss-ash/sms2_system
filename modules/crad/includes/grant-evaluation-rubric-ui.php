<?php
/**
 * Shared rubric UI for grant Reviewer Evaluation and Approval Workflows score pills.
 */
declare(strict_types=1);

require_once __DIR__ . '/grant-evaluation-helpers.php';

function grantPipelineScorePillLabel(string $evaluationType): string
{
    return match ($evaluationType) {
        'committee'        => 'Committee',
        'adviser'          => 'Adviser',
        'department_chair' => 'Dept. Chair',
        'dean'             => 'Dean',
        'research_office'  => 'Research Office',
        'vpaa'             => 'VPAA',
        'finance'          => 'Finance',
        default            => grantEvaluationStepLabel($evaluationType),
    };
}

function grantPipelineScorePillIcon(string $evaluationType): string
{
    return match ($evaluationType) {
        'committee'        => 'users',
        'adviser'          => 'user-tie',
        'department_chair' => 'user-check',
        'dean'             => 'user-shield',
        'research_office'  => 'flask',
        'vpaa'             => 'award',
        'finance'          => 'credit-card',
        default            => 'clipboard-check',
    };
}

/**
 * @param array<string, array<string, mixed>> $pipelineEvals
 * @return list<array{type: string, label: string, icon: string, total_score: float}>
 */
function grantFormatPipelineScorePills(array $pipelineEvals): array
{
    $pills = [];
    foreach (grantPipelineEvaluationTypes() as $type) {
        if (empty($pipelineEvals[$type])) {
            continue;
        }
        $pills[] = [
            'type'        => $type,
            'label'       => grantPipelineScorePillLabel($type),
            'icon'        => grantPipelineScorePillIcon($type),
            'total_score' => (float) ($pipelineEvals[$type]['total_score'] ?? 0),
        ];
    }

    return $pills;
}

/**
 * @param list<array{type: string, label: string, icon: string, total_score: float}>|null $pills
 */
function grantRenderPipelineScorePills(?array $pills, string $wrapperClass = 'gaw-monitor-scores'): void
{
    if ($pills === null || $pills === []) {
        return;
    }
    ?>
    <div class="<?= htmlspecialchars($wrapperClass) ?>">
        <?php foreach ($pills as $pill): ?>
        <span class="gaw-monitor-score-pill">
            <?= smsIcon((string) ($pill['icon'] ?? 'clipboard-check'), ['class' => 'me-1']) ?>
            <?= htmlspecialchars((string) ($pill['label'] ?? '')) ?>:
            <strong><?= number_format((float) ($pill['total_score'] ?? 0), 1) ?>/100</strong>
        </span>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * @param array<string, mixed> $eval
 * @param array<string, int>   $rubric
 */
function grantRenderRubricReadonlyCard(
    array $eval,
    string $title,
    array $rubric,
    string $icon = 'clipboard-list',
    bool $open = true
): void {
    ?>
    <details class="gre-committee-ref"<?= $open ? ' open' : '' ?>>
        <summary class="gre-committee-ref-summary">
            <?= smsIcon($icon, ['class' => 'me-1']) ?>
            <?= htmlspecialchars($title) ?>
            <strong><?= number_format((float) ($eval['total_score'] ?? 0), 1) ?> / 100</strong>
        </summary>
        <div class="gre-committee-ref-body">
            <?php if (!empty($eval['submitted_at'])): ?>
            <p class="text-muted mb-3">Submitted on <?= htmlspecialchars(date('M d, Y g:i A', strtotime((string) $eval['submitted_at']))) ?></p>
            <?php endif; ?>
            <table class="gre-rubric-table gre-rubric-readonly">
                <thead><tr><th>Criteria</th><th>Maximum</th><th>Score</th></tr></thead>
                <tbody>
                <?php foreach ($rubric as $key => $max):
                    $col = 'score_' . $key;
                    $label = ucwords(str_replace('_', ' ', $key));
                ?>
                    <tr>
                        <td><?= htmlspecialchars($label) ?></td>
                        <td><?= $max ?></td>
                        <td><strong><?= number_format((float) ($eval[$col] ?? 0), 1) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
                    <tr class="gre-total-row">
                        <td colspan="2"><strong>Total Score</strong></td>
                        <td><strong><?= number_format((float) ($eval['total_score'] ?? 0), 1) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <?php if (!empty($eval['comments'])): ?>
            <div class="gre-block"><h3>Comments</h3><p><?= nl2br(htmlspecialchars((string) $eval['comments'])) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($eval['recommendations'])): ?>
            <div class="gre-block"><h3>Recommendations</h3><p><?= nl2br(htmlspecialchars((string) $eval['recommendations'])) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($eval['required_corrections'])): ?>
            <div class="gre-block"><h3>Required Corrections</h3><p><?= nl2br(htmlspecialchars((string) $eval['required_corrections'])) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($eval['recommendation'])): ?>
            <div class="gre-block">
                <h3>Recommendation Decision</h3>
                <p><strong><?= htmlspecialchars(grantRecommendationLabel((string) $eval['recommendation'])) ?></strong></p>
                <?php if (!empty($eval['revision_reason'])): ?>
                <p class="mb-0"><span style="font-size:.75rem;font-weight:700;color:var(--sms-text-muted);">Revision reason:</span><br><?= nl2br(htmlspecialchars((string) $eval['revision_reason'])) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </details>
    <?php
}

/**
 * @param array{
 *   application_id: int,
 *   rubric: array<string, int>,
 *   title: string,
 *   description?: string,
 *   submit_label: string,
 *   form_attrs?: array<string, string>,
 *   show_recommendation?: bool,
 *   comments_placeholder?: string,
 *   recommendations_placeholder?: string,
 *   corrections_placeholder?: string,
 * } $options
 */
function grantRenderRubricEvaluationForm(array $options): void
{
    $applicationId = (int) ($options['application_id'] ?? 0);
    $rubric        = $options['rubric'] ?? grantRubricCriteria();
    $title         = (string) ($options['title'] ?? 'Score Proposal Using Rubric');
    $description   = (string) ($options['description'] ?? 'Enter scores for each criterion. Total is computed automatically (max 100).');
    $submitLabel   = (string) ($options['submit_label'] ?? 'Submit Evaluation');
    $formAttrs     = $options['form_attrs'] ?? [];
    $showRecommendation = !empty($options['show_recommendation']);
    $commentsPlaceholder = (string) ($options['comments_placeholder'] ?? 'General comments on the proposal…');
    $recommendationsPlaceholder = (string) ($options['recommendations_placeholder'] ?? 'Recommendations…');
    $correctionsPlaceholder = (string) ($options['corrections_placeholder'] ?? 'List required corrections, if any…');

    $attrString = '';
    foreach ($formAttrs as $key => $value) {
        $attrString .= ' ' . htmlspecialchars((string) $key) . '="' . htmlspecialchars((string) $value) . '"';
    }
    ?>
    <h2><?= smsIcon('star-half-alt', ['class' => 'me-2 text-primary']) ?><?= htmlspecialchars($title) ?></h2>
    <p class="text-muted mb-3"><?= htmlspecialchars($description) ?></p>

    <div id="greEvalAlert" class="mpl-alert" style="display:none;" role="alert"></div>

    <form id="greEvalForm" data-no-loader novalidate<?= $attrString ?>>
        <input type="hidden" name="grant_application_id" value="<?= $applicationId ?>">

        <table class="gre-rubric-table">
            <thead>
                <tr>
                    <th>Criteria</th>
                    <th style="width:90px;">Maximum</th>
                    <th style="width:140px;">Score</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rubric as $key => $max):
                $label = ucwords(str_replace('_', ' ', $key));
                $inputId = 'score_' . $key;
            ?>
                <tr>
                    <td><?= htmlspecialchars($label) ?></td>
                    <td class="text-center"><?= $max ?></td>
                    <td>
                        <input type="number" class="go-form-input gre-score-input"
                               id="<?= htmlspecialchars($inputId) ?>"
                               name="<?= htmlspecialchars($inputId) ?>"
                               min="0" max="<?= $max ?>" step="0.5" required
                               data-max="<?= $max ?>"
                               aria-label="<?= htmlspecialchars($label) ?> score">
                    </td>
                </tr>
            <?php endforeach; ?>
                <tr class="gre-total-row">
                    <td colspan="2"><strong>Total Score</strong></td>
                    <td><strong id="greTotalScore">0</strong> / 100</td>
                </tr>
            </tbody>
        </table>

        <div class="gre-form-group">
            <label for="greComments" class="go-form-label">Comments</label>
            <textarea id="greComments" name="comments" class="go-form-input" rows="3"
                      placeholder="<?= htmlspecialchars($commentsPlaceholder) ?>"></textarea>
        </div>
        <div class="gre-form-group">
            <label for="greRecommendations" class="go-form-label">Recommendations</label>
            <textarea id="greRecommendations" name="recommendations" class="go-form-input" rows="3"
                      placeholder="<?= htmlspecialchars($recommendationsPlaceholder) ?>"></textarea>
        </div>
        <div class="gre-form-group">
            <label for="greCorrections" class="go-form-label">Required Corrections</label>
            <textarea id="greCorrections" name="required_corrections" class="go-form-input" rows="3"
                      placeholder="<?= htmlspecialchars($correctionsPlaceholder) ?>"></textarea>
        </div>

        <?php if ($showRecommendation): ?>
        <div class="gre-form-group">
            <span class="go-form-label">Recommendation <span class="text-danger">*</span></span>
            <div class="gre-recommendation-options" role="radiogroup" aria-label="Recommendation decision">
                <?php foreach (grantRecommendationOptions() as $value => $label): ?>
                <label class="gre-recommendation-option">
                    <input type="radio" name="recommendation" value="<?= htmlspecialchars($value) ?>" required>
                    <span><?= htmlspecialchars($label) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <p class="gre-recommendation-hint">Disapprove ends the proposal. Require Revisions sends it back to the researcher. Recommend forwards it to the approval workflow.</p>
        </div>

        <div class="gre-form-group" id="greRevisionReasonGroup" style="display:none;">
            <label for="greRevisionReason" class="go-form-label">Revision Reason <span class="text-danger">*</span></label>
            <textarea id="greRevisionReason" name="revision_reason" class="go-form-input" rows="3"
                      placeholder="Explain what must be revised before resubmission…"></textarea>
        </div>
        <?php endif; ?>

        <button type="submit" class="mpl-btn mpl-btn-primary" id="greSubmitBtn">
            <?= smsIcon('check', ['class' => 'me-1']) ?><?= htmlspecialchars($submitLabel) ?>
        </button>
    </form>
    <?php
}

/**
 * @param array<string, array<string, mixed>> $pipelineEvals
 */
function grantRenderPipelineScorePillsFromEvals(array $pipelineEvals, string $wrapperClass = 'gaw-monitor-scores'): void
{
    grantRenderPipelineScorePills(grantFormatPipelineScorePills($pipelineEvals), $wrapperClass);
}
