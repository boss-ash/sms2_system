<?php
/** @var array<string, mixed> $detail */
/** @var array<string, mixed> $app */
/** @var string $ref */
$milestones = $detail['milestones'] ?? [];
$tranches = $detail['tranches'] ?? [];
$evidence = $detail['evidence'] ?? [];
$pending = $detail['pending_requirements'] ?? [];
$timeline = $detail['timeline'] ?? [];
$canSubmitEvidence = !empty($detail['can_submit_evidence']);
?>
<div class="gfr-detail" data-application-id="<?= (int) ($app['grant_application_id'] ?? 0) ?>">
    <div class="gfr-panel gfr-grant-card">
        <div class="gfr-grant-head">
            <div>
                <h2 class="gfr-panel-title"><?= $ref ?></h2>
                <p class="gfr-grant-subtitle"><?= htmlspecialchars((string) ($app['research_title'] ?? '—')) ?></p>
            </div>
            <span class="gfr-funded-badge"><?= smsIcon('check-circle') ?> APPROVED &amp; FUNDED</span>
        </div>
        <div class="gfr-summary-grid">
            <div class="gfr-summary-card">
                <span>Grant Program</span>
                <strong><?= htmlspecialchars((string) ($app['funding_title'] ?? '—')) ?></strong>
            </div>
            <div class="gfr-summary-card">
                <span>Approved Budget</span>
                <strong><?= htmlspecialchars(grantFormatPeso((float) ($detail['approved_budget'] ?? 0))) ?></strong>
            </div>
            <div class="gfr-summary-card released">
                <span>Total Released</span>
                <strong data-gfr-total-released><?= htmlspecialchars(grantFormatPeso((float) ($detail['total_released'] ?? 0))) ?></strong>
            </div>
            <div class="gfr-summary-card pending">
                <span>Balance Pending</span>
                <strong data-gfr-balance-pending><?= htmlspecialchars(grantFormatPeso((float) ($detail['balance_pending'] ?? 0))) ?></strong>
            </div>
            <div class="gfr-summary-card">
                <span>Funding Status</span>
                <strong data-gfr-funding-status><?= htmlspecialchars((string) ($detail['funding_status_label'] ?? '')) ?></strong>
            </div>
            <div class="gfr-summary-card">
                <span>Overall Progress</span>
                <strong><?= number_format((float) ($app['avg_completion_pct'] ?? 0), 1) ?>%</strong>
            </div>
        </div>
        <div class="gfr-link-row">
            <a class="gfr-btn gfr-btn-ghost" href="<?= htmlspecialchars((string) ($detail['milestones_url'] ?? grantProjectMilestonesUrl())) ?>">
                <?= smsIcon('tasks') ?> Project Milestones
            </a>
            <a class="gfr-btn gfr-btn-ghost" href="<?= htmlspecialchars((string) ($detail['disbursement_url'] ?? grantBudgetDisbursementUrl())) ?>">
                <?= smsIcon('money-bill-wave') ?> Fund Releases
            </a>
            <?php if ($canSubmitEvidence): ?>
            <button type="button" class="gfr-btn gfr-btn-primary gfrSubmitEvidenceBtn">
                <?= smsIcon('file-upload') ?> Submit Progress Evidence
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="gfr-grid-2">
        <div class="gfr-panel">
            <h3 class="gfr-section-title"><?= smsIcon('stream', ['class' => 'me-1']) ?>Project Timeline</h3>
            <div class="gfr-timeline" id="gfrTimeline">
                <?php foreach ($timeline as $item): ?>
                <div class="gfr-timeline-item <?= htmlspecialchars((string) ($item['status_class'] ?? 'pending')) ?>">
                    <div class="gfr-timeline-dot"></div>
                    <div class="gfr-timeline-content">
                        <strong><?= htmlspecialchars((string) ($item['name'] ?? '')) ?></strong>
                        <span class="gfr-status <?= htmlspecialchars((string) ($item['status_class'] ?? 'pending')) ?>">
                            <?= htmlspecialchars((string) ($item['status'] ?? 'Pending')) ?>
                        </span>
                        <div class="gfr-timeline-meta">
                            <span><?= number_format((float) ($item['completion_pct'] ?? 0), 0) ?>% complete</span>
                            <?php if (!empty($item['due_date'])): ?>
                            <span>Due <?= htmlspecialchars(date('M j, Y', strtotime((string) $item['due_date']))) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="gfr-panel">
            <h3 class="gfr-section-title"><?= smsIcon('exclamation-circle', ['class' => 'me-1']) ?>Pending Requirements</h3>
            <div class="gfr-requirements" id="gfrRequirements">
                <?php if ($pending === []): ?>
                    <p class="gfr-muted">No pending requirements. You are on track.</p>
                <?php else: ?>
                    <?php foreach ($pending as $req): ?>
                    <div class="gfr-requirement <?= htmlspecialchars((string) ($req['level'] ?? 'info')) ?>"<?= !empty($req['milestone_id']) ? ' data-milestone-id="' . (int) $req['milestone_id'] . '"' : '' ?>>
                        <strong><?= htmlspecialchars((string) ($req['label'] ?? '')) ?></strong>
                        <span><?= htmlspecialchars((string) ($req['detail'] ?? '')) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="gfr-panel">
        <h3 class="gfr-section-title"><?= smsIcon('layer-group', ['class' => 'me-1']) ?>Milestones</h3>
        <div class="gfr-table-wrap">
            <table class="gfr-table">
                <thead>
                    <tr>
                        <th>Milestone</th>
                        <th>Due Date</th>
                        <th>Completion %</th>
                        <th>Status</th>
                        <th>Document</th>
                    </tr>
                </thead>
                <tbody id="gfrMilestoneBody">
                    <?php foreach ($milestones as $milestone):
                        $status = (string) ($milestone['status'] ?? 'Pending');
                        $statusClass = grantMilestoneStatusClass($status);
                    ?>
                    <tr>
                        <td style="font-weight:700;"><?= htmlspecialchars((string) ($milestone['milestone_name'] ?? '')) ?></td>
                        <td><?= !empty($milestone['due_date']) ? htmlspecialchars(date('M d, Y', strtotime((string) $milestone['due_date']))) : '—' ?></td>
                        <td><strong><?= number_format((float) ($milestone['completion_pct'] ?? 0), 0) ?>%</strong></td>
                        <td><span class="gfr-status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($status) ?></span></td>
                        <td>
                            <?php if (!empty($milestone['has_document'])): ?>
                                <a href="<?= htmlspecialchars((string) ($milestone['document_url'] ?? '')) ?>" target="_blank" rel="noopener">
                                    <?= smsIcon('file-alt') ?> <?= htmlspecialchars((string) ($milestone['supporting_doc_original'] ?? 'View')) ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="gfr-panel">
        <h3 class="gfr-section-title"><?= smsIcon('money-bill-wave', ['class' => 'me-1']) ?>Fund Releases</h3>
        <div class="gfr-table-wrap">
            <table class="gfr-table">
                <thead>
                    <tr>
                        <th>Tranche</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Release Date</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody id="gfrTrancheBody">
                    <?php foreach ($tranches as $tranche):
                        $isReleased = (string) ($tranche['status'] ?? '') === 'Released';
                    ?>
                    <tr>
                        <td style="font-weight:700;"><?= htmlspecialchars((string) ($tranche['tranche_label'] ?? ('Tranche ' . (int) ($tranche['tranche_number'] ?? 0)))) ?></td>
                        <td><?= htmlspecialchars(grantFormatPeso((float) ($tranche['amount_released'] ?? 0))) ?></td>
                        <td><span class="gfr-status <?= $isReleased ? 'completed' : 'pending' ?>"><?= htmlspecialchars((string) ($tranche['status'] ?? 'Pending')) ?></span></td>
                        <td><?= !empty($tranche['release_date']) ? htmlspecialchars((string) $tranche['release_date']) : '—' ?></td>
                        <td><?= htmlspecialchars((string) ($tranche['reference_number'] ?? '—')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="gfr-panel">
        <h3 class="gfr-section-title"><?= smsIcon('folder-open', ['class' => 'me-1']) ?>Your Progress Evidence</h3>
        <div class="gfr-table-wrap">
            <table class="gfr-table">
                <thead>
                    <tr>
                        <th>Submitted</th>
                        <th>Milestone</th>
                        <th>Title</th>
                        <th>File</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="gfrEvidenceBody">
                    <?php if ($evidence === []): ?>
                    <tr><td colspan="5" class="gfr-muted" style="text-align:center;padding:1.25rem;">No evidence submitted yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($evidence as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('M j, Y h:i A', strtotime((string) ($row['created_at'] ?? 'now')))) ?></td>
                        <td><?= htmlspecialchars((string) ($row['milestone_name'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars((string) ($row['evidence_title'] ?? '')) ?></td>
                        <td>
                            <?php if (!empty($row['has_file'])): ?>
                            <a href="<?= htmlspecialchars((string) ($row['file_url'] ?? '')) ?>" target="_blank" rel="noopener">
                                <?= smsIcon('file-alt') ?> <?= htmlspecialchars((string) ($row['file_original'] ?? 'View')) ?>
                            </a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><span class="gfr-status in-progress"><?= htmlspecialchars((string) ($row['status'] ?? 'Submitted')) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
