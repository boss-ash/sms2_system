<?php
/**
 * SMS 2 - Role & module accurate process definitions for Reports & Analytics
 */

if (!function_exists('smsReportProcess')) {
    /**
     * Build a crad-module-process compatible board for a report slug.
     */
    function smsReportProcess(string $slug, ?string $roleKey = null): array
    {
        $roleKey = $roleKey ?: getCurrentUserRoleKey();
        $profile = smsRoleReportProfile($roleKey);
        $office = $profile['office'] ?? 'Office';
        $defs = smsReportProcessCatalog();

        // Shared reports adapt to the signed-in role's modules.
        if (in_array($slug, ['performance-trends', 'export-center'], true)) {
            $shared = $defs['shared'][$slug] ?? null;
            if (is_callable($shared)) {
                return $shared($roleKey, $office, $profile);
            }
        }

        $def = $defs[$slug] ?? null;
        if ($def === null) {
            return smsReportProcessFallback($slug, $office, $profile);
        }

        // Admin opening a role-scoped report uses that report's office process.
        return $def;
    }
}

if (!function_exists('smsReportProcessFallback')) {
    function smsReportProcessFallback(string $slug, string $office, array $profile): array
    {
        return [
            'kicker' => $office . ' · Reports Process',
            'description' => $profile['focus'] ?? 'Generate and review role-scoped analytics.',
            'metrics' => array_map(
                static fn(array $m): array => [
                    'label' => $m['label'],
                    'value' => $m['value'],
                    'icon' => 'fa-chart-bar',
                    'tone' => 'blue',
                ],
                array_slice($profile['metrics'] ?? [], 0, 4)
            ),
            'steps' => [
                ['Open Report Scope', 'Confirm the reporting period and office filters for ' . $slug . '.'],
                ['Validate Source Data', 'Check module records feeding this analytics view.'],
                ['Run Analysis', 'Compute metrics, charts, and exception queues.'],
                ['Release Output', 'Export, share, or archive the approved report package.'],
            ],
            'columns' => ['Reference', 'Item', 'Owner', 'Status', 'Updated'],
            'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
            'records' => [
                [
                    'reference' => 'RPT-001',
                    'title' => ucwords(str_replace('-', ' ', $slug)),
                    'owner' => $office,
                    'status' => 'Ready',
                    'status_class' => 'approved',
                    'updated' => 'Jul 20, 2026',
                ],
            ],
            'actions' => [
                ['label' => 'Refresh Dataset', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                ['label' => 'Validate Sources', 'process' => 'validate', 'icon' => 'fa-check-double', 'class' => 'ghost'],
                ['label' => 'Approve Report', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                ['label' => 'Export Package', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
            ],
            'form' => [
                ['label' => 'Reporting Period', 'type' => 'select', 'name' => 'period', 'options' => [
                    '1st Semester AY 2025-2026',
                    '2nd Semester AY 2025-2026',
                    'Summer 2026',
                    'Fiscal Year 2026',
                ]],
                ['label' => 'Office Filter', 'type' => 'text', 'name' => 'office', 'value' => $office],
                ['label' => 'Remarks', 'type' => 'textarea', 'name' => 'remarks', 'placeholder' => 'Notes for this report run...'],
            ],
            'notice' => 'Only users with matching role access can run this report process.',
        ];
    }
}

if (!function_exists('smsReportRoleModuleMap')) {
    function smsReportRoleModuleMap(): array
    {
        return [
            'admin' => [
                'modules' => 'All BCP modules',
                'source' => 'Institutional data warehouse',
                'prefix' => 'ADM',
                'owner' => 'System Administration',
            ],
            'registrar' => [
                'modules' => 'Enrollment, Registrar, Curriculum, Class Schedule',
                'source' => 'Enrollment + Registrar + Curriculum + Scheduling records',
                'prefix' => 'REG',
                'owner' => 'Registrar Office',
            ],
            'crad_officer' => [
                'modules' => 'CRAD (Proposals, Advisers, Grants, Publication, Repository)',
                'source' => 'CRAD research workflow records',
                'prefix' => 'CRD',
                'owner' => 'CRAD Office',
            ],
            'finance' => [
                'modules' => 'Payment Management',
                'source' => 'Billing, collections, and receivables ledgers',
                'prefix' => 'FIN',
                'owner' => 'Finance Office',
            ],
            'hr' => [
                'modules' => 'Faculty Management',
                'source' => 'Faculty load, leave, and evaluation files',
                'prefix' => 'HR',
                'owner' => 'HR / Faculty Management',
            ],
            'it_office' => [
                'modules' => 'LMS',
                'source' => 'Class portals, submissions, and completion logs',
                'prefix' => 'IT',
                'owner' => 'IT Office / LMS',
            ],
            'osa' => [
                'modules' => 'Co-Curricular / OSA',
                'source' => 'Clubs, events, volunteer, and budget records',
                'prefix' => 'OSA',
                'owner' => 'Office of Student Affairs',
            ],
            'qa' => [
                'modules' => 'Accreditation',
                'source' => 'Compliance evidence and audit findings',
                'prefix' => 'QA',
                'owner' => 'Quality Assurance',
            ],
        ];
    }
}

if (!function_exists('smsReportProcessCatalog')) {
    function smsReportProcessCatalog(): array
    {
        $roleMap = smsReportRoleModuleMap();

        $sharedBuilder = static function (string $kind) use ($roleMap): callable {
            return static function (string $roleKey, string $office, array $profile) use ($kind, $roleMap): array {
                $map = $roleMap[$roleKey] ?? $roleMap['admin'];
                $metrics = [];
                $icons = ['fa-chart-pie', 'fa-chart-line', 'fa-database', 'fa-clock'];
                $tones = ['blue', 'green', 'amber', 'purple'];
                foreach (array_slice($profile['metrics'] ?? [], 0, 4) as $i => $m) {
                    $metrics[] = [
                        'label' => $m['label'],
                        'value' => $m['value'],
                        'icon' => $icons[$i] ?? 'fa-chart-bar',
                        'tone' => $tones[$i] ?? 'blue',
                    ];
                }

                if ($kind === 'performance-trends') {
                    return [
                        'kicker' => $office . ' · Performance Trends Process',
                        'description' => 'Track period-over-period movement for metrics owned by ' . $map['modules'] . '.',
                        'metrics' => $metrics,
                        'steps' => [
                            ['Choose Trend Window', 'Set weekly, monthly, or term comparison for your office metrics.'],
                            ['Align Baseline Data', 'Match current figures against prior-period ' . $map['source'] . '.'],
                            ['Flag Variance', 'Highlight spikes, drops, and items needing office action.'],
                            ['Issue Trend Report', 'Release chart pack and narrative for supervisors.'],
                        ],
                        'columns' => ['Reference', 'Trend Series', 'Owner', 'Status', 'Updated'],
                        'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                        'records' => [
                            ['reference' => $map['prefix'] . '-TRD-01', 'title' => ($profile['charts'][0] ?? 'Primary trend'), 'owner' => $map['owner'], 'status' => 'Rising', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                            ['reference' => $map['prefix'] . '-TRD-02', 'title' => ($profile['charts'][1] ?? 'Secondary trend'), 'owner' => $map['owner'], 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                            ['reference' => $map['prefix'] . '-TRD-03', 'title' => ($profile['charts'][2] ?? 'Operational trend'), 'owner' => $map['owner'], 'status' => 'Stable', 'status_class' => 'active', 'updated' => 'Jul 18, 2026'],
                            ['reference' => $map['prefix'] . '-TRD-04', 'title' => ($profile['charts'][3] ?? 'Support trend'), 'owner' => $map['owner'], 'status' => 'Draft', 'status_class' => 'pending', 'updated' => 'Jul 17, 2026'],
                        ],
                        'actions' => [
                            ['label' => 'Rebuild Trends', 'process' => 'refresh', 'icon' => 'fa-chart-line', 'class' => 'primary'],
                            ['label' => 'Mark Variance', 'process' => 'validate', 'icon' => 'fa-exclamation-triangle', 'class' => 'ghost'],
                            ['label' => 'Approve Narrative', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                            ['label' => 'Export Charts', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                        ],
                        'form' => [
                            ['label' => 'Trend Window', 'type' => 'select', 'name' => 'window', 'options' => ['Last 7 days', 'Last 30 days', 'Current Term', 'Year to Date']],
                            ['label' => 'Compare Against', 'type' => 'select', 'name' => 'baseline', 'options' => ['Previous period', 'Same period last year', 'Target KPI']],
                            ['label' => 'Variance Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Explain notable movement...'],
                        ],
                        'notice' => 'Trend series are limited to datasets from modules assigned to your role access.',
                    ];
                }

                // export-center
                return [
                    'kicker' => $office . ' · Export Center Process',
                    'description' => 'Package approved analytics from ' . $map['modules'] . ' into downloadable office reports.',
                    'metrics' => $metrics,
                    'steps' => [
                        ['Select Export Templates', 'Choose CSV, XLSX, or PDF packs allowed for your office.'],
                        ['Apply Role Filters', 'Restrict rows to records visible under ' . $map['modules'] . '.'],
                        ['Generate Package', 'Compile files from ' . $map['source'] . ' and stamp the run ID.'],
                        ['Release Download', 'Queue the package for download and keep an audit trail.'],
                    ],
                    'columns' => ['Reference', 'Export Package', 'Owner', 'Status', 'Updated'],
                    'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                    'records' => [
                        ['reference' => $map['prefix'] . '-EXP-01', 'title' => 'Primary office CSV pack', 'owner' => $map['owner'], 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                        ['reference' => $map['prefix'] . '-EXP-02', 'title' => 'Workbook (XLSX) pack', 'owner' => $map['owner'], 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 20, 2026'],
                        ['reference' => $map['prefix'] . '-EXP-03', 'title' => 'Signed PDF summary', 'owner' => $map['owner'], 'status' => 'Review', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                        ['reference' => $map['prefix'] . '-EXP-04', 'title' => 'Audit export log', 'owner' => $map['owner'], 'status' => 'Archived', 'status_class' => 'archived', 'updated' => 'Jul 15, 2026'],
                    ],
                    'actions' => [
                        ['label' => 'Queue Export', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'primary'],
                        ['label' => 'Validate Filters', 'process' => 'validate', 'icon' => 'fa-filter', 'class' => 'ghost'],
                        ['label' => 'Approve Release', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                        ['label' => 'Refresh Catalog', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'ghost'],
                    ],
                    'form' => [
                        ['label' => 'Package Type', 'type' => 'select', 'name' => 'package', 'options' => ['CSV Summary', 'XLSX Workbook', 'PDF Briefing', 'Full Audit Bundle']],
                        ['label' => 'Destination', 'type' => 'select', 'name' => 'destination', 'options' => ['Download now', 'Email to office', 'Archive only']],
                        ['label' => 'Export Remarks', 'type' => 'textarea', 'name' => 'remarks', 'placeholder' => 'Purpose of this export run...'],
                    ],
                    'notice' => 'Export Center never includes datasets outside your role module access.',
                ];
            };
        };

        return [
            'shared' => [
                'performance-trends' => $sharedBuilder('performance-trends'),
                'export-center' => $sharedBuilder('export-center'),
            ],

            // ── Registrar ──────────────────────────────────────────────
            'enrollment-analytics' => [
                'kicker' => 'Registrar · Enrollment Module Process',
                'description' => 'Analyze enrollment funnel from pre-registration through validation, sectioning, and confirmation (Enrollment Management module).',
                'metrics' => [
                    ['label' => 'Pre-registered', 'value' => '1,486', 'icon' => 'fa-user-plus', 'tone' => 'blue'],
                    ['label' => 'Validated', 'value' => '1,204', 'icon' => 'fa-user-check', 'tone' => 'green'],
                    ['label' => 'Waiting List', 'value' => '47', 'icon' => 'fa-list', 'tone' => 'amber'],
                    ['label' => 'Cross-enroll Flags', 'value' => '9', 'icon' => 'fa-exchange-alt', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Pull Enrollment Feeds', 'Collect Online Pre-registration, Document Upload, and Validation queues.'],
                    ['Segment by Program / Level', 'Break down counts via Grade Level Assignment and Auto Section Assignment.'],
                    ['Review Bottlenecks', 'Inspect Waiting List Queue and Cross-enrollment Checker exceptions.'],
                    ['Release Enrollment Analytics', 'Publish dashboard figures and export for Registrar briefing.'],
                ],
                'columns' => ['Reference', 'Enrollment Stage', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'ENR-AN-014', 'title' => 'Pre-registration conversion', 'owner' => 'Admissions Team', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'ENR-AN-013', 'title' => 'Validation backlog', 'owner' => 'Enrollment Desk', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'ENR-AN-012', 'title' => 'Waiting-list pressure', 'owner' => 'Sectioning Unit', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'ENR-AN-011', 'title' => 'Parent notification coverage', 'owner' => 'Enrollment Desk', 'status' => 'Synced', 'status_class' => 'approved', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Enrollment Data', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Funnel', 'process' => 'validate', 'icon' => 'fa-check-double', 'class' => 'ghost'],
                    ['label' => 'Approve Snapshot', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Analytics', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Term', 'type' => 'select', 'name' => 'term', 'options' => ['1st Sem AY 2025-2026', '2nd Sem AY 2025-2026', 'Summer 2026']],
                    ['label' => 'Program Filter', 'type' => 'select', 'name' => 'program', 'options' => ['All Programs', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Analysis Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Bottlenecks, capacity notes...'],
                ],
                'notice' => 'Enrollment Analytics is limited to Registrar role access over the Enrollment Management module.',
            ],

            'student-records-report' => [
                'kicker' => 'Registrar · Student Records Process',
                'description' => 'Compile student information, academic history, status, and digital file completeness from the Registrar module.',
                'metrics' => [
                    ['label' => 'Active Records', 'value' => '2,893', 'icon' => 'fa-folder-open', 'tone' => 'blue'],
                    ['label' => 'Incomplete Files', 'value' => '61', 'icon' => 'fa-file-excel', 'tone' => 'amber'],
                    ['label' => 'Status Updates', 'value' => '34', 'icon' => 'fa-user-edit', 'tone' => 'purple'],
                    ['label' => 'Transcript Jobs', 'value' => '18', 'icon' => 'fa-scroll', 'tone' => 'green'],
                ],
                'steps' => [
                    ['Select Record Cohort', 'Filter SIS, Persona File, and Academic History by program or status.'],
                    ['Audit Completeness', 'Check Guardian Contact, Health Log, Digital File Storage, and ID readiness.'],
                    ['Reconcile Status Tracker', 'Align Student Status Tracker with transcript and ID generation queues.'],
                    ['Release Records Report', 'Approve the cohort report and export for Registrar filing.'],
                ],
                'columns' => ['Reference', 'Record Set', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'REG-SR-028', 'title' => 'SIS completeness sweep', 'owner' => 'Records Unit', 'status' => 'In Review', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'REG-SR-027', 'title' => 'Academic history export', 'owner' => 'Records Unit', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'REG-SR-026', 'title' => 'Status tracker exceptions', 'owner' => 'Registrar Staff', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'REG-SR-025', 'title' => 'Digital file gaps', 'owner' => 'File Custodian', 'status' => 'Pending', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'New Records Sweep', 'process' => 'new', 'icon' => 'fa-plus', 'class' => 'primary'],
                    ['label' => 'Verify Files', 'process' => 'validate', 'icon' => 'fa-folder-open', 'class' => 'ghost'],
                    ['label' => 'Approve Report', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Records', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Cohort', 'type' => 'select', 'name' => 'cohort', 'options' => ['All Active', 'Incoming Freshmen', 'Transferees', 'Graduating']],
                    ['label' => 'Focus Area', 'type' => 'select', 'name' => 'focus', 'options' => ['SIS Profile', 'Academic History', 'Digital Files', 'Status Tracker']],
                    ['label' => 'Findings', 'type' => 'textarea', 'name' => 'findings', 'placeholder' => 'Missing documents, status mismatches...'],
                ],
                'notice' => 'Student Records Report draws only from Registrar module pages visible to the Registrar role.',
            ],

            'document-release-analytics' => [
                'kicker' => 'Registrar · Document Release Process',
                'description' => 'Track document requests, turnaround, and release volume from Document Requests and Transcript Management.',
                'metrics' => [
                    ['label' => 'Open Requests', 'value' => '28', 'icon' => 'fa-inbox', 'tone' => 'amber'],
                    ['label' => 'Released Today', 'value' => '16', 'icon' => 'fa-file-signature', 'tone' => 'green'],
                    ['label' => 'Avg Turnaround', 'value' => '2.1d', 'icon' => 'fa-clock', 'tone' => 'blue'],
                    ['label' => 'Overdue', 'value' => '5', 'icon' => 'fa-exclamation', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Load Request Queue', 'Import Document Requests and Transcript Management pending items.'],
                    ['Measure SLA', 'Compute age, owner, and overdue flags per document type.'],
                    ['Prioritize Releases', 'Flag rush TOR, certificates, and ID-related releases.'],
                    ['Publish Release Analytics', 'Export turnaround dashboard for Registrar operations.'],
                ],
                'columns' => ['Reference', 'Document Type', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'DOC-AN-019', 'title' => 'Transcript of Records', 'owner' => 'Records Unit', 'status' => 'Processing', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'DOC-AN-018', 'title' => 'Certificate of Enrollment', 'owner' => 'Front Desk', 'status' => 'Released', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'DOC-AN-017', 'title' => 'Good Moral Certificate', 'owner' => 'Front Desk', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'DOC-AN-016', 'title' => 'Certified True Copy', 'owner' => 'Records Unit', 'status' => 'Overdue', 'status_class' => 'denied', 'updated' => 'Jul 15, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Queue', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Check SLA', 'process' => 'validate', 'icon' => 'fa-stopwatch', 'class' => 'ghost'],
                    ['label' => 'Mark Released Batch', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Turnaround', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Document Type', 'type' => 'select', 'name' => 'doctype', 'options' => ['All Types', 'TOR', 'Certificate', 'CTC', 'Student ID Related']],
                    ['label' => 'SLA Window', 'type' => 'select', 'name' => 'sla', 'options' => ['Same day', '1-2 days', '3-5 days', 'Overdue only']],
                    ['label' => 'Release Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Batch notes or exceptions...'],
                ],
                'notice' => 'Document Release Analytics is scoped to Registrar document workflows only.',
            ],

            'curriculum-analytics' => [
                'kicker' => 'Registrar · Curriculum Module Process',
                'description' => 'Analyze curriculum coverage, prerequisites, electives, and CHED/DepEd validation from Curriculum & Subject Management.',
                'metrics' => [
                    ['label' => 'Active Curricula', 'value' => '18', 'icon' => 'fa-book', 'tone' => 'blue'],
                    ['label' => 'Subjects Mapped', 'value' => '426', 'icon' => 'fa-sitemap', 'tone' => 'green'],
                    ['label' => 'Prereq Gaps', 'value' => '11', 'icon' => 'fa-exclamation-triangle', 'tone' => 'amber'],
                    ['label' => 'Pending Validate', 'value' => '4', 'icon' => 'fa-clipboard-check', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Pull Curriculum Versions', 'Load Curriculum Builder and Subject Mapping for the active school year.'],
                    ['Check Prerequisite Rules', 'Review Pre-requisite Configuration and Subject Equivalency Tool conflicts.'],
                    ['Validate Offerings', 'Run CHED/DepEd Validator and Electives Manager coverage checks.'],
                    ['Release Curriculum Analytics', 'Export curriculum health report for Registrar / Academic Affairs.'],
                ],
                'columns' => ['Reference', 'Curriculum Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'CUR-AN-022', 'title' => 'BSIT 2024 curriculum map', 'owner' => 'Curriculum Desk', 'status' => 'Validated', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'CUR-AN-021', 'title' => 'Prereq gap — Math sequence', 'owner' => 'Academic Affairs', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'CUR-AN-020', 'title' => 'Electives coverage CBA', 'owner' => 'Curriculum Desk', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'CUR-AN-019', 'title' => 'CHED validator queue', 'owner' => 'QA Liaison', 'status' => 'Pending', 'status_class' => 'pending', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Curriculum', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Rules', 'process' => 'validate', 'icon' => 'fa-check-double', 'class' => 'ghost'],
                    ['label' => 'Approve Snapshot', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Curriculum', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Program', 'type' => 'select', 'name' => 'program', 'options' => ['All Programs', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Curriculum Version', 'type' => 'select', 'name' => 'version', 'options' => ['AY 2025-2026', 'AY 2024-2025', 'Draft']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Prereq gaps, equivalency issues...'],
                ],
                'notice' => 'Curriculum Analytics is limited to roles with Curriculum & Subject Management access.',
            ],

            'class-schedule-analytics' => [
                'kicker' => 'Registrar · Class Schedule Process',
                'description' => 'Track section loads, teacher conflicts, room usage, and exam timetable readiness from Class Schedule.',
                'metrics' => [
                    ['label' => 'Active Sections', 'value' => '186', 'icon' => 'fa-calendar-alt', 'tone' => 'blue'],
                    ['label' => 'Conflicts', 'value' => '6', 'icon' => 'fa-exclamation-circle', 'tone' => 'amber'],
                    ['label' => 'Rooms Used', 'value' => '54', 'icon' => 'fa-door-open', 'tone' => 'green'],
                    ['label' => 'Exam Blocks', 'value' => '28', 'icon' => 'fa-clock', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Load Schedule Grid', 'Pull Section Assignment Tool and Teacher Schedule Mapping.'],
                    ['Run Conflict Checker', 'Flag teacher, room, and section overlaps.'],
                    ['Review Room & Exam Blocks', 'Check Room Availability and Exam Timetable Generator coverage.'],
                    ['Release Schedule Analytics', 'Export utilization and conflict board for Registrar.'],
                ],
                'columns' => ['Reference', 'Schedule Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'SCH-AN-031', 'title' => 'CCS morning conflict set', 'owner' => 'Scheduling Desk', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'SCH-AN-030', 'title' => 'Room utilization — Lab 3', 'owner' => 'Facilities Liaison', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'SCH-AN-029', 'title' => 'Midterm exam blocks', 'owner' => 'Scheduling Desk', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'SCH-AN-028', 'title' => 'Substitute coverage map', 'owner' => 'Dept Chair', 'status' => 'Synced', 'status_class' => 'approved', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Schedules', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Check Conflicts', 'process' => 'validate', 'icon' => 'fa-bomb', 'class' => 'ghost'],
                    ['label' => 'Approve Grid', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Schedule', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Term', 'type' => 'select', 'name' => 'term', 'options' => ['1st Sem AY 2025-2026', '2nd Sem AY 2025-2026', 'Summer 2026']],
                    ['label' => 'Focus', 'type' => 'select', 'name' => 'focus', 'options' => ['All', 'Conflicts only', 'Room usage', 'Exam timetable']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Conflict notes, room swaps...'],
                ],
                'notice' => 'Class Schedule Analytics is limited to roles with Class Schedule module access.',
            ],

            // ── CRAD ───────────────────────────────────────────────────
            'research-proposal-analytics' => [
                'kicker' => 'CRAD · Proposal Submission & Tracking Process',
                'description' => 'Monitor title proposal intake, review stages, and college distribution from Proposal Submission & Tracking.',
                'metrics' => [
                    ['label' => 'Submitted', 'value' => '24', 'icon' => 'fa-clipboard-list', 'tone' => 'blue'],
                    ['label' => 'Under Review', 'value' => '7', 'icon' => 'fa-search', 'tone' => 'amber'],
                    ['label' => 'Approved', 'value' => '11', 'icon' => 'fa-check-circle', 'tone' => 'green'],
                    ['label' => 'Returned', 'value' => '3', 'icon' => 'fa-undo', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Sync Proposal Hub', 'Load submissions from Proposal Submission & Tracking pipeline.'],
                    ['Classify by College / Status', 'Group titles by college, agenda, and review stage.'],
                    ['Spot Review Delays', 'Flag proposals past expected evaluator turnaround.'],
                    ['Release Proposal Analytics', 'Export pipeline charts for CRAD officer briefing.'],
                ],
                'columns' => ['Reference', 'Proposal Stage', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'SUB-2026-024', 'title' => 'IoT Flood Monitoring — CCS', 'owner' => 'Evaluator Panel', 'status' => 'Under Review', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'SUB-2026-023', 'title' => 'Micro-Enterprise Marketing', 'owner' => 'CRAD Officer', 'status' => 'Approved', 'status_class' => 'approved', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'SUB-2026-022', 'title' => 'Mental Health Literacy Study', 'owner' => 'Evaluator Panel', 'status' => 'Returned', 'status_class' => 'denied', 'updated' => 'Jul 17, 2026'],
                    ['reference' => 'SUB-2026-021', 'title' => 'Solid Waste Awareness', 'owner' => 'CRAD Officer', 'status' => 'Submitted', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Pipeline', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Stages', 'process' => 'validate', 'icon' => 'fa-tasks', 'class' => 'ghost'],
                    ['label' => 'Approve Snapshot', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Pipeline', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'College Filter', 'type' => 'select', 'name' => 'college', 'options' => ['All Colleges', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Pipeline Stage', 'type' => 'select', 'name' => 'stage', 'options' => ['All Stages', 'Submitted', 'Under Review', 'Approved', 'Returned']],
                    ['label' => 'Review Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Delay reasons, evaluator load...'],
                ],
                'notice' => 'Research Proposal Analytics is available only to CRAD Officer (and Admin) with CRAD module access.',
            ],

            'adviser-grants-report' => [
                'kicker' => 'CRAD · Adviser & Grants Process',
                'description' => 'Combine Adviser Assignment System and Research Grants & Funding Assistance into one load and funding report.',
                'metrics' => [
                    ['label' => 'Pending Advisers', 'value' => '5', 'icon' => 'fa-user-clock', 'tone' => 'amber'],
                    ['label' => 'Assigned Groups', 'value' => '18', 'icon' => 'fa-user-tie', 'tone' => 'green'],
                    ['label' => 'Grant Applications', 'value' => '9', 'icon' => 'fa-hand-holding-usd', 'tone' => 'blue'],
                    ['label' => 'Funded', 'value' => '4', 'icon' => 'fa-coins', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Pull Assignment & Grant Queues', 'Sync Adviser Assignment and Grants & Funding Assistance records.'],
                    ['Match Load vs Expertise', 'Analyze adviser capacity against pending research groups.'],
                    ['Score Funding Requests', 'Rank grant applications by eligibility and evaluation status.'],
                    ['Release Combined Report', 'Export adviser load and grants summary for CRAD management.'],
                ],
                'columns' => ['Reference', 'Track Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'ADV-2026-014', 'title' => 'Adviser match — CCS Group A', 'owner' => 'CRAD Officer', 'status' => 'For Assignment', 'status_class' => 'pending', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'GRN-2026-009', 'title' => 'Seed grant — IoT project', 'owner' => 'Grants Panel', 'status' => 'Evaluation', 'status_class' => 'evaluation', 'updated' => 'Jul 17, 2026'],
                    ['reference' => 'ADV-2026-013', 'title' => 'Adviser confirmed — CBA', 'owner' => 'CRAD Officer', 'status' => 'Assigned', 'status_class' => 'assigned', 'updated' => 'Jul 16, 2026'],
                    ['reference' => 'GRN-2026-008', 'title' => 'Publication support grant', 'owner' => 'Grants Panel', 'status' => 'Funded', 'status_class' => 'approved', 'updated' => 'Jul 12, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Dual Queues', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Check Load Limits', 'process' => 'validate', 'icon' => 'fa-balance-scale', 'class' => 'ghost'],
                    ['label' => 'Approve Report', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Combined', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Report Focus', 'type' => 'select', 'name' => 'focus', 'options' => ['Both tracks', 'Adviser Assignment only', 'Grants only']],
                    ['label' => 'College', 'type' => 'select', 'name' => 'college', 'options' => ['All Colleges', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Remarks', 'type' => 'textarea', 'name' => 'remarks', 'placeholder' => 'Load issues, funding priorities...'],
                ],
                'notice' => 'This report merges CRAD Adviser Assignment and Grants modules for CRAD Officer access only.',
            ],

            'publication-repository-report' => [
                'kicker' => 'CRAD · Publication & Repository Process',
                'description' => 'Track Documentation & Publication Management and Research Repository catalog growth and readiness.',
                'metrics' => [
                    ['label' => 'Manuscripts', 'value' => '14', 'icon' => 'fa-file-alt', 'tone' => 'blue'],
                    ['label' => 'For Publication', 'value' => '6', 'icon' => 'fa-book', 'tone' => 'amber'],
                    ['label' => 'Repository Items', 'value' => '128', 'icon' => 'fa-archive', 'tone' => 'green'],
                    ['label' => 'Collaborations', 'value' => '8', 'icon' => 'fa-handshake', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Collect Publication Queue', 'Load manuscripts from Documentation & Publication Management.'],
                    ['Audit Repository Catalog', 'Verify Research Repository metadata, embargo, and access tags.'],
                    ['Link Collaboration Entries', 'Include Research Collaboration Portal partners tied to outputs.'],
                    ['Release Catalog Report', 'Export publication and repository analytics for CRAD.'],
                ],
                'columns' => ['Reference', 'Catalog Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'PUB-2026-006', 'title' => 'Journal manuscript — CCS', 'owner' => 'Publication Desk', 'status' => 'Editing', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'REP-2026-128', 'title' => 'Repository deposit — Capstone', 'owner' => 'Repository Admin', 'status' => 'Published', 'status_class' => 'published', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'COL-2026-008', 'title' => 'External partner MoA link', 'owner' => 'Collaboration Lead', 'status' => 'Active', 'status_class' => 'active', 'updated' => 'Jul 15, 2026'],
                    ['reference' => 'PUB-2026-005', 'title' => 'Conference paper pack', 'owner' => 'Publication Desk', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 14, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Catalog', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Metadata', 'process' => 'validate', 'icon' => 'fa-tags', 'class' => 'ghost'],
                    ['label' => 'Approve Release', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Catalog', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Catalog Scope', 'type' => 'select', 'name' => 'scope', 'options' => ['Publication + Repository', 'Publication only', 'Repository only', 'Collaborations']],
                    ['label' => 'Access Level', 'type' => 'select', 'name' => 'access', 'options' => ['All', 'Open access', 'Embargoed', 'Internal only']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Missing metadata, embargo dates...'],
                ],
                'notice' => 'Publication & Repository Report is restricted to CRAD Officer workflows.',
            ],

            // ── Finance ────────────────────────────────────────────────
            'collections-analytics' => [
                'kicker' => 'Finance · Collections Process',
                'description' => 'Analyze Payment Collection Portal, online payments, and Collection Reporting & Analytics performance.',
                'metrics' => [
                    ['label' => 'Collected (MTD)', 'value' => '₱1.2M', 'icon' => 'fa-peso-sign', 'tone' => 'green'],
                    ['label' => 'Online Share', 'value' => '41%', 'icon' => 'fa-globe', 'tone' => 'blue'],
                    ['label' => 'Pending Posting', 'value' => '37', 'icon' => 'fa-hourglass-half', 'tone' => 'amber'],
                    ['label' => 'Discrepancies', 'value' => '4', 'icon' => 'fa-exclamation-circle', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Pull Collection Ledgers', 'Load Payment Collection Portal and Online Payment Integration posts.'],
                    ['Reconcile Fee Types', 'Map Fee Setup Configuration against collected amounts.'],
                    ['Flag Unposted Items', 'Review Payment History & Ledger System gaps.'],
                    ['Release Collections Pack', 'Export Collection Reporting & Analytics for Finance.'],
                ],
                'columns' => ['Reference', 'Collection Stream', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'COL-AN-031', 'title' => 'Cashier window collections', 'owner' => 'Cashier Lead', 'status' => 'Posted', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'COL-AN-030', 'title' => 'Online payment batch', 'owner' => 'E-Payment Desk', 'status' => 'Reconciling', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'COL-AN-029', 'title' => 'Scholarship offsets', 'owner' => 'Finance Analyst', 'status' => 'Posted', 'status_class' => 'approved', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'COL-AN-028', 'title' => 'Unmatched OR entries', 'owner' => 'Cashier Lead', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Collections', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Reconcile Batch', 'process' => 'validate', 'icon' => 'fa-balance-scale', 'class' => 'ghost'],
                    ['label' => 'Post Snapshot', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Collections', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Channel', 'type' => 'select', 'name' => 'channel', 'options' => ['All Channels', 'Cashier', 'Online', 'Bank Deposit']],
                    ['label' => 'Fee Category', 'type' => 'select', 'name' => 'fee', 'options' => ['All Fees', 'Tuition', 'Misc', 'Other']],
                    ['label' => 'Recon Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Batch references, variance notes...'],
                ],
                'notice' => 'Collections Analytics is limited to Finance role access on the Payment Management module.',
            ],

            'receivables-report' => [
                'kicker' => 'Finance · Receivables Process',
                'description' => 'Age and prioritize student balances from Accounts Receivable Management, billing, and penalty rules.',
                'metrics' => [
                    ['label' => 'Open AR', 'value' => '₱486K', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'amber'],
                    ['label' => '0-30 Days', 'value' => '₱210K', 'icon' => 'fa-calendar-day', 'tone' => 'green'],
                    ['label' => '31-60 Days', 'value' => '₱152K', 'icon' => 'fa-calendar-week', 'tone' => 'blue'],
                    ['label' => '61+ Days', 'value' => '₱124K', 'icon' => 'fa-calendar-times', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Load AR Balances', 'Pull Accounts Receivable Management and Student Billing & Invoicing.'],
                    ['Apply Aging Buckets', 'Classify by due dates from Penalty & Due Date Management.'],
                    ['Apply Discount / Scholarship', 'Net Discount & Scholarship Application effects on balances.'],
                    ['Release Aging Report', 'Export receivables aging for Finance follow-up.'],
                ],
                'columns' => ['Reference', 'Aging Bucket', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'AR-2026-088', 'title' => '0-30 day balances', 'owner' => 'AR Clerk', 'status' => 'Current', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'AR-2026-087', 'title' => '31-60 day follow-up', 'owner' => 'AR Clerk', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'AR-2026-086', 'title' => '61+ day escalations', 'owner' => 'Finance Officer', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'AR-2026-085', 'title' => 'Penalty assessment batch', 'owner' => 'Billing Desk', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Rebuild Aging', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Buckets', 'process' => 'validate', 'icon' => 'fa-layer-group', 'class' => 'ghost'],
                    ['label' => 'Approve Aging', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export AR', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Aging Cutoff', 'type' => 'select', 'name' => 'cutoff', 'options' => ['Today', 'End of month', 'Custom']],
                    ['label' => 'Program Filter', 'type' => 'select', 'name' => 'program', 'options' => ['All Programs', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Follow-up Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Escalation list, payment plans...'],
                ],
                'notice' => 'Receivables Report is scoped to Finance Payment Management data only.',
            ],

            // ── HR ─────────────────────────────────────────────────────
            'faculty-load-report' => [
                'kicker' => 'HR · Faculty Load Process',
                'description' => 'Measure teaching assignments using Subject Load Tracker, Schedule Assignment, and Faculty Directory.',
                'metrics' => [
                    ['label' => 'Faculty Count', 'value' => '102', 'icon' => 'fa-chalkboard-teacher', 'tone' => 'blue'],
                    ['label' => 'Overload Cases', 'value' => '7', 'icon' => 'fa-exclamation-triangle', 'tone' => 'amber'],
                    ['label' => 'Underload', 'value' => '4', 'icon' => 'fa-balance-scale', 'tone' => 'purple'],
                    ['label' => 'Avg Units', 'value' => '18.4', 'icon' => 'fa-calculator', 'tone' => 'green'],
                ],
                'steps' => [
                    ['Collect Load Sheets', 'Pull Subject Load Tracker and Schedule Assignment for the term.'],
                    ['Compare Against Policy', 'Flag overload / underload versus Faculty Profile contracts.'],
                    ['Cross-check Attendance', 'Spot inactive loads via Attendance Monitoring.'],
                    ['Release Load Report', 'Export department load matrix for HR / Academic Affairs.'],
                ],
                'columns' => ['Reference', 'Load Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'FAC-LD-044', 'title' => 'CCS overload review', 'owner' => 'HR Analyst', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'FAC-LD-043', 'title' => 'CBA balanced loads', 'owner' => 'Dept Chair', 'status' => 'OK', 'status_class' => 'approved', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'FAC-LD-042', 'title' => 'Part-time underload', 'owner' => 'HR Analyst', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'FAC-LD-041', 'title' => 'Schedule conflict loads', 'owner' => 'Scheduling Liaison', 'status' => 'Pending', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Loads', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Policy', 'process' => 'validate', 'icon' => 'fa-clipboard-check', 'class' => 'ghost'],
                    ['label' => 'Approve Matrix', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Load Report', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Department', 'type' => 'select', 'name' => 'dept', 'options' => ['All Departments', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Employment Type', 'type' => 'select', 'name' => 'etype', 'options' => ['All', 'Full-time', 'Part-time']],
                    ['label' => 'Load Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Overload justifications...'],
                ],
                'notice' => 'Faculty Load Report uses Faculty Management module data available to HR role.',
            ],

            'leave-evaluation-analytics' => [
                'kicker' => 'HR · Leave & Evaluation Process',
                'description' => 'Combine Leave Application & Approval with Evaluation Summary for HR people-analytics.',
                'metrics' => [
                    ['label' => 'Pending Leave', 'value' => '8', 'icon' => 'fa-calendar-check', 'tone' => 'amber'],
                    ['label' => 'On Leave Today', 'value' => '5', 'icon' => 'fa-user-clock', 'tone' => 'blue'],
                    ['label' => 'Avg Evaluation', 'value' => '4.2', 'icon' => 'fa-star', 'tone' => 'green'],
                    ['label' => 'Clearance Open', 'value' => '3', 'icon' => 'fa-stamp', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Import Leave Queue', 'Load Leave Application & Approval pending and approved items.'],
                    ['Attach Evaluation Scores', 'Join Evaluation Summary and Teaching History metrics.'],
                    ['Check Clearance Impact', 'Note Clearance System items affecting leave or ratings.'],
                    ['Release HR Analytics', 'Export leave vs evaluation dashboard for HR.'],
                ],
                'columns' => ['Reference', 'HR Analytics Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'HR-LV-022', 'title' => 'Sick leave spike — Week 29', 'owner' => 'HR Officer', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'HR-EV-015', 'title' => 'Eval band distribution', 'owner' => 'HR Analyst', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'HR-CL-007', 'title' => 'Clearance blockers', 'owner' => 'Clearance Desk', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'HR-LV-021', 'title' => 'Approved leave calendar', 'owner' => 'HR Officer', 'status' => 'Synced', 'status_class' => 'approved', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh HR Feeds', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Joins', 'process' => 'validate', 'icon' => 'fa-link', 'class' => 'ghost'],
                    ['label' => 'Approve Analytics', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Pack', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Focus', 'type' => 'select', 'name' => 'focus', 'options' => ['Leave + Evaluation', 'Leave only', 'Evaluation only', 'Clearance']],
                    ['label' => 'Period', 'type' => 'select', 'name' => 'period', 'options' => ['Current month', 'Current term', 'AY 2025-2026']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'People-analytics remarks...'],
                ],
                'notice' => 'Leave & Evaluation Analytics is restricted to HR Faculty Management access.',
            ],

            // ── IT / LMS ───────────────────────────────────────────────
            'lms-engagement-report' => [
                'kicker' => 'IT Office · LMS Engagement Process',
                'description' => 'Measure Class Portal activity, lesson access, and virtual classroom usage from the LMS module.',
                'metrics' => [
                    ['label' => 'Active Users', 'value' => '2,104', 'icon' => 'fa-users', 'tone' => 'blue'],
                    ['label' => 'Active Classes', 'value' => '148', 'icon' => 'fa-chalkboard', 'tone' => 'green'],
                    ['label' => 'Materials Views', 'value' => '9.4K', 'icon' => 'fa-book-open', 'tone' => 'purple'],
                    ['label' => 'Low-Engagement', 'value' => '22', 'icon' => 'fa-battery-quarter', 'tone' => 'amber'],
                ],
                'steps' => [
                    ['Capture Portal Activity', 'Pull Class Portal logins and Lesson Material Upload views.'],
                    ['Score Engagement Bands', 'Classify classes by participation and Feedback/Comments volume.'],
                    ['Spot Inactive Rooms', 'Flag virtual classrooms with low attendance signals.'],
                    ['Release Engagement Report', 'Export LMS engagement pack for IT Office.'],
                ],
                'columns' => ['Reference', 'Engagement Stream', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'LMS-EN-052', 'title' => 'Daily login trend', 'owner' => 'LMS Admin', 'status' => 'Synced', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'LMS-EN-051', 'title' => 'Low-engagement classes', 'owner' => 'IT Officer', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'LMS-EN-050', 'title' => 'Material view spike', 'owner' => 'LMS Admin', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'LMS-EN-049', 'title' => 'Feedback volume map', 'owner' => 'IT Officer', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh LMS Logs', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Bands', 'process' => 'validate', 'icon' => 'fa-chart-bar', 'class' => 'ghost'],
                    ['label' => 'Approve Report', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Engagement', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Course Filter', 'type' => 'select', 'name' => 'course', 'options' => ['All Courses', 'General Education', 'Major Subjects', 'NSTP']],
                    ['label' => 'Engagement Band', 'type' => 'select', 'name' => 'band', 'options' => ['All', 'High', 'Medium', 'Low']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Classes needing outreach...'],
                ],
                'notice' => 'LMS Engagement Report is available to IT Office role with LMS module access.',
            ],

            'module-completion-analytics' => [
                'kicker' => 'IT Office · Module Completion Process',
                'description' => 'Track assignment/quiz completion and submission throughput across LMS learning activities.',
                'metrics' => [
                    ['label' => 'Avg Completion', 'value' => '68%', 'icon' => 'fa-tasks', 'tone' => 'green'],
                    ['label' => 'Pending Submits', 'value' => '312', 'icon' => 'fa-inbox', 'tone' => 'amber'],
                    ['label' => 'On-time Rate', 'value' => '74%', 'icon' => 'fa-clock', 'tone' => 'blue'],
                    ['label' => 'At-risk Classes', 'value' => '11', 'icon' => 'fa-exclamation', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Aggregate Activity Status', 'Collect assignment, quiz, and module progress from LMS pages.'],
                    ['Compute Completion Mix', 'Break down finished vs pending vs overdue submissions.'],
                    ['Identify At-risk Sections', 'List classes below completion threshold for faculty follow-up.'],
                    ['Release Completion Analytics', 'Export module completion board for IT / LMS admin.'],
                ],
                'columns' => ['Reference', 'Completion Series', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'LMS-MC-033', 'title' => 'Assignment completion mix', 'owner' => 'LMS Admin', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'LMS-MC-032', 'title' => 'Quiz on-time rate', 'owner' => 'IT Officer', 'status' => 'Synced', 'status_class' => 'approved', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'LMS-MC-031', 'title' => 'At-risk section list', 'owner' => 'LMS Admin', 'status' => 'Watch', 'status_class' => 'review', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'LMS-MC-030', 'title' => 'Overdue submissions', 'owner' => 'IT Officer', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 17, 2026'],
                ],
                'actions' => [
                    ['label' => 'Rebuild Completion', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Thresholds', 'process' => 'validate', 'icon' => 'fa-sliders-h', 'class' => 'ghost'],
                    ['label' => 'Approve Analytics', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Completion', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Activity Type', 'type' => 'select', 'name' => 'activity', 'options' => ['All', 'Assignments', 'Quizzes', 'Modules']],
                    ['label' => 'Threshold', 'type' => 'select', 'name' => 'threshold', 'options' => ['60%', '70%', '80%', '90%']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Intervention notes...'],
                ],
                'notice' => 'Module Completion Analytics is restricted to IT Office LMS datasets.',
            ],

            // ── OSA ────────────────────────────────────────────────────
            'club-activity-report' => [
                'kicker' => 'OSA · Club & Activity Process',
                'description' => 'Report club membership, directory health, and event activity from Co-Curricular modules.',
                'metrics' => [
                    ['label' => 'Registered Clubs', 'value' => '24', 'icon' => 'fa-users', 'tone' => 'blue'],
                    ['label' => 'Active Members', 'value' => '876', 'icon' => 'fa-user-friends', 'tone' => 'green'],
                    ['label' => 'Events (Month)', 'value' => '9', 'icon' => 'fa-calendar-alt', 'tone' => 'purple'],
                    ['label' => 'Attendance Gaps', 'value' => '3', 'icon' => 'fa-user-slash', 'tone' => 'amber'],
                ],
                'steps' => [
                    ['Sync Club Directory', 'Load Club Directory and Student Club Membership rosters.'],
                    ['Attach Event Logs', 'Join Event & Activity Logs with Attendance Tracker results.'],
                    ['Score Participation', 'Rank clubs by membership growth and event attendance.'],
                    ['Release OSA Club Report', 'Export club & activity analytics for Student Affairs.'],
                ],
                'columns' => ['Reference', 'Activity Track', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'OSA-CL-018', 'title' => 'Membership renewal sweep', 'owner' => 'OSA Staff', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'OSA-EV-009', 'title' => 'July event attendance', 'owner' => 'Event Coordinator', 'status' => 'Review', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'OSA-CL-017', 'title' => 'Inactive club flags', 'owner' => 'OSA Officer', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'OSA-AT-012', 'title' => 'Attendance completeness', 'owner' => 'OSA Staff', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Club Feeds', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Rosters', 'process' => 'validate', 'icon' => 'fa-clipboard-check', 'class' => 'ghost'],
                    ['label' => 'Approve Report', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Club Pack', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Club Category', 'type' => 'select', 'name' => 'category', 'options' => ['All Clubs', 'Academic', 'Cultural', 'Sports', 'Interest']],
                    ['label' => 'Period', 'type' => 'select', 'name' => 'period', 'options' => ['This month', 'This term', 'AY 2025-2026']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Inactive clubs, event issues...'],
                ],
                'notice' => 'Club & Activity Report is limited to OSA Co-Curricular module access.',
            ],

            'volunteer-budget-analytics' => [
                'kicker' => 'OSA · Volunteer & Budget Process',
                'description' => 'Analyze Volunteer Hour Tracking and student activity budget requests under OSA.',
                'metrics' => [
                    ['label' => 'Volunteer Hours', 'value' => '1,240', 'icon' => 'fa-hand-holding-heart', 'tone' => 'green'],
                    ['label' => 'Budget Requests', 'value' => '6', 'icon' => 'fa-file-invoice', 'tone' => 'amber'],
                    ['label' => 'Approved Budget', 'value' => '₱86K', 'icon' => 'fa-check-circle', 'tone' => 'blue'],
                    ['label' => 'Pending Review', 'value' => '2', 'icon' => 'fa-hourglass', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Import Volunteer Logs', 'Pull Volunteer Hour Tracking verified hours by club/student.'],
                    ['Attach Budget Queue', 'Join pending activity budget requests and approvals.'],
                    ['Balance Hours vs Funds', 'Compare volunteer output against funded activities.'],
                    ['Release OSA Budget Analytics', 'Export volunteer and budget board for OSA leadership.'],
                ],
                'columns' => ['Reference', 'Volunteer / Budget Item', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'OSA-VH-027', 'title' => 'Community outreach hours', 'owner' => 'Volunteer Desk', 'status' => 'Verified', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'OSA-BG-006', 'title' => 'Cultural night budget', 'owner' => 'OSA Officer', 'status' => 'Review', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'OSA-BG-005', 'title' => 'Sports intramurals funds', 'owner' => 'OSA Officer', 'status' => 'Approved', 'status_class' => 'approved', 'updated' => 'Jul 17, 2026'],
                    ['reference' => 'OSA-VH-026', 'title' => 'Unverified hour batch', 'owner' => 'Volunteer Desk', 'status' => 'Pending', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Dual Feeds', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Verify Hours', 'process' => 'validate', 'icon' => 'fa-user-check', 'class' => 'ghost'],
                    ['label' => 'Approve Analytics', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Pack', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Focus', 'type' => 'select', 'name' => 'focus', 'options' => ['Volunteer + Budget', 'Volunteer hours only', 'Budget only']],
                    ['label' => 'Club Filter', 'type' => 'select', 'name' => 'club', 'options' => ['All Clubs', 'Academic', 'Cultural', 'Sports', 'Interest']],
                    ['label' => 'Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Funding priorities, verification issues...'],
                ],
                'notice' => 'Volunteer & Budget Analytics is scoped to OSA role and Co-Curricular modules.',
            ],

            // ── QA ─────────────────────────────────────────────────────
            'accreditation-compliance-report' => [
                'kicker' => 'QA · Accreditation Compliance Process',
                'description' => 'Map compliance evidence across Accreditation Document Repository and continuous improvement plans.',
                'metrics' => [
                    ['label' => 'Compliance Items', 'value' => '142', 'icon' => 'fa-award', 'tone' => 'blue'],
                    ['label' => 'Evidence Ready', 'value' => '118', 'icon' => 'fa-folder-open', 'tone' => 'green'],
                    ['label' => 'Gaps', 'value' => '17', 'icon' => 'fa-exclamation-triangle', 'tone' => 'amber'],
                    ['label' => 'Action Plans', 'value' => '9', 'icon' => 'fa-tasks', 'tone' => 'purple'],
                ],
                'steps' => [
                    ['Collect Evidence Matrix', 'Load Accreditation Document Repository completeness by criterion.'],
                    ['Score Compliance %', 'Compute ready vs missing evidence for each program area.'],
                    ['Link Improvement Plans', 'Attach Continuous Improvement / Action Planning items to gaps.'],
                    ['Release Compliance Report', 'Export accreditation readiness pack for QA.'],
                ],
                'columns' => ['Reference', 'Compliance Area', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'QA-CM-041', 'title' => 'Criterion II evidence', 'owner' => 'QA Officer', 'status' => 'Ready', 'status_class' => 'approved', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'QA-CM-040', 'title' => 'Faculty profile gaps', 'owner' => 'Program Chair', 'status' => 'Gap', 'status_class' => 'review', 'updated' => 'Jul 19, 2026'],
                    ['reference' => 'QA-AP-009', 'title' => 'Action plan — facilities', 'owner' => 'QA Officer', 'status' => 'In Progress', 'status_class' => 'active', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'QA-CM-039', 'title' => 'Student services folder', 'owner' => 'Document Custodian', 'status' => 'Queued', 'status_class' => 'pending', 'updated' => 'Jul 16, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Evidence', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Assess Gaps', 'process' => 'validate', 'icon' => 'fa-search', 'class' => 'ghost'],
                    ['label' => 'Approve Matrix', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Compliance', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Program', 'type' => 'select', 'name' => 'program', 'options' => ['All Programs', 'CCS', 'CBA', 'COE', 'Criminology', 'CHTM']],
                    ['label' => 'Criterion', 'type' => 'select', 'name' => 'criterion', 'options' => ['All Criteria', 'I', 'II', 'III', 'IV', 'V']],
                    ['label' => 'Gap Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Missing evidence owners...'],
                ],
                'notice' => 'Accreditation Compliance Report is limited to QA Accreditation module access.',
            ],

            'audit-findings-analytics' => [
                'kicker' => 'QA · Audit Findings Process',
                'description' => 'Track Internal Quality Audit Scheduler findings, severity, and corrective action progress.',
                'metrics' => [
                    ['label' => 'Open Findings', 'value' => '7', 'icon' => 'fa-clipboard-check', 'tone' => 'amber'],
                    ['label' => 'Major', 'value' => '2', 'icon' => 'fa-exclamation-circle', 'tone' => 'purple'],
                    ['label' => 'Minor', 'value' => '5', 'icon' => 'fa-info-circle', 'tone' => 'blue'],
                    ['label' => 'Closed MTD', 'value' => '4', 'icon' => 'fa-check-double', 'tone' => 'green'],
                ],
                'steps' => [
                    ['Import Audit Schedule Results', 'Pull findings from Internal Quality Audit Scheduler.'],
                    ['Classify Severity', 'Tag major / minor / observation and assign owners.'],
                    ['Track Corrective Actions', 'Link Continuous Improvement Action Planning due dates.'],
                    ['Release Findings Analytics', 'Export audit findings board for QA leadership.'],
                ],
                'columns' => ['Reference', 'Finding', 'Owner', 'Status', 'Updated'],
                'fields' => ['reference', 'title', 'owner', 'status', 'updated'],
                'records' => [
                    ['reference' => 'AUD-2026-014', 'title' => 'Document control lag', 'owner' => 'Process Owner', 'status' => 'Open', 'status_class' => 'open', 'updated' => 'Jul 20, 2026'],
                    ['reference' => 'AUD-2026-013', 'title' => 'Faculty dossier incomplete', 'owner' => 'Program Chair', 'status' => 'CAPA', 'status_class' => 'review', 'updated' => 'Jul 18, 2026'],
                    ['reference' => 'AUD-2026-012', 'title' => 'Lab safety checklist', 'owner' => 'Lab Custodian', 'status' => 'Closed', 'status_class' => 'approved', 'updated' => 'Jul 15, 2026'],
                    ['reference' => 'AUD-2026-011', 'title' => 'Student support SLA', 'owner' => 'OSA Liaison', 'status' => 'Monitoring', 'status_class' => 'active', 'updated' => 'Jul 12, 2026'],
                ],
                'actions' => [
                    ['label' => 'Refresh Findings', 'process' => 'refresh', 'icon' => 'fa-sync-alt', 'class' => 'primary'],
                    ['label' => 'Validate Severity', 'process' => 'validate', 'icon' => 'fa-layer-group', 'class' => 'ghost'],
                    ['label' => 'Close CAPA Batch', 'process' => 'approve', 'icon' => 'fa-check', 'class' => 'ghost'],
                    ['label' => 'Export Findings', 'process' => 'export', 'icon' => 'fa-file-export', 'class' => 'ghost'],
                ],
                'form' => [
                    ['label' => 'Severity', 'type' => 'select', 'name' => 'severity', 'options' => ['All', 'Major', 'Minor', 'Observation']],
                    ['label' => 'Status Filter', 'type' => 'select', 'name' => 'status', 'options' => ['All', 'Open', 'CAPA', 'Closed', 'Monitoring']],
                    ['label' => 'Auditor Notes', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'Follow-up schedule, owners...'],
                ],
                'notice' => 'Audit Findings Analytics is restricted to QA role Accreditation workflows.',
            ],
        ];
    }
}
