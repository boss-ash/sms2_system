<?php
/**
 * SMS 2 - Dynamic Submodule Process View
 *
 * Expects $pageTitle, $activeModule, $activePage, and global $MODULES.
 */
$module = $MODULES[$activeModule] ?? [
    'label' => 'System Module',
    'icon' => 'fa-th-large',
];

$moduleLabel = $module['label'];
$moduleIcon = $module['icon'];
$pageName = $pageTitle ?? 'Selected Process';
$pageSlug = $activePage ?? strtolower(str_replace(' ', '-', $pageName));
$processAction = $_GET['process'] ?? '';

$moduleProfiles = [
    'enrollment' => [
        'subject' => 'Applicant / Student',
        'owner' => 'Admissions Team',
        'output' => 'enrollment confirmation',
        'reference' => 'ENR',
        'stats' => ['New Applicants', 'For Validation', 'Processed', '1-2 days'],
        'steps' => [
            ['Receive Application', 'Capture applicant details, chosen program, grade level, and submitted online form.'],
            ['Check Requirements', 'Review uploaded documents, admission status, duplicate records, and enrollment rules.'],
            ['Encode Enrollment Data', 'Create or update the student record, section, ID number, and enrollment queue.'],
            ['Release Enrollment Result', 'Publish confirmation, pending requirements, section assignment, or next action.'],
        ],
        'actions' => ['New Application', 'Validate Requirements', 'Approve Enrollment', 'Enrollment Report'],
    ],
    'registrar' => [
        'subject' => 'Student Record',
        'owner' => 'Registrar Staff',
        'output' => 'official student record',
        'reference' => 'REG',
        'stats' => ['New Requests', 'For Checking', 'Released', '2 days'],
        'steps' => [
            ['Receive Record Request', 'Search the student profile, request type, attached proof, and academic reference.'],
            ['Verify Student File', 'Check personal data, academic history, status, ID, transcript, and document rules.'],
            ['Update Registrar Record', 'Apply approved corrections, generate documents, or route the file for signing.'],
            ['Release Official Output', 'Issue the transcript, certificate, student ID, digital file, or status update.'],
        ],
        'actions' => ['New Record Request', 'Verify Record', 'Approve Release', 'Registrar Report'],
    ],
    'curriculum' => [
        'subject' => 'Curriculum Item',
        'owner' => 'Academic Affairs',
        'output' => 'approved curriculum update',
        'reference' => 'CUR',
        'stats' => ['New Items', 'For Review', 'Approved', '3 days'],
        'steps' => [
            ['Create Curriculum Entry', 'Define subjects, prerequisites, strands, equivalencies, or grade weighting details.'],
            ['Review Academic Rules', 'Validate units, CHED or DepEd mapping, prerequisites, and curriculum version.'],
            ['Apply Curriculum Change', 'Update the subject offering, curriculum map, or exportable academic structure.'],
            ['Publish Academic Output', 'Release the curriculum update, validation result, equivalency, or export file.'],
        ],
        'actions' => ['New Curriculum Entry', 'Check Rules', 'Approve Update', 'Export Report'],
    ],
    'accreditation' => [
        'subject' => 'Accreditation Item',
        'owner' => 'Quality Assurance',
        'output' => 'compliance evidence',
        'reference' => 'ACC',
        'stats' => ['New Evidence', 'For Compliance', 'Completed', '5 days'],
        'steps' => [
            ['Collect Evidence', 'Register documents, criteria, audit findings, facility notes, or program requirements.'],
            ['Assess Compliance', 'Map the item against accreditation standards, owners, dates, and required evidence.'],
            ['Route Corrective Action', 'Assign follow-up work, improvement plans, or document revisions to the owner.'],
            ['Finalize Accreditation File', 'Mark evidence complete, generate the report, and prepare visit-ready records.'],
        ],
        'actions' => ['New Evidence', 'Assess Compliance', 'Mark Complete', 'QA Report'],
    ],
    'payment' => [
        'subject' => 'Billing Account',
        'owner' => 'Finance Office',
        'output' => 'financial transaction record',
        'reference' => 'PAY',
        'stats' => ['New Billings', 'For Payment', 'Posted', '1 day'],
        'steps' => [
            ['Create Billing Transaction', 'Identify student account, fee setup, invoice, discount, scholarship, or penalty.'],
            ['Validate Assessment', 'Check balances, due dates, receipts, ledgers, collections, and payment rules.'],
            ['Post Finance Entry', 'Record payment, adjustment, receivable, penalty, or online payment confirmation.'],
            ['Release Finance Output', 'Generate receipt, SOA, ledger report, collection analytics, or audit record.'],
        ],
        'actions' => ['New Billing', 'Validate Payment', 'Post Transaction', 'Finance Report'],
    ],
    'faculty' => [
        'subject' => 'Faculty Record',
        'owner' => 'HR / Academic Office',
        'output' => 'faculty management update',
        'reference' => 'FAC',
        'stats' => ['New Updates', 'For Approval', 'Completed', '2-3 days'],
        'steps' => [
            ['Receive Faculty Request', 'Capture profile, load, schedule, leave, payroll, attendance, or clearance details.'],
            ['Review HR Details', 'Check teaching history, subject load, leave balance, salary grade, and approvals.'],
            ['Process Faculty Update', 'Apply assignments, attendance entries, payroll setup, or clearance routing.'],
            ['Release Faculty Output', 'Publish schedule, evaluation, payroll setup, leave result, or profile update.'],
        ],
        'actions' => ['New Faculty Request', 'Review Details', 'Approve Update', 'Faculty Report'],
    ],
    'scheduling' => [
        'subject' => 'Schedule Item',
        'owner' => 'Scheduling Office',
        'output' => 'validated class schedule',
        'reference' => 'SCH',
        'stats' => ['New Schedules', 'Conflicts', 'Published', '1 day'],
        'steps' => [
            ['Build Schedule Request', 'Select section, teacher, room, exam timetable, substitute, or calendar item.'],
            ['Run Conflict Check', 'Validate room availability, teacher load, time blocks, sections, and special classes.'],
            ['Assign Schedule', 'Apply room, teacher, substitute, cloned schedule, or generated time block.'],
            ['Publish Timetable', 'Release the schedule, exam table, calendar sync, or conflict resolution result.'],
        ],
        'actions' => ['New Schedule', 'Check Conflict', 'Publish Schedule', 'Schedule Report'],
    ],
    'cocurricular' => [
        'subject' => 'Student Activity',
        'owner' => 'OSA Office',
        'output' => 'activity record',
        'reference' => 'OSA',
        'stats' => ['New Activities', 'For Approval', 'Logged', '2 days'],
        'steps' => [
            ['Receive Activity Request', 'Capture club registration, membership, event, attendance, budget, or volunteer details.'],
            ['Review Participation Rules', 'Check officers, membership, event requirements, attendance logs, and budget limits.'],
            ['Process Activity Record', 'Approve club actions, update attendance, log achievements, or route communication.'],
            ['Release Activity Output', 'Publish directory entries, event logs, budget status, or volunteer-hour records.'],
        ],
        'actions' => ['New Activity', 'Review Request', 'Approve Activity', 'OSA Report'],
    ],
    'lms' => [
        'subject' => 'Learning Item',
        'owner' => 'LMS Administrator',
        'output' => 'learning activity update',
        'reference' => 'LMS',
        'stats' => ['New Content', 'In Review', 'Completed', '1 day'],
        'steps' => [
            ['Create Learning Entry', 'Capture class portal, lesson material, assignment, quiz, multimedia, or module details.'],
            ['Check Learning Rules', 'Validate access, deadlines, grading links, feedback, completion, and classroom settings.'],
            ['Publish LMS Activity', 'Upload content, open quiz, sync grades, post feedback, or update module progress.'],
            ['Release Learning Output', 'Show analytics, completion tracking, submissions, grades, or virtual class status.'],
        ],
        'actions' => ['New LMS Entry', 'Validate Access', 'Publish Activity', 'LMS Report'],
    ],
    'crad' => [
        'subject' => 'Research Record',
        'owner' => 'CRAD Office',
        'output' => 'research workflow record',
        'reference' => 'CRD',
        'stats' => ['New Proposals', 'Under Review', 'Accepted', '4 days'],
        'steps' => [
            ['Submit Research Item', 'Capture proposal, adviser request, funding assistance, documentation, or repository entry.'],
            ['Review Research Details', 'Check adviser assignment, grant requirements, collaboration status, and publication data.'],
            ['Process CRAD Action', 'Route proposal review, assign adviser, update funding, or archive research documents.'],
            ['Release Research Output', 'Publish review result, adviser assignment, funding status, repository file, or report.'],
        ],
        'actions' => ['New Research Item', 'Review Submission', 'Approve Research', 'CRAD Report'],
    ],
    'crad_grant' => [
        'subject' => 'Grant Record',
        'owner' => 'Research Grant Office',
        'output' => 'research funding record',
        'reference' => 'GRN',
        'stats' => ['New Applications', 'For Evaluation', 'Funded', '5 days'],
        'steps' => [
            ['Receive Grant Item', 'Capture grant calls, applications, evaluation files, approval requests, or funding records.'],
            ['Review Funding Details', 'Check eligibility, budget, scoring, approval status, and supporting documents.'],
            ['Process Grant Action', 'Route evaluation, approve funding, release funds, or update project milestones.'],
            ['Release Grant Output', 'Publish funding status, disbursement record, milestone update, or grant report.'],
        ],
        'actions' => ['New Grant Item', 'Review Funding', 'Approve Grant', 'Grant Report'],
    ],
];

