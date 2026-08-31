<?php
/**
 * SMS 2 - Dashboard chart datasets for Reports & Analytics
 * Module / role accurate example data for the Overview-style board.
 */

if (!function_exists('smsReportDashboardData')) {
    function smsReportDashboardData(string $slug, ?string $roleKey = null): array
    {
        $roleKey = $roleKey ?: getCurrentUserRoleKey();
        $profile = smsRoleReportProfile($roleKey);
        $catalog = smsReportDashboardCatalog();

        if (isset($catalog[$slug]) && is_callable($catalog[$slug])) {
            $data = $catalog[$slug]($roleKey, $profile);
        } elseif (isset($catalog[$slug]) && is_array($catalog[$slug])) {
            $data = $catalog[$slug];
        } else {
            $data = smsReportDashboardFallback($slug, $profile);
        }

        // Stamp exact clicked report identity so UI always matches sidebar selection.
        $reportTitle = ucwords(str_replace('-', ' ', $slug));
        foreach (smsReportsCatalog() as $item) {
            if (($item['slug'] ?? '') === $slug) {
                $reportTitle = $item['title'] ?? $reportTitle;
                $sourceModule = $item['module'] ?? 'shared';
                break;
            }
        }
        $sourceModule = $sourceModule ?? 'shared';

        $moduleLabels = [
            'shared' => 'All assigned office modules',
            'enrollment' => 'Enrollment Management',
            'registrar' => 'Registrar',
            'curriculum' => 'Curriculum & Subject Management',
            'scheduling' => 'Class Schedule',
            'crad' => 'CRAD',
            'payment' => 'Payment Management',
            'faculty' => 'Faculty Management',
            'lms' => 'Online Learning & LMS',
            'cocurricular' => 'Co-Curricular',
            'accreditation' => 'Accreditation Management',
        ];

        $allowed = function_exists('smsAllowedModuleKeysForRole')
            ? smsAllowedModuleKeysForRole($roleKey)
            : getAllowedModuleKeys();
        $officeModules = [];
        foreach ($allowed as $mod) {
            if (!in_array($mod, ['reports-analytics', 'student_portal', 'user-management'], true)) {
                $officeModules[] = $mod;
            }
        }

        $data['report_slug'] = $slug;
        $data['report_title'] = $reportTitle;
        $data['source_module'] = $sourceModule;
        $data['accessible_modules'] = $officeModules;
        $data['subtitle'] = ($data['subtitle'] ?? '') !== ''
            ? $data['subtitle']
            : ('Analytics for ' . $reportTitle . '.');

        // Always force filter labels to match the exact report clicked.
        $data['filters'] = [
            'Date Range: ' . ($data['date_range'] ?? 'Last 30 Days'),
            'Report Type: ' . $reportTitle,
            'Source Module: ' . ($moduleLabels[$sourceModule] ?? $sourceModule),
            'Account Modules: ' . (count($officeModules) ? implode(', ', array_map(
                static fn(string $m): string => $moduleLabels[$m] ?? $m,
                $officeModules
            )) : 'None'),
        ];

        return $data;
    }
}

if (!function_exists('smsReportDashboardFallback')) {
    function smsReportDashboardFallback(string $slug, array $profile): array
    {
        $title = ucwords(str_replace('-', ' ', $slug));
        return [
            'subtitle' => $profile['focus'] ?? 'Role-scoped analytics overview.',
            'filters' => [
                'Date Range: Last 30 Days',
                'Report Type: ' . $title,
            ],
            'donut' => [
                'title' => 'Status Distribution',
                'labels' => ['Completed', 'In Progress', 'Pending', 'On Hold'],
                'values' => [42, 28, 18, 12],
                'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7'],
            ],
            'bar' => [
                'title' => 'Monthly Activity',
                'labels' => ['Apr', 'May', 'Jun', 'Jul'],
                'values' => [32, 45, 38, 51],
                'color' => '#22c55e',
            ],
            'grouped' => [
                'title' => 'Comparison Trend',
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'series' => [
                    ['label' => 'Processed', 'values' => [12, 18, 15, 22], 'color' => '#22c55e'],
                    ['label' => 'Pending', 'values' => [8, 6, 9, 5], 'color' => '#f59e0b'],
                ],
            ],
            'horizontal' => [
                'title' => 'Top Items',
                'labels' => ['Item A', 'Item B', 'Item C', 'Item D'],
                'values' => [48, 36, 29, 21],
                'color' => '#3b82f6',
            ],
            'summary' => [
                ['metric' => 'Total Records', 'value' => '128'],
                ['metric' => 'Completed', 'value' => '84'],
                ['metric' => 'Pending', 'value' => '31'],
                ['metric' => 'Exceptions', 'value' => '13'],
                ['metric' => 'Completion Rate', 'value' => '65.6%'],
            ],
        ];
    }
}

