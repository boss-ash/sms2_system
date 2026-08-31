<?php
/**
 * SMS 2 - Accurate page UI catalogs (Payment, Faculty, LMS, OSA, Accreditation)
 */

if (!function_exists('smsPaymentPageUiCatalog')) {
    function smsPaymentPageUiCatalog(): array
    {
        return [
            'student-billing-invoicing' => smsPageUiDef(
                'Student Billing & Invoicing',
                'Create and manage student tuition invoices, miscellaneous fees, and assessment bills.',
                '+ New Invoice',
                'INV',
                smsStats4('Invoices', '5', 'fa-file-invoice', 'Unpaid', '2', 'fa-clock', 'Paid', '2', 'fa-check', 'Void', '1', 'fa-ban'),
                smsCols('Invoice No.', 'Student', 'Assessment', 'Amount', 'Issued'),
                [
                    ['Sofia Reyes · S260000101', 'Tuition 1st Sem', '₱18,500.00', 'Unpaid', 'pending', 'Invoice'],
                    ['Mark Villanueva · S260000102', 'Tuition + Misc', '₱21,200.00', 'Paid', 'completed', 'Invoice'],
                    ['Angela Cruz · S260000103', 'Tuition 1st Sem', '₱17,800.00', 'Unpaid', 'pending', 'Invoice'],
                    ['Carlos Mendoza · S230000088', 'Balance Forward', '₱6,450.00', 'Paid', 'completed', 'Invoice'],
                    ['Liza Santos · S260000105', 'Voided duplicate', '₱18,500.00', 'Void', 'cancelled', 'Invoice'],
                ],
                'invoice no., student, or amount'
            ),
            'payment-collection-portal' => smsPageUiDef(
                'Payment Collection Portal',
                'Record over-the-counter and verified payments against student invoices.',
                '+ Record Payment',
                'PAY',
                smsStats4('Payments', '5', 'fa-cash-register', 'Today', '2', 'fa-calendar-day', 'Posted', '2', 'fa-check', 'Pending', '1', 'fa-clock'),
                smsCols('OR No.', 'Student', 'Method', 'Amount', 'Collected'),
                [
                    ['Mark Villanueva', 'Cash · Window 2', '₱21,200.00', 'Posted', 'completed', 'Payment'],
                    ['Carlos Mendoza', 'GCash verified', '₱6,450.00', 'Posted', 'completed', 'Payment'],
                    ['Sofia Reyes', 'Cash · Window 1', '₱5,000.00', 'Pending', 'pending', 'Payment'],
                    ['Angela Cruz', 'Bank deposit', '₱8,900.00', 'Today', 'scheduled', 'Payment'],
                    ['Liza Santos', 'Card terminal', '₱3,000.00', 'Today', 'scheduled', 'Payment'],
                ],
                'OR no., student, or method'
            ),
            'online-payment-integration' => smsPageUiDef(
                'Online Payment Integration',
                'Monitor online payment gateway transactions, callbacks, and settlement status.',
                '+ Sync Gateway',
                'OLP',
                smsStats4('Transactions', '5', 'fa-globe', 'Settled', '2', 'fa-check', 'Pending', '2', 'fa-clock', 'Failed', '1', 'fa-times'),
                smsCols('Txn No.', 'Student', 'Gateway', 'Amount / Ref', 'Received'),
                [
                    ['Sofia Reyes', 'PayMongo', '₱5,000 · PM-88921', 'Settled', 'completed', 'Online'],
                    ['Angela Cruz', 'GCash', '₱8,900 · GC-44102', 'Pending', 'pending', 'Online'],
                    ['Mark Villanueva', 'Maya', '₱21,200 · MY-99211', 'Settled', 'completed', 'Online'],
                    ['Liza Santos', 'PayMongo', '₱3,000 · PM-88955', 'Pending', 'progress', 'Online'],
                    ['Jules Ramos', 'GCash', '₱2,500 · GC-failed', 'Failed', 'cancelled', 'Online'],
                ],
                'transaction, gateway, or reference'
            ),
            'fee-setup-configuration' => smsPageUiDef(
                'Fee Setup & Configuration',
                'Configure tuition matrices, miscellaneous fees, and program fee templates.',
                '+ New Fee Template',
                'FEE',
                smsStats4('Templates', '5', 'fa-cogs', 'Draft', '1', 'fa-pen', 'Active', '3', 'fa-check', 'Retired', '1', 'fa-archive'),
                smsCols('Fee No.', 'Fee Template', 'Program', 'Amount Set', 'Updated'),
                [
                    ['BSIT Tuition Matrix AY2526', 'BSIT', '₱18,500 / sem', 'Active', 'completed', 'Fee'],
                    ['BSED Tuition Matrix AY2526', 'BSED', '₱17,200 / sem', 'Active', 'completed', 'Fee'],
                    ['Laboratory Fee · IT Labs', 'All IT labs', '₱1,500 / subject', 'Active', 'completed', 'Fee'],
                    ['BSHM Skills Fee Draft', 'BSHM', '₱2,200 / lab', 'Draft', 'pending', 'Fee'],
                    ['Old Misc Fee 2023', 'All programs', 'Legacy schedule', 'Retired', 'cancelled', 'Fee'],
                ],
                'fee template, program, or amount'
            ),
            'discount-scholarship-application' => smsPageUiDef(
                'Discount & Scholarship Application',
                'Process scholarship, sibling discount, and financial assistance applications.',
                '+ New Scholarship App',
                'SCH',
                smsStats4('Applications', '5', 'fa-hand-holding-heart', 'For Eval', '2', 'fa-search', 'Approved', '2', 'fa-check', 'Denied', '1', 'fa-times'),
                smsCols('App No.', 'Student', 'Scholarship Type', 'Discount', 'Updated'),
                [
                    ['Sofia Reyes', 'Academic Scholar', '50% tuition', 'For Evaluation', 'pending', 'Scholarship'],
                    ['Mark Villanueva', 'Athletic Scholar', '100% tuition', 'Approved', 'completed', 'Scholarship'],
                    ['Angela Cruz', 'Sibling Discount', '10% tuition', 'Approved', 'completed', 'Scholarship'],
                    ['Liza Santos', 'Employee Dependent', '25% tuition', 'For Evaluation', 'progress', 'Scholarship'],
                    ['Jules Ramos', 'Need-based Aid', '₱5,000 grant', 'Denied', 'cancelled', 'Scholarship'],
                ],
                'student, scholarship type, or discount'
            ),
            'payment-history-ledger-system' => smsPageUiDef(
                'Payment History & Ledger System',
                'View student ledgers, payment history, and running balances.',
                '+ Open Ledger',
                'LED',
                smsStats4('Ledgers', '5', 'fa-book', 'Balanced', '3', 'fa-check', 'Outstanding', '1', 'fa-exclamation', 'Audit', '1', 'fa-search'),
                smsCols('Ledger No.', 'Student', 'Running Balance', 'Last Entry', 'Updated'),
                [
                    ['Mark Villanueva', '₱0.00', 'Full payment posted', 'Balanced', 'completed', 'Ledger'],
                    ['Carlos Mendoza', '₱0.00', 'Balance cleared', 'Balanced', 'completed', 'Ledger'],
                    ['Sofia Reyes', '₱13,500.00', 'Partial payment', 'Outstanding', 'pending', 'Ledger'],
                    ['Angela Cruz', '₱8,900.00', 'Online pending', 'Outstanding', 'progress', 'Ledger'],
                    ['Liza Santos', '₱15,500.00', 'Under audit hold', 'Audit', 'cancelled', 'Ledger'],
                ],
                'ledger, student, or balance'
            ),
            'collection-reporting-analytics' => smsPageUiDef(
                'Collection Reporting & Analytics',
                'Monitor daily collections, aging, and cashier performance summaries.',
                '+ Generate Collection Report',
                'COL',
                smsStats4('Reports', '5', 'fa-chart-line', 'Today', '2', 'fa-calendar-day', 'Ready', '2', 'fa-check', 'Queued', '1', 'fa-hourglass'),
                smsCols('Report No.', 'Report Name', 'Period', 'Collected', 'Generated'),
                [
                    ['Daily Collection · Jul 18', 'Jul 18, 2026', '₱486,200.00', 'Ready', 'completed', 'Collection'],
                    ['Cashier Window 1 Summary', 'Week 29', '₱210,450.00', 'Ready', 'completed', 'Collection'],
                    ['Online Settlement Report', 'Jul 1-18', '₱132,900.00', 'Today', 'scheduled', 'Collection'],
                    ['Aging Receivables Snapshot', 'As of Jul 18', '₱1.24M outstanding', 'Today', 'scheduled', 'Collection'],
                    ['Monthly Collection Draft', 'July 2026', 'Queued export', 'Queued', 'pending', 'Collection'],
                ],
                'report, period, or amount'
            ),
            'accounts-receivable-management' => smsPageUiDef(
                'Accounts Receivable Management',
                'Track unpaid assessments, follow-ups, and receivable aging buckets.',
                '+ New AR Follow-up',
                'AR',
                smsStats4('AR Accounts', '5', 'fa-file-invoice-dollar', 'Current', '2', 'fa-check', '30 Days', '1', 'fa-clock', '60+ Days', '2', 'fa-exclamation'),
                smsCols('AR No.', 'Student', 'Aging', 'Balance', 'Updated'),
                [
                    ['Sofia Reyes', 'Current', '₱13,500.00', 'Current', 'scheduled', 'Receivable'],
                    ['Angela Cruz', '1-30 days', '₱8,900.00', '30 Days', 'pending', 'Receivable'],
                    ['Liza Santos', '31-60 days', '₱15,500.00', '60+ Days', 'progress', 'Receivable'],
                    ['Jules Ramos', '61-90 days', '₱22,100.00', '60+ Days', 'cancelled', 'Receivable'],
                    ['Nina Lopez', 'Current', '₱4,200.00', 'Current', 'completed', 'Receivable'],
                ],
                'student, aging, or balance'
            ),
            'penalty-due-date-management' => smsPageUiDef(
                'Penalty & Due Date Management',
                'Configure due dates, late payment penalties, and installment schedules.',
                '+ New Penalty Rule',
                'PEN',
                smsStats4('Rules', '5', 'fa-calendar-times', 'Active', '3', 'fa-check', 'Draft', '1', 'fa-pen', 'Expired', '1', 'fa-archive'),
                smsCols('Rule No.', 'Penalty Rule', 'Applies To', 'Rate / Due', 'Updated'),
                [
                    ['Late Tuition Penalty', 'All undergrad', '2% / month after due', 'Active', 'completed', 'Penalty'],
                    ['Installment Due · Plan A', 'Installment payers', 'Due every 15th', 'Active', 'completed', 'Penalty'],
                    ['Misc Fee Due Date', 'Laboratory fees', 'Due within 30 days', 'Active', 'completed', 'Penalty'],
                    ['Summer Penalty Draft', 'Summer enrollees', '1.5% / month', 'Draft', 'pending', 'Penalty'],
                    ['Old Penalty 2023', 'Legacy accounts', 'Expired schedule', 'Expired', 'cancelled', 'Penalty'],
                ],
                'penalty rule, due date, or rate'
            ),
            'audit-access-control' => smsPageUiDef(
                'Audit & Access Control',
                'Review finance access logs, voided ORs, and sensitive ledger changes.',
                '+ New Audit Review',
                'AUD',
                smsStats4('Audit Items', '5', 'fa-user-shield', 'Open', '2', 'fa-folder-open', 'Closed', '2', 'fa-check', 'Critical', '1', 'fa-exclamation'),
                smsCols('Audit No.', 'Event', 'User', 'Impact', 'Logged'),
                [
                    ['OR void · Window 2', 'cashier.maria', '₱3,000 voided', 'Closed', 'completed', 'Audit'],
                    ['Ledger override', 'finance.head', 'Balance adjusted', 'Open', 'pending', 'Audit'],
                    ['Fee template edit', 'finance.staff', 'BSIT matrix change', 'Closed', 'completed', 'Audit'],
                    ['Failed login burst', 'unknown', 'Finance portal', 'Critical', 'cancelled', 'Audit'],
                    ['Export of AR list', 'auditor.qa', 'Receivables dump', 'Open', 'progress', 'Audit'],
                ],
                'audit event, user, or impact'
            ),
        ];
    }
}

