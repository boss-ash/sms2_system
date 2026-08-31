<?php
/** @var array<string, mixed> $detail */
/** @var array<string, mixed> $app */
$app = $detail['application'] ?? [];
$submission = $detail['submission'] ?? null;
$repository = $detail['repository'] ?? null;
$supportingFiles = $detail['supporting_files'] ?? [];
$ref = htmlspecialchars((string) ($app['proposal_reference'] ?? 'Proposal'));
$workflowLabel = (string) ($detail['workflow_label'] ?? '');
$workflowClass = (string) ($detail['workflow_class'] ?? 'ready');
$canSubmitAction = !empty($detail['can_submit']);
$canVerifyAction = !empty($detail['can_verify']);
$fileBase = (string) ($detail['file_base_url'] ?? '');
?>
<div class="gpip-detail" data-application-id="<?= (int) ($app['id'] ?? 0) ?>">
    <div class="gpip-panel gpip-grant-card">
        <div class="gpip-grant-head">
            <div>
                <h2 class="gpip-panel-title"><?= $ref ?></h2>
                <p class="gpip-grant-subtitle"><?= htmlspecialchars((string) ($app['research_title'] ?? '—')) ?></p>
            </div>
            <span class="gpip-status-badge <?= htmlspecialchars($workflowClass) ?>" data-gpip-workflow-badge><?= htmlspecialchars($workflowLabel) ?></span>
        </div>
        <div class="gpip-summary-grid">
            <div class="gpip-summary-card">
                <span>Grant Program</span>
                <strong><?= htmlspecialchars((string) ($app['funding_title'] ?? '—')) ?></strong>
            </div>
            <div class="gpip-summary-card">
                <span>Lead Proponent</span>
                <strong><?= htmlspecialchars((string) ($app['applicant_name'] ?? '—')) ?></strong>
            </div>
            <div class="gpip-summary-card">
                <span>College / Dept</span>
                <strong><?= htmlspecialchars((string) ($app['college_dept'] ?? '—')) ?></strong>
            </div>
            <div class="gpip-summary-card">
                <span>Application Status</span>
                <strong data-gpip-app-status><?= htmlspecialchars(grantApplicationStatusLabel((string) ($app['status'] ?? ''))) ?></strong>
            </div>
            <?php if ($repository !== null): ?>
            <div class="gpip-summary-card verified">
                <span>Repository Reference</span>
                <strong data-gpip-repo-ref><?= htmlspecialchars((string) ($repository['repository_reference'] ?? '—')) ?></strong>
            </div>
            <?php endif; ?>
        </div>
        <div class="gpip-link-row">
            <?php if ($canSubmitAction): ?>
            <button type="button" class="gpip-btn gpip-btn-primary gpipOpenSubmitBtn">
                <?= smsIcon('file-upload') ?> Submit Final Output
            </button>
            <?php endif; ?>
            <?php if ($canVerifyAction): ?>
            <button type="button" class="gpip-btn gpip-btn-primary gpipVerifyBtn">
                <?= smsIcon('check-circle') ?> Verify
            </button>
            <button type="button" class="gpip-btn gpip-btn-danger gpipOpenReturnBtn">
                <?= smsIcon('undo') ?> Return for Correction
            </button>
            <?php endif; ?>
            <a class="gpip-btn gpip-btn-ghost" href="<?= htmlspecialchars(grantFundedResearchUrl((int) ($app['id'] ?? 0))) ?>">
                <?= smsIcon('flask') ?> Funded Research
            </a>
            <?php if (grantUserCanArchiveDocuments() && in_array((string) ($app['status'] ?? ''), [grantStatusOutputVerified(), grantStatusArchived()], true)): ?>
            <a class="gpip-btn gpip-btn-ghost" href="<?= htmlspecialchars(grantDocumentRepositoryUrl((int) ($app['id'] ?? 0))) ?>">
                <?= smsIcon('archive') ?> Document Repository
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($canVerifyAction): ?>
    <div class="gpip-panel gpip-verify-panel">
        <h3 class="gpip-section-title"><?= smsIcon('clipboard-check', ['class' => 'me-1']) ?>CRAD Verification Checklist</h3>
        <p class="gpip-muted" style="margin:0 0 .75rem;">Review before clicking <strong>Verify</strong> or <strong>Return for Correction</strong>:</p>
        <ul class="gpip-verify-list">
            <li>Final research title &amp; PDF (publication proof)</li>
            <li>Authors</li>
            <li>Journal / Conference</li>
            <li>DOI &amp; Publication URL</li>
            <li>Copyright information</li>
            <li>Patent records</li>
            <li>Other IP documentation</li>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($submission !== null && (string) ($submission['status'] ?? '') === 'RETURNED_FOR_CORRECTION'): ?>
    <div class="gpip-panel gpip-return-alert" data-gpip-return-alert>
        <h3 class="gpip-section-title"><?= smsIcon('exclamation-triangle', ['class' => 'me-1']) ?>Returned for Correction</h3>
        <p><?= nl2br(htmlspecialchars((string) ($submission['return_reason'] ?? ''))) ?></p>
        <?php if (!empty($submission['reviewed_by_name'])): ?>
        <p class="gpip-muted">Returned by <?= htmlspecialchars((string) $submission['reviewed_by_name']) ?>
            <?php if (!empty($submission['reviewed_at'])): ?>
            on <?= htmlspecialchars(date('M j, Y g:i A', strtotime((string) $submission['reviewed_at']))) ?>
            <?php endif; ?>
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($submission !== null): ?>
    <div class="gpip-panel" id="gpipSubmissionPanel">
        <h3 class="gpip-section-title"><?= smsIcon('file-alt', ['class' => 'me-1']) ?>Final Output Submission</h3>
        <div class="gpip-detail-grid">
            <div>
                <span class="gpip-label">Final Research Title</span>
                <strong data-gpip-final-title><?= htmlspecialchars((string) ($submission['final_research_title'] ?? '—')) ?></strong>
            </div>
            <div>
                <span class="gpip-label">Authors</span>
                <strong data-gpip-authors><?= htmlspecialchars((string) ($submission['authors'] ?? '—')) ?></strong>
            </div>
            <div>
                <span class="gpip-label">Publication Type</span>
                <strong data-gpip-pub-type><?= htmlspecialchars((string) ($submission['publication_type'] ?? '—')) ?></strong>
            </div>
            <div>
                <span class="gpip-label">Journal / Conference</span>
                <strong data-gpip-journal><?= htmlspecialchars((string) ($submission['journal_conference'] ?? '—')) ?></strong>
            </div>
            <div>
                <span class="gpip-label">DOI</span>
                <strong data-gpip-doi><?= htmlspecialchars((string) ($submission['doi'] ?? '—')) ?: '—' ?></strong>
            </div>
            <div>
                <span class="gpip-label">Publication URL</span>
                <strong data-gpip-pub-url>
                    <?php $pubUrl = trim((string) ($submission['publication_url'] ?? '')); ?>
                    <?php if ($pubUrl !== ''): ?>
                        <a href="<?= htmlspecialchars($pubUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($pubUrl) ?></a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </strong>
            </div>
            <div class="gpip-span-2">
                <span class="gpip-label">Abstract</span>
                <p class="gpip-abstract" data-gpip-abstract><?= nl2br(htmlspecialchars((string) ($submission['abstract'] ?? '—'))) ?></p>
            </div>
            <?php if (!empty($submission['ip_information'])): ?>
            <div class="gpip-span-2">
                <span class="gpip-label">IP Information</span>
                <p data-gpip-ip-info><?= nl2br(htmlspecialchars((string) $submission['ip_information'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if (!empty($submission['copyright_info'])): ?>
            <div>
                <span class="gpip-label">Copyright</span>
                <strong data-gpip-copyright><?= htmlspecialchars((string) $submission['copyright_info']) ?></strong>
            </div>
            <?php endif; ?>
            <?php if (!empty($submission['patent_info'])): ?>
            <div>
                <span class="gpip-label">Patent</span>
                <strong data-gpip-patent><?= htmlspecialchars((string) $submission['patent_info']) ?></strong>
            </div>
            <?php endif; ?>
            <?php if (!empty($submission['other_ip_info'])): ?>
            <div>
                <span class="gpip-label">Other IP Records</span>
                <strong data-gpip-other-ip><?= htmlspecialchars((string) $submission['other_ip_info']) ?></strong>
            </div>
            <?php endif; ?>
            <div>
                <span class="gpip-label">Final Research PDF</span>
                <?php if (!empty($submission['final_pdf_path'])): ?>
                <a class="gpip-file-link" href="<?= htmlspecialchars($fileBase) ?>?type=final_pdf&amp;submission_id=<?= (int) ($submission['id'] ?? 0) ?>" target="_blank" rel="noopener">
                    <?= smsIcon('file-pdf') ?> <?= htmlspecialchars((string) ($submission['final_pdf_original'] ?? 'Download PDF')) ?>
                </a>
                <?php else: ?>
                <span>—</span>
                <?php endif; ?>
            </div>
            <div>
                <span class="gpip-label">Submitted</span>
                <strong data-gpip-submitted-at>
                    <?php if (!empty($submission['submitted_at'])): ?>
                        <?= htmlspecialchars(date('M j, Y g:i A', strtotime((string) $submission['submitted_at']))) ?>
                        <?php if (!empty($submission['submitted_by_name'])): ?>
                        by <?= htmlspecialchars((string) $submission['submitted_by_name']) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </strong>
            </div>
            <?php if ($supportingFiles !== []): ?>
            <div class="gpip-span-2">
                <span class="gpip-label">Supporting Files</span>
                <div class="gpip-file-list" data-gpip-supporting-files>
                    <?php foreach ($supportingFiles as $idx => $file): ?>
                    <a class="gpip-file-link" href="<?= htmlspecialchars($fileBase) ?>?type=supporting&amp;submission_id=<?= (int) ($submission['id'] ?? 0) ?>&amp;index=<?= (int) $idx ?>" target="_blank" rel="noopener">
                        <?= smsIcon('paperclip') ?> <?= htmlspecialchars((string) ($file['original_name'] ?? 'Supporting file')) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php elseif ($canSubmitAction): ?>
    <div class="gpip-panel">
        <div class="gpip-empty compact">
            <?= smsIcon('upload') ?>
            <p style="margin:0;">No final output submitted yet. Click <strong>Submit Final Output</strong> when your research and publication are ready.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($repository !== null): ?>
    <div class="gpip-panel gpip-repo-panel" id="gpipRepositoryPanel">
        <h3 class="gpip-section-title"><?= smsIcon('archive', ['class' => 'me-1']) ?>Publications &amp; IP Repository Record</h3>
        <p class="gpip-muted">Verified and recorded<?php if (!empty($repository['verified_at'])): ?> on <?= htmlspecialchars(date('M j, Y g:i A', strtotime((string) $repository['verified_at']))) ?><?php endif; ?><?php if (!empty($repository['verified_by_name'])): ?> by <?= htmlspecialchars((string) $repository['verified_by_name']) ?><?php endif; ?>.</p>
        <div class="gpip-detail-grid">
            <div>
                <span class="gpip-label">Repository Reference</span>
                <strong><?= htmlspecialchars((string) ($repository['repository_reference'] ?? '—')) ?></strong>
            </div>
            <div>
                <span class="gpip-label">Recorded Title</span>
                <strong><?= htmlspecialchars((string) ($repository['final_research_title'] ?? '—')) ?></strong>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