if (!function_exists('smsReportDashboardCatalog')) {
    function smsReportDashboardCatalog(): array
    {
        require_once ROOT_PATH . '/includes/reports-account-dashboard.php';

        $shared = static function (string $kind): callable {
            return static function (string $roleKey, array $profile) use ($kind): array {
                return smsBuildSharedAccountDashboard($kind, $roleKey, $profile);
            };
        };

        return [
            'overview' => $shared('overview'),
            'performance-trends' => $shared('performance-trends'),
            'export-center' => $shared('export-center'),

            'enrollment-analytics' => [
                'subtitle' => 'Enrollment funnel analytics from pre-registration through validation and sectioning.',
                'filters' => ['Date Range: Current Term', 'Report Type: Enrollment Analytics'],
                'donut' => [
                    'title' => 'Enrollment Status Distribution',
                    'labels' => ['Validated', 'Pre-registered', 'Waiting List', 'Cross-enroll'],
                    'values' => [1204, 1486, 47, 9],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7'],
                ],
                'bar' => [
                    'title' => 'Monthly Enrollment Completions',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [860, 940, 1080, 1204],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Validated vs Waiting List',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Validated', 'values' => [240, 280, 310, 374], 'color' => '#22c55e'],
                        ['label' => 'Waiting', 'values' => [18, 14, 10, 5], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Enrollment by Program',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology', 'CHTM'],
                    'values' => [312, 278, 241, 198, 175],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Pre-registered', 'value' => '1,486'],
                    ['metric' => 'Validated', 'value' => '1,204'],
                    ['metric' => 'Waiting List', 'value' => '47'],
                    ['metric' => 'Cross-enroll Flags', 'value' => '9'],
                    ['metric' => 'Conversion Rate', 'value' => '81.0%'],
                    ['metric' => 'Parent Notify Coverage', 'value' => '92%'],
                ],
            ],

            'student-records-report' => [
                'subtitle' => 'Student records completeness from SIS, academic history, and digital file storage.',
                'filters' => ['Date Range: Current Term', 'Report Type: Student Records Report'],
                'donut' => [
                    'title' => 'Record Completeness',
                    'labels' => ['Complete', 'Incomplete', 'Updating', 'Archived'],
                    'values' => [2832, 61, 34, 18],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#94a3b8'],
                ],
                'bar' => [
                    'title' => 'Monthly Record Updates',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [120, 145, 160, 184],
                    'color' => '#3b82f6',
                ],
                'grouped' => [
                    'title' => 'Profile vs Digital Files',
                    'labels' => ['Freshmen', 'Continuing', 'Transferees', 'Graduating'],
                    'series' => [
                        ['label' => 'Profile OK', 'values' => [764, 1631, 217, 220], 'color' => '#22c55e'],
                        ['label' => 'File Gaps', 'values' => [48, 9, 4, 0], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Incomplete Files by Cohort',
                    'labels' => ['Freshmen', 'Continuing', 'Transferees', 'Graduating'],
                    'values' => [48, 9, 4, 0],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Active Records', 'value' => '2,893'],
                    ['metric' => 'Incomplete Files', 'value' => '61'],
                    ['metric' => 'Status Updates', 'value' => '34'],
                    ['metric' => 'Transcript Jobs', 'value' => '18'],
                    ['metric' => 'Profile Complete', 'value' => '94%'],
                ],
            ],

            'document-release-analytics' => [
                'subtitle' => 'Document request turnaround from Document Requests and Transcript Management.',
                'filters' => ['Date Range: Last 30 Days', 'Report Type: Document Release Analytics'],
                'donut' => [
                    'title' => 'Document Status Distribution',
                    'labels' => ['Released', 'Processing', 'Queued', 'Overdue'],
                    'values' => [86, 18, 10, 5],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Monthly Releases',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [62, 74, 81, 86],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Released vs Overdue',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Released', 'values' => [18, 22, 24, 22], 'color' => '#22c55e'],
                        ['label' => 'Overdue', 'values' => [3, 2, 1, 2], 'color' => '#ef4444'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Requests by Document Type',
                    'labels' => ['TOR', 'Certificates', 'CTC', 'Other'],
                    'values' => [38, 29, 21, 12],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Open Requests', 'value' => '28'],
                    ['metric' => 'Released Today', 'value' => '16'],
                    ['metric' => 'Avg Turnaround', 'value' => '2.1 days'],
                    ['metric' => 'Overdue', 'value' => '5'],
                    ['metric' => 'Same-day Rate', 'value' => '41%'],
                ],
            ],

            'curriculum-analytics' => [
                'subtitle' => 'Curriculum coverage, prerequisites, and CHED/DepEd validation analytics.',
                'filters' => ['Date Range: AY 2025-2026', 'Report Type: Curriculum Analytics'],
                'donut' => [
                    'title' => 'Curriculum Health',
                    'labels' => ['Validated', 'Watch', 'Pending', 'Draft'],
                    'values' => [11, 3, 4, 2],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#94a3b8'],
                ],
                'bar' => [
                    'title' => 'Subjects Mapped by Month',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [380, 395, 410, 426],
                    'color' => '#3b82f6',
                ],
                'grouped' => [
                    'title' => 'Mapped vs Prereq Gaps',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'series' => [
                        ['label' => 'Mapped %', 'values' => [96, 92, 90, 88], 'color' => '#22c55e'],
                        ['label' => 'Gaps', 'values' => [2, 3, 1, 4], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Subjects per Program',
                    'labels' => ['BSIT', 'BSBA', 'BSED', 'BSCrim', 'BSHM'],
                    'values' => [64, 58, 52, 49, 47],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Active Curricula', 'value' => '18'],
                    ['metric' => 'Subjects Mapped', 'value' => '426'],
                    ['metric' => 'Prereq Gaps', 'value' => '11'],
                    ['metric' => 'Pending Validate', 'value' => '4'],
                    ['metric' => 'CHED Validated', 'value' => '78%'],
                ],
            ],

            'class-schedule-analytics' => [
                'subtitle' => 'Section loads, conflicts, room usage, and exam timetable analytics.',
                'filters' => ['Date Range: Current Term', 'Report Type: Class Schedule Analytics'],
                'donut' => [
                    'title' => 'Schedule Issue Mix',
                    'labels' => ['Clear', 'Teacher Conflict', 'Room Conflict', 'Overlap'],
                    'values' => [180, 3, 2, 1],
                    'colors' => ['#22c55e', '#ef4444', '#f59e0b', '#a855f7'],
                ],
                'bar' => [
                    'title' => 'Active Sections Trend',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [160, 168, 175, 186],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Conflicts vs Resolved',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Conflicts', 'values' => [9, 7, 6, 6], 'color' => '#ef4444'],
                        ['label' => 'Resolved', 'values' => [5, 6, 4, 3], 'color' => '#22c55e'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Room Utilization Rank',
                    'labels' => ['Lab 3', 'Room 204', 'AVR', 'Gym'],
                    'values' => [92, 81, 74, 58],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Active Sections', 'value' => '186'],
                    ['metric' => 'Open Conflicts', 'value' => '6'],
                    ['metric' => 'Rooms Used', 'value' => '54'],
                    ['metric' => 'Exam Blocks', 'value' => '28'],
                    ['metric' => 'Peak Room Free', 'value' => '18%'],
                ],
            ],

            'research-proposal-analytics' => [
                'subtitle' => 'CRAD proposal pipeline from Proposal Submission & Tracking.',
                'filters' => ['Date Range: Current Cycle', 'Report Type: Research Proposal Analytics'],
                'donut' => [
                    'title' => 'Proposal Stage Distribution',
                    'labels' => ['Submitted', 'Under Review', 'Approved', 'Returned'],
                    'values' => [24, 7, 11, 3],
                    'colors' => ['#3b82f6', '#f59e0b', '#22c55e', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Monthly Proposal Intake',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [14, 18, 21, 24],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Approved vs Returned',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Approved', 'values' => [2, 3, 3, 3], 'color' => '#22c55e'],
                        ['label' => 'Returned', 'values' => [1, 0, 1, 1], 'color' => '#ef4444'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Proposals by College',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'values' => [8, 6, 5, 5],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Submitted', 'value' => '24'],
                    ['metric' => 'Under Review', 'value' => '7'],
                    ['metric' => 'Approved', 'value' => '11'],
                    ['metric' => 'Returned', 'value' => '3'],
                    ['metric' => 'Avg Review Days', 'value' => '4'],
                ],
            ],

            'adviser-grants-report' => [
                'subtitle' => 'Adviser Assignment and Research Grants combined analytics.',
                'filters' => ['Date Range: Current Term', 'Report Type: Adviser & Grants Report'],
                'donut' => [
                    'title' => 'Assignment & Grant Status',
                    'labels' => ['Assigned', 'For Match', 'Funded', 'Evaluation'],
                    'values' => [18, 5, 4, 5],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#a855f7'],
                ],
                'bar' => [
                    'title' => 'Monthly Adviser Confirmations',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [10, 13, 16, 18],
                    'color' => '#3b82f6',
                ],
                'grouped' => [
                    'title' => 'Adviser Load vs Grants',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Assignments', 'values' => [3, 4, 5, 6], 'color' => '#22c55e'],
                        ['label' => 'Grant Apps', 'values' => [1, 2, 3, 3], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Adviser Capacity by College',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'values' => [82, 74, 68, 71],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Pending Advisers', 'value' => '5'],
                    ['metric' => 'Assigned Groups', 'value' => '18'],
                    ['metric' => 'Grant Applications', 'value' => '9'],
                    ['metric' => 'Funded', 'value' => '4'],
                    ['metric' => 'Capacity Used', 'value' => '76%'],
                ],
            ],

            'publication-repository-report' => [
                'subtitle' => 'Publication pipeline and research repository catalog analytics.',
                'filters' => ['Date Range: Last 90 Days', 'Report Type: Publication & Repository Report'],
                'donut' => [
                    'title' => 'Catalog Access Mix',
                    'labels' => ['Open Access', 'Embargoed', 'Internal', 'Editing'],
                    'values' => [54, 22, 18, 6],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#a855f7'],
                ],
                'bar' => [
                    'title' => 'Repository Growth',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [110, 116, 122, 128],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Manuscripts vs Deposits',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Manuscripts', 'values' => [2, 3, 4, 5], 'color' => '#3b82f6'],
                        ['label' => 'Deposits', 'values' => [4, 5, 6, 6], 'color' => '#22c55e'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Outputs by Type',
                    'labels' => ['Capstone', 'Journal', 'Conference', 'MoA'],
                    'values' => [48, 14, 9, 8],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Manuscripts', 'value' => '14'],
                    ['metric' => 'For Publication', 'value' => '6'],
                    ['metric' => 'Repository Items', 'value' => '128'],
                    ['metric' => 'Collaborations', 'value' => '8'],
                    ['metric' => 'Metadata Complete', 'value' => '91%'],
                ],
            ],

            'collections-analytics' => [
                'subtitle' => 'Collections performance from Payment Collection Portal and online payments.',
                'filters' => ['Date Range: This Month', 'Report Type: Collections Analytics'],
                'donut' => [
                    'title' => 'Collection Channel Mix',
                    'labels' => ['Cashier', 'Online', 'Bank', 'Unmatched'],
                    'values' => [52, 41, 7, 4],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Monthly Collections (₱)',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [920000, 980000, 1100000, 1200000],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Cashier vs Online',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Cashier', 'values' => [140000, 155000, 160000, 165000], 'color' => '#22c55e'],
                        ['label' => 'Online', 'values' => [110000, 120000, 125000, 137000], 'color' => '#3b82f6'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Collections by Fee Type',
                    'labels' => ['Tuition', 'Misc', 'Lab', 'Other'],
                    'values' => [620000, 280000, 190000, 110000],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Collected (MTD)', 'value' => '₱1,200,000.00'],
                    ['metric' => 'Online Share', 'value' => '41%'],
                    ['metric' => 'Pending Posting', 'value' => '37'],
                    ['metric' => 'Discrepancies', 'value' => '4'],
                    ['metric' => 'Posted Ratio', 'value' => '97%'],
                ],
            ],

            'receivables-report' => [
                'subtitle' => 'Receivables aging from Accounts Receivable Management.',
                'filters' => ['Date Range: As of Today', 'Report Type: Receivables Report'],
                'donut' => [
                    'title' => 'Aging Bucket Mix',
                    'labels' => ['0-30', '31-60', '61-90', '90+'],
                    'values' => [43, 31, 16, 10],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Open AR Trend (₱)',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [520000, 505000, 495000, 486000],
                    'color' => '#f59e0b',
                ],
                'grouped' => [
                    'title' => 'Current vs Escalated',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Current', 'values' => [210, 205, 200, 210], 'color' => '#22c55e'],
                        ['label' => '61+', 'values' => [140, 135, 130, 124], 'color' => '#ef4444'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'AR by Program (₱K)',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'values' => [145, 128, 110, 103],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Open AR', 'value' => '₱486,000.00'],
                    ['metric' => '0-30 Days', 'value' => '₱210,000.00'],
                    ['metric' => '31-60 Days', 'value' => '₱152,000.00'],
                    ['metric' => '61+ Days', 'value' => '₱124,000.00'],
                    ['metric' => 'Accounts Flagged', 'value' => '23'],
                ],
            ],

            'faculty-load-report' => [
                'subtitle' => 'Faculty teaching load from Subject Load Tracker and Schedule Assignment.',
                'filters' => ['Date Range: Current Term', 'Report Type: Faculty Load Report'],
                'donut' => [
                    'title' => 'Load Status Distribution',
                    'labels' => ['OK', 'Overload', 'Underload', 'Watch'],
                    'values' => [84, 7, 4, 7],
                    'colors' => ['#22c55e', '#ef4444', '#f59e0b', '#3b82f6'],
                ],
                'bar' => [
                    'title' => 'Avg Units Trend',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [17.8, 18.0, 18.2, 18.4],
                    'color' => '#3b82f6',
                ],
                'grouped' => [
                    'title' => 'Overload vs Underload',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'series' => [
                        ['label' => 'Overload', 'values' => [3, 1, 1, 2], 'color' => '#ef4444'],
                        ['label' => 'Underload', 'values' => [1, 1, 1, 1], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Avg Units by Department',
                    'labels' => ['CCS', 'CBA', 'Education', 'Criminology'],
                    'values' => [21.2, 18.1, 17.4, 16.8],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Faculty Count', 'value' => '102'],
                    ['metric' => 'Overload Cases', 'value' => '7'],
                    ['metric' => 'Underload', 'value' => '4'],
                    ['metric' => 'Avg Units', 'value' => '18.4'],
                    ['metric' => 'Policy Compliance', 'value' => '89%'],
                ],
            ],

            'leave-evaluation-analytics' => [
                'subtitle' => 'Leave applications and faculty evaluation analytics.',
                'filters' => ['Date Range: This Month', 'Report Type: Leave & Evaluation Analytics'],
                'donut' => [
                    'title' => 'Evaluation Score Bands',
                    'labels' => ['4.5-5.0', '4.0-4.4', '3.5-3.9', 'Below 3.5'],
                    'values' => [28, 46, 19, 7],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Leave Applications',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [12, 15, 11, 18],
                    'color' => '#f59e0b',
                ],
                'grouped' => [
                    'title' => 'Approved vs Pending Leave',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Approved', 'values' => [3, 4, 5, 4], 'color' => '#22c55e'],
                        ['label' => 'Pending', 'values' => [2, 2, 3, 1], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Leave Types',
                    'labels' => ['Sick', 'Personal', 'Official', 'Other'],
                    'values' => [9, 5, 3, 1],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Pending Leave', 'value' => '8'],
                    ['metric' => 'On Leave Today', 'value' => '5'],
                    ['metric' => 'Avg Evaluation', 'value' => '4.2'],
                    ['metric' => 'Clearance Open', 'value' => '3'],
                    ['metric' => 'Eval ≥ 4.0', 'value' => '74%'],
                ],
            ],

            'lms-engagement-report' => [
                'subtitle' => 'LMS engagement from Class Portal and lesson material activity.',
                'filters' => ['Date Range: Last 30 Days', 'Report Type: LMS Engagement Report'],
                'donut' => [
                    'title' => 'Engagement Band Mix',
                    'labels' => ['High', 'Medium', 'Low', 'Inactive'],
                    'values' => [41, 44, 15, 8],
                    'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#94a3b8'],
                ],
                'bar' => [
                    'title' => 'Active Users Trend',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [1800, 1920, 2010, 2104],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Logins vs Material Views',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Logins (K)', 'values' => [8, 9, 10, 11], 'color' => '#3b82f6'],
                        ['label' => 'Views (K)', 'values' => [18, 20, 22, 24], 'color' => '#22c55e'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Low-engagement Classes',
                    'labels' => ['EDUC 120', 'GE 101', 'NSTP 1', 'PE 2'],
                    'values' => [22, 19, 17, 14],
                    'color' => '#f59e0b',
                ],
                'summary' => [
                    ['metric' => 'Active LMS Users', 'value' => '2,104'],
                    ['metric' => 'Active Classes', 'value' => '148'],
                    ['metric' => 'Material Views', 'value' => '9,400'],
                    ['metric' => 'Low-engagement', 'value' => '22'],
                    ['metric' => 'Daily Login Peak', 'value' => '78%'],
                ],
            ],

            'module-completion-analytics' => [
                'subtitle' => 'Assignment, quiz, and module completion analytics across LMS.',
                'filters' => ['Date Range: Current Term', 'Report Type: Module Completion Analytics'],
                'donut' => [
                    'title' => 'Completion Risk Mix',
                    'labels' => ['Low Risk', 'Medium', 'High', 'Overdue'],
                    'values' => [58, 40, 11, 18],
                    'colors' => ['#22c55e', '#3b82f6', '#ef4444', '#f59e0b'],
                ],
                'bar' => [
                    'title' => 'Avg Completion %',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [60, 63, 66, 68],
                    'color' => '#3b82f6',
                ],
                'grouped' => [
                    'title' => 'Assignments vs Quizzes',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Assignments', 'values' => [65, 68, 70, 71], 'color' => '#22c55e'],
                        ['label' => 'Quizzes', 'values' => [70, 72, 74, 76], 'color' => '#3b82f6'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Completion by Activity',
                    'labels' => ['Quizzes', 'Assignments', 'Modules', 'Forums'],
                    'values' => [76, 71, 64, 52],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Avg Completion', 'value' => '68%'],
                    ['metric' => 'Pending Submits', 'value' => '312'],
                    ['metric' => 'On-time Rate', 'value' => '74%'],
                    ['metric' => 'At-risk Classes', 'value' => '11'],
                    ['metric' => 'Quiz Completion', 'value' => '76%'],
                ],
            ],

            'club-activity-report' => [
                'subtitle' => 'Club membership and event activity from Co-Curricular modules.',
                'filters' => ['Date Range: This Month', 'Report Type: Club & Activity Report'],
                'donut' => [
                    'title' => 'Club Category Mix',
                    'labels' => ['Academic', 'Cultural', 'Sports', 'Interest'],
                    'values' => [34, 26, 22, 18],
                    'colors' => ['#22c55e', '#a855f7', '#3b82f6', '#f59e0b'],
                ],
                'bar' => [
                    'title' => 'Active Members Trend',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [760, 800, 840, 876],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Events vs Attendance Gaps',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Events', 'values' => [2, 2, 3, 2], 'color' => '#3b82f6'],
                        ['label' => 'Gaps', 'values' => [1, 0, 1, 1], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Top Clubs by Members',
                    'labels' => ['CCS Society', 'Dance Troupe', 'Photo Club', 'Varsity'],
                    'values' => [96, 54, 41, 28],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Registered Clubs', 'value' => '24'],
                    ['metric' => 'Active Members', 'value' => '876'],
                    ['metric' => 'Events This Month', 'value' => '9'],
                    ['metric' => 'Attendance Gaps', 'value' => '3'],
                    ['metric' => 'Inactive Flags', 'value' => '2'],
                ],
            ],

            'volunteer-budget-analytics' => [
                'subtitle' => 'Volunteer hours and activity budget request analytics.',
                'filters' => ['Date Range: This Month', 'Report Type: Volunteer & Budget Analytics'],
                'donut' => [
                    'title' => 'Budget Request Status',
                    'labels' => ['Approved', 'Review', 'Pending', 'Denied'],
                    'values' => [4, 1, 1, 0],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
                ],
                'bar' => [
                    'title' => 'Volunteer Hours Logged',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [820, 940, 1100, 1240],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Verified Hours vs Budget (₱K)',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Hours', 'values' => [280, 300, 320, 340], 'color' => '#3b82f6'],
                        ['label' => 'Budget ₱K', 'values' => [18, 22, 24, 22], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Budget by Activity',
                    'labels' => ['Intramurals', 'Cultural Night', 'Outreach', 'Other'],
                    'values' => [35, 28, 15, 8],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Volunteer Hours', 'value' => '1,240'],
                    ['metric' => 'Budget Requests', 'value' => '6'],
                    ['metric' => 'Approved Budget', 'value' => '₱86,000.00'],
                    ['metric' => 'Pending Review', 'value' => '2'],
                    ['metric' => 'Verified Hours', 'value' => '88%'],
                ],
            ],

            'accreditation-compliance-report' => [
                'subtitle' => 'Accreditation compliance matrix and evidence readiness analytics.',
                'filters' => ['Date Range: Visit Cycle 2026', 'Report Type: Accreditation Compliance Report'],
                'donut' => [
                    'title' => 'Evidence Status',
                    'labels' => ['Ready', 'Gaps', 'Action Plans', 'Queued'],
                    'values' => [118, 17, 9, 8],
                    'colors' => ['#22c55e', '#f59e0b', '#3b82f6', '#94a3b8'],
                ],
                'bar' => [
                    'title' => 'Compliance Items Growth',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [120, 128, 136, 142],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Ready vs Gaps by Criterion',
                    'labels' => ['I', 'II', 'III', 'IV'],
                    'series' => [
                        ['label' => 'Ready', 'values' => [17, 28, 25, 22], 'color' => '#22c55e'],
                        ['label' => 'Gaps', 'values' => [1, 6, 4, 4], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Compliance % by Criterion',
                    'labels' => ['Criterion I', 'II', 'III', 'IV'],
                    'values' => [92, 85, 78, 81],
                    'color' => '#3b82f6',
                ],
                'summary' => [
                    ['metric' => 'Compliance Items', 'value' => '142'],
                    ['metric' => 'Evidence Ready', 'value' => '118'],
                    ['metric' => 'Gaps', 'value' => '17'],
                    ['metric' => 'Action Plans', 'value' => '9'],
                    ['metric' => 'Accredited Programs', 'value' => '8'],
                ],
            ],

            'audit-findings-analytics' => [
                'subtitle' => 'Internal quality audit findings and CAPA progress analytics.',
                'filters' => ['Date Range: This Month', 'Report Type: Audit Findings Analytics'],
                'donut' => [
                    'title' => 'Finding Status Mix',
                    'labels' => ['Open', 'CAPA', 'Monitoring', 'Closed'],
                    'values' => [39, 28, 17, 16],
                    'colors' => ['#ef4444', '#f59e0b', '#3b82f6', '#22c55e'],
                ],
                'bar' => [
                    'title' => 'Findings Closed (MTD)',
                    'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                    'values' => [2, 3, 3, 4],
                    'color' => '#22c55e',
                ],
                'grouped' => [
                    'title' => 'Major vs Minor',
                    'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    'series' => [
                        ['label' => 'Major', 'values' => [1, 0, 1, 0], 'color' => '#ef4444'],
                        ['label' => 'Minor', 'values' => [2, 1, 1, 1], 'color' => '#f59e0b'],
                    ],
                ],
                'horizontal' => [
                    'title' => 'Findings by Area',
                    'labels' => ['Docs', 'Faculty', 'Labs', 'Services'],
                    'values' => [3, 2, 1, 1],
                    'color' => '#a855f7',
                ],
                'summary' => [
                    ['metric' => 'Open Findings', 'value' => '7'],
                    ['metric' => 'Major', 'value' => '2'],
                    ['metric' => 'Minor', 'value' => '5'],
                    ['metric' => 'Closed MTD', 'value' => '4'],
                    ['metric' => 'CAPA On Track', 'value' => '71%'],
                ],
            ],
        ];
    }
}