if (!function_exists('smsFacultyPageUiCatalog')) {
    function smsFacultyPageUiCatalog(): array
    {
        return [
            'faculty-profile' => smsPageUiDef(
                'Faculty Profile',
                'Maintain faculty personal data, employment status, and academic credentials.',
                '+ New Faculty Profile',
                'FAC',
                smsStats4('Profiles', '5', 'fa-id-badge', 'Active', '3', 'fa-check', 'For Update', '1', 'fa-pen', 'Inactive', '1', 'fa-user-slash'),
                smsCols('Faculty No.', 'Faculty Name', 'Department', 'Status Detail', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'College of Computer Studies', 'Full-time · Active', 'Active', 'completed', 'Profile'],
                    ['Prof. Joel Cruz', 'College of Criminology', 'Full-time · Active', 'Active', 'completed', 'Profile'],
                    ['Prof. Clara Santos', 'College of Education', 'Needs credential update', 'For Update', 'pending', 'Profile'],
                    ['Prof. Mark Lim', 'College of Business', 'Full-time · Active', 'Active', 'completed', 'Profile'],
                    ['Prof. Rico Tan', 'College of Computer Studies', 'Part-time ended', 'Inactive', 'cancelled', 'Profile'],
                ],
                'faculty name, department, or status'
            ),
            'subject-load-tracker' => smsPageUiDef(
                'Subject Load Tracker',
                'Track teaching units, overload, and underload per faculty per term.',
                '+ New Load Entry',
                'LOD',
                smsStats4('Load Records', '5', 'fa-tasks', 'Normal', '2', 'fa-check', 'Overload', '2', 'fa-arrow-up', 'Underload', '1', 'fa-arrow-down'),
                smsCols('Load No.', 'Faculty', 'Subjects / Units', 'Load Status', 'Term'),
                [
                    ['Prof. Ana Reyes', 'IT101, IT201 · 18 units', 'Normal', 'Normal', 'completed', 'Load'],
                    ['Prof. Clara Santos', 'EDUC201, EDUC301, GE · 24 units', 'Overload', 'Overload', 'cancelled', 'Load'],
                    ['Prof. Joel Cruz', 'CRIM301, CRIM302 · 15 units', 'Normal', 'Normal', 'completed', 'Load'],
                    ['Prof. Mark Lim', 'ACCTG1 only · 6 units', 'Underload', 'Underload', 'pending', 'Load'],
                    ['Prof. Liza Gomez', 'HM labs · 21 units', 'Overload', 'Overload', 'progress', 'Load'],
                ],
                'faculty, units, or load status'
            ),
            'schedule-assignment' => smsPageUiDef(
                'Schedule Assignment',
                'Assign faculty to class schedules and confirm acceptance of teaching slots.',
                '+ Assign Schedule',
                'FSC',
                smsStats4('Assignments', '5', 'fa-calendar-check', 'Pending', '1', 'fa-clock', 'Accepted', '3', 'fa-check', 'Declined', '1', 'fa-times'),
                smsCols('Assign No.', 'Faculty', 'Class / Section', 'Schedule', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'IT101 · BSIT-1A', 'MWF 8:00-9:00 · R204', 'Accepted', 'completed', 'Schedule'],
                    ['Prof. Joel Cruz', 'CRIM301 · CRIM-3A', 'TTh 1:00-2:30 · R105', 'Accepted', 'completed', 'Schedule'],
                    ['Prof. Clara Santos', 'EDUC201 · BSED-2B', 'MWF 10:00-11:00 · R301', 'Pending', 'pending', 'Schedule'],
                    ['Prof. Mark Lim', 'ACCTG1 · BSBA-1C', 'TTh 9:00-10:30 · R110', 'Accepted', 'completed', 'Schedule'],
                    ['Prof. Rico Tan', 'IT Elective', 'Sat conflict', 'Declined', 'cancelled', 'Schedule'],
                ],
                'faculty, section, or schedule'
            ),
            'attendance-monitoring' => smsPageUiDef(
                'Attendance Monitoring',
                'Monitor faculty daily attendance, late logs, and absence filings.',
                '+ Log Attendance',
                'ATT',
                smsStats4('Logs', '5', 'fa-user-check', 'Present', '3', 'fa-check', 'Late', '1', 'fa-clock', 'Absent', '1', 'fa-user-times'),
                smsCols('Log No.', 'Faculty', 'Date', 'Time In / Out', 'Logged'),
                [
                    ['Prof. Ana Reyes', 'Jul 18, 2026', '07:45 · 16:10', 'Present', 'completed', 'Attendance'],
                    ['Prof. Joel Cruz', 'Jul 18, 2026', '08:20 · 15:55', 'Late', 'pending', 'Attendance'],
                    ['Prof. Mark Lim', 'Jul 18, 2026', '07:50 · 16:00', 'Present', 'completed', 'Attendance'],
                    ['Prof. Clara Santos', 'Jul 18, 2026', 'On official leave', 'Absent', 'cancelled', 'Attendance'],
                    ['Prof. Liza Gomez', 'Jul 18, 2026', '07:40 · 16:05', 'Present', 'completed', 'Attendance'],
                ],
                'faculty, date, or attendance status'
            ),
            'leave-application-approval' => smsPageUiDef(
                'Leave Application & Approval',
                'Receive, validate, and approve faculty leave applications.',
                '+ New Leave Application',
                'LVE',
                smsStats4('Applications', '5', 'fa-plane-departure', 'For Approval', '2', 'fa-clock', 'Approved', '2', 'fa-check', 'Denied', '1', 'fa-times'),
                smsCols('Leave No.', 'Faculty', 'Leave Type', 'Inclusive Dates', 'Updated'),
                [
                    ['Prof. Clara Santos', 'Sick Leave', 'Jul 18-19, 2026', 'Approved', 'completed', 'Leave'],
                    ['Prof. Rico Tan', 'Personal Leave', 'Jul 22, 2026', 'For Approval', 'pending', 'Leave'],
                    ['Prof. Liza Gomez', 'Emergency Leave', 'Jul 16, 2026', 'Approved', 'completed', 'Leave'],
                    ['Prof. Mark Lim', 'Official Business', 'Jul 25, 2026', 'For Approval', 'progress', 'Leave'],
                    ['Prof. Joel Cruz', 'Vacation Leave', 'Aug 1-5, 2026', 'Denied', 'cancelled', 'Leave'],
                ],
                'faculty, leave type, or dates'
            ),
            'salary-grade-payroll-setup' => smsPageUiDef(
                'Salary Grade & Payroll Setup',
                'Maintain salary grades, rate setups, and payroll mapping for faculty.',
                '+ New Salary Setup',
                'PAYR',
                smsStats4('Setups', '5', 'fa-money-check-alt', 'Active', '3', 'fa-check', 'Draft', '1', 'fa-pen', 'For Review', '1', 'fa-search'),
                smsCols('Setup No.', 'Faculty / Grade', 'Salary Grade', 'Monthly Rate', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'SG 18', '₱46,725.00', 'Active', 'completed', 'Payroll'],
                    ['Prof. Joel Cruz', 'SG 16', '₱39,672.00', 'Active', 'completed', 'Payroll'],
                    ['Prof. Clara Santos', 'SG 17', '₱43,030.00', 'Active', 'completed', 'Payroll'],
                    ['Prof. Mark Lim', 'SG 15 draft', '₱36,628.00', 'Draft', 'pending', 'Payroll'],
                    ['Part-time Pool Rates', 'Hourly rates', 'For HR review', 'For Review', 'progress', 'Payroll'],
                ],
                'faculty, salary grade, or rate'
            ),
            'teaching-history' => smsPageUiDef(
                'Teaching History',
                'Review historical subjects taught by faculty across terms.',
                '+ Add History Entry',
                'HST',
                smsStats4('History Rows', '5', 'fa-history', 'Current', '2', 'fa-calendar', 'Past', '2', 'fa-archive', 'Missing', '1', 'fa-exclamation'),
                smsCols('Hist No.', 'Faculty', 'Subject / Term', 'Eval Score', 'Logged'),
                [
                    ['Prof. Ana Reyes', 'IT101 · 1st Sem AY2526', '4.7 / 5.0', 'Current', 'scheduled', 'History'],
                    ['Prof. Joel Cruz', 'CRIM301 · 1st Sem AY2526', '4.5 / 5.0', 'Current', 'scheduled', 'History'],
                    ['Prof. Clara Santos', 'EDUC201 · 2nd Sem AY2425', '4.8 / 5.0', 'Past', 'completed', 'History'],
                    ['Prof. Mark Lim', 'ACCTG1 · 1st Sem AY2425', '4.2 / 5.0', 'Past', 'completed', 'History'],
                    ['Prof. Rico Tan', 'IT Elective · incomplete log', 'No score', 'Missing', 'cancelled', 'History'],
                ],
                'faculty, subject, or term'
            ),
            'clearance-system' => smsPageUiDef(
                'Clearance System',
                'Process faculty clearances for end-of-term, resignation, or leave.',
                '+ New Clearance',
                'CLR',
                smsStats4('Clearances', '5', 'fa-stamp', 'In Progress', '2', 'fa-spinner', 'Cleared', '2', 'fa-check', 'Hold', '1', 'fa-hand-paper'),
                smsCols('Clearance No.', 'Faculty', 'Clearance Type', 'Pending Office', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'End of Term', 'None · Complete', 'Cleared', 'completed', 'Clearance'],
                    ['Prof. Joel Cruz', 'End of Term', 'Library', 'In Progress', 'progress', 'Clearance'],
                    ['Prof. Clara Santos', 'Leave Clearance', 'None · Complete', 'Cleared', 'completed', 'Clearance'],
                    ['Prof. Mark Lim', 'End of Term', 'Property Custodian', 'In Progress', 'pending', 'Clearance'],
                    ['Prof. Rico Tan', 'Resignation', 'Finance hold', 'Hold', 'cancelled', 'Clearance'],
                ],
                'faculty, clearance type, or office'
            ),
            'evaluation-summary' => smsPageUiDef(
                'Evaluation Summary',
                'Consolidate student and peer evaluation results for faculty performance.',
                '+ Generate Evaluation',
                'EVAL',
                smsStats4('Evaluations', '5', 'fa-star', 'Published', '3', 'fa-check', 'Draft', '1', 'fa-pen', 'Disputed', '1', 'fa-exclamation'),
                smsCols('Eval No.', 'Faculty', 'Period', 'Overall Rating', 'Published'),
                [
                    ['Prof. Ana Reyes', '1st Sem AY2526', '4.70 · Outstanding', 'Published', 'completed', 'Evaluation'],
                    ['Prof. Joel Cruz', '1st Sem AY2526', '4.50 · Very Satisfactory', 'Published', 'completed', 'Evaluation'],
                    ['Prof. Clara Santos', '1st Sem AY2526', '4.80 · Outstanding', 'Published', 'completed', 'Evaluation'],
                    ['Prof. Mark Lim', '1st Sem AY2526', '4.20 · Satisfactory', 'Draft', 'pending', 'Evaluation'],
                    ['Prof. Liza Gomez', '1st Sem AY2526', 'Score contested', 'Disputed', 'cancelled', 'Evaluation'],
                ],
                'faculty, period, or rating'
            ),
            'faculty-directory' => smsPageUiDef(
                'Faculty Directory',
                'Publish searchable faculty directory entries for offices and students.',
                '+ New Directory Entry',
                'DIR',
                smsStats4('Entries', '5', 'fa-address-book', 'Published', '3', 'fa-globe', 'Draft', '1', 'fa-pen', 'Hidden', '1', 'fa-eye-slash'),
                smsCols('Dir No.', 'Faculty', 'Department', 'Contact', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'CCS', 'ana.reyes@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Prof. Joel Cruz', 'Criminology', 'joel.cruz@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Prof. Clara Santos', 'Education', 'clara.santos@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Prof. Mark Lim', 'Business', 'mark.lim@bestlink.edu.ph', 'Draft', 'pending', 'Directory'],
                    ['Prof. Rico Tan', 'CCS', 'Hidden from portal', 'Hidden', 'cancelled', 'Directory'],
                ],
                'faculty, department, or email'
            ),
        ];
    }
}