$profile = $moduleProfiles[$activeModule] ?? [
    'subject' => 'Record',
    'owner' => 'Assigned Office',
    'output' => 'transaction output',
    'reference' => strtoupper(substr((string) $activeModule, 0, 3)),
    'stats' => ['New Requests', 'In Process', 'Completed', '2 days'],
    'steps' => [
        ['Receive Request', 'Encode or capture the transaction details.'],
        ['Validate Requirements', 'Check documents, records, and approval rules.'],
        ['Process Transaction', 'Route the request to the assigned office user for update or approval.'],
        ['Release Output', 'Generate the final confirmation, report, or official record.'],
    ],
    'actions' => ['New Transaction', 'Validate', 'Approve', 'Generate Report'],
];

$pageRules = [
    [
        'match' => ['online-pre-registration', 'pre-registration'],
        'subject' => 'Applicant',
        'lead' => 'Start from the applicant online form, validate admission requirements, and prepare the enrollment decision.',
        'steps' => [
            ['Receive Online Form', 'Capture applicant profile, program choice, grade level, and contact information.'],
            ['Validate Admission Data', 'Check duplicate applications, eligibility, uploaded proof, and initial requirements.'],
            ['Prepare Enrollment Slot', 'Tag the applicant for interview, assessment, waiting list, or section assignment.'],
            ['Release Application Result', 'Send the approved, pending, or returned pre-registration status to the applicant.'],
        ],
        'actions' => ['New Pre-registration', 'Validate Application', 'Approve Slot', 'Applicant Report'],
        'records' => ['Sofia Reyes', 'Mark Villanueva', 'Angela Cruz'],
    ],
    [
        'match' => ['document-upload'],
        'subject' => 'Uploaded Requirement',
        'lead' => 'Monitor submitted files, missing documents, verification remarks, and applicant follow-up.',
        'steps' => [
            ['Receive Uploaded File', 'Log each submitted document with applicant, file type, and upload timestamp.'],
            ['Check File Validity', 'Review readability, completeness, required signatures, and accepted file formats.'],
            ['Tag Requirement Status', 'Mark the document as verified, rejected, duplicate, or needing resubmission.'],
            ['Notify Applicant', 'Send the document result and next requirement instructions.'],
        ],
        'actions' => ['Add Requirement', 'Verify File', 'Accept Upload', 'Requirement Report'],
        'records' => ['Birth Certificate', 'Report Card', 'Good Moral Certificate'],
    ],
    [
        'match' => ['validation', 'checker', 'validator', 'conflict'],
        'subject' => 'Validation Item',
        'lead' => 'Run the page checks, flag conflicts or missing details, and record the validation result.',
        'steps' => [
            ['Load Validation Queue', 'Pull records that need checking for the selected page.'],
            ['Run Rule Check', 'Compare requirements, schedules, balances, eligibility, or compliance rules.'],
            ['Resolve Findings', 'Tag passed items, exceptions, duplicates, and records needing correction.'],
            ['Save Validation Result', 'Publish the final validation status and reviewer remarks.'],
        ],
        'actions' => ['New Check', 'Run Validation', 'Clear Item', 'Validation Report'],
        'records' => ['Rule Check 001', 'Rule Check 002', 'Rule Check 003'],
    ],
    [
        'match' => ['id-number', 'id-generation', 'student-id'],
        'subject' => 'Student ID Request',
        'lead' => 'Generate or review student identification details before official ID release.',
        'steps' => [
            ['Select Student Record', 'Open the approved student record and confirm enrollment status.'],
            ['Generate ID Details', 'Create the ID number, barcode, QR or RFID reference, and print metadata.'],
            ['Verify ID Data', 'Check name, program, year level, photo, and active school year.'],
            ['Release Student ID', 'Mark the ID as printed, replaced, encoded, or ready for claiming.'],
        ],
        'actions' => ['New ID Request', 'Verify ID Data', 'Approve ID', 'ID Report'],
        'records' => ['S230000001', 'S230000002', 'S230000003'],
    ],
    [
        'match' => ['section', 'grade-level', 'assignment'],
        'modules' => ['enrollment'],
        'subject' => 'Assignment Record',
        'lead' => 'Assign the selected record to the correct level, section, adviser, teacher, room, or academic owner.',
        'steps' => [
            ['Load Assignment Pool', 'Collect records that need placement or reassignment.'],
            ['Check Capacity and Rules', 'Review available slots, eligibility, workload, schedules, and restrictions.'],
            ['Apply Assignment', 'Save the recommended level, section, teacher, adviser, room, or owner.'],
            ['Publish Assignment', 'Release the final assignment and update related records.'],
        ],
        'actions' => ['New Assignment', 'Check Capacity', 'Approve Assignment', 'Assignment Report'],
        'records' => ['Assignment Batch A', 'Assignment Batch B', 'Assignment Batch C'],
    ],
    [
        'match' => ['waiting-list'],
        'subject' => 'Waitlisted Applicant',
        'lead' => 'Rank waitlisted applicants, review available slots, and update admission movement.',
        'steps' => [
            ['Receive Waitlist Entry', 'Add qualified applicants that cannot be assigned immediately.'],
            ['Rank Queue Priority', 'Check score, timestamp, program capacity, and requirement status.'],
            ['Move Applicant Status', 'Promote, hold, or remove applicants based on slot availability.'],
            ['Notify Waitlist Result', 'Send queue movement and next-step instructions.'],
        ],
        'actions' => ['Add to Waitlist', 'Rank Queue', 'Move Applicant', 'Queue Report'],
        'records' => ['Waitlist Rank 01', 'Waitlist Rank 02', 'Waitlist Rank 03'],
    ],
    [
        'match' => ['dashboard', 'analytics', 'reporting'],
        'subject' => 'Report Dataset',
        'lead' => 'Compile live counts, trends, exceptions, and performance metrics for decision-making.',
        'steps' => [
            ['Collect Data Sources', 'Pull records from the selected module and related transaction tables.'],
            ['Filter Reporting Scope', 'Apply date, status, department, program, or owner filters.'],
            ['Generate Insights', 'Compute totals, trends, exceptions, and workload indicators.'],
            ['Release Dashboard Output', 'Publish charts, summary cards, downloadable reports, and audit notes.'],
        ],
        'actions' => ['New Report View', 'Refresh Data', 'Approve Summary', 'Export Report'],
        'records' => ['Report Dataset A', 'Report Dataset B', 'Report Dataset C'],
    ],
    [
        'match' => ['student-information'],
        'subject' => 'Student Master Record',
        'lead' => 'Open the student master record, validate profile data, and route approved registrar updates.',
        'steps' => [
            ['Search Student Profile', 'Find the student by ID, name, program, or active enrollment record.'],
            ['Review Master Data', 'Check personal details, contact information, program, status, and linked files.'],
            ['Apply Record Update', 'Save approved corrections or route sensitive changes for registrar approval.'],
            ['Finalize Student Record', 'Lock the update history and publish the updated student information.'],
        ],
        'actions' => ['New Student Record', 'Verify Profile', 'Approve Update', 'Student Record Report'],
        'records' => ['S230000001', 'S230000002', 'S230000003'],
    ],
    [
        'match' => ['guardian', 'emergency'],
        'subject' => 'Guardian Contact',
        'lead' => 'Maintain guardian, parent, and emergency contact details for student safety and official notices.',
        'steps' => [
            ['Receive Contact Update', 'Capture guardian name, relationship, phone, address, and emergency notes.'],
            ['Verify Contact Details', 'Check completeness, duplicate contacts, and student relationship validity.'],
            ['Save Approved Contact', 'Update the active guardian and emergency contact file.'],
            ['Release Contact Log', 'Record the update history for registrar and student services reference.'],
        ],
        'actions' => ['New Contact Update', 'Verify Contact', 'Approve Contact', 'Contact Report'],
        'records' => ['Maria Dela Cruz', 'Ramon Santos', 'Elena Reyes'],
    ],
    [
        'match' => ['academic-history', 'transcript'],
        'subject' => 'Academic Record',
        'lead' => 'Review grades, units, standing, transfer history, and transcript release requirements.',
        'steps' => [
            ['Load Academic File', 'Open subjects, grades, terms, credited units, and academic standing.'],
            ['Verify Grade History', 'Check completion, deficiencies, retakes, equivalencies, and transcript rules.'],
            ['Prepare Official Record', 'Generate academic history, transcript details, or registrar certification.'],
            ['Release Academic Output', 'Mark the record ready for printing, signing, claiming, or digital release.'],
        ],
        'actions' => ['New Academic Request', 'Verify Grades', 'Approve Transcript', 'Academic Report'],
        'records' => ['Transcript Draft 001', 'Grade History 002', 'TOR Request 003'],
    ],
    [
        'match' => ['health-record'],
        'subject' => 'Health Record',
        'lead' => 'Track student health information, clinic notes, medical requirements, and release status.',
        'steps' => [
            ['Receive Health Entry', 'Encode clinic visit, medical requirement, condition note, or clearance file.'],
            ['Review Medical Details', 'Check completeness, dates, restrictions, and required supporting documents.'],
            ['Update Health Log', 'Save the verified health entry and attach clinic remarks.'],
            ['Release Health Status', 'Publish cleared, pending, or follow-up status to the authorized office.'],
        ],
        'actions' => ['New Health Entry', 'Review Medical File', 'Approve Health Log', 'Health Report'],
        'records' => ['Clinic Log 001', 'Medical File 002', 'Clearance Note 003'],
    ],
    [
        'match' => ['rfid', 'qr-code'],
        'subject' => 'RFID / QR Token',
        'lead' => 'Connect RFID and QR credentials to the correct student record and access status.',
        'steps' => [
            ['Scan Credential Request', 'Capture card number, QR token, student ID, and device source.'],
            ['Validate Token Link', 'Check duplicates, active status, access rules, and assigned student record.'],
            ['Activate Credential', 'Bind the RFID or QR code to the student profile.'],
            ['Release Access Status', 'Publish active, blocked, replaced, or pending credential status.'],
        ],
        'actions' => ['New Credential', 'Validate Token', 'Activate Credential', 'Credential Report'],
        'records' => ['RFID-0001', 'QR-0002', 'RFID-0003'],
    ],
    [
        'match' => ['document-requests', 'digital-file'],
        'subject' => 'Document Request',
        'lead' => 'Process requested documents, digital files, attachments, approval, and release tracking.',
        'steps' => [
            ['Receive Document Request', 'Capture document type, requester, purpose, and supporting details.'],
            ['Verify Release Rules', 'Check student status, balances, signatures, file availability, and approval needs.'],
            ['Prepare Document File', 'Generate, scan, attach, or update the requested official document.'],
            ['Release Document', 'Mark the document as ready, claimed, emailed, archived, or returned.'],
        ],
        'actions' => ['New Document Request', 'Verify Request', 'Approve Release', 'Document Report'],
        'records' => ['Certificate Request', 'Digital File Batch', 'Clearance Copy'],
    ],
    [
        'match' => ['status-tracker'],
        'subject' => 'Status Record',
        'lead' => 'Track active, enrolled, pending, graduated, withdrawn, or inactive student status changes.',
        'steps' => [
            ['Open Status Case', 'Select the student and current status record.'],
            ['Check Status Basis', 'Review enrollment, grades, clearance, payments, and registrar rules.'],
            ['Update Student Status', 'Apply the approved status transition with remarks and effective date.'],
            ['Publish Status History', 'Save the status log and notify linked offices.'],
        ],
        'actions' => ['New Status Case', 'Check Status', 'Approve Status', 'Status Report'],
        'records' => ['Active Status', 'Pending Status', 'Graduated Status'],
    ],
    [
        'match' => ['curriculum-builder'],
        'subject' => 'Curriculum Version',
        'lead' => 'Build the curriculum version with subjects, units, year levels, terms, and approval status.',
        'steps' => [
            ['Create Curriculum Version', 'Set curriculum code, program, year level structure, and effective school year.'],
            ['Add Subject Blocks', 'Encode subjects, units, lecture or lab hours, prerequisites, and semester placement.'],
            ['Review Curriculum Rules', 'Check total units, duplicate subjects, prerequisites, and CHED or DepEd alignment.'],
            ['Publish Curriculum Version', 'Mark the curriculum as draft, reviewed, approved, or archived.'],
        ],
        'actions' => ['New Curriculum Version', 'Check Curriculum', 'Approve Version', 'Curriculum Report'],
        'records' => ['BSIT 2026 Curriculum', 'BSCS 2026 Draft', 'SHS ICT Track'],
    ],
    [
        'match' => ['subject-mapping'],
        'subject' => 'Subject Map',
        'lead' => 'Map subjects to programs, strands, year levels, terms, and curriculum versions.',
        'steps' => [
            ['Select Source Subject', 'Choose the subject code, title, units, and owning curriculum.'],
            ['Map Target Program', 'Assign program, strand, year level, term, and course grouping.'],
            ['Validate Mapping', 'Check duplicate mappings, unit mismatches, and curriculum conflicts.'],
            ['Save Subject Map', 'Publish the accepted mapping for enrollment and scheduling use.'],
        ],
        'actions' => ['New Subject Map', 'Validate Map', 'Approve Mapping', 'Mapping Report'],
        'records' => ['IT101 Mapping', 'ENG201 Mapping', 'MATH105 Mapping'],
    ],
    [
        'match' => ['pre-requisite'],
        'subject' => 'Prerequisite Rule',
        'lead' => 'Configure prerequisite, corequisite, and completion rules before subject enrollment.',
        'steps' => [
            ['Select Target Subject', 'Open the subject that needs prerequisite or corequisite control.'],
            ['Define Requirement Rule', 'Add required subjects, minimum grades, year level, or completed units.'],
            ['Test Enrollment Rule', 'Validate sample student records against the configured prerequisite.'],
            ['Activate Requirement', 'Publish the approved prerequisite rule for enrollment validation.'],
        ],
        'actions' => ['New Prerequisite', 'Test Rule', 'Activate Rule', 'Prerequisite Report'],
        'records' => ['IT102 requires IT101', 'DB201 requires IT103', 'CAP301 requires 90 Units'],
    ],
    [
        'match' => ['electives'],
        'subject' => 'Elective Group',
        'lead' => 'Manage elective pools, allowed subjects, unit limits, and student selection rules.',
        'steps' => [
            ['Create Elective Pool', 'Define elective group name, program, term, and required units.'],
            ['Assign Elective Subjects', 'Add allowed subjects, capacity, availability, and restrictions.'],
            ['Review Selection Rules', 'Check unit limits, duplicate choices, and curriculum compatibility.'],
            ['Publish Elective Options', 'Release the elective pool for advising or enrollment selection.'],
        ],
        'actions' => ['New Elective Pool', 'Review Options', 'Approve Pool', 'Elective Report'],
        'records' => ['IT Elective Pool', 'Business Elective Pool', 'SHS Specialized Track'],
    ],
    [
        'match' => ['strand'],
        'subject' => 'Academic Strand',
        'lead' => 'Assign academic strands, tracks, or specializations to students and curriculum paths.',
        'steps' => [
            ['Select Student or Program', 'Choose the student group, program, or grade level needing strand assignment.'],
            ['Review Strand Eligibility', 'Check track rules, completed subjects, capacity, and advising notes.'],
            ['Apply Strand Assignment', 'Save the approved strand, track, or specialization path.'],
            ['Release Strand Status', 'Publish the assignment for curriculum and enrollment use.'],
        ],
        'actions' => ['New Strand Assignment', 'Check Eligibility', 'Approve Strand', 'Strand Report'],
        'records' => ['ICT Strand Batch', 'ABM Strand Batch', 'STEM Strand Batch'],
    ],
    [
        'match' => ['subject-offering'],
        'subject' => 'Subject Offering',
        'lead' => 'Track when subjects are offered, reopened, closed, or moved across terms.',
        'steps' => [
            ['Create Subject Offering', 'Select subject, curriculum, school year, semester, section, and capacity.'],
            ['Review Offering History', 'Check previous terms, demand, faculty load, room needs, and restrictions.'],
            ['Update Offering Status', 'Mark the subject as open, closed, merged, cancelled, or archived.'],
            ['Publish Offering Record', 'Release the offering history for enrollment and scheduling.'],
        ],
        'actions' => ['New Offering', 'Review History', 'Approve Offering', 'Offering Report'],
        'records' => ['IT101 1st Sem', 'NSTP 2nd Sem', 'CAP301 Summer'],
    ],
    [
        'match' => ['equivalency'],
        'subject' => 'Equivalency Rule',
        'lead' => 'Match old and new subjects for credited units, transfer records, and curriculum migration.',
        'steps' => [
            ['Select Source Subject', 'Choose the completed or transferred subject to evaluate.'],
            ['Match Equivalent Subject', 'Compare code, title, units, outcomes, and curriculum version.'],
            ['Approve Credit Result', 'Tag the subject as equivalent, partial, rejected, or for dean review.'],
            ['Release Equivalency Record', 'Save the approved crediting result to the academic file.'],
        ],
        'actions' => ['New Equivalency', 'Match Subject', 'Approve Credit', 'Equivalency Report'],
        'records' => ['CS101 to IT101', 'MATH1 to MATH101', 'ENGA to ENG101'],
    ],
    [
        'match' => ['grade-weighting'],
        'subject' => 'Grade Weighting Rule',
        'lead' => 'Configure grade components, percentages, grading periods, and computation rules.',
        'steps' => [
            ['Create Grade Component', 'Define quizzes, activities, exams, attendance, or performance task weights.'],
            ['Validate Percent Totals', 'Check if component totals, grading periods, and passing rules are correct.'],
            ['Apply Grade Formula', 'Save the computation setup for the selected subject or department.'],
            ['Publish Grading Setup', 'Release the weighting rule for faculty grading use.'],
        ],
        'actions' => ['New Grade Rule', 'Validate Weights', 'Approve Formula', 'Grade Setup Report'],
        'records' => ['Lecture Formula', 'Laboratory Formula', 'SHS Formula'],
    ],
    [
        'match' => ['export'],
        'subject' => 'Export File',
        'lead' => 'Prepare filtered module data for downloadable files, reports, and archive copies.',
        'steps' => [
            ['Select Export Scope', 'Choose records, school year, department, status, and format.'],
            ['Preview Export Data', 'Check columns, totals, filters, and restricted fields.'],
            ['Generate Export File', 'Create the spreadsheet, PDF, or system-ready data file.'],
            ['Log Export Release', 'Record who generated the file and when it was released.'],
        ],
        'actions' => ['New Export', 'Preview Data', 'Generate File', 'Download Report'],
        'records' => ['Export Batch 001', 'Export Batch 002', 'Export Batch 003'],
    ],
    [
        'match' => ['billing', 'invoice', 'payment', 'ledger', 'receivable', 'collection', 'discount', 'scholarship', 'fee', 'penalty', 'audit'],
        'modules' => ['payment'],
        'subject' => 'Finance Transaction',
        'lead' => 'Process finance records using assessment, payment, ledger, discount, penalty, or audit rules.',
        'steps' => [
            ['Open Finance Transaction', 'Select student account, invoice, fee, payment, discount, or ledger item.'],
            ['Validate Amounts', 'Check assessment, balances, due dates, receipts, scholarship, and posting rules.'],
            ['Post Finance Update', 'Save the billing, collection, receivable, discount, penalty, or audit entry.'],
            ['Release Finance Record', 'Generate receipt, SOA, ledger, collection report, or audit trail.'],
        ],
        'actions' => ['New Finance Entry', 'Validate Amount', 'Post Entry', 'Finance Report'],
        'records' => ['OR-2026-0018', 'INV-2026-0042', 'LED-2026-0011'],
    ],
    [
        'match' => ['club', 'event', 'attendance', 'volunteer', 'budget', 'membership', 'officer', 'inter-school', 'achievement'],
        'modules' => ['cocurricular'],
        'subject' => 'Activity Record',
        'lead' => 'Manage student activity records, approvals, participation, attendance, budgets, and club outputs.',
        'steps' => [
            ['Receive Activity Entry', 'Capture club, event, member, officer, budget, volunteer, or communication details.'],
            ['Review OSA Rules', 'Check requirements, adviser approval, attendance, membership, budget, and activity status.'],
            ['Update Activity Log', 'Approve, record, route, or revise the selected co-curricular transaction.'],
            ['Release Activity Status', 'Publish the directory, log, attendance, budget result, or volunteer hours.'],
        ],
        'actions' => ['New Activity Entry', 'Review Activity', 'Approve Activity', 'Activity Report'],
        'records' => ['IT Club Event', 'Volunteer Batch', 'Budget Request'],
    ],
    [
        'match' => ['class-portal', 'lesson', 'assignment', 'quiz', 'virtual', 'grading', 'feedback', 'module-completion', 'multimedia', 'lms'],
        'modules' => ['lms'],
        'subject' => 'Learning Record',
        'lead' => 'Manage learning content, class access, submissions, quizzes, grading, feedback, and LMS progress.',
        'steps' => [
            ['Create LMS Item', 'Select class, lesson, assignment, quiz, media, grading link, or module record.'],
            ['Review Learning Settings', 'Check access, deadline, visibility, scoring, completion, and feedback rules.'],
            ['Publish Learning Activity', 'Upload, open, grade, comment, sync, or update the selected LMS item.'],
            ['Release Learning Status', 'Show submissions, grades, feedback, analytics, or completion progress.'],
        ],
        'actions' => ['New LMS Item', 'Review Settings', 'Publish Item', 'LMS Report'],
        'records' => ['Lesson Week 1', 'Quiz 1', 'Module Progress'],
    ],
    [
        'match' => ['schedule', 'timetable', 'room', 'calendar', 'time-block', 'substitute', 'teacher'],
        'modules' => ['scheduling'],
        'subject' => 'Schedule Record',
        'lead' => 'Build, validate, assign, and publish schedule records for classes, rooms, exams, or calendars.',
        'steps' => [
            ['Create Schedule Draft', 'Select section, teacher, subject, room, exam, substitute, or calendar item.'],
            ['Check Availability', 'Validate time blocks, room use, teacher load, conflicts, and section capacity.'],
            ['Apply Schedule Decision', 'Assign, clone, replace, or adjust the selected schedule entry.'],
            ['Publish Schedule Output', 'Release timetable, room list, exam schedule, substitute notice, or calendar sync.'],
        ],
        'actions' => ['New Schedule Draft', 'Check Availability', 'Publish Schedule', 'Schedule Report'],
        'records' => ['BSIT 2A Schedule', 'Room 204 Block', 'Exam Timetable'],
    ],
    [
        'match' => ['faculty', 'subject-load', 'schedule', 'attendance', 'leave', 'salary', 'payroll', 'teaching', 'clearance', 'evaluation'],
        'modules' => ['faculty'],
        'subject' => 'Faculty Record',
        'lead' => 'Process faculty profile, workload, leave, payroll, teaching history, clearance, or evaluation records.',
        'steps' => [
            ['Open Faculty Transaction', 'Select faculty profile, load, schedule, leave, payroll, evaluation, or clearance item.'],
            ['Review Faculty Rules', 'Check HR records, academic load, approvals, salary grade, attendance, and history.'],
            ['Apply Faculty Update', 'Save the approved faculty record, assignment, leave, payroll, or clearance result.'],
            ['Release Faculty Output', 'Publish the updated profile, schedule, evaluation, payroll setup, or clearance status.'],
        ],
        'actions' => ['New Faculty Entry', 'Review Faculty Data', 'Approve Update', 'Faculty Report'],
        'records' => ['Prof. Santos', 'Prof. Reyes', 'Prof. Lim'],
    ],
    [
        'match' => ['accreditation', 'assessment', 'compliance', 'audit', 'visit', 'qualification', 'facilities', 'improvement'],
        'modules' => ['accreditation'],
        'subject' => 'Compliance Record',
        'lead' => 'Collect evidence, check accreditation criteria, assign actions, and prepare compliance output.',
        'steps' => [
            ['Collect Compliance Evidence', 'Attach documents, criteria, audit notes, visit details, or facility findings.'],
            ['Assess Criteria Status', 'Check completeness, owners, due dates, qualification, and accreditation requirements.'],
            ['Route Improvement Action', 'Assign corrective actions, staff follow-up, facility work, or document revision.'],
            ['Finalize Compliance Output', 'Generate matrix, report, visit file, or continuous-improvement record.'],
        ],
        'actions' => ['New Evidence', 'Assess Criteria', 'Close Action', 'Compliance Report'],
        'records' => ['Criterion 1 Evidence', 'Audit Finding 002', 'Visit File 003'],
    ],
    [
        'match' => ['research', 'proposal', 'adviser', 'grants', 'funding', 'documentation', 'publication', 'collaboration', 'repository'],
        'modules' => ['crad', 'crad_grant'],
        'subject' => 'Research Record',
        'lead' => 'Process research proposals, adviser assignment, grants, documentation, collaboration, and repository files.',
        'steps' => [
            ['Receive Research Submission', 'Capture proposal, adviser request, grant application, publication, or repository file.'],
            ['Review Research Requirements', 'Check format, adviser load, funding rules, collaboration status, and documentation.'],
            ['Route CRAD Decision', 'Assign adviser, approve proposal, update funding, publish documentation, or archive file.'],
            ['Release Research Output', 'Publish the decision, adviser assignment, grant status, publication record, or repository link.'],
        ],
        'actions' => ['New Research Entry', 'Review Research', 'Approve Research', 'Research Report'],
        'records' => ['Proposal 001', 'Grant Request 002', 'Repository File 003'],
    ],
];

