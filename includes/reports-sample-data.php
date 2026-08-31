<?php
/**
 * SMS 2 - Example datasets for Reports & Analytics pages
 */

if (!function_exists('smsReportSampleDataset')) {
    function smsReportSampleDataset(string $slug, ?string $roleKey = null): array
    {
        $roleKey = $roleKey ?: getCurrentUserRoleKey();
        $profile = smsRoleReportProfile($roleKey);
        $map = smsReportRoleModuleMap()[$roleKey] ?? smsReportRoleModuleMap()['admin'];

        $sharedSummary = [
            'intro' => 'Sample executive summary built from ' . $map['modules'] . '.',
            'table_title' => 'Office KPI Snapshot',
            'highlights' => [
                ['label' => 'Primary KPI', 'value' => $profile['metrics'][0]['value'] ?? '—', 'note' => $profile['metrics'][0]['delta'] ?? ''],
                ['label' => 'Secondary KPI', 'value' => $profile['metrics'][1]['value'] ?? '—', 'note' => $profile['metrics'][1]['delta'] ?? ''],
                ['label' => 'Watch Item', 'value' => $profile['metrics'][2]['value'] ?? '—', 'note' => $profile['metrics'][2]['delta'] ?? ''],
                ['label' => 'Support Metric', 'value' => $profile['metrics'][3]['value'] ?? '—', 'note' => $profile['metrics'][3]['delta'] ?? ''],
            ],
            'bars' => [
                ['label' => $profile['charts'][0] ?? 'Metric A', 'value' => '82%', 'pct' => 82],
                ['label' => $profile['charts'][1] ?? 'Metric B', 'value' => '67%', 'pct' => 67],
                ['label' => $profile['charts'][2] ?? 'Metric C', 'value' => '74%', 'pct' => 74],
                ['label' => $profile['charts'][3] ?? 'Metric D', 'value' => '59%', 'pct' => 59],
            ],
            'insights' => [
                'Overall office performance is within the target band for the active term.',
                'Exception queues remain manageable; prioritize items marked Watch/Open.',
                'Export the briefing pack before the weekly office meeting.',
            ],
            'columns' => ['KPI', 'Current', 'Previous', 'Variance', 'Owner'],
            'rows' => [
                [$profile['metrics'][0]['label'] ?? 'KPI 1', $profile['metrics'][0]['value'] ?? '—', 'Baseline', $profile['metrics'][0]['delta'] ?? '—', $map['owner']],
                [$profile['metrics'][1]['label'] ?? 'KPI 2', $profile['metrics'][1]['value'] ?? '—', 'Baseline', $profile['metrics'][1]['delta'] ?? '—', $map['owner']],
                [$profile['metrics'][2]['label'] ?? 'KPI 3', $profile['metrics'][2]['value'] ?? '—', 'Baseline', $profile['metrics'][2]['delta'] ?? '—', $map['owner']],
                [$profile['metrics'][3]['label'] ?? 'KPI 4', $profile['metrics'][3]['value'] ?? '—', 'Baseline', $profile['metrics'][3]['delta'] ?? '—', $map['owner']],
            ],
        ];

        $catalog = [
            'performance-trends' => [
                'intro' => 'Sample period-over-period trend pack for ' . $map['owner'] . '.',
                'table_title' => 'Trend Series (Example)',
                'highlights' => [
                    ['label' => 'Rising series', 'value' => '2', 'note' => '+ vs last period'],
                    ['label' => 'Watch series', 'value' => '1', 'note' => 'Needs review'],
                    ['label' => 'Stable series', 'value' => '1', 'note' => 'On target'],
                    ['label' => 'Window', 'value' => '30 days', 'note' => 'Sample range'],
                ],
                'bars' => $sharedSummary['bars'],
                'insights' => [
                    'Week-over-week movement is mostly positive for the primary office metric.',
                    'One watch series dipped below the internal threshold — investigate source module records.',
                    'Keep the same comparison window when exporting charts for supervisors.',
                ],
                'columns' => ['Series', 'This Period', 'Last Period', 'Change', 'Status'],
                'rows' => [
                    [$profile['charts'][0] ?? 'Series A', '82%', '76%', '+6 pts', 'Rising'],
                    [$profile['charts'][1] ?? 'Series B', '67%', '71%', '-4 pts', 'Watch'],
                    [$profile['charts'][2] ?? 'Series C', '74%', '73%', '+1 pt', 'Stable'],
                    [$profile['charts'][3] ?? 'Series D', '59%', '54%', '+5 pts', 'Rising'],
                ],
            ],
            'export-center' => [
                'intro' => 'Sample export catalog limited to datasets from ' . $map['modules'] . '.',
                'table_title' => 'Export Queue (Example)',
                'highlights' => [
                    ['label' => 'Ready packs', 'value' => '3', 'note' => 'Downloadable'],
                    ['label' => 'Queued', 'value' => '1', 'note' => 'Generating'],
                    ['label' => 'Last export', 'value' => 'Today', 'note' => 'Sample'],
                    ['label' => 'Formats', 'value' => 'CSV/XLSX/PDF', 'note' => 'Office standard'],
                ],
                'bars' => [
                    ['label' => 'CSV usage', 'value' => '48%', 'pct' => 48],
                    ['label' => 'XLSX usage', 'value' => '31%', 'pct' => 31],
                    ['label' => 'PDF usage', 'value' => '21%', 'pct' => 21],
                    ['label' => 'Audit retention', 'value' => '100%', 'pct' => 100],
                ],
                'insights' => [
                    'Only role-scoped columns are included in generated packages.',
                    'Keep an export remark for audit trail before releasing downloads.',
                    'Large workbooks may take a few seconds to queue in demo mode.',
                ],
                'columns' => ['Package', 'Format', 'Rows', 'Status', 'Generated'],
                'rows' => [
                    ['Primary office summary', 'CSV', '1,240', 'Ready', 'Jul 20, 2026'],
                    ['Detailed workbook', 'XLSX', '3,812', 'Queued', 'Jul 20, 2026'],
                    ['Signed briefing', 'PDF', '12 pages', 'Ready', 'Jul 19, 2026'],
                    ['Audit export log', 'CSV', '86', 'Archived', 'Jul 15, 2026'],
                ],
            ],
            'enrollment-analytics' => [
                'intro' => 'Sample enrollment funnel from Online Pre-registration through validation and sectioning.',
                'table_title' => 'Enrollment Funnel by Program (Example)',
                'highlights' => [
                    ['label' => 'Pre-registered', 'value' => '1,486', 'note' => '+8.1%'],
                    ['label' => 'Validated', 'value' => '1,204', 'note' => '81% convert'],
                    ['label' => 'Waiting list', 'value' => '47', 'note' => '-5.4%'],
                    ['label' => 'Cross-enroll flags', 'value' => '9', 'note' => 'Needs check'],
                ],
                'bars' => [
                    ['label' => 'CCS', 'value' => '312', 'pct' => 88],
                    ['label' => 'CBA', 'value' => '278', 'pct' => 79],
                    ['label' => 'Education', 'value' => '241', 'pct' => 68],
                    ['label' => 'Criminology', 'value' => '198', 'pct' => 56],
                ],
                'insights' => [
                    'CCS shows the strongest pre-registration to validation conversion this term.',
                    'Waiting-list pressure is highest in popular morning sections.',
                    'Parent notification coverage is above 90% for validated enrollees.',
                ],
                'columns' => ['Program', 'Pre-reg', 'Validated', 'Waiting', 'Conversion'],
                'rows' => [
                    ['College of Computer Studies', '354', '312', '11', '88%'],
                    ['College of Business Administration', '321', '278', '9', '87%'],
                    ['College of Education', '286', '241', '14', '84%'],
                    ['College of Criminology', '242', '198', '8', '82%'],
                    ['CHTM', '283', '175', '5', '62%'],
                ],
            ],
            'student-records-report' => [
                'intro' => 'Sample student records completeness from SIS, academic history, and digital file storage.',
                'table_title' => 'Record Completeness by Cohort (Example)',
                'highlights' => [
                    ['label' => 'Active records', 'value' => '2,893', 'note' => '+2.1%'],
                    ['label' => 'Incomplete files', 'value' => '61', 'note' => 'Needs follow-up'],
                    ['label' => 'Status updates', 'value' => '34', 'note' => 'This week'],
                    ['label' => 'Transcript jobs', 'value' => '18', 'note' => 'In queue'],
                ],
                'bars' => [
                    ['label' => 'Profile complete', 'value' => '94%', 'pct' => 94],
                    ['label' => 'Guardian contact', 'value' => '91%', 'pct' => 91],
                    ['label' => 'Digital files', 'value' => '87%', 'pct' => 87],
                    ['label' => 'Health log', 'value' => '82%', 'pct' => 82],
                ],
                'insights' => [
                    'Freshmen cohort has the most missing digital file uploads.',
                    'Status tracker exceptions are mostly transfer and returning students.',
                    'Transcript jobs are within the 2-day Registrar SLA in this sample.',
                ],
                'columns' => ['Cohort', 'Records', 'Complete', 'Incomplete', 'Owner'],
                'rows' => [
                    ['Incoming Freshmen', '812', '764', '48', 'Records Unit'],
                    ['Continuing', '1,640', '1,631', '9', 'Records Unit'],
                    ['Transferees', '221', '217', '4', 'Registrar Staff'],
                    ['Graduating', '220', '220', '0', 'Records Unit'],
                ],
            ],
            'document-release-analytics' => [
                'intro' => 'Sample document request turnaround from Document Requests and Transcript Management.',
                'table_title' => 'Document Release Log (Example)',
                'highlights' => [
                    ['label' => 'Open requests', 'value' => '28', 'note' => '+3 today'],
                    ['label' => 'Released today', 'value' => '16', 'note' => 'On track'],
                    ['label' => 'Avg turnaround', 'value' => '2.1 days', 'note' => '-0.3 day'],
                    ['label' => 'Overdue', 'value' => '5', 'note' => 'Escalate'],
                ],
                'bars' => [
                    ['label' => 'TOR', 'value' => '38%', 'pct' => 38],
                    ['label' => 'Certificates', 'value' => '29%', 'pct' => 29],
                    ['label' => 'CTC', 'value' => '21%', 'pct' => 21],
                    ['label' => 'Other', 'value' => '12%', 'pct' => 12],
                ],
                'insights' => [
                    'TOR requests drive most of the open queue volume.',
                    'Same-day releases are highest for Certificate of Enrollment.',
                    'Five overdue CTCs need Records Unit follow-up before cut-off.',
                ],
                'columns' => ['Request No.', 'Document', 'Student', 'Age', 'Status'],
                'rows' => [
                    ['DOC-2026-118', 'Transcript of Records', 'Dela Cruz, Ana', '3 days', 'Processing'],
                    ['DOC-2026-117', 'Certificate of Enrollment', 'Santos, Mark', '0 day', 'Released'],
                    ['DOC-2026-116', 'Good Moral Certificate', 'Reyes, Carla', '1 day', 'Queued'],
                    ['DOC-2026-115', 'Certified True Copy', 'Lopez, Juan', '6 days', 'Overdue'],
                    ['DOC-2026-114', 'Transcript of Records', 'Garcia, Mia', '2 days', 'Processing'],
                ],
            ],
            'curriculum-analytics' => [
                'intro' => 'Sample curriculum coverage from Curriculum Builder, Subject Mapping, and CHED/DepEd Validator.',
                'table_title' => 'Curriculum Health by Program (Example)',
                'highlights' => [
                    ['label' => 'Active curricula', 'value' => '18', 'note' => 'Versions'],
                    ['label' => 'Subjects mapped', 'value' => '426', 'note' => '+12'],
                    ['label' => 'Prereq gaps', 'value' => '11', 'note' => 'Watch'],
                    ['label' => 'Pending validate', 'value' => '4', 'note' => 'Queue'],
                ],
                'bars' => [
                    ['label' => 'Mapped subjects', 'value' => '94%', 'pct' => 94],
                    ['label' => 'Prereq complete', 'value' => '87%', 'pct' => 87],
                    ['label' => 'Electives ok', 'value' => '81%', 'pct' => 81],
                    ['label' => 'CHED validated', 'value' => '78%', 'pct' => 78],
                ],
                'insights' => [
                    'Math sequence prerequisites still show the largest gap count.',
                    'BSIT 2024 curriculum map is fully validated in this sample.',
                    'Four programs remain in the CHED/DepEd validator queue.',
                ],
                'columns' => ['Program', 'Subjects', 'Prereq Gaps', 'Electives', 'Status'],
                'rows' => [
                    ['BSIT', '64', '2', '8', 'Validated'],
                    ['BSBA', '58', '3', '6', 'Watch'],
                    ['BSED', '52', '1', '5', 'Validated'],
                    ['BSCrim', '49', '4', '4', 'Pending'],
                    ['BSHM', '47', '1', '7', 'Validated'],
                ],
            ],
            'class-schedule-analytics' => [
                'intro' => 'Sample class schedule utilization from Section Assignment, Conflict Checker, and Room Availability.',
                'table_title' => 'Schedule Conflicts & Utilization (Example)',
                'highlights' => [
                    ['label' => 'Active sections', 'value' => '186', 'note' => '+5'],
                    ['label' => 'Conflicts', 'value' => '6', 'note' => 'Open'],
                    ['label' => 'Rooms used', 'value' => '54', 'note' => '82%'],
                    ['label' => 'Exam blocks', 'value' => '28', 'note' => 'Ready'],
                ],
                'bars' => [
                    ['label' => 'Teacher conflicts', 'value' => '3', 'pct' => 50],
                    ['label' => 'Room conflicts', 'value' => '2', 'pct' => 33],
                    ['label' => 'Section overlaps', 'value' => '1', 'pct' => 17],
                    ['label' => 'Rooms free peak', 'value' => '18%', 'pct' => 18],
                ],
                'insights' => [
                    'Most open conflicts are concentrated in CCS morning blocks.',
                    'Lab 3 utilization is near capacity on Tue/Thu.',
                    'Midterm exam timetable blocks are ready for publish in this sample.',
                ],
                'columns' => ['Ref', 'Item', 'Type', 'Owner', 'Status'],
                'rows' => [
                    ['SCH-AN-031', 'CCS 101 vs CCS 110', 'Teacher conflict', 'Scheduling Desk', 'Open'],
                    ['SCH-AN-030', 'Lab 3 overload', 'Room usage', 'Facilities', 'Watch'],
                    ['SCH-AN-029', 'Midterm Block A', 'Exam timetable', 'Scheduling Desk', 'Ready'],
                    ['SCH-AN-028', 'Sub cover — CBA', 'Substitute', 'Dept Chair', 'Synced'],
                ],
            ],
            'research-proposal-analytics' => [
                'intro' => 'Sample proposal pipeline from CRAD Proposal Submission & Tracking.',
                'table_title' => 'Proposal Pipeline by College (Example)',
                'highlights' => [
                    ['label' => 'Submitted', 'value' => '24', 'note' => '+4'],
                    ['label' => 'Under review', 'value' => '7', 'note' => 'Active'],
                    ['label' => 'Approved', 'value' => '11', 'note' => '+2'],
                    ['label' => 'Returned', 'value' => '3', 'note' => 'For revise'],
                ],
                'bars' => [
                    ['label' => 'CCS', 'value' => '8', 'pct' => 90],
                    ['label' => 'CBA', 'value' => '6', 'pct' => 70],
                    ['label' => 'Education', 'value' => '5', 'pct' => 55],
                    ['label' => 'Criminology', 'value' => '5', 'pct' => 50],
                ],
                'insights' => [
                    'CCS leads proposal volume this cycle; evaluator load is highest there.',
                    'Returned titles mostly need methodology clarifications.',
                    'Average review turnaround in this sample is about 4 working days.',
                ],
                'columns' => ['Ref', 'Title', 'College', 'Stage', 'Updated'],
                'rows' => [
                    ['SUB-2026-024', 'IoT Flood Monitoring', 'CCS', 'Under Review', 'Jul 20, 2026'],
                    ['SUB-2026-023', 'Micro-Enterprise Marketing', 'CBA', 'Approved', 'Jul 18, 2026'],
                    ['SUB-2026-022', 'Mental Health Literacy', 'Education', 'Returned', 'Jul 17, 2026'],
                    ['SUB-2026-021', 'Solid Waste Awareness', 'Criminology', 'Submitted', 'Jul 16, 2026'],
                    ['SUB-2026-020', 'Hospitality Service Quality', 'CHTM', 'Approved', 'Jul 14, 2026'],
                ],
            ],
            'adviser-grants-report' => [
                'intro' => 'Sample combined view of Adviser Assignment and Research Grants queues.',
                'table_title' => 'Adviser & Grants Queue (Example)',
                'highlights' => [
                    ['label' => 'Pending advisers', 'value' => '5', 'note' => 'For match'],
                    ['label' => 'Assigned groups', 'value' => '18', 'note' => 'Active'],
                    ['label' => 'Grant apps', 'value' => '9', 'note' => 'In evaluation'],
                    ['label' => 'Funded', 'value' => '4', 'note' => 'Released'],
                ],
                'bars' => [
                    ['label' => 'Adviser capacity used', 'value' => '76%', 'pct' => 76],
                    ['label' => 'Expertise match rate', 'value' => '88%', 'pct' => 88],
                    ['label' => 'Grants approved', 'value' => '44%', 'pct' => 44],
                    ['label' => 'Docs complete', 'value' => '81%', 'pct' => 81],
                ],
                'insights' => [
                    'Five approved titles still await adviser matching this week.',
                    'Seed grant evaluations are concentrated in CCS and CBA proposals.',
                    'Do not assign advisers beyond the approved maximum load.',
                ],
                'columns' => ['Ref', 'Track', 'Item', 'Owner', 'Status'],
                'rows' => [
                    ['ADV-2026-014', 'Adviser', 'CCS Group A match', 'CRAD Officer', 'For Assignment'],
                    ['GRN-2026-009', 'Grant', 'IoT seed funding', 'Grants Panel', 'Evaluation'],
                    ['ADV-2026-013', 'Adviser', 'CBA group confirm', 'CRAD Officer', 'Assigned'],
                    ['GRN-2026-008', 'Grant', 'Publication support', 'Grants Panel', 'Funded'],
                ],
            ],
            'publication-repository-report' => [
                'intro' => 'Sample publication and repository catalog from CRAD documentation modules.',
                'table_title' => 'Publication & Repository Items (Example)',
                'highlights' => [
                    ['label' => 'Manuscripts', 'value' => '14', 'note' => 'In pipeline'],
                    ['label' => 'For publication', 'value' => '6', 'note' => 'Editing'],
                    ['label' => 'Repository items', 'value' => '128', 'note' => '+6'],
                    ['label' => 'Collaborations', 'value' => '8', 'note' => 'Active'],
                ],
                'bars' => [
                    ['label' => 'Open access', 'value' => '54%', 'pct' => 54],
                    ['label' => 'Embargoed', 'value' => '22%', 'pct' => 22],
                    ['label' => 'Internal only', 'value' => '18%', 'pct' => 18],
                    ['label' => 'Metadata complete', 'value' => '91%', 'pct' => 91],
                ],
                'insights' => [
                    'Repository deposits are growing steadily for Capstone outputs.',
                    'Two manuscripts still lack keywords/abstract metadata.',
                    'Collaboration MoAs should be linked before public release.',
                ],
                'columns' => ['Ref', 'Item', 'Type', 'Access', 'Status'],
                'rows' => [
                    ['PUB-2026-006', 'Journal manuscript — CCS', 'Publication', 'Internal', 'Editing'],
                    ['REP-2026-128', 'Capstone deposit', 'Repository', 'Open', 'Published'],
                    ['COL-2026-008', 'External partner MoA', 'Collaboration', 'Internal', 'Active'],
                    ['PUB-2026-005', 'Conference paper pack', 'Publication', 'Embargo', 'Queued'],
                ],
            ],
            'collections-analytics' => [
                'intro' => 'Sample collections performance from Payment Collection Portal and online payments.',
                'table_title' => 'Collections by Channel (Example)',
                'highlights' => [
                    ['label' => 'Collected MTD', 'value' => '₱1.20M', 'note' => '+8.4%'],
                    ['label' => 'Online share', 'value' => '41%', 'note' => '+3 pts'],
                    ['label' => 'Pending posting', 'value' => '37', 'note' => 'Reconcile'],
                    ['label' => 'Discrepancies', 'value' => '4', 'note' => 'Open'],
                ],
                'bars' => [
                    ['label' => 'Cashier', 'value' => '₱620K', 'pct' => 52],
                    ['label' => 'Online', 'value' => '₱492K', 'pct' => 41],
                    ['label' => 'Bank deposit', 'value' => '₱88K', 'pct' => 7],
                    ['label' => 'Posted ratio', 'value' => '97%', 'pct' => 97],
                ],
                'insights' => [
                    'Online payments continue to climb versus cashier window volume.',
                    'Four unmatched OR entries need cashier reconciliation today.',
                    'Scholarship offsets are already reflected in the posted ledger sample.',
                ],
                'columns' => ['Batch', 'Channel', 'Amount', 'OR Count', 'Status'],
                'rows' => [
                    ['COL-AN-031', 'Cashier', '₱182,450', '64', 'Posted'],
                    ['COL-AN-030', 'Online', '₱156,900', '51', 'Reconciling'],
                    ['COL-AN-029', 'Scholarship offset', '₱42,000', '18', 'Posted'],
                    ['COL-AN-028', 'Bank deposit', '₱28,500', '7', 'Open'],
                ],
            ],
            'receivables-report' => [
                'intro' => 'Sample receivables aging from Accounts Receivable Management.',
                'table_title' => 'Receivables Aging (Example)',
                'highlights' => [
                    ['label' => 'Open AR', 'value' => '₱486K', 'note' => 'Current book'],
                    ['label' => '0-30 days', 'value' => '₱210K', 'note' => '43%'],
                    ['label' => '31-60 days', 'value' => '₱152K', 'note' => '31%'],
                    ['label' => '61+ days', 'value' => '₱124K', 'note' => 'Escalate'],
                ],
                'bars' => [
                    ['label' => '0-30 days', 'value' => '43%', 'pct' => 43],
                    ['label' => '31-60 days', 'value' => '31%', 'pct' => 31],
                    ['label' => '61-90 days', 'value' => '16%', 'pct' => 16],
                    ['label' => '90+ days', 'value' => '10%', 'pct' => 10],
                ],
                'insights' => [
                    'Most overdue balances sit with continuing students on installment plans.',
                    'Penalty assessment batch is queued for accounts past 60 days.',
                    'Prioritize 61+ day escalations before the next billing cycle.',
                ],
                'columns' => ['Student / Account', 'Program', 'Balance', 'Bucket', 'Status'],
                'rows' => [
                    ['2024-001234 · Dela Cruz', 'CCS', '₱12,800', '0-30', 'Current'],
                    ['2023-000891 · Santos', 'CBA', '₱18,450', '31-60', 'Follow-up'],
                    ['2022-000455 · Reyes', 'Education', '₱24,100', '61-90', 'Escalate'],
                    ['2023-001102 · Lopez', 'Criminology', '₱9,750', '90+', 'Penalty due'],
                ],
            ],
            'faculty-load-report' => [
                'intro' => 'Sample faculty teaching load from Subject Load Tracker and Schedule Assignment.',
                'table_title' => 'Faculty Load by Department (Example)',
                'highlights' => [
                    ['label' => 'Faculty count', 'value' => '102', 'note' => '+1'],
                    ['label' => 'Overload', 'value' => '7', 'note' => 'Review'],
                    ['label' => 'Underload', 'value' => '4', 'note' => 'Open'],
                    ['label' => 'Avg units', 'value' => '18.4', 'note' => 'On policy'],
                ],
                'bars' => [
                    ['label' => 'CCS', 'value' => '21.2 u', 'pct' => 88],
                    ['label' => 'CBA', 'value' => '18.1 u', 'pct' => 75],
                    ['label' => 'Education', 'value' => '17.4 u', 'pct' => 72],
                    ['label' => 'Criminology', 'value' => '16.8 u', 'pct' => 70],
                ],
                'insights' => [
                    'CCS shows the highest average units and most overload cases.',
                    'Part-time underload cases need department chair confirmation.',
                    'Schedule conflict loads should be cleared with Scheduling liaison.',
                ],
                'columns' => ['Faculty', 'Dept', 'Units', 'Sections', 'Flag'],
                'rows' => [
                    ['Dr. Santos, R.', 'CCS', '24', '5', 'Overload'],
                    ['Prof. Reyes, C.', 'CBA', '18', '4', 'OK'],
                    ['Dr. Mendoza, A.', 'Education', '15', '3', 'Underload'],
                    ['Prof. Cruz, J.', 'Criminology', '21', '4', 'Watch'],
                ],
            ],
            'leave-evaluation-analytics' => [
                'intro' => 'Sample leave and evaluation analytics from Faculty Management.',
                'table_title' => 'Leave & Evaluation Summary (Example)',
                'highlights' => [
                    ['label' => 'Pending leave', 'value' => '8', 'note' => 'For approval'],
                    ['label' => 'On leave today', 'value' => '5', 'note' => 'Covered'],
                    ['label' => 'Avg evaluation', 'value' => '4.2', 'note' => '+0.1'],
                    ['label' => 'Clearance open', 'value' => '3', 'note' => 'Blockers'],
                ],
                'bars' => [
                    ['label' => 'Eval 4.5-5.0', 'value' => '28%', 'pct' => 28],
                    ['label' => 'Eval 4.0-4.4', 'value' => '46%', 'pct' => 46],
                    ['label' => 'Eval 3.5-3.9', 'value' => '19%', 'pct' => 19],
                    ['label' => 'Below 3.5', 'value' => '7%', 'pct' => 7],
                ],
                'insights' => [
                    'Sick leave applications spiked during Week 29 in this sample.',
                    'Most faculty remain in the 4.0–4.4 evaluation band.',
                    'Three open clearance items may block leave or payroll actions.',
                ],
                'columns' => ['Faculty', 'Leave Type', 'Days', 'Eval Score', 'Status'],
                'rows' => [
                    ['Santos, R.', 'Sick', '2', '4.5', 'Approved'],
                    ['Reyes, C.', 'Personal', '1', '4.1', 'Pending'],
                    ['Mendoza, A.', 'Official', '3', '4.3', 'Approved'],
                    ['Cruz, J.', 'Sick', '1', '3.8', 'Pending'],
                ],
            ],
            'lms-engagement-report' => [
                'intro' => 'Sample LMS engagement from Class Portal and lesson material activity.',
                'table_title' => 'Class Engagement Snapshot (Example)',
                'highlights' => [
                    ['label' => 'Active users', 'value' => '2,104', 'note' => '+7.3%'],
                    ['label' => 'Active classes', 'value' => '148', 'note' => '+5.6%'],
                    ['label' => 'Material views', 'value' => '9.4K', 'note' => 'MTD'],
                    ['label' => 'Low-engagement', 'value' => '22', 'note' => 'Watch'],
                ],
                'bars' => [
                    ['label' => 'High engagement', 'value' => '41%', 'pct' => 41],
                    ['label' => 'Medium', 'value' => '44%', 'pct' => 44],
                    ['label' => 'Low', 'value' => '15%', 'pct' => 15],
                    ['label' => 'Daily login peak', 'value' => '78%', 'pct' => 78],
                ],
                'insights' => [
                    'Login peaks occur on Monday and Wednesday evenings in this sample.',
                    'Twenty-two classes fall under the low-engagement threshold.',
                    'Feedback/comments volume correlates with higher material views.',
                ],
                'columns' => ['Class', 'Instructor', 'Active students', 'Views', 'Band'],
                'rows' => [
                    ['CCS 101 · Sec A', 'Prof. Santos', '42', '860', 'High'],
                    ['CBA 210 · Sec B', 'Prof. Reyes', '38', '540', 'Medium'],
                    ['EDUC 120 · Sec C', 'Dr. Mendoza', '35', '210', 'Low'],
                    ['CRIM 150 · Sec A', 'Prof. Cruz', '40', '610', 'Medium'],
                ],
            ],
            'module-completion-analytics' => [
                'intro' => 'Sample module/assignment completion mix across LMS activities.',
                'table_title' => 'Completion by Activity Type (Example)',
                'highlights' => [
                    ['label' => 'Avg completion', 'value' => '68%', 'note' => '+2.4%'],
                    ['label' => 'Pending submits', 'value' => '312', 'note' => '-4.1%'],
                    ['label' => 'On-time rate', 'value' => '74%', 'note' => 'Stable'],
                    ['label' => 'At-risk classes', 'value' => '11', 'note' => 'Below 60%'],
                ],
                'bars' => [
                    ['label' => 'Assignments done', 'value' => '71%', 'pct' => 71],
                    ['label' => 'Quizzes done', 'value' => '76%', 'pct' => 76],
                    ['label' => 'Modules done', 'value' => '64%', 'pct' => 64],
                    ['label' => 'On-time submits', 'value' => '74%', 'pct' => 74],
                ],
                'insights' => [
                    'Quiz completion outperforms assignment completion in this sample.',
                    'Eleven sections are below the 60% completion threshold.',
                    'Overdue submissions are concentrated in GE subjects.',
                ],
                'columns' => ['Class', 'Assignments', 'Quizzes', 'Modules', 'Risk'],
                'rows' => [
                    ['CCS 101 · Sec A', '82%', '88%', '79%', 'Low'],
                    ['CBA 210 · Sec B', '70%', '74%', '66%', 'Medium'],
                    ['EDUC 120 · Sec C', '52%', '58%', '49%', 'High'],
                    ['CRIM 150 · Sec A', '68%', '72%', '63%', 'Medium'],
                ],
            ],
            'club-activity-report' => [
                'intro' => 'Sample club membership and event activity from OSA Co-Curricular modules.',
                'table_title' => 'Club Activity Summary (Example)',
                'highlights' => [
                    ['label' => 'Registered clubs', 'value' => '24', 'note' => '+1'],
                    ['label' => 'Active members', 'value' => '876', 'note' => '+4.8%'],
                    ['label' => 'Events this month', 'value' => '9', 'note' => '+3'],
                    ['label' => 'Attendance gaps', 'value' => '3', 'note' => 'Follow-up'],
                ],
                'bars' => [
                    ['label' => 'Academic clubs', 'value' => '34%', 'pct' => 34],
                    ['label' => 'Cultural', 'value' => '26%', 'pct' => 26],
                    ['label' => 'Sports', 'value' => '22%', 'pct' => 22],
                    ['label' => 'Interest', 'value' => '18%', 'pct' => 18],
                ],
                'insights' => [
                    'Membership renewals are mostly complete for academic clubs.',
                    'Three events still have incomplete attendance logs.',
                    'Inactive club flags should be cleared before the next OSA audit.',
                ],
                'columns' => ['Club', 'Category', 'Members', 'Events', 'Status'],
                'rows' => [
                    ['CCS Society', 'Academic', '96', '2', 'Active'],
                    ['Dance Troupe', 'Cultural', '54', '1', 'Active'],
                    ['Basketball Varsity', 'Sports', '28', '3', 'Active'],
                    ['Photography Club', 'Interest', '41', '0', 'Watch'],
                ],
            ],
            'volunteer-budget-analytics' => [
                'intro' => 'Sample volunteer hours and activity budget requests under OSA.',
                'table_title' => 'Volunteer Hours & Budget Requests (Example)',
                'highlights' => [
                    ['label' => 'Volunteer hours', 'value' => '1,240', 'note' => 'Verified'],
                    ['label' => 'Budget requests', 'value' => '6', 'note' => 'This month'],
                    ['label' => 'Approved budget', 'value' => '₱86K', 'note' => 'Released'],
                    ['label' => 'Pending review', 'value' => '2', 'note' => 'Open'],
                ],
                'bars' => [
                    ['label' => 'Verified hours', 'value' => '88%', 'pct' => 88],
                    ['label' => 'Unverified hours', 'value' => '12%', 'pct' => 12],
                    ['label' => 'Budget approved', 'value' => '67%', 'pct' => 67],
                    ['label' => 'Budget pending', 'value' => '33%', 'pct' => 33],
                ],
                'insights' => [
                    'Community outreach accounts for most verified volunteer hours.',
                    'Cultural night budget is still under OSA officer review.',
                    'Unverified hour batches should be cleared weekly.',
                ],
                'columns' => ['Ref', 'Item', 'Club / Unit', 'Amount / Hours', 'Status'],
                'rows' => [
                    ['OSA-VH-027', 'Community outreach', 'NSO', '320 hrs', 'Verified'],
                    ['OSA-BG-006', 'Cultural night budget', 'Dance Troupe', '₱28,000', 'Review'],
                    ['OSA-BG-005', 'Intramurals funds', 'Sports Council', '₱35,000', 'Approved'],
                    ['OSA-VH-026', 'Unverified hour batch', 'CCS Society', '48 hrs', 'Pending'],
                ],
            ],
            'accreditation-compliance-report' => [
                'intro' => 'Sample accreditation compliance matrix from QA document repository.',
                'table_title' => 'Compliance by Criterion (Example)',
                'highlights' => [
                    ['label' => 'Compliance items', 'value' => '142', 'note' => '+6.2%'],
                    ['label' => 'Evidence ready', 'value' => '118', 'note' => '83%'],
                    ['label' => 'Gaps', 'value' => '17', 'note' => 'Action needed'],
                    ['label' => 'Action plans', 'value' => '9', 'note' => 'In progress'],
                ],
                'bars' => [
                    ['label' => 'Criterion I', 'value' => '92%', 'pct' => 92],
                    ['label' => 'Criterion II', 'value' => '85%', 'pct' => 85],
                    ['label' => 'Criterion III', 'value' => '78%', 'pct' => 78],
                    ['label' => 'Criterion IV', 'value' => '81%', 'pct' => 81],
                ],
                'insights' => [
                    'Faculty profile evidence still has the largest gap count.',
                    'Facilities action plan is on track for the September visit sample date.',
                    'Mark evidence complete only after repository metadata is validated.',
                ],
                'columns' => ['Criterion', 'Items', 'Ready', 'Gaps', 'Owner'],
                'rows' => [
                    ['I · Vision & Mission', '18', '17', '1', 'QA Officer'],
                    ['II · Faculty', '34', '28', '6', 'Program Chair'],
                    ['III · Curriculum', '29', '25', '4', 'Academic Affairs'],
                    ['IV · Support Services', '26', '22', '4', 'OSA Liaison'],
                ],
            ],
            'audit-findings-analytics' => [
                'intro' => 'Sample internal quality audit findings and CAPA progress.',
                'table_title' => 'Audit Findings Register (Example)',
                'highlights' => [
                    ['label' => 'Open findings', 'value' => '7', 'note' => 'Active'],
                    ['label' => 'Major', 'value' => '2', 'note' => 'Priority'],
                    ['label' => 'Minor', 'value' => '5', 'note' => 'Tracked'],
                    ['label' => 'Closed MTD', 'value' => '4', 'note' => 'Done'],
                ],
                'bars' => [
                    ['label' => 'Open', 'value' => '39%', 'pct' => 39],
                    ['label' => 'CAPA', 'value' => '28%', 'pct' => 28],
                    ['label' => 'Monitoring', 'value' => '17%', 'pct' => 17],
                    ['label' => 'Closed', 'value' => '16%', 'pct' => 16],
                ],
                'insights' => [
                    'Document control lag remains the top open major finding.',
                    'Closed findings this month are mostly checklist-type minors.',
                    'Assign CAPA due dates before the next internal audit schedule.',
                ],
                'columns' => ['Finding No.', 'Description', 'Severity', 'Owner', 'Status'],
                'rows' => [
                    ['AUD-2026-014', 'Document control lag', 'Major', 'Process Owner', 'Open'],
                    ['AUD-2026-013', 'Faculty dossier incomplete', 'Major', 'Program Chair', 'CAPA'],
                    ['AUD-2026-012', 'Lab safety checklist', 'Minor', 'Lab Custodian', 'Closed'],
                    ['AUD-2026-011', 'Student support SLA', 'Minor', 'OSA Liaison', 'Monitoring'],
                ],
            ],
        ];

        return $catalog[$slug] ?? [
            'intro' => 'Sample analytics content scoped to ' . $map['owner'] . '.',
            'table_title' => 'Sample Records',
            'highlights' => [
                ['label' => 'Records', 'value' => '24', 'note' => 'Example'],
                ['label' => 'Ready', 'value' => '18', 'note' => '75%'],
                ['label' => 'Review', 'value' => '4', 'note' => 'Watch'],
                ['label' => 'Open', 'value' => '2', 'note' => 'Action'],
            ],
            'bars' => [
                ['label' => 'Completed', 'value' => '75%', 'pct' => 75],
                ['label' => 'In progress', 'value' => '17%', 'pct' => 17],
                ['label' => 'Blocked', 'value' => '8%', 'pct' => 8],
                ['label' => 'Coverage', 'value' => '100%', 'pct' => 100],
            ],
            'insights' => [
                'This is demo content for your role-scoped report page.',
                'Replace sample rows with live module data when the database is connected.',
            ],
            'columns' => ['Reference', 'Item', 'Owner', 'Status', 'Updated'],
            'rows' => [
                ['RPT-001', ucwords(str_replace('-', ' ', $slug)), $map['owner'], 'Ready', 'Jul 20, 2026'],
                ['RPT-002', 'Secondary sample row', $map['owner'], 'Review', 'Jul 19, 2026'],
            ],
        ];
    }
}