if (!function_exists('smsLmsPageUiCatalog')) {
    function smsLmsPageUiCatalog(): array
    {
        return [
            'class-portal' => smsPageUiDef(
                'Class Portal',
                'Manage LMS class portals, enrollment sync, and classroom access.',
                '+ New Class Portal',
                'CLS',
                smsStats4('Portals', '5', 'fa-chalkboard', 'Open', '3', 'fa-door-open', 'Draft', '1', 'fa-pen', 'Closed', '1', 'fa-ban'),
                smsCols('Portal No.', 'Class Portal', 'Faculty', 'Students', 'Updated'),
                [
                    ['IT101 · BSIT-1A Portal', 'Prof. Ana Reyes', '40 students synced', 'Open', 'completed', 'Portal'],
                    ['EDUC201 · BSED-2B Portal', 'Prof. Clara Santos', '32 students synced', 'Open', 'completed', 'Portal'],
                    ['ACCTG1 · BSBA-1C Portal', 'Prof. Mark Lim', '28 students synced', 'Open', 'completed', 'Portal'],
                    ['HM102 Lab Portal', 'Prof. Liza Gomez', 'Setup incomplete', 'Draft', 'pending', 'Portal'],
                    ['CRIM301 Archive Portal', 'Prof. Joel Cruz', 'Previous term', 'Closed', 'cancelled', 'Portal'],
                ],
                'portal, faculty, or class'
            ),
            'lesson-material-upload' => smsPageUiDef(
                'Lesson Material Upload',
                'Upload and publish lesson files, modules, and reading packs to class portals.',
                '+ Upload Material',
                'MAT',
                smsStats4('Materials', '5', 'fa-file-upload', 'Published', '3', 'fa-check', 'Draft', '1', 'fa-pen', 'Rejected', '1', 'fa-times'),
                smsCols('File No.', 'Material Title', 'Class Portal', 'File Type', 'Uploaded'),
                [
                    ['Week 1 · Intro to Computing PDF', 'IT101 · BSIT-1A', 'PDF · 2.1 MB', 'Published', 'completed', 'Material'],
                    ['Module 2 · Facilitating Learning', 'EDUC201 · BSED-2B', 'DOCX · 840 KB', 'Published', 'completed', 'Material'],
                    ['Accounting Worksheet Pack', 'ACCTG1 · BSBA-1C', 'XLSX · 1.4 MB', 'Published', 'completed', 'Material'],
                    ['Kitchen Safety Slides', 'HM102 Lab', 'PPTX · draft', 'Draft', 'pending', 'Material'],
                    ['Unclear scan upload', 'CRIM301', 'JPG · low quality', 'Rejected', 'cancelled', 'Material'],
                ],
                'material, class, or file type'
            ),
            'assignment-submission' => smsPageUiDef(
                'Assignment Submission',
                'Track student assignment submissions, deadlines, and late flags.',
                '+ New Assignment',
                'ASN',
                smsStats4('Assignments', '5', 'fa-inbox', 'Open', '2', 'fa-folder-open', 'Closed', '2', 'fa-check', 'Overdue', '1', 'fa-exclamation'),
                smsCols('Assign No.', 'Assignment', 'Class', 'Submissions', 'Due'),
                [
                    ['Lab Report 1', 'IT101 · BSIT-1A', '38 / 40 submitted', 'Open', 'scheduled', 'Assignment'],
                    ['Reflection Paper', 'EDUC201 · BSED-2B', '32 / 32 submitted', 'Closed', 'completed', 'Assignment'],
                    ['Journal Entries Week 3', 'ACCTG1 · BSBA-1C', '20 / 28 submitted', 'Open', 'pending', 'Assignment'],
                    ['Knife Skills Video', 'HM102 Lab', 'Past due · 10 missing', 'Overdue', 'cancelled', 'Assignment'],
                    ['Case Digest 2', 'CRIM301 · CRIM-3A', '35 / 35 submitted', 'Closed', 'completed', 'Assignment'],
                ],
                'assignment, class, or submissions'
            ),
            'online-quiz' => smsPageUiDef(
                'Online Quiz',
                'Create and monitor timed quizzes, attempts, and auto-scoring results.',
                '+ New Quiz',
                'QZ',
                smsStats4('Quizzes', '5', 'fa-question-circle', 'Live', '2', 'fa-play', 'Closed', '2', 'fa-check', 'Draft', '1', 'fa-pen'),
                smsCols('Quiz No.', 'Quiz Title', 'Class', 'Attempts / Avg', 'Schedule'),
                [
                    ['Quiz 1 · Computing Basics', 'IT101 · BSIT-1A', '40 attempts · 82%', 'Closed', 'completed', 'Quiz'],
                    ['Quiz 2 · Learning Theories', 'EDUC201 · BSED-2B', 'Live now', 'Live', 'scheduled', 'Quiz'],
                    ['Quiz 1 · Accounting Cycle', 'ACCTG1 · BSBA-1C', 'Live · 12 taking', 'Live', 'scheduled', 'Quiz'],
                    ['Safety Pretest', 'HM102 Lab', 'Draft items', 'Draft', 'pending', 'Quiz'],
                    ['Criminal Law Quiz 1', 'CRIM301 · CRIM-3A', '35 attempts · 78%', 'Closed', 'completed', 'Quiz'],
                ],
                'quiz, class, or score'
            ),
            'virtual-classroom-integration' => smsPageUiDef(
                'Virtual Classroom Integration',
                'Connect LMS classes to Meet/Zoom sessions and attendance sync.',
                '+ Link Virtual Class',
                'VCR',
                smsStats4('Sessions', '5', 'fa-video', 'Scheduled', '2', 'fa-calendar', 'Done', '2', 'fa-check', 'Error', '1', 'fa-exclamation'),
                smsCols('Session No.', 'Virtual Class', 'Platform', 'Meeting Link Status', 'Schedule'),
                [
                    ['IT101 Live Session', 'Google Meet', 'Link active', 'Scheduled', 'scheduled', 'Virtual'],
                    ['EDUC201 Sync Class', 'Zoom', 'Completed · 30 joined', 'Done', 'completed', 'Virtual'],
                    ['ACCTG1 Review Call', 'Google Meet', 'Link active', 'Scheduled', 'scheduled', 'Virtual'],
                    ['HM102 Demo Stream', 'Zoom', 'Ended successfully', 'Done', 'completed', 'Virtual'],
                    ['CRIM301 Link Error', 'Meet', 'OAuth token expired', 'Error', 'cancelled', 'Virtual'],
                ],
                'virtual class, platform, or status'
            ),
            'grading-integration' => smsPageUiDef(
                'Grading Integration',
                'Sync LMS scores to the official grading sheet and registrar grade capture.',
                '+ Sync Grades',
                'GRD',
                smsStats4('Sync Jobs', '5', 'fa-sync', 'Synced', '3', 'fa-check', 'Pending', '1', 'fa-clock', 'Mismatch', '1', 'fa-exclamation'),
                smsCols('Sync No.', 'Class / Component', 'Destination', 'Records', 'Synced'),
                [
                    ['IT101 Quizzes → Gradebook', 'Official grading sheet', '40 records', 'Synced', 'completed', 'Grading'],
                    ['EDUC201 Portfolio → Gradebook', 'Official grading sheet', '32 records', 'Synced', 'completed', 'Grading'],
                    ['ACCTG1 Journals → Gradebook', 'Official grading sheet', '28 records', 'Pending', 'pending', 'Grading'],
                    ['CRIM301 Digests → Gradebook', 'Official grading sheet', '35 records', 'Synced', 'completed', 'Grading'],
                    ['HM102 Skills score mismatch', 'Grade capture', '5 conflicts', 'Mismatch', 'cancelled', 'Grading'],
                ],
                'class, component, or sync status'
            ),
            'feedback-comments' => smsPageUiDef(
                'Feedback & Comments',
                'Review faculty feedback threads and student comment moderation.',
                '+ New Feedback',
                'FBK',
                smsStats4('Threads', '5', 'fa-comments', 'Open', '2', 'fa-folder-open', 'Resolved', '2', 'fa-check', 'Flagged', '1', 'fa-flag'),
                smsCols('Thread No.', 'Topic', 'Class', 'Author', 'Updated'),
                [
                    ['Clarify Lab Rubric', 'IT101 · BSIT-1A', 'Sofia Reyes', 'Open', 'pending', 'Feedback'],
                    ['Great module feedback', 'EDUC201 · BSED-2B', 'Anonymous student', 'Resolved', 'completed', 'Feedback'],
                    ['Deadline extension request', 'ACCTG1 · BSBA-1C', 'Angela Cruz', 'Open', 'progress', 'Feedback'],
                    ['Helpful video comment', 'HM102 Lab', 'Liza Santos', 'Resolved', 'completed', 'Feedback'],
                    ['Inappropriate remark', 'CRIM301', 'Flagged user', 'Flagged', 'cancelled', 'Feedback'],
                ],
                'topic, class, or author'
            ),
            'module-completion-tracking' => smsPageUiDef(
                'Module Completion Tracking',
                'Track learner progress through LMS modules and required activities.',
                '+ Refresh Progress',
                'CMPL',
                smsStats4('Learners', '5', 'fa-tasks', 'On Track', '3', 'fa-check', 'At Risk', '1', 'fa-exclamation', 'Complete', '1', 'fa-flag-checkered'),
                smsCols('Progress No.', 'Student', 'Class Module', 'Completion', 'Updated'),
                [
                    ['Sofia Reyes', 'IT101 Module 1-3', '100%', 'Complete', 'completed', 'Progress'],
                    ['Mark Villanueva', 'EDUC201 Module 1-4', '85%', 'On Track', 'scheduled', 'Progress'],
                    ['Angela Cruz', 'ACCTG1 Module 1-2', '40%', 'At Risk', 'pending', 'Progress'],
                    ['Carlos Mendoza', 'CRIM301 Module 1-5', '70%', 'On Track', 'progress', 'Progress'],
                    ['Liza Santos', 'HM102 Module 1', '60%', 'On Track', 'scheduled', 'Progress'],
                ],
                'student, module, or completion'
            ),
            'multimedia-support' => smsPageUiDef(
                'Multimedia Support',
                'Manage multimedia learning assets, encoding jobs, and playback status.',
                '+ Upload Media',
                'MED',
                smsStats4('Assets', '5', 'fa-photo-video', 'Ready', '3', 'fa-check', 'Processing', '1', 'fa-spinner', 'Failed', '1', 'fa-times'),
                smsCols('Media No.', 'Asset Title', 'Class', 'Format', 'Uploaded'),
                [
                    ['Intro Computing Lecture Recap', 'IT101', 'MP4 · Ready', 'Ready', 'completed', 'Media'],
                    ['Facilitating Learning Podcast', 'EDUC201', 'MP3 · Ready', 'Ready', 'completed', 'Media'],
                    ['Accounting Walkthrough', 'ACCTG1', 'MP4 · Processing', 'Processing', 'pending', 'Media'],
                    ['Kitchen Demo Clip', 'HM102', 'MP4 · Ready', 'Ready', 'completed', 'Media'],
                    ['Corrupt upload retry', 'CRIM301', 'MOV · Failed', 'Failed', 'cancelled', 'Media'],
                ],
                'asset, class, or format'
            ),
            'lms-analytics' => smsPageUiDef(
                'LMS Analytics',
                'Review LMS engagement, completion, and activity analytics for IT/LMS office decisions.',
                '+ Refresh Analytics',
                'LAN',
                smsStats4('Datasets', '5', 'fa-chart-pie', 'Fresh', '3', 'fa-check', 'Stale', '1', 'fa-clock', 'Error', '1', 'fa-exclamation'),
                smsCols('Dataset No.', 'Analytics View', 'Scope', 'Key Metric', 'Synced'),
                [
                    ['Weekly Active Learners', 'All LMS classes', '2,184 active', 'Fresh', 'completed', 'Analytics'],
                    ['Assignment On-time Rate', 'Undergraduate', '86% on time', 'Fresh', 'completed', 'Analytics'],
                    ['Quiz Average Scores', 'CCS classes', '81% mean', 'Fresh', 'completed', 'Analytics'],
                    ['Module Completion Heatmap', 'All colleges', 'Needs refresh', 'Stale', 'pending', 'Analytics'],
                    ['Virtual Class Attendance API', 'Meet/Zoom', 'Feed error', 'Error', 'cancelled', 'Analytics'],
                ],
                'analytics view, scope, or metric'
            ),
        ];
    }
}