$pageProfile = null;
foreach ($pageRules as $rule) {
    if (isset($rule['modules']) && !in_array($activeModule, $rule['modules'], true)) {
        continue;
    }

    foreach ($rule['match'] as $needle) {
        if (strpos($pageSlug, $needle) !== false) {
            $pageProfile = $rule;
            break 2;
        }
    }
}

if ($pageProfile === null) {
    $pageProfile = [
        'subject' => $profile['subject'],
        'lead' => 'This process is specific to ' . $pageName . ', including its records, checks, approvals, and final output.',
        'steps' => [
            ['Open ' . $pageName, 'Select or create the record that belongs to this page.'],
            ['Validate ' . $pageName . ' Details', 'Check required fields, attached records, ownership, and approval rules.'],
            ['Process ' . $pageName, 'Save updates, route approvals, and record office remarks for this page.'],
            ['Release ' . $pageName . ' Output', 'Publish the final status, report, confirmation, or official record.'],
        ],
        'actions' => ['New ' . $pageName, 'Validate ' . $pageName, 'Approve ' . $pageName, $pageName . ' Report'],
        'records' => [$pageName . ' 001', $pageName . ' 002', $pageName . ' 003'],
    ];
}

$profile['subject'] = $pageProfile['subject'];
$profile['steps'] = $pageProfile['steps'];
$profile['actions'] = $pageProfile['actions'];
$profile['output'] = strtolower($pageName) . ' output';
$leadText = $pageProfile['lead'];

