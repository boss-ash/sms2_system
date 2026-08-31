<?php
/**
 * SMS 2 – Glass analytics board (staff dashboard)
 * Expects: $roleKey, $statCards, $visibleModules
 */

// Self-contained bootstrap. In the normal dashboard/index.php include flow the
// variables below are already set, so the null-coalescing defaults never
// override them. This only covers direct access to this file (which previously
// emitted "undefined variable" warnings and a fatal getCurrentUserName() error).
if (!function_exists('getCurrentUserRoleKey')) {
    require_once __DIR__ . '/../includes/authentication.php';
}
$roleKey        = $roleKey        ?? (function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '');
$visibleModules = $visibleModules ?? [];
$statCards      = $statCards      ?? [];

$perfColors = ['blue', 'purple', 'green', 'orange'];
$sourceLegend = [
    ['label' => 'BSIT', 'pct' => '21%', 'color' => '#3b82f6'],
    ['label' => 'BSBA', 'pct' => '19%', 'color' => '#8b5cf6'],
    ['label' => 'BSEd', 'pct' => '17%', 'color' => '#22c55e'],
    ['label' => 'BSN', 'pct' => '14%', 'color' => '#f59e0b'],
    ['label' => 'Others', 'pct' => '29%', 'color' => '#06b6d4'],
];
$donutCenterValue = '2,893';
$donutCenterLabel = 'Students';
$trendBig = '₱98,750';
$trendDelta = '+8.2%';
$trendTitle = 'Collections overview';
$trendSub = 'Month-to-date revenue trend';
$sourceTitle = 'Students by program';
$sourceSub = 'Distribution across programs';
$inflow = '₱98,750';
$outflow = '₱63,250';
$netFlow = '+35.6%';

$tableRows = [
    ['name' => 'Ana Reyes', 'initial' => 'A', 'role' => 'Registrar', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '2 min ago'],
    ['name' => 'Carlo Lim', 'initial' => 'C', 'role' => 'Faculty', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '12 min ago'],
    ['name' => 'Mia Santos', 'initial' => 'M', 'role' => 'Finance', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '28 min ago'],
    ['name' => 'Jon Cruz', 'initial' => 'J', 'role' => 'IT Office', 'status' => 'offline', 'statusLabel' => 'Offline', 'when' => '1 hr ago'],
    ['name' => 'Liza Ong', 'initial' => 'L', 'role' => 'OSA', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '2 hr ago'],
];

$progressItems = [
    ['label' => 'Enrollment completion', 'pct' => 72, 'tone' => 'blue'],
    ['label' => 'Document processing', 'pct' => 58, 'tone' => 'green'],
    ['label' => 'Payment collection', 'pct' => 84, 'tone' => 'orange'],
    ['label' => 'Schedule conflicts cleared', 'pct' => 41, 'tone' => 'red'],
    ['label' => 'LMS module progress', 'pct' => 89, 'tone' => 'gradient'],
];

$activities = [
    ['icon' => 'fa-user-plus', 'tone' => 'blue', 'text' => 'New student enrolled in BSIT', 'when' => '2 min ago'],
    ['icon' => 'fa-file-alt', 'tone' => 'orange', 'text' => 'Transcript request submitted', 'when' => '9 min ago'],
    ['icon' => 'fa-peso-sign', 'tone' => 'green', 'text' => 'Tuition payment recorded', 'when' => '18 min ago'],
    ['icon' => 'fa-chalkboard-teacher', 'tone' => 'purple', 'text' => 'Faculty schedule updated', 'when' => '34 min ago'],
    ['icon' => 'fa-exclamation-circle', 'tone' => 'red', 'text' => 'Overdue account flagged', 'when' => '1 hr ago'],
];

$badges = [
    ['icon' => 'fa-star', 'class' => 'b1', 'label' => 'New', 'state' => 'Unlocked'],
    ['icon' => 'fa-shield-alt', 'class' => 'b2', 'label' => 'Active', 'state' => 'Unlocked'],
    ['icon' => 'fa-fire', 'class' => 'b3', 'label' => 'Popular', 'state' => 'Unlocked'],
    ['icon' => 'fa-crown', 'class' => 'b4', 'label' => 'Premium', 'state' => 'Unlocked'],
    ['icon' => 'fa-user-shield', 'class' => 'b5', 'label' => 'Admin', 'state' => 'Unlocked'],
];

$tableTitle = 'Active staff';
$tableSub = 'Recent presence across offices';
$progressTitle = 'Progress overview';
$progressSub = 'Operational completion rates';
$pipelineTitle = 'Cash flow';
$pipelineSub = 'Inflow vs outflow this month';
$pipelineInLabel = 'Total inflow';
$pipelineOutLabel = 'Total outflow';
$pipelineGaugeLabel = 'Net flow';
$activityTitle = 'Recent activity';
$activitySub = 'Latest system events';
$dashboardIntro = 'Live institutional performance board.';