if (!function_exists('smsCocurricularPageUiCatalog')) {
    function smsCocurricularPageUiCatalog(): array
    {
        return [
            'club-registration-portal' => smsPageUiDef(
                'Club Registration Portal',
                'Register student organizations and renew campus club recognition.',
                '+ Register Club',
                'CLB',
                smsStats4('Clubs', '5', 'fa-users', 'Pending', '1', 'fa-clock', 'Recognized', '3', 'fa-check', 'Denied', '1', 'fa-times'),
                smsCols('Reg No.', 'Club Name', 'Adviser', 'Members', 'Updated'),
                [
                    ['CCS Programming Guild', 'Prof. Ana Reyes', '48 members', 'Recognized', 'completed', 'Club'],
                    ['Future Educators Club', 'Prof. Clara Santos', '36 members', 'Recognized', 'completed', 'Club'],
                    ['Young Entrepreneurs Society', 'Prof. Mark Lim', '29 members', 'Pending', 'pending', 'Club'],
                    ['Criminology Circle', 'Prof. Joel Cruz', '41 members', 'Recognized', 'completed', 'Club'],
                    ['Unverified Sports Club', 'No adviser', 'Incomplete docs', 'Denied', 'cancelled', 'Club'],
                ],
                'club, adviser, or members'
            ),
            'student-club-membership' => smsPageUiDef(
                'Student Club Membership',
                'Manage student membership applications and active club rosters.',
                '+ Add Member',
                'MEM',
                smsStats4('Members', '5', 'fa-user-friends', 'Active', '3', 'fa-check', 'Pending', '1', 'fa-clock', 'Inactive', '1', 'fa-user-slash'),
                smsCols('Member No.', 'Student', 'Club', 'Role', 'Updated'),
                [
                    ['Sofia Reyes', 'CCS Programming Guild', 'Member', 'Active', 'completed', 'Membership'],
                    ['Mark Villanueva', 'Future Educators Club', 'Secretary', 'Active', 'completed', 'Membership'],
                    ['Angela Cruz', 'Young Entrepreneurs Society', 'Applicant', 'Pending', 'pending', 'Membership'],
                    ['Carlos Mendoza', 'Criminology Circle', 'Member', 'Active', 'completed', 'Membership'],
                    ['Jules Ramos', 'Sports Club', 'Former member', 'Inactive', 'cancelled', 'Membership'],
                ],
                'student, club, or role'
            ),
            'club-officer-elections' => smsPageUiDef(
                'Club Officer Elections',
                'Schedule elections, record candidates, and publish officer results.',
                '+ New Election',
                'ELC',
                smsStats4('Elections', '5', 'fa-vote-yea', 'Scheduled', '2', 'fa-calendar', 'Completed', '2', 'fa-check', 'Disputed', '1', 'fa-exclamation'),
                smsCols('Election No.', 'Club / Position', 'Candidate', 'Votes', 'Date'),
                [
                    ['CCS Guild · President', 'Sofia Reyes', '28 votes', 'Completed', 'completed', 'Election'],
                    ['Educators Club · VP', 'Mark Villanueva', 'Scheduled Jul 25', 'Scheduled', 'scheduled', 'Election'],
                    ['Entrepreneurs · Treasurer', 'Angela Cruz', 'Scheduled Jul 26', 'Scheduled', 'scheduled', 'Election'],
                    ['Crim Circle · Secretary', 'Carlos Mendoza', '22 votes', 'Completed', 'completed', 'Election'],
                    ['Sports Club · President', 'Result contested', 'Recount pending', 'Disputed', 'cancelled', 'Election'],
                ],
                'club, position, or candidate'
            ),
            'event-activity-logs' => smsPageUiDef(
                'Event & Activity Logs',
                'Log campus club events, activity reports, and post-event documentation.',
                '+ Log Event',
                'EVT',
                smsStats4('Events', '5', 'fa-calendar-check', 'Upcoming', '2', 'fa-calendar', 'Done', '2', 'fa-check', 'Cancelled', '1', 'fa-ban'),
                smsCols('Event No.', 'Event Title', 'Club', 'Venue', 'Schedule'),
                [
                    ['Coding Bootcamp Night', 'CCS Programming Guild', 'Lab 3', 'Upcoming', 'scheduled', 'Event'],
                    ['Teaching Demo Day', 'Future Educators Club', 'Room 301', 'Done', 'completed', 'Event'],
                    ['Startup Pitch Mini', 'Young Entrepreneurs', 'Audio Visual Room', 'Upcoming', 'scheduled', 'Event'],
                    ['Crime Prevention Forum', 'Criminology Circle', 'Gym', 'Done', 'completed', 'Event'],
                    ['Sports Fest Heat', 'Sports Club', 'Cancelled rain', 'Cancelled', 'cancelled', 'Event'],
                ],
                'event, club, or venue'
            ),
            'attendance-tracker' => smsPageUiDef(
                'Attendance Tracker',
                'Track attendance for club meetings, events, and OSA-required activities.',
                '+ Log Attendance',
                'CATT',
                smsStats4('Sessions', '5', 'fa-clipboard-list', 'Complete', '3', 'fa-check', 'Partial', '1', 'fa-clock', 'Missing', '1', 'fa-exclamation'),
                smsCols('Session No.', 'Activity', 'Club', 'Present / Expected', 'Date'),
                [
                    ['Guild Weekly Meeting', 'CCS Programming Guild', '40 / 48', 'Complete', 'completed', 'Attendance'],
                    ['Educators Assembly', 'Future Educators Club', '30 / 36', 'Complete', 'completed', 'Attendance'],
                    ['Pitch Practice', 'Young Entrepreneurs', '18 / 29', 'Partial', 'pending', 'Attendance'],
                    ['Forum Ushering', 'Criminology Circle', '41 / 41', 'Complete', 'completed', 'Attendance'],
                    ['Sports Drill', 'Sports Club', 'No roster submitted', 'Missing', 'cancelled', 'Attendance'],
                ],
                'activity, club, or attendance count'
            ),
            'club-achievement-records' => smsPageUiDef(
                'Club Achievement Records',
                'Record awards, certifications, and recognition earned by student clubs.',
                '+ Add Achievement',
                'ACH',
                smsStats4('Achievements', '5', 'fa-trophy', 'Verified', '3', 'fa-check', 'Pending', '1', 'fa-clock', 'Rejected', '1', 'fa-times'),
                smsCols('Achieve No.', 'Achievement', 'Club', 'Awarding Body', 'Date'),
                [
                    ['Best Programming Org 2026', 'CCS Programming Guild', 'CCS Student Council', 'Verified', 'completed', 'Achievement'],
                    ['Community Teaching Award', 'Future Educators Club', 'COE Dean', 'Verified', 'completed', 'Achievement'],
                    ['Pitch Contest Finalist', 'Young Entrepreneurs', 'CBA Week', 'Pending', 'pending', 'Achievement'],
                    ['Peace Advocacy Citation', 'Criminology Circle', 'LGU Partner', 'Verified', 'completed', 'Achievement'],
                    ['Unverified medal claim', 'Sports Club', 'No certificate', 'Rejected', 'cancelled', 'Achievement'],
                ],
                'achievement, club, or awarding body'
            ),
            'budget-requests' => smsPageUiDef(
                'Budget Requests',
                'Process student organization budget requests and liquidation follow-ups.',
                '+ New Budget Request',
                'BUD',
                smsStats4('Requests', '5', 'fa-wallet', 'For Eval', '2', 'fa-search', 'Approved', '2', 'fa-check', 'Denied', '1', 'fa-times'),
                smsCols('Budget No.', 'Request Title', 'Club', 'Amount', 'Updated'),
                [
                    ['Bootcamp Supplies', 'CCS Programming Guild', '₱8,500.00', 'Approved', 'completed', 'Budget'],
                    ['Demo Day Materials', 'Future Educators Club', '₱5,200.00', 'Approved', 'completed', 'Budget'],
                    ['Pitch Event Kit', 'Young Entrepreneurs', '₱12,000.00', 'For Evaluation', 'pending', 'Budget'],
                    ['Forum Tarpaulin', 'Criminology Circle', '₱3,800.00', 'For Evaluation', 'progress', 'Budget'],
                    ['Sports Uniform Claim', 'Sports Club', '₱25,000.00', 'Denied', 'cancelled', 'Budget'],
                ],
                'request, club, or amount'
            ),
            'inter-school-communication' => smsPageUiDef(
                'Inter-school Communication',
                'Manage invitations, partnerships, and communications with other schools.',
                '+ New Communication',
                'COM',
                smsStats4('Messages', '5', 'fa-envelope', 'Sent', '2', 'fa-paper-plane', 'Received', '2', 'fa-inbox', 'Draft', '1', 'fa-pen'),
                smsCols('Comm No.', 'Subject', 'Partner School', 'Channel', 'Date'),
                [
                    ['Invite · Coding Olympiad', 'Tech University Manila', 'Email', 'Sent', 'completed', 'Communication'],
                    ['Reply · Educators Exchange', 'City Teachers College', 'Email', 'Received', 'scheduled', 'Communication'],
                    ['MOA Draft · Entrepreneurship', 'Business Institute PH', 'Courier + Email', 'Draft', 'pending', 'Communication'],
                    ['Forum Co-host Confirm', 'Metro Crim College', 'Email', 'Received', 'completed', 'Communication'],
                    ['Sports Meet Invitation', 'Regional Sports League', 'Email', 'Sent', 'progress', 'Communication'],
                ],
                'subject, partner school, or channel'
            ),
            'volunteer-hour-tracking' => smsPageUiDef(
                'Volunteer Hour Tracking',
                'Log and verify student volunteer hours for OSA and club outreach.',
                '+ Log Volunteer Hours',
                'VOL',
                smsStats4('Logs', '5', 'fa-hands-helping', 'Verified', '3', 'fa-check', 'Pending', '1', 'fa-clock', 'Rejected', '1', 'fa-times'),
                smsCols('Log No.', 'Student', 'Activity', 'Hours', 'Logged'),
                [
                    ['Sofia Reyes', 'Barangay Coding Workshop', '8.0 hrs', 'Verified', 'completed', 'Volunteer'],
                    ['Mark Villanueva', 'Tutorial Outreach', '6.5 hrs', 'Verified', 'completed', 'Volunteer'],
                    ['Angela Cruz', 'Market Survey Help', '4.0 hrs', 'Pending', 'pending', 'Volunteer'],
                    ['Carlos Mendoza', 'Peace Forum Usher', '5.0 hrs', 'Verified', 'completed', 'Volunteer'],
                    ['Jules Ramos', 'Unverified activity', '2.0 hrs', 'Rejected', 'cancelled', 'Volunteer'],
                ],
                'student, activity, or hours'
            ),
            'club-directory' => smsPageUiDef(
                'Club Directory',
                'Publish the official directory of recognized student organizations.',
                '+ Publish Directory Entry',
                'CDIR',
                smsStats4('Listings', '5', 'fa-sitemap', 'Published', '3', 'fa-globe', 'Draft', '1', 'fa-pen', 'Hidden', '1', 'fa-eye-slash'),
                smsCols('Listing No.', 'Club', 'Category', 'Contact', 'Updated'),
                [
                    ['CCS Programming Guild', 'Academic / Tech', 'ccs.guild@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Future Educators Club', 'Academic / Education', 'educators@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Young Entrepreneurs Society', 'Business', 'yes@bestlink.edu.ph', 'Published', 'completed', 'Directory'],
                    ['Criminology Circle', 'Academic / Crim', 'crim.circle@bestlink.edu.ph', 'Draft', 'pending', 'Directory'],
                    ['Sports Club', 'Athletics', 'Hidden pending docs', 'Hidden', 'cancelled', 'Directory'],
                ],
                'club, category, or contact'
            ),
        ];
    }
}

