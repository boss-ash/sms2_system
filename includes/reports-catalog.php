<?php
/**
 * SMS 2 - Module-based Reports & Analytics catalog
 *
 * Each report is tied to a source module (or "shared").
 * Staff only see reports for modules they can access.
 */

// These helpers assume an authenticated session (getCurrentUserRoleKey etc.).
// When reports-catalog is loaded from module index / report pages, layout-start
// has already bootstrapped authentication. This guard makes the file safe to
// include standalone as well (prevents "undefined function" fatals).
if (!function_exists('getCurrentUserRoleKey')) {
    require_once __DIR__ . '/authentication.php';
}

if (!function_exists('smsReportsCatalog')) {
    function smsReportsCatalog(): array
    {
        return [
            // Shared office reports (any staff with reports-analytics)
            [
                'slug'   => 'performance-trends',
                'title'  => 'Performance Trends',
                'icon'   => 'fa-chart-line',
                'module' => 'shared',
            ],
            [
                'slug'   => 'export-center',
                'title'  => 'Export Center',
                'icon'   => 'fa-file-export',
                'module' => 'shared',
            ],

            // Enrollment Management
            [
                'slug'   => 'enrollment-analytics',
                'title'  => 'Enrollment Analytics',
                'icon'   => 'fa-user-graduate',
                'module' => 'enrollment',
            ],

            // Registrar
            [
                'slug'   => 'student-records-report',
                'title'  => 'Student Records Report',
                'icon'   => 'fa-folder-open',
                'module' => 'registrar',
            ],
            [
                'slug'   => 'document-release-analytics',
                'title'  => 'Document Release Analytics',
                'icon'   => 'fa-file-signature',
                'module' => 'registrar',
            ],

            // Curriculum & Subject Management
            [
                'slug'   => 'curriculum-analytics',
                'title'  => 'Curriculum Analytics',
                'icon'   => 'fa-book',
                'module' => 'curriculum',
            ],

            // Class Schedule
            [
                'slug'   => 'class-schedule-analytics',
                'title'  => 'Class Schedule Analytics',
                'icon'   => 'fa-calendar-alt',
                'module' => 'scheduling',
            ],

            // CRAD
            [
                'slug'   => 'research-proposal-analytics',
                'title'  => 'Research Proposal Analytics',
                'icon'   => 'fa-clipboard-list',
                'module' => 'crad',
            ],
            [
                'slug'   => 'adviser-grants-report',
                'title'  => 'Adviser & Grants Report',
                'icon'   => 'fa-user-tie',
                'module' => 'crad',
            ],
            [
                'slug'   => 'publication-repository-report',
                'title'  => 'Publication & Repository Report',
                'icon'   => 'fa-book-open',
                'module' => 'crad',
            ],

            // Payment / Finance
            [
                'slug'   => 'collections-analytics',
                'title'  => 'Collections Analytics',
                'icon'   => 'fa-peso-sign',
                'module' => 'payment',
            ],
            [
                'slug'   => 'receivables-report',
                'title'  => 'Receivables Report',
                'icon'   => 'fa-file-invoice-dollar',
                'module' => 'payment',
            ],

            // Faculty / HR
            [
                'slug'   => 'faculty-load-report',
                'title'  => 'Faculty Load Report',
                'icon'   => 'fa-chalkboard-teacher',
                'module' => 'faculty',
            ],
            [
                'slug'   => 'leave-evaluation-analytics',
                'title'  => 'Leave & Evaluation Analytics',
                'icon'   => 'fa-calendar-check',
                'module' => 'faculty',
            ],

            // LMS / IT
            [
                'slug'   => 'lms-engagement-report',
                'title'  => 'LMS Engagement Report',
                'icon'   => 'fa-laptop',
                'module' => 'lms',
            ],
            [
                'slug'   => 'module-completion-analytics',
                'title'  => 'Module Completion Analytics',
                'icon'   => 'fa-tasks',
                'module' => 'lms',
            ],

            // Co-Curricular / OSA
            [
                'slug'   => 'club-activity-report',
                'title'  => 'Club & Activity Report',
                'icon'   => 'fa-users',
                'module' => 'cocurricular',
            ],
            [
                'slug'   => 'volunteer-budget-analytics',
                'title'  => 'Volunteer & Budget Analytics',
                'icon'   => 'fa-hand-holding-heart',
                'module' => 'cocurricular',
            ],

            // Accreditation / QA
            [
                'slug'   => 'accreditation-compliance-report',
                'title'  => 'Accreditation Compliance Report',
                'icon'   => 'fa-award',
                'module' => 'accreditation',
            ],
            [
                'slug'   => 'audit-findings-analytics',
                'title'  => 'Audit Findings Analytics',
                'icon'   => 'fa-clipboard-check',
                'module' => 'accreditation',
            ],
        ];
    }
}