$processMessages = [
    'new' => $profile['actions'][0] . ' draft has been created for ' . $pageName . '.',
    'validate' => $pageName . ' validation checklist has been completed.',
    'approve' => $pageName . ' approval has been recorded.',
    'report' => $pageName . ' report has been generated.',
    'view' => $pageName . ' record opened for viewing.',
    'edit' => $pageName . ' record opened for editing.',
    'archive' => $pageName . ' moved to Archive.',
    'restore' => $pageName . ' restored to the active list.',
    'delete' => $pageName . ' moved to Archive.',
];
$processMessage = $processMessages[$processAction] ?? '';

require_once ROOT_PATH . '/includes/module-page-ui-catalog.php';
require_once ROOT_PATH . '/includes/mpl-archive.php';

$modKey = (string) ($activeModule ?? '');
$pageKey = (string) ($pageSlug ?? ($activePage ?? ''));
$archiveRef = trim((string) ($_GET['ref'] ?? ''));

if ($processAction === 'archive' && $archiveRef !== '') {
    smsMplArchiveAdd($modKey, $pageKey, $archiveRef);
}
if ($processAction === 'restore' && $archiveRef !== '') {
    smsMplArchiveRemove($modKey, $pageKey, $archiveRef);
}
if ($processAction === 'delete' && $archiveRef !== '') {
    smsMplArchiveAdd($modKey, $pageKey, $archiveRef);
}

