<?php
/**
 * SMS 2 - Account-scoped shared report dashboards
 * Summary / Trends / Export reflect ALL modules the signed-in account can access.
 */

if (!function_exists('smsAccountModuleKpis')) {
    function smsAccountModuleKpis(): array
    {
        return [
            'enrollment' => [
                'label' => 'Enrollment',
                'donut' => 'Validated',
                'score' => 1204,
                'trend' => [860, 940, 1080, 1204],
                'metric' => 'Validated Enrollees',
                'value' => '1,204',
                'export' => 'Enrollment funnel package',
            ],
            'registrar' => [
                'label' => 'Registrar',
                'donut' => 'Complete Records',
                'score' => 2832,
                'trend' => [2500, 2610, 2720, 2832],
                'metric' => 'Active Student Records',
                'value' => '2,893',
                'export' => 'Student records & document release pack',
            ],
            'curriculum' => [
                'label' => 'Curriculum',
                'donut' => 'Validated Maps',
                'score' => 18,
                'trend' => [12, 14, 16, 18],
                'metric' => 'Active Curriculum Versions',
                'value' => '18',
                'export' => 'Curriculum & CHED validation pack',
            ],
            'scheduling' => [
                'label' => 'Class Schedule',
                'donut' => 'Published Sections',
                'score' => 146,
                'trend' => [120, 128, 138, 146],
                'metric' => 'Open Schedule Conflicts',
                'value' => '6',
                'export' => 'Schedule & conflict report pack',
            ],
            'crad' => [
                'label' => 'CRAD',
                'donut' => 'Approved Proposals',
                'score' => 11,
                'trend' => [5, 7, 9, 11],
                'metric' => 'Submitted Proposals',
                'value' => '24',
                'export' => 'Proposal / adviser / grants pack',
            ],
            'payment' => [
                'label' => 'Payment',
                'donut' => 'Posted Payments',
                'score' => 842,
                'trend' => [620, 710, 780, 842],
                'metric' => 'Collections (Month)',
                'value' => '₱1,200,000.00',
                'export' => 'Collections & receivables pack',
            ],
            'faculty' => [
                'label' => 'Faculty',
                'donut' => 'Active Faculty',
                'score' => 86,
                'trend' => [80, 82, 84, 86],
                'metric' => 'Total Faculty',
                'value' => '102',
                'export' => 'Faculty load & leave pack',
            ],
            'lms' => [
                'label' => 'LMS',
                'donut' => 'High Engagement',
                'score' => 61,
                'trend' => [48, 52, 57, 61],
                'metric' => 'Active LMS Users',
                'value' => '2,104',
                'export' => 'LMS engagement & completion pack',
            ],
            'cocurricular' => [
                'label' => 'Co-Curricular',
                'donut' => 'Active Clubs',
                'score' => 24,
                'trend' => [18, 20, 22, 24],
                'metric' => 'Registered Clubs',
                'value' => '24',
                'export' => 'Club activity & budget pack',
            ],
            'accreditation' => [
                'label' => 'Accreditation',
                'donut' => 'Evidence Ready',
                'score' => 118,
                'trend' => [90, 100, 110, 118],
                'metric' => 'Compliance Items',
                'value' => '142',
                'export' => 'Compliance & audit findings pack',
            ],
        ];
    }
}

if (!function_exists('smsBuildSharedAccountDashboard')) {
    function smsBuildSharedAccountDashboard(string $kind, string $roleKey, array $profile): array
    {
        $allowed = function_exists('smsAllowedModuleKeysForRole')
            ? smsAllowedModuleKeysForRole($roleKey)
            : getAllowedModuleKeys();
        $kpis = smsAccountModuleKpis();
        $modules = [];

        foreach ($allowed as $mod) {
            if (isset($kpis[$mod])) {
                $modules[] = $mod;
            }
        }

        if ($modules === []) {
            $modules = array_slice(array_keys($kpis), 0, 4);
        }

        $colors = ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7', '#06b6d4', '#ef4444', '#84cc16', '#f97316', '#14b8a6', '#64748b'];
        $donutLabels = [];
        $donutValues = [];
        $horizLabels = [];
        $horizValues = [];
        $summary = [];
        $bar = [0, 0, 0, 0];
        $groupedA = [];
        $groupedB = [];

        foreach ($modules as $mod) {
            $k = $kpis[$mod];
            $donutLabels[] = $k['label'] . ' · ' . $k['donut'];
            $donutValues[] = $k['score'];
            $horizLabels[] = $k['label'];
            $horizValues[] = $k['score'];
            $summary[] = ['metric' => $k['label'] . ' · ' . $k['metric'], 'value' => $k['value']];
            foreach ($k['trend'] as $ti => $val) {
                $bar[$ti] = ($bar[$ti] ?? 0) + (int) $val;
            }
            $groupedA[] = (int) $k['trend'][2];
            $groupedB[] = (int) $k['trend'][3];
        }

        $summary[] = ['metric' => 'Assigned Office', 'value' => $profile['office'] ?? 'Office'];
        $summary[] = ['metric' => 'Modules in this account', 'value' => (string) count($modules)];

        if ($kind === 'export-center') {
            $summary = [];
            foreach ($modules as $mod) {
                $k = $kpis[$mod];
                $summary[] = ['metric' => $k['label'] . ' export', 'value' => $k['export'] . ' · Ready'];
            }
            $summary[] = ['metric' => 'Combined office package', 'value' => 'Queued for ' . ($profile['office'] ?? 'office')];
        }

        $weekLabels = array_map(static fn(string $m): string => $kpis[$m]['label'], $modules);
        if (count($weekLabels) > 5) {
            $weekLabels = array_slice($weekLabels, 0, 5);
            $groupedA = array_slice($groupedA, 0, 5);
            $groupedB = array_slice($groupedB, 0, 5);
        }

        $moduleList = implode(', ', array_map(static fn(string $m): string => $kpis[$m]['label'], $modules));

        return [
            'subtitle' => 'Account-wide view for ' . ($profile['office'] ?? 'your office') . ' covering: ' . $moduleList . '.',
            'date_range' => 'Last 30 Days',
            'donut' => [
                'title' => $kind === 'export-center' ? 'Export Readiness by Module' : 'Key Metric by Assigned Module',
                'labels' => $donutLabels,
                'values' => $donutValues,
                'colors' => array_slice($colors, 0, max(count($donutLabels), 1)),
            ],
            'bar' => [
                'title' => $kind === 'performance-trends' ? 'Combined Module Trend' : 'Monthly Combined Activity',
                'labels' => ['Apr 26', 'May 26', 'Jun 26', 'Jul 26'],
                'values' => $bar,
                'color' => '#22c55e',
            ],
            'grouped' => [
                'title' => 'Module Snapshot · Prior vs Current',
                'labels' => $weekLabels,
                'series' => [
                    ['label' => 'Prior period', 'values' => $groupedA, 'color' => '#94a3b8'],
                    ['label' => 'Current period', 'values' => $groupedB, 'color' => '#3b82f6'],
                ],
            ],
            'horizontal' => [
                'title' => 'Module Ranking (this account)',
                'labels' => $horizLabels,
                'values' => $horizValues,
                'color' => '#3b82f6',
            ],
            'summary' => $summary,
        ];
    }
}