if (!function_exists('smsReportsForRole')) {
    /**
     * Reports visible to the current (or given) role, based on module access.
     */
    function smsReportsForRole(?string $roleKey = null): array
    {
        $roleKey = $roleKey ?: getCurrentUserRoleKey();
        $allowedModules = function_exists('smsAllowedModuleKeysForRole')
            ? smsAllowedModuleKeysForRole($roleKey)
            : getAllowedModuleKeys();
        $hasReportsModule = in_array('reports-analytics', $allowedModules, true);

        if (!$hasReportsModule) {
            return [];
        }

        $items = [];
        foreach (smsReportsCatalog() as $item) {
            $sourceModule = $item['module'] ?? 'shared';

            if ($sourceModule === 'shared') {
                $items[] = $item;
                continue;
            }

            if (in_array($sourceModule, $allowedModules, true)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}

if (!function_exists('smsUserCanAccessReport')) {
    function smsUserCanAccessReport(string $slug): bool
    {
        foreach (smsReportsForRole() as $item) {
            if ($item['slug'] === $slug) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('smsRoleReportProfile')) {
    /**
     * Role-accurate metrics/copy for shared report pages.
     */
    function smsRoleReportProfile(?string $roleKey = null): array
    {
        $roleKey = $roleKey ?: getCurrentUserRoleKey();

        $profiles = [
            'admin' => [
                'office' => 'System Administration',
                'focus' => 'Institution-wide performance across all BCP modules.',
                'metrics' => [
                    ['label' => 'Active Students', 'value' => '2,893', 'delta' => '+4.2%'],
                    ['label' => 'Faculty Members', 'value' => '102', 'delta' => '+2.0%'],
                    ['label' => 'Open Tickets', 'value' => '37', 'delta' => '-6.1%'],
                    ['label' => 'Modules Online', 'value' => '11', 'delta' => 'Stable'],
                ],
                'charts' => [
                    'Enrollment completion',
                    'Collections progress',
                    'Research proposals reviewed',
                    'LMS engagement',
                ],
            ],
            'registrar' => [
                'office' => 'Registrar Office',
                'focus' => 'Enrollment, student records, curriculum, and class schedule analytics.',
                'metrics' => [
                    ['label' => 'Enrolled This Term', 'value' => '1,204', 'delta' => '+6.1%'],
                    ['label' => 'Pending Enrollments', 'value' => '47', 'delta' => '-5.4%'],
                    ['label' => 'Document Requests', 'value' => '28', 'delta' => '+9.2%'],
                    ['label' => 'Schedule Conflicts', 'value' => '6', 'delta' => '-2'],
                ],
                'charts' => [
                    'Enrollment by program',
                    'Document release turnaround',
                    'Curriculum coverage',
                    'Section utilization',
                ],
            ],
            'crad_officer' => [
                'office' => 'Center for Research and Development',
                'focus' => 'Title proposals, adviser assignment, grants, publication, and repository tracking.',
                'metrics' => [
                    ['label' => 'Submitted Proposals', 'value' => '24', 'delta' => '+4'],
                    ['label' => 'Approved Titles', 'value' => '11', 'delta' => '+2'],
                    ['label' => 'Pending Review', 'value' => '7', 'delta' => '-1'],
                    ['label' => 'Active Advisers', 'value' => '12', 'delta' => '+1'],
                ],
                'charts' => [
                    'Proposals by college',
                    'Adviser assignment load',
                    'Grants evaluation status',
                    'Repository catalog growth',
                ],
            ],
            'finance' => [
                'office' => 'Finance Office',
                'focus' => 'Collections, receivables, discounts, and payment posting performance.',
                'metrics' => [
                    ['label' => 'Collections (Month)', 'value' => '₱1.2M', 'delta' => '+8.4%'],
                    ['label' => 'Pending Payments', 'value' => '134', 'delta' => '-3.2%'],
                    ['label' => 'Overdue Accounts', 'value' => '23', 'delta' => '-1.5%'],
                    ['label' => 'Scholarships Applied', 'value' => '86', 'delta' => '+5'],
                ],
                'charts' => [
                    'Daily collections trend',
                    'Fee-type distribution',
                    'Receivables aging',
                    'Discount utilization',
                ],
            ],
            'hr' => [
                'office' => 'Human Resources / Faculty Management',
                'focus' => 'Faculty load, leave, evaluation, and attendance analytics.',
                'metrics' => [
                    ['label' => 'Total Faculty', 'value' => '102', 'delta' => '+1.0%'],
                    ['label' => 'On Leave Today', 'value' => '5', 'delta' => '+2'],
                    ['label' => 'Avg Evaluation', 'value' => '4.2', 'delta' => '+0.1'],
                    ['label' => 'Pending Leave', 'value' => '8', 'delta' => '-3'],
                ],
                'charts' => [
                    'Faculty by department',
                    'Teaching load distribution',
                    'Leave application trend',
                    'Evaluation score bands',
                ],
            ],
            'it_office' => [
                'office' => 'IT Office / LMS',
                'focus' => 'LMS engagement, module completion, submissions, and classroom usage.',
                'metrics' => [
                    ['label' => 'Active LMS Users', 'value' => '2,104', 'delta' => '+7.3%'],
                    ['label' => 'Active Classes', 'value' => '148', 'delta' => '+5.6%'],
                    ['label' => 'Pending Submissions', 'value' => '312', 'delta' => '-4.1%'],
                    ['label' => 'Avg Completion', 'value' => '68%', 'delta' => '+2.4%'],
                ],
                'charts' => [
                    'Login activity trend',
                    'Module completion mix',
                    'Assignment submission rate',
                    'Virtual classroom usage',
                ],
            ],
            'osa' => [
                'office' => 'Office of Student Affairs',
                'focus' => 'Clubs, events, volunteer hours, and student activity budgets.',
                'metrics' => [
                    ['label' => 'Registered Clubs', 'value' => '24', 'delta' => '+1'],
                    ['label' => 'Events This Month', 'value' => '9', 'delta' => '+3'],
                    ['label' => 'Active Members', 'value' => '876', 'delta' => '+4.8%'],
                    ['label' => 'Budget Requests', 'value' => '6', 'delta' => '-2'],
                ],
                'charts' => [
                    'Membership by club',
                    'Event attendance',
                    'Volunteer hours logged',
                    'Budget request status',
                ],
            ],
            'qa' => [
                'office' => 'Quality Assurance / Accreditation',
                'focus' => 'Compliance evidence, audit findings, and program accreditation progress.',
                'metrics' => [
                    ['label' => 'Accredited Programs', 'value' => '8', 'delta' => '+1'],
                    ['label' => 'Compliance Items', 'value' => '142', 'delta' => '+6.2%'],
                    ['label' => 'Non-Conformities', 'value' => '7', 'delta' => '-2'],
                    ['label' => 'Next Visit', 'value' => 'Sep 2026', 'delta' => 'On track'],
                ],
                'charts' => [
                    'Compliance by criteria',
                    'Audit finding severity',
                    'Evidence completeness',
                    'Action plan progress',
                ],
            ],
        ];

        return $profiles[$roleKey] ?? $profiles['admin'];
    }
}