$catalogUi = smsModulePageUi((string) $activeModule, (string) $pageSlug, (string) $pageName);

if (is_array($catalogUi)) {
    $mpl = $catalogUi;
    $mpl['alert'] = $processMessage;
    $mpl['module_key'] = $modKey;
    $mpl['page_slug'] = $pageKey;
    require ROOT_PATH . '/includes/module-process-list-view.php';
    return;
}

$recordNames = $pageProfile['records'];
while (count($recordNames) < 5) {
    $recordNames[] = $pageName . ' Record ' . str_pad((string) (count($recordNames) + 1), 2, '0', STR_PAD_LEFT);
}

$statusSeed = [
    ['Pending', 'pending'],
    ['In Progress', 'progress'],
    ['Scheduled', 'scheduled'],
    ['Completed', 'completed'],
    ['Cancelled', 'cancelled'],
];
$scheduleSeed = [
    'Jul 18, 2026 09:00',
    'Jul 17, 2026 14:30',
    'Jul 16, 2026 10:15',
    'Jul 15, 2026 08:45',
    'Jul 14, 2026 13:00',
];
$detailSeed = [
    $profile['owner'] . ' · Queue',
    $moduleLabel . ' Desk',
    'Window A · Processing',
    'For ' . ucfirst($profile['output']),
    'Follow-up · Records',
];