if (smsIsGrantedAdminRole($roleKey)) {
    $sourceTitle = 'Access by module';
    $sourceSub = 'Visible workspaces for this account';
    $donutCenterValue = (string) max(1, count($visibleModules));
    $donutCenterLabel = 'Modules';
    $trendTitle = 'Security activity';
    $trendSub = 'Audit events across the system';
    $trendBig = '186';
    $trendDelta = '+10.4%';
    $tableTitle = 'System accounts';
    $tableSub = 'Recent administrative presence';
    $tableRows = [
        ['name' => 'Super Admin', 'initial' => 'S', 'role' => 'Super Admin', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'Registrar', 'initial' => 'R', 'role' => 'Registrar', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '12 min ago'],
        ['name' => 'Finance', 'initial' => 'F', 'role' => 'Finance', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '28 min ago'],
        ['name' => 'CRAD Officer', 'initial' => 'C', 'role' => 'CRAD', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '42 min ago'],
        ['name' => 'IT Office', 'initial' => 'I', 'role' => 'IT Office', 'status' => 'offline', 'statusLabel' => 'Offline', 'when' => '1 hr ago'],
    ];
    $progressTitle = 'Module security';
    $progressSub = 'Administrative readiness by workspace';
    $progressItems = [
        ['label' => 'User account review', 'pct' => 92, 'tone' => 'blue'],
        ['label' => 'Role permission audit', 'pct' => 86, 'tone' => 'green'],
        ['label' => 'Module maintenance checks', 'pct' => 74, 'tone' => 'orange'],
        ['label' => 'Password reset queue', 'pct' => 48, 'tone' => 'red'],
        ['label' => 'Activity log coverage', 'pct' => 95, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Account health';
    $pipelineSub = 'Active vs locked accounts this month';
    $pipelineInLabel = 'Active';
    $pipelineOutLabel = 'Locked';
    $inflow = '12';
    $outflow = '2';
    $netFlow = '85.7%';
    $pipelineGaugeLabel = 'Healthy';
    $activityTitle = 'Recent admin activity';
    $activitySub = 'Latest account and permission events';
    $dashboardIntro = 'Live account, security, and module administration board.';
} elseif ($roleKey === 'admission') {
    $sourceTitle = 'Applicants by program';
    $sourceSub = 'Pre-registration demand';
    $donutCenterValue = '86';
    $donutCenterLabel = 'Applicants';
    $sourceLegend = [
        ['label' => 'BSIT', 'pct' => '30%', 'color' => '#3b82f6'],
        ['label' => 'BSBA', 'pct' => '21%', 'color' => '#8b5cf6'],
        ['label' => 'Education', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Criminology', 'pct' => '16%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '15%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Validation trend';
    $trendSub = 'Applicants processed this month';
    $trendBig = '54';
    $trendDelta = '+16.2%';
    $tableTitle = 'Admission queue';
    $tableSub = 'Applicant processing workload';
    $tableRows = [
        ['name' => 'Online Pre-registration', 'initial' => 'P', 'role' => 'Registration', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'Document Upload Portal', 'initial' => 'D', 'role' => 'Requirements', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '8 min ago'],
        ['name' => 'Enrollment Validation', 'initial' => 'V', 'role' => 'Validation', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '18 min ago'],
        ['name' => 'Waiting List Queue', 'initial' => 'W', 'role' => 'Queue', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '35 min ago'],
        ['name' => 'Parent Notification', 'initial' => 'N', 'role' => 'Communication', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
    ];
    $progressTitle = 'Enrollment progress';
    $progressSub = 'Admission process completion rates';
    $progressItems = [
        ['label' => 'Pre-registration review', 'pct' => 76, 'tone' => 'blue'],
        ['label' => 'Document verification', 'pct' => 64, 'tone' => 'green'],
        ['label' => 'Validation decisions', 'pct' => 58, 'tone' => 'orange'],
        ['label' => 'Section placement', 'pct' => 46, 'tone' => 'red'],
        ['label' => 'Parent notification', 'pct' => 83, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Admission pipeline';
    $pipelineSub = 'Validated vs pending applicants';
    $pipelineInLabel = 'Validated';
    $pipelineOutLabel = 'Pending';
    $inflow = '54';
    $outflow = '31';
    $netFlow = '63.5%';
    $pipelineGaugeLabel = 'Validated';
    $dashboardIntro = 'Live admission and enrollment performance board.';
} elseif ($roleKey === 'registrar') {
    $sourceTitle = 'Records by workspace';
    $sourceSub = 'Registrar, curriculum, and scheduling load';
    $donutCenterValue = '2,893';
    $donutCenterLabel = 'Records';
    $sourceLegend = [
        ['label' => 'Student Records', 'pct' => '42%', 'color' => '#3b82f6'],
        ['label' => 'Documents', 'pct' => '18%', 'color' => '#8b5cf6'],
        ['label' => 'Curriculum', 'pct' => '16%', 'color' => '#22c55e'],
        ['label' => 'Scheduling', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '10%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Records trend';
    $trendSub = 'Registrar transactions this month';
    $trendBig = '1,204';
    $trendDelta = '+6.1%';
    $tableTitle = 'Registrar workspaces';
    $tableSub = 'Student records and academic operations';
    $tableRows = [
        ['name' => 'Student Information System', 'initial' => 'S', 'role' => 'Records', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'Document Requests', 'initial' => 'D', 'role' => 'Documents', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '9 min ago'],
        ['name' => 'Transcript Management', 'initial' => 'T', 'role' => 'Academic Records', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '24 min ago'],
        ['name' => 'Curriculum Builder', 'initial' => 'C', 'role' => 'Curriculum', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '39 min ago'],
        ['name' => 'Class Schedule', 'initial' => 'Q', 'role' => 'Scheduling', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
    ];
    $progressTitle = 'Academic operations';
    $progressSub = 'Registrar module completion rates';
    $progressItems = [
        ['label' => 'Student record updates', 'pct' => 84, 'tone' => 'blue'],
        ['label' => 'Document release', 'pct' => 69, 'tone' => 'green'],
        ['label' => 'Transcript preparation', 'pct' => 58, 'tone' => 'orange'],
        ['label' => 'Curriculum validation', 'pct' => 63, 'tone' => 'gradient'],
        ['label' => 'Schedule conflicts cleared', 'pct' => 47, 'tone' => 'red'],
    ];
    $pipelineTitle = 'Records pipeline';
    $pipelineSub = 'Released vs pending requests';
    $pipelineInLabel = 'Released';
    $pipelineOutLabel = 'Pending';
    $inflow = '72';
    $outflow = '28';
    $netFlow = '72%';
    $pipelineGaugeLabel = 'Released';
    $dashboardIntro = 'Live registrar, curriculum, and scheduling performance board.';
} elseif ($roleKey === 'finance') {
    $sourceTitle = 'Collections by fee type';
    $sourceSub = 'Share of month-to-date intake';
    $donutCenterValue = '₱1.2M';
    $donutCenterLabel = 'Collected';
    $sourceLegend = [
        ['label' => 'Tuition', 'pct' => '68%', 'color' => '#3b82f6'],
        ['label' => 'Misc', 'pct' => '12%', 'color' => '#8b5cf6'],
        ['label' => 'Lab', 'pct' => '8%', 'color' => '#22c55e'],
        ['label' => 'Registration', 'pct' => '7%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '5%', 'color' => '#06b6d4'],
    ];
    $dashboardIntro = 'Live finance and payment management board.';
} elseif ($roleKey === 'hr') {
    $sourceTitle = 'Faculty by department';
    $sourceSub = 'Headcount distribution';
    $donutCenterValue = '102';
    $donutCenterLabel = 'Faculty';
    $trendTitle = 'Leave applications';
    $trendSub = 'Monthly leave trend';
    $trendBig = '48';
    $trendDelta = '+6.1%';
    $tableTitle = 'Faculty workload';
    $tableSub = 'Dean office monitoring';
    $progressTitle = 'Faculty services';
    $progressSub = 'Faculty process completion rates';
    $pipelineTitle = 'Leave pipeline';
    $pipelineSub = 'Approved vs pending requests';
    $pipelineInLabel = 'Approved';
    $pipelineOutLabel = 'Pending';
    $inflow = '18';
    $outflow = '8';
    $netFlow = '69.2%';
    $pipelineGaugeLabel = 'Cleared';
    $dashboardIntro = 'Live dean and faculty management board.';
} elseif ($roleKey === 'it_office') {
    $sourceTitle = 'LMS completion mix';
    $sourceSub = 'Module completion by subject group';
    $donutCenterValue = '68%';
    $donutCenterLabel = 'Avg complete';
    $trendTitle = 'LMS activity';
    $trendSub = 'Logins over the term';
    $trendBig = '2,104';
    $trendDelta = '+7.3%';
    $tableTitle = 'LMS workspaces';
    $tableSub = 'Active classes and learning items';
    $progressTitle = 'Learning progress';
    $progressSub = 'LMS module completion rates';
    $pipelineTitle = 'LMS pipeline';
    $pipelineSub = 'Completed vs pending submissions';
    $pipelineInLabel = 'Completed';
    $pipelineOutLabel = 'Pending';
    $inflow = '812';
    $outflow = '312';
    $netFlow = '72.2%';
    $pipelineGaugeLabel = 'Complete';
    $dashboardIntro = 'Live online learning and LMS performance board.';
} elseif ($roleKey === 'osa') {
    $sourceTitle = 'Activities by type';
    $sourceSub = 'Student affairs and co-curricular load';
    $donutCenterValue = '24';
    $donutCenterLabel = 'Clubs';
    $sourceLegend = [
        ['label' => 'Clubs', 'pct' => '34%', 'color' => '#3b82f6'],
        ['label' => 'Events', 'pct' => '24%', 'color' => '#8b5cf6'],
        ['label' => 'Attendance', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Budgets', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Volunteers', 'pct' => '10%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Activity trend';
    $trendSub = 'Events and club updates this month';
    $trendBig = '876';
    $trendDelta = '+4.8%';
    $tableTitle = 'OSA workspaces';
    $tableSub = 'Club and activity operations';
    $progressTitle = 'Co-curricular progress';
    $progressSub = 'Student affairs completion rates';
    $pipelineTitle = 'Activity pipeline';
    $pipelineSub = 'Approved vs pending activities';
    $pipelineInLabel = 'Approved';
    $pipelineOutLabel = 'Pending';
    $inflow = '18';
    $outflow = '6';
    $netFlow = '75%';
    $pipelineGaugeLabel = 'Approved';
    $dashboardIntro = 'Live student affairs and co-curricular board.';
} elseif ($roleKey === 'qa') {
    $sourceTitle = 'Compliance by area';
    $sourceSub = 'Accreditation evidence readiness';
    $donutCenterValue = '142';
    $donutCenterLabel = 'Items';
    $sourceLegend = [
        ['label' => 'Documents', 'pct' => '32%', 'color' => '#3b82f6'],
        ['label' => 'Criteria', 'pct' => '24%', 'color' => '#8b5cf6'],
        ['label' => 'Programs', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Facilities', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Findings', 'pct' => '12%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Compliance trend';
    $trendSub = 'Evidence and audit updates';
    $trendBig = '142';
    $trendDelta = '+6.2%';
    $tableTitle = 'QA workspaces';
    $tableSub = 'Accreditation and quality operations';
    $progressTitle = 'Accreditation progress';
    $progressSub = 'Compliance completion rates';
    $pipelineTitle = 'Audit pipeline';
    $pipelineSub = 'Compliant vs findings';
    $pipelineInLabel = 'Compliant';
    $pipelineOutLabel = 'Findings';
    $inflow = '118';
    $outflow = '7';
    $netFlow = '94.4%';
    $pipelineGaugeLabel = 'Ready';
    $dashboardIntro = 'Live quality assurance and accreditation board.';
} elseif ($roleKey === 'crad_officer') {
    $sourceTitle = 'Proposals by college';
    $sourceSub = 'Title submissions across departments';
    $donutCenterValue = '24';
    $donutCenterLabel = 'Proposals';
    $sourceLegend = [
        ['label' => 'CCS', 'pct' => '32%', 'color' => '#3b82f6'],
        ['label' => 'CBA', 'pct' => '22%', 'color' => '#8b5cf6'],
        ['label' => 'Education', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Criminology', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '14%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Submission trend';
    $trendSub = 'Title proposals this month';
    $trendBig = '24';
    $trendDelta = '+12.5%';
    $tableTitle = 'Active advisers';
    $tableSub = 'Research adviser assignments';
    $tableRows = [
        ['name' => 'Dr. Roberto Santos', 'initial' => 'R', 'role' => 'Research Adviser', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '5 min ago'],
        ['name' => 'Prof. Clara Reyes', 'initial' => 'C', 'role' => 'Program Coordinator', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '18 min ago'],
        ['name' => 'Dr. Ana Mendoza', 'initial' => 'A', 'role' => 'Research Adviser', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '40 min ago'],
        ['name' => 'Prof. Joel Cruz', 'initial' => 'J', 'role' => 'Research Adviser', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
        ['name' => 'CRAD Desk', 'initial' => 'C', 'role' => 'CRAD Officer', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
    ];
    $progressTitle = 'Module progress';
    $progressSub = 'CRAD process completion rates';
    $progressItems = [
        ['label' => 'Proposal screening', 'pct' => 78, 'tone' => 'blue'],
        ['label' => 'Adviser assignment', 'pct' => 65, 'tone' => 'green'],
        ['label' => 'Grants evaluation', 'pct' => 52, 'tone' => 'orange'],
        ['label' => 'Publication endorsement', 'pct' => 41, 'tone' => 'red'],
        ['label' => 'Repository cataloguing', 'pct' => 88, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Approval pipeline';
    $pipelineSub = 'Approved vs for-revision this month';
    $pipelineInLabel = 'Approved titles';
    $pipelineOutLabel = 'For revision';
    $inflow = '11';
    $outflow = '5';
    $netFlow = '68.8%';
    $pipelineGaugeLabel = 'Approval rate';
    $activityTitle = 'Recent CRAD activity';
    $activitySub = 'Latest research office events';
    $activities = [
        ['icon' => 'fa-file-alt', 'tone' => 'blue', 'text' => 'New title proposal submitted from CCS', 'when' => '4 min ago'],
        ['icon' => 'fa-user-tie', 'tone' => 'purple', 'text' => 'Adviser assigned to SUB-2026-004', 'when' => '22 min ago'],
        ['icon' => 'fa-check-circle', 'tone' => 'green', 'text' => 'Title approved: Flood Monitoring Study', 'when' => '1 hr ago'],
        ['icon' => 'fa-hand-holding-usd', 'tone' => 'orange', 'text' => 'Grant application under evaluation', 'when' => '2 hr ago'],
        ['icon' => 'fa-archive', 'tone' => 'blue', 'text' => 'Study archived in research repository', 'when' => '3 hr ago'],
    ];
    $badges = [
        ['icon' => 'fa-clipboard-list', 'class' => 'b1', 'label' => 'Tracking', 'state' => 'Active'],
        ['icon' => 'fa-user-tie', 'class' => 'b2', 'label' => 'Advisers', 'state' => 'Active'],
        ['icon' => 'fa-hand-holding-usd', 'class' => 'b3', 'label' => 'Grants', 'state' => 'Active'],
        ['icon' => 'fa-book', 'class' => 'b4', 'label' => 'Publish', 'state' => 'Active'],
        ['icon' => 'fa-archive', 'class' => 'b5', 'label' => 'Repository', 'state' => 'Active'],
    ];
    $dashboardIntro = 'Live CRAD research performance board.';
} elseif ($roleKey === 'research_coordinator') {
    $sourceTitle = 'Assignments by college';
    $sourceSub = 'Approved research coordination load';
    $donutCenterValue = '18';
    $donutCenterLabel = 'Approved';
    $sourceLegend = [
        ['label' => 'CCS', 'pct' => '34%', 'color' => '#3b82f6'],
        ['label' => 'CBA', 'pct' => '22%', 'color' => '#8b5cf6'],
        ['label' => 'Education', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Criminology', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '12%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Assignment trend';
    $trendSub = 'Adviser matching activity';
    $trendBig = '12';
    $trendDelta = '+9.4%';
    $tableTitle = 'Coordination queue';
    $tableSub = 'Research coordinator workflow';
    $tableRows = [
        ['name' => 'Approved Research', 'initial' => 'A', 'role' => 'Research List', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'Find/Contact Adviser', 'initial' => 'F', 'role' => 'Adviser Matching', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '10 min ago'],
        ['name' => 'Check Adviser Availability', 'initial' => 'C', 'role' => 'Availability', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '22 min ago'],
        ['name' => 'Send Notifications', 'initial' => 'S', 'role' => 'Communication', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '46 min ago'],
        ['name' => 'Manage Assignments', 'initial' => 'M', 'role' => 'Monitoring', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
    ];
    $progressTitle = 'Coordinator progress';
    $progressSub = 'Research assignment completion rates';
    $progressItems = [
        ['label' => 'Approved research review', 'pct' => 82, 'tone' => 'blue'],
        ['label' => 'Adviser availability checks', 'pct' => 68, 'tone' => 'green'],
        ['label' => 'Assignment routing', 'pct' => 61, 'tone' => 'orange'],
        ['label' => 'Notification completion', 'pct' => 74, 'tone' => 'gradient'],
        ['label' => 'Open follow-ups', 'pct' => 43, 'tone' => 'red'],
    ];
    $pipelineTitle = 'Assignment pipeline';
    $pipelineSub = 'Assigned vs pending adviser matches';
    $pipelineInLabel = 'Assigned';
    $pipelineOutLabel = 'Pending';
    $inflow = '12';
    $outflow = '6';
    $netFlow = '66.7%';
    $pipelineGaugeLabel = 'Assigned';
    $dashboardIntro = 'Live research coordination board.';
} elseif ($roleKey === 'research_grant') {
    $sourceTitle = 'Funding by grant type';
    $sourceSub = 'Research grant application mix';
    $donutCenterValue = '16';
    $donutCenterLabel = 'Applications';
    $sourceLegend = [
        ['label' => 'Equipment', 'pct' => '35%', 'color' => '#3b82f6'],
        ['label' => 'Fieldwork', 'pct' => '24%', 'color' => '#8b5cf6'],
        ['label' => 'Publication', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Prototype', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Others', 'pct' => '9%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Grant trend';
    $trendSub = 'Applications and releases this month';
    $trendBig = 'PHP 486K';
    $trendDelta = '+11%';
    $tableTitle = 'Grant queue';
    $tableSub = 'Funding management workload';
    $tableRows = [
        ['name' => 'Grant Applications', 'initial' => 'G', 'role' => 'Applications', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'For Evaluation', 'initial' => 'E', 'role' => 'Evaluation', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '14 min ago'],
        ['name' => 'Pending Approvals', 'initial' => 'P', 'role' => 'Approval', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '38 min ago'],
        ['name' => 'Fund Release', 'initial' => 'F', 'role' => 'Disbursement', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
        ['name' => 'Project Milestones', 'initial' => 'M', 'role' => 'Monitoring', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '2 hr ago'],
    ];
    $progressTitle = 'Grant progress';
    $progressSub = 'Funding workflow completion rates';
    $progressItems = [
        ['label' => 'Application screening', 'pct' => 71, 'tone' => 'blue'],
        ['label' => 'Evaluation scoring', 'pct' => 57, 'tone' => 'orange'],
        ['label' => 'Approval routing', 'pct' => 49, 'tone' => 'red'],
        ['label' => 'Fund release', 'pct' => 62, 'tone' => 'green'],
        ['label' => 'Milestone monitoring', 'pct' => 78, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Funding pipeline';
    $pipelineSub = 'Funded vs for-evaluation this month';
    $pipelineInLabel = 'Funded';
    $pipelineOutLabel = 'For evaluation';
    $inflow = '9';
    $outflow = '7';
    $netFlow = '56.3%';
    $pipelineGaugeLabel = 'Funded';
    $dashboardIntro = 'Live research grant and funding board.';
} elseif (in_array($roleKey, ['adviser', 'panel'], true)) {
    $sourceTitle = 'Research load by status';
    $sourceSub = 'Faculty research assignments';
    $donutCenterValue = '8';
    $donutCenterLabel = 'Assigned';
    $sourceLegend = [
        ['label' => 'Active', 'pct' => '38%', 'color' => '#3b82f6'],
        ['label' => 'For Review', 'pct' => '24%', 'color' => '#8b5cf6'],
        ['label' => 'Defense', 'pct' => '18%', 'color' => '#22c55e'],
        ['label' => 'Revision', 'pct' => '12%', 'color' => '#f59e0b'],
        ['label' => 'Archived', 'pct' => '8%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Review trend';
    $trendSub = 'Faculty research activity this month';
    $trendBig = '11';
    $trendDelta = '+8.0%';
    $tableTitle = $roleKey === 'panel' ? 'Panel workload' : 'Adviser workload';
    $tableSub = 'Assigned research and defense tasks';
    $tableRows = [
        ['name' => 'Assigned Research', 'initial' => 'A', 'role' => 'Research', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'Research Documents', 'initial' => 'D', 'role' => 'Documents', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '12 min ago'],
        ['name' => 'Research Progress', 'initial' => 'P', 'role' => 'Monitoring', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '34 min ago'],
        ['name' => 'Defense Schedule', 'initial' => 'S', 'role' => 'Defense', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
        ['name' => 'Approved Research', 'initial' => 'R', 'role' => 'Archive', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '2 hr ago'],
    ];
    $progressTitle = 'Faculty research progress';
    $progressSub = 'Research task completion rates';
    $progressItems = [
        ['label' => 'Document review', 'pct' => 74, 'tone' => 'blue'],
        ['label' => 'Progress checking', 'pct' => 63, 'tone' => 'green'],
        ['label' => 'Defense preparation', 'pct' => 58, 'tone' => 'orange'],
        ['label' => 'Revision follow-up', 'pct' => 45, 'tone' => 'red'],
        ['label' => 'Completed endorsements', 'pct' => 81, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Review pipeline';
    $pipelineSub = 'Completed vs pending research reviews';
    $pipelineInLabel = 'Completed';
    $pipelineOutLabel = 'Pending';
    $inflow = '11';
    $outflow = '5';
    $netFlow = '68.8%';
    $pipelineGaugeLabel = 'Reviewed';
    $dashboardIntro = 'Live faculty research assignment board.';
} elseif ($roleKey === 'research_director') {
    $sourceTitle = 'Defense by status';
    $sourceSub = 'Research defense scheduling load';
    $donutCenterValue = '14';
    $donutCenterLabel = 'Ready';
    $sourceLegend = [
        ['label' => 'Verified', 'pct' => '34%', 'color' => '#3b82f6'],
        ['label' => 'Proposed', 'pct' => '22%', 'color' => '#8b5cf6'],
        ['label' => 'Scheduled', 'pct' => '20%', 'color' => '#22c55e'],
        ['label' => 'Finalized', 'pct' => '14%', 'color' => '#f59e0b'],
        ['label' => 'Archive', 'pct' => '10%', 'color' => '#06b6d4'],
    ];
    $trendTitle = 'Defense trend';
    $trendSub = 'Scheduling activity this month';
    $trendBig = '21';
    $trendDelta = '+13.2%';
    $tableTitle = 'Defense management';
    $tableSub = 'Research director workflow';
    $tableRows = [
        ['name' => 'Verify Research for Defense', 'initial' => 'V', 'role' => 'Verification', 'status' => 'active', 'statusLabel' => 'Active', 'when' => 'Just now'],
        ['name' => 'AI Scheduling Optimizer', 'initial' => 'A', 'role' => 'Scheduling', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '11 min ago'],
        ['name' => 'Proposed Schedules', 'initial' => 'P', 'role' => 'Scheduling', 'status' => 'away', 'statusLabel' => 'Away', 'when' => '27 min ago'],
        ['name' => 'Panel Members', 'initial' => 'M', 'role' => 'Participants', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '46 min ago'],
        ['name' => 'Proceed to Archiving', 'initial' => 'R', 'role' => 'Archiving', 'status' => 'active', 'statusLabel' => 'Active', 'when' => '1 hr ago'],
    ];
    $progressTitle = 'Defense progress';
    $progressSub = 'Research defense process completion rates';
    $progressItems = [
        ['label' => 'Research verification', 'pct' => 78, 'tone' => 'blue'],
        ['label' => 'Schedule optimization', 'pct' => 66, 'tone' => 'green'],
        ['label' => 'Panel confirmation', 'pct' => 57, 'tone' => 'orange'],
        ['label' => 'Defense results', 'pct' => 44, 'tone' => 'red'],
        ['label' => 'Archiving readiness', 'pct' => 72, 'tone' => 'gradient'],
    ];
    $pipelineTitle = 'Defense pipeline';
    $pipelineSub = 'Finalized vs proposed schedules';
    $pipelineInLabel = 'Finalized';
    $pipelineOutLabel = 'Proposed';
    $inflow = '6';
    $outflow = '9';
    $netFlow = '40%';
    $pipelineGaugeLabel = 'Finalized';
    $dashboardIntro = 'Live research director defense scheduling board.';
}

require_once __DIR__ . '/period-filter.php';
$dashboardPeriodKey    = $dashboardPeriodKey ?? smsDashboardCurrentPeriod();
$dashboardPeriods      = $dashboardPeriods ?? smsDashboardPeriods();
$dashboardPeriodMeta   = $dashboardPeriodMeta ?? $dashboardPeriods[$dashboardPeriodKey];
$dashboardPeriodFactor = $dashboardPeriodFactor ?? $dashboardPeriodMeta['factor'];

$donutCenterValue = smsDashboardScaleMetricValue((string) $donutCenterValue, $dashboardPeriodFactor);
$trendBig         = smsDashboardScaleMetricValue((string) $trendBig, $dashboardPeriodFactor);
$inflow           = smsDashboardScaleMetricValue((string) $inflow, $dashboardPeriodFactor);
$outflow          = smsDashboardScaleMetricValue((string) $outflow, $dashboardPeriodFactor);
$netFlow          = smsDashboardScaleMetricValue((string) $netFlow, $dashboardPeriodFactor);
$trendDelta       = smsDashboardScaleDelta((string) $trendDelta, $dashboardPeriodFactor);
?>

<div class="dashboard-shell glass-dashboard academic-dashboard">
    <div class="page-header dashboard-page-header sms-page-header">
        <div>
            <span class="dash-kicker">Dashboard</span>
            <h1>Overview</h1>
            <p>Welcome back, <?= htmlspecialchars(getCurrentUserName()) ?>. <?= htmlspecialchars($dashboardIntro) ?></p>
        </div>
        <div class="dash-period glass-period-filter dropdown">
            <?= smsIcon('calendar-alt', ['aria-hidden' => 'true']) ?>
            <button type="button"
                    class="glass-period-btn dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="Select reporting period">
                <span><?= htmlspecialchars($dashboardPeriodMeta['sy'] . ' · ' . $dashboardPeriodMeta['label']) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end glass-period-menu">
                <?php foreach ($dashboardPeriods as $periodKey => $periodMeta): ?>
                    <li>
                        <a class="dropdown-item<?= $periodKey === $dashboardPeriodKey ? ' active' : '' ?>"
                           href="<?= htmlspecialchars(smsDashboardPeriodUrl($periodKey)) ?>">
                            <?= htmlspecialchars($periodMeta['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <section class="academic-notices-panel" aria-labelledby="academicNoticesTitle">
        <div class="academic-notices-icon" aria-hidden="true"><?= smsIcon('bullhorn') ?></div>
        <div>
            <span class="ai-insight-kicker">Academic notices</span>
            <h2 class="ai-insight-title" id="academicNoticesTitle">Workspace summary</h2>
            <p class="ai-insight-copy">Important items for <?= htmlspecialchars(getCurrentUserName()) ?> this reporting period.</p>
            <ul class="ai-insight-list">
                <li><?= htmlspecialchars($progressTitle) ?> may need follow-up before the next cycle.</li>
                <li>Check <?= htmlspecialchars(strtolower($activityTitle)) ?> for the latest institutional updates.</li>
            </ul>
        </div>
    </section>

    <div class="glass-board"
         id="glassBoard"
         data-role="<?= htmlspecialchars($roleKey) ?>"
         data-period="<?= htmlspecialchars($dashboardPeriodKey) ?>"
         data-period-factor="<?= htmlspecialchars((string) $dashboardPeriodFactor) ?>">

        <section class="glass-panel">
            <div class="glass-panel-body">
                <div class="glass-panel-head">
                    <div>
                        <h2 class="glass-panel-title">Key metrics</h2>
                        <p class="glass-panel-sub">Summary for your workspace</p>
                    </div>
                    <div class="glass-period-filter dropdown">
                        <button type="button"
                                class="glass-chip glass-chip-btn dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-label="Filter metrics by period">
                            <?= smsIcon('filter', ['aria-hidden' => 'true']) ?>
                            <span><?= htmlspecialchars($dashboardPeriodMeta['label']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-period-menu">
                            <?php foreach ($dashboardPeriods as $periodKey => $periodMeta): ?>
                                <li>
                                    <a class="dropdown-item<?= $periodKey === $dashboardPeriodKey ? ' active' : '' ?>"
                                       href="<?= htmlspecialchars(smsDashboardPeriodUrl($periodKey)) ?>">
                                        <?= htmlspecialchars($periodMeta['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="perf-grid">
                    <?php foreach ($statCards as $i => $card): ?>
                        <?php
                        $tone = $perfColors[$i % count($perfColors)];
                        $dir = $card['deltaDir'] ?? 'neutral';
                        $arrow = $dir === 'down' ? 'fa-arrow-down' : ($dir === 'up' ? 'fa-arrow-up' : 'fa-minus');
                        ?>
                        <article class="perf-item">
                            <div class="perf-icon <?= $tone ?>"><?= smsIcon($card['icon'], ['aria-hidden' => 'true']) ?></div>
                            <p class="perf-label"><?= htmlspecialchars($card['label']) ?></p>
                            <p class="perf-value"><?= htmlspecialchars($card['value']) ?></p>
                            <p class="perf-trend <?= htmlspecialchars($dir) ?>">
                                <?= smsIcon($arrow, ['aria-hidden' => 'true']) ?>
                                <?= htmlspecialchars($card['delta'] ?? '') ?>
                                <span class="perf-delta-label"><?= htmlspecialchars($card['deltaLabel'] ?? '') ?></span>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php if ($roleKey === 'crad_officer'): ?>
            <?php include __DIR__ . '/partials/grant-dashboard-metrics.php'; ?>
        <?php endif; ?>

        <?php if ($roleKey === 'crad_officer'):
            require_once __DIR__ . '/../modules/crad/config/config.php';
            require_once __DIR__ . '/../modules/crad/includes/grant-approval-helpers.php';
        ?>
        <section class="glass-panel gaw-dashboard-dock" data-crad-approval-dock="1">
            <div class="glass-panel-body">
                <div class="glass-panel-head">
                    <div>
                        <h2 class="glass-panel-title">
                            Grant Approval Workflows
                            <span class="gaw-live-badge">Live</span>
                        </h2>
                        <p class="glass-panel-sub">Same pipeline view as advisers and approvers — <?= htmlspecialchars(grantApprovalPipelineLabel()) ?></p>
                    </div>
                    <a class="glass-chip" href="<?= BASE_URL ?>/modules/crad/pages/approval-workflows.php">
                        <?= smsIcon('external-link-alt', ['aria-hidden' => 'true']) ?>
                        Open full pipeline
                    </a>
                </div>
                <div id="cradApprovalDockStats" class="gaw-stat-row" style="margin-bottom:1rem;">
                    <span class="gaw-stat pending"><strong data-gaw-in-progress>0</strong> in progress</span>
                    <span class="gaw-stat completed"><strong data-gaw-completed>0</strong> completed</span>
                </div>
                <div id="cradApprovalDockList" class="gaw-dashboard-list">
                    <p class="gaw-dashboard-empty">Loading approval workflows…</p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($visibleModules)): ?>
        <section class="glass-panel glass-quick-actions">
            <div class="glass-panel-body">
                <div class="glass-panel-head">
                    <div>
                        <h2 class="glass-panel-title">Quick actions</h2>
                        <p class="glass-panel-sub">Open a module workspace</p>
                    </div>
                </div>
                <div class="row g-3 module-grid">
                    <?php foreach ($visibleModules as $moduleKey => $module): ?>
                        <?php $moduleFolder = $moduleKey === 'student_portal' ? 'student-portal' : $moduleKey; ?>
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                            <a href="<?= BASE_URL ?>/modules/<?= htmlspecialchars($moduleFolder) ?>/index.php" class="quick-module">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <?= smsIcon($module['icon'], ['aria-hidden' => 'true']) ?>
                                        <p class="small mb-0 fw-medium"><?= htmlspecialchars($module['label']) ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <div class="glass-row glass-row-bot">
            <section class="glass-panel">
                <div class="glass-panel-body">
                    <div class="glass-panel-head">
                        <div>
                            <h2 class="glass-panel-title"><?= htmlspecialchars($activityTitle) ?></h2>
                            <p class="glass-panel-sub"><?= htmlspecialchars($activitySub) ?></p>
                        </div>
                    </div>
                    <ul class="glass-activity">
                        <?php foreach ($activities as $act): ?>
                            <li>
                                <span class="act-icon <?= htmlspecialchars($act['tone']) ?>"><?= smsIcon($act['icon'], ['aria-hidden' => 'true']) ?></span>
                                <div class="act-body"><strong><?= htmlspecialchars($act['text']) ?></strong></div>
                                <span class="act-time"><?= htmlspecialchars($act['when']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <section class="glass-panel">
                <div class="glass-panel-body">
                    <div class="glass-panel-head">
                        <div>
                            <h2 class="glass-panel-title"><?= htmlspecialchars($sourceTitle) ?></h2>
                            <p class="glass-panel-sub"><?= htmlspecialchars($sourceSub) ?></p>
                        </div>
                    </div>
                    <div class="donut-layout">
                        <div class="donut-wrap">
                            <canvas id="glassDonut" aria-label="Distribution chart"></canvas>
                            <div class="donut-center">
                                <strong><?= htmlspecialchars($donutCenterValue) ?></strong>
                                <span><?= htmlspecialchars($donutCenterLabel) ?></span>
                            </div>
                        </div>
                        <ul class="glass-legend">
                            <?php foreach ($sourceLegend as $item): ?>
                                <li>
                                    <span class="leg-left">
                                        <span class="dot" style="background:<?= htmlspecialchars($item['color']) ?>"></span>
                                        <span class="name"><?= htmlspecialchars($item['label']) ?></span>
                                    </span>
                                    <span class="pct"><?= htmlspecialchars($item['pct']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