if (!function_exists('smsAccreditationPageUiCatalog')) {
    function smsAccreditationPageUiCatalog(): array
    {
        return [
            'accreditation-document-repository' => smsPageUiDef(
                'Accreditation Document Repository',
                'Store and tag accreditation evidence documents for program and institutional reviews.',
                '+ Upload Evidence',
                'ACC',
                smsStats4('Documents', '5', 'fa-folder-open', 'Indexed', '3', 'fa-check', 'For Tagging', '1', 'fa-tags', 'Missing', '1', 'fa-exclamation'),
                smsCols('Doc No.', 'Evidence Title', 'Area / Criterion', 'Owner', 'Updated'),
                [
                    ['Faculty Development Plan 2026', 'Area III · Faculty', 'HR Office', 'Indexed', 'completed', 'Evidence'],
                    ['Library Holdings Summary', 'Area V · Support', 'Library', 'Indexed', 'completed', 'Evidence'],
                    ['Student Services Manual', 'Area IV · Students', 'OSA', 'For Tagging', 'pending', 'Evidence'],
                    ['Lab Equipment Inventory', 'Area VI · Facilities', 'Property', 'Indexed', 'completed', 'Evidence'],
                    ['Research Output Matrix', 'Area II · Research', 'CRAD · missing file', 'Missing', 'cancelled', 'Evidence'],
                ],
                'document, criterion, or owner'
            ),
            'self-assessment-report-builder' => smsPageUiDef(
                'Self Assessment Report Builder',
                'Build SAR sections, narrative responses, and evidence links per criterion.',
                '+ New SAR Section',
                'SAR',
                smsStats4('Sections', '5', 'fa-file-alt', 'Draft', '2', 'fa-pen', 'Complete', '2', 'fa-check', 'Review', '1', 'fa-search'),
                smsCols('SAR No.', 'Section / Area', 'Writer', 'Progress', 'Updated'),
                [
                    ['Area I · Vision-Mission', 'QA Lead', '100% narrative', 'Complete', 'completed', 'SAR'],
                    ['Area II · Faculty', 'HR Coordinator', '80% with gaps', 'Review', 'progress', 'SAR'],
                    ['Area III · Curriculum', 'Academic Affairs', 'Draft outline', 'Draft', 'pending', 'SAR'],
                    ['Area IV · Support Services', 'OSA', '100% with evidence', 'Complete', 'completed', 'SAR'],
                    ['Area V · Facilities', 'Admin', 'Draft inventory', 'Draft', 'pending', 'SAR'],
                ],
                'SAR section, writer, or progress'
            ),
            'compliance-matrix-criteria-tracking' => smsPageUiDef(
                'Compliance Matrix & Criteria Tracking',
                'Track compliance status for each accreditation criterion and required evidence.',
                '+ Update Criterion',
                'MAT',
                smsStats4('Criteria', '5', 'fa-th', 'Compliant', '2', 'fa-check', 'Partial', '2', 'fa-adjust', 'Gap', '1', 'fa-exclamation'),
                smsCols('Criterion No.', 'Criterion', 'Owner', 'Evidence Count', 'Updated'),
                [
                    ['VMGO Dissemination', 'QA Office', '12 evidence files', 'Compliant', 'completed', 'Criterion'],
                    ['Faculty Qualification', 'HR Office', '9 of 12 required', 'Partial', 'progress', 'Criterion'],
                    ['Curriculum Mapping', 'Academic Affairs', 'Complete maps', 'Compliant', 'completed', 'Criterion'],
                    ['Student Support Services', 'OSA', '7 of 10 required', 'Partial', 'pending', 'Criterion'],
                    ['Research Productivity', 'CRAD', 'Below minimum', 'Gap', 'cancelled', 'Criterion'],
                ],
                'criterion, owner, or evidence'
            ),
            'internal-quality-audit-scheduler' => smsPageUiDef(
                'Internal Quality Audit Scheduler',
                'Schedule IQA activities, assign auditors, and track audit completion.',
                '+ Schedule Audit',
                'IQA',
                smsStats4('Audits', '5', 'fa-clipboard-list', 'Scheduled', '2', 'fa-calendar', 'Done', '2', 'fa-check', 'Overdue', '1', 'fa-exclamation'),
                smsCols('Audit No.', 'Audit Scope', 'Lead Auditor', 'Office Audited', 'Schedule'),
                [
                    ['IQA · Registrar Processes', 'QA Auditor 1', 'Registrar', 'Done', 'completed', 'Audit'],
                    ['IQA · Finance Collections', 'QA Auditor 2', 'Finance', 'Scheduled', 'scheduled', 'Audit'],
                    ['IQA · LMS Content Control', 'IT Auditor', 'IT Office', 'Scheduled', 'scheduled', 'Audit'],
                    ['IQA · Faculty Loading', 'HR Auditor', 'HR', 'Done', 'completed', 'Audit'],
                    ['IQA · Research Docs', 'QA Auditor 1', 'CRAD · overdue', 'Overdue', 'cancelled', 'Audit'],
                ],
                'audit scope, auditor, or office'
            ),
            'accreditation-visit-management' => smsPageUiDef(
                'Accreditation Visit Management',
                'Plan accreditation visits, itineraries, rooms, and document war rooms.',
                '+ New Visit Plan',
                'VST',
                smsStats4('Visits', '5', 'fa-handshake', 'Upcoming', '2', 'fa-calendar', 'Done', '2', 'fa-check', 'Deferred', '1', 'fa-pause'),
                smsCols('Visit No.', 'Visit / Program', 'Accrediting Body', 'Date Window', 'Status'),
                [
                    ['BSIT Program Visit', 'PACUCOA', 'Aug 12-14, 2026', 'Upcoming', 'scheduled', 'Visit'],
                    ['BSED Preliminary Visit', 'PACUCOA', 'Sep 3-4, 2026', 'Upcoming', 'scheduled', 'Visit'],
                    ['Institutional Visit 2025', 'PACUCOA', 'Completed Nov 2025', 'Done', 'completed', 'Visit'],
                    ['Library Area Visit', 'Internal Mock', 'Completed Jun 2026', 'Done', 'completed', 'Visit'],
                    ['BSHM Visit Window', 'PACUCOA', 'Deferred to 2027', 'Deferred', 'cancelled', 'Visit'],
                ],
                'visit, program, or accrediting body'
            ),
            'program-accreditation-tracker' => smsPageUiDef(
                'Program Accreditation Tracker',
                'Track accreditation level, validity dates, and renewal actions per program.',
                '+ Update Program Status',
                'PRG',
                smsStats4('Programs', '5', 'fa-award', 'Accredited', '3', 'fa-check', 'Candidate', '1', 'fa-hourglass', 'Expired', '1', 'fa-exclamation'),
                smsCols('Track No.', 'Program', 'Level / Status', 'Valid Until', 'Updated'),
                [
                    ['BSIT', 'Level II · Reaccredited', 'Mar 2028', 'Accredited', 'completed', 'Program'],
                    ['BSED English', 'Level I · Accredited', 'Nov 2027', 'Accredited', 'completed', 'Program'],
                    ['BSBA', 'Level II · Accredited', 'Jan 2029', 'Accredited', 'completed', 'Program'],
                    ['BSCrim', 'Candidate status', 'Visit pending', 'Candidate', 'pending', 'Program'],
                    ['BSHM', 'Accreditation lapsed', 'Renewal needed', 'Expired', 'cancelled', 'Program'],
                ],
                'program, level, or validity'
            ),
            'faculty-staff-qualification-tracking' => smsPageUiDef(
                'Faculty & Staff Qualification Tracking',
                'Monitor faculty/staff credentials, licenses, and qualification compliance.',
                '+ Add Qualification',
                'QUAL',
                smsStats4('Records', '5', 'fa-user-graduate', 'Compliant', '3', 'fa-check', 'Expiring', '1', 'fa-clock', 'Non-compliant', '1', 'fa-times'),
                smsCols('Qual No.', 'Personnel', 'Credential', 'Expiry', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'MSIT · Complete', 'N/A', 'Compliant', 'completed', 'Qualification'],
                    ['Prof. Clara Santos', 'MAED · Complete', 'N/A', 'Compliant', 'completed', 'Qualification'],
                    ['Prof. Joel Cruz', 'Crim license', 'Expires Oct 2026', 'Expiring', 'pending', 'Qualification'],
                    ['Prof. Mark Lim', 'MBA · Complete', 'N/A', 'Compliant', 'completed', 'Qualification'],
                    ['Lab Aide Pool', 'Missing TESDA certs', 'Required ASAP', 'Non-compliant', 'cancelled', 'Qualification'],
                ],
                'personnel, credential, or expiry'
            ),
            'physical-facilities-monitoring' => smsPageUiDef(
                'Physical Facilities Monitoring',
                'Monitor classrooms, labs, and facility readiness for accreditation standards.',
                '+ New Facility Check',
                'FACI',
                smsStats4('Facilities', '5', 'fa-building', 'Ready', '3', 'fa-check', 'Needs Repair', '1', 'fa-tools', 'Critical', '1', 'fa-exclamation'),
                smsCols('Facility No.', 'Facility', 'Capacity / Spec', 'Finding', 'Inspected'),
                [
                    ['Room 204', '40 seats · LCD', 'Ready for classes', 'Ready', 'completed', 'Facility'],
                    ['Computer Lab 3', '40 PCs · networked', 'Ready', 'Ready', 'completed', 'Facility'],
                    ['Kitchen Lab', '25 stations', '2 stoves down', 'Needs Repair', 'pending', 'Facility'],
                    ['Library Reading Area', '120 seats', 'Ready', 'Ready', 'completed', 'Facility'],
                    ['Science Prep Room', 'Ventilation issue', 'Safety critical', 'Critical', 'cancelled', 'Facility'],
                ],
                'facility, finding, or capacity'
            ),
            'continuous-improvement-action-planning' => smsPageUiDef(
                'Continuous Improvement Action Planning',
                'Create and monitor corrective/preventive actions from audits and accreditation findings.',
                '+ New Action Plan',
                'CIP',
                smsStats4('Actions', '5', 'fa-tasks', 'Open', '2', 'fa-folder-open', 'Closed', '2', 'fa-check', 'Overdue', '1', 'fa-exclamation'),
                smsCols('Action No.', 'Improvement Action', 'Owner', 'Due Date', 'Updated'),
                [
                    ['Update faculty credential matrix', 'HR Office', 'Jul 30, 2026', 'Open', 'pending', 'Action'],
                    ['Complete library evidence tags', 'Library', 'Done Jul 10', 'Closed', 'completed', 'Action'],
                    ['Repair kitchen lab stoves', 'Property', 'Jul 22, 2026', 'Open', 'progress', 'Action'],
                    ['Close research output gap', 'CRAD', 'Overdue Jul 5', 'Overdue', 'cancelled', 'Action'],
                    ['Publish revised SAR Area III', 'Academic Affairs', 'Done Jul 12', 'Closed', 'completed', 'Action'],
                ],
                'action, owner, or due date'
            ),
            'accreditation-reports-analytics' => smsPageUiDef(
                'Accreditation Reports & Analytics',
                'Generate compliance dashboards, visit readiness scores, and accreditation analytics.',
                '+ Generate QA Report',
                'QAR',
                smsStats4('Reports', '5', 'fa-chart-bar', 'Ready', '3', 'fa-check', 'Queued', '1', 'fa-hourglass', 'Failed', '1', 'fa-times'),
                smsCols('Report No.', 'Report Name', 'Scope', 'Score / Output', 'Generated'),
                [
                    ['Visit Readiness Scorecard', 'BSIT Program', '92% ready', 'Ready', 'completed', 'Report'],
                    ['Criteria Compliance Heatmap', 'All areas', '78% compliant', 'Ready', 'completed', 'Report'],
                    ['Faculty Qualification Gap Report', 'HR + QA', '4 gaps open', 'Ready', 'completed', 'Report'],
                    ['Facilities Risk Summary', 'Admin + QA', 'Queued export', 'Queued', 'pending', 'Report'],
                    ['Research Evidence Pack', 'CRAD + QA', 'Export failed', 'Failed', 'cancelled', 'Report'],
                ],
                'report, scope, or score'
            ),
        ];
    }
}