$roleLabel = function_exists('getCurrentUserRole') ? getCurrentUserRole() : 'Staff';

$mplRows = [];
foreach (array_slice($recordNames, 0, 5) as $index => $name) {
    [$statusLabel, $statusClass] = $statusSeed[$index % count($statusSeed)];
    $mplRows[] = [
        'reference' => $profile['reference'] . '-2026-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
        'subject' => $name,
        'subtitle' => $pageName,
        'owner' => $profile['owner'],
        'detail' => $detailSeed[$index % count($detailSeed)],
        'schedule' => $scheduleSeed[$index % count($scheduleSeed)],
        'status' => $statusLabel,
        'status_class' => $statusClass,
        'type' => $pageName,
    ];
}

$mpl = [
    'description' => 'Manage ' . $moduleLabel . ' ' . strtolower($pageName) . ' records, schedules, approvals, and assignments. ' . $leadText,
    'add_label' => '+ ' . $profile['actions'][0],
    'add_process' => 'new',
    'alert' => $processMessage,
    'stats' => [
        ['label' => 'Total Records', 'value' => (string) count($mplRows), 'icon' => 'fa-layer-group', 'tone' => 'blue'],
        ['label' => $profile['stats'][0], 'value' => '12', 'icon' => 'fa-folder-plus', 'tone' => 'amber'],
        ['label' => $profile['stats'][1], 'value' => '8', 'icon' => 'fa-spinner', 'tone' => 'purple'],
        ['label' => $profile['stats'][2], 'value' => '24', 'icon' => 'fa-check-circle', 'tone' => 'green'],
    ],
    'search_placeholder' => 'Search by reference, ' . strtolower($profile['subject']) . ', or detail.',
    'statuses' => ['All Status', 'Pending', 'In Progress', 'Scheduled', 'Completed', 'Cancelled'],
    'types' => ['All Types', $pageName, 'Review', 'Update', 'Release'],
    'list_title' => $pageName . ' List',
    'list_subtitle' => 'View and manage all ' . strtolower($pageName) . ' records for ' . $roleLabel . '.',
    'columns' => [
        'ref' => 'Reference No.',
        'subject' => $profile['subject'],
        'owner' => 'Assigned To',
        'detail' => 'Office / Detail',
        'schedule' => 'Schedule',
    ],
    'rows' => $mplRows,
];

require ROOT_PATH . '/includes/module-process-list-view.php';
