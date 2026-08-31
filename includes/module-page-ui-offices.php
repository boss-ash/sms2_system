<?php
/**
 * SMS 2 - Accurate page UI catalogs for office modules
 * (Curriculum, Scheduling, Payment, Faculty, LMS, Co-Curricular, Accreditation)
 */

if (!function_exists('smsPageUiDef')) {
    /**
     * @param array $rows [subject, owner, detail, status, status_class, type?]
     */
    function smsPageUiDef(
        string $title,
        string $description,
        string $addLabel,
        string $prefix,
        array $stats,
        array $columns,
        array $rows,
        string $searchHint = 'reference, name, or detail'
    ): array {
        return [
            'title' => $title,
            'description' => $description,
            'add_label' => $addLabel,
            'list_subtitle' => 'View and manage all ' . strtolower($title) . ' records.',
            'search_placeholder' => 'Search by ' . $searchHint . '.',
            'stats' => $stats,
            'columns' => $columns,
            'rows' => smsBuildSampleRows($prefix, (crc32($title) % 80) + 1, array_map(
                static function (array $r): array {
                    return [$r[0], $r[1], $r[2], $r[3], $r[4], $r[5] ?? 'General'];
                },
                $rows
            ), $title),
        ];
    }
}

if (!function_exists('smsStats4')) {
    function smsStats4(string $a, string $av, string $ai, string $b, string $bv, string $bi, string $c, string $cv, string $ci, string $d, string $dv, string $di): array
    {
        return [
            ['label' => $a, 'value' => $av, 'icon' => $ai, 'tone' => 'blue'],
            ['label' => $b, 'value' => $bv, 'icon' => $bi, 'tone' => 'amber'],
            ['label' => $c, 'value' => $cv, 'icon' => $ci, 'tone' => 'green'],
            ['label' => $d, 'value' => $dv, 'icon' => $di, 'tone' => 'purple'],
        ];
    }
}

if (!function_exists('smsCols')) {
    function smsCols(string $ref, string $subject, string $owner, string $detail, string $schedule = 'Updated'): array
    {
        return [
            'ref' => $ref,
            'subject' => $subject,
            'owner' => $owner,
            'detail' => $detail,
            'schedule' => $schedule,
        ];
    }
}

if (!function_exists('smsCurriculumPageUiCatalog')) {
    function smsCurriculumPageUiCatalog(): array
    {
        return [
            'curriculum-builder' => smsPageUiDef(
                'Curriculum Builder',
                'Build and maintain program curricula, subject sequences, and versioned academic maps.',
                '+ New Curriculum Map',
                'CUR',
                smsStats4('Maps', '5', 'fa-book', 'Draft', '2', 'fa-pen', 'Approved', '2', 'fa-check', 'Archived', '1', 'fa-archive'),
                smsCols('Map No.', 'Curriculum / Program', 'Version', 'Units / Subjects', 'Updated'),
                [
                    ['BSIT Curriculum AY 2025-2026', 'v3.2', '158 units · 52 subjects', 'Approved', 'completed', 'Curriculum'],
                    ['BSED English Curriculum', 'v2.1', '164 units · 54 subjects', 'Draft', 'pending', 'Curriculum'],
                    ['BSBA Curriculum Revision', 'v4.0', '150 units · 48 subjects', 'For Review', 'progress', 'Curriculum'],
                    ['BSCrim Curriculum Map', 'v1.8', '160 units · 50 subjects', 'Approved', 'completed', 'Curriculum'],
                    ['BSHM Ladderized Track', 'v1.0', '90 units · 30 subjects', 'Archived', 'cancelled', 'Curriculum'],
                ],
                'map no., program, or version'
            ),
            'subject-mapping' => smsPageUiDef(
                'Subject Mapping',
                'Map subjects to programs, year levels, and curriculum nodes.',
                '+ New Subject Map',
                'SUB',
                smsStats4('Mapped', '5', 'fa-project-diagram', 'Pending', '1', 'fa-clock', 'Active', '3', 'fa-check', 'Conflicts', '1', 'fa-exclamation'),
                smsCols('Map No.', 'Subject Code / Title', 'Program', 'Year Level', 'Updated'),
                [
                    ['IT101 · Introduction to Computing', 'BSIT', '1st Year · 1st Sem', 'Active', 'completed', 'Subject'],
                    ['EDUC201 · Facilitating Learning', 'BSED English', '2nd Year · 1st Sem', 'Active', 'completed', 'Subject'],
                    ['ACCTG1 · Fundamentals of Accounting', 'BSBA', '1st Year · 2nd Sem', 'Pending', 'pending', 'Subject'],
                    ['CRIM301 · Criminal Law 1', 'BSCrim', '3rd Year · 1st Sem', 'Active', 'completed', 'Subject'],
                    ['HM102 · Kitchen Operations', 'BSHM', '1st Year · 2nd Sem', 'Conflict', 'cancelled', 'Subject'],
                ],
                'subject code, title, or program'
            ),
            'pre-requisite-configuration' => smsPageUiDef(
                'Pre-requisite Configuration',
                'Configure subject prerequisites and co-requisites before enrollment opening.',
                '+ New Prerequisite',
                'PRE',
                smsStats4('Rules', '5', 'fa-link', 'For Check', '1', 'fa-search', 'Active', '3', 'fa-check', 'Broken', '1', 'fa-unlink'),
                smsCols('Rule No.', 'Subject', 'Requires', 'Type', 'Updated'),
                [
                    ['IT201 · Data Structures', 'IT101 · Intro to Computing', 'Hard prerequisite', 'Active', 'completed', 'Prerequisite'],
                    ['ACCTG2 · Financial Accounting', 'ACCTG1 · Fundamentals', 'Hard prerequisite', 'Active', 'completed', 'Prerequisite'],
                    ['EDUC301 · Assessment', 'EDUC201 · Facilitating Learning', 'Hard prerequisite', 'For Check', 'pending', 'Prerequisite'],
                    ['CRIM302 · Criminal Law 2', 'CRIM301 · Criminal Law 1', 'Hard prerequisite', 'Active', 'completed', 'Prerequisite'],
                    ['HM201 · Quantity Cookery', 'HM102 · Kitchen Operations', 'Co-requisite issue', 'Broken', 'cancelled', 'Prerequisite'],
                ],
                'rule no., subject, or required course'
            ),
            'electives-manager' => smsPageUiDef(
                'Electives Manager',
                'Manage elective offerings, slots, and program elective banks.',
                '+ New Elective',
                'ELE',
                smsStats4('Electives', '5', 'fa-list', 'Open', '3', 'fa-door-open', 'Full', '1', 'fa-door-closed', 'Closed', '1', 'fa-ban'),
                smsCols('Elective No.', 'Elective Subject', 'Program Bank', 'Slots', 'Updated'),
                [
                    ['IT Elective · Mobile Development', 'BSIT Elective Bank', '40 / 40 seats', 'Full', 'cancelled', 'Elective'],
                    ['Educ Elective · Inclusive Education', 'BSED Elective Bank', '28 / 40 seats', 'Open', 'completed', 'Elective'],
                    ['BA Elective · Digital Marketing', 'BSBA Elective Bank', '35 / 40 seats', 'Open', 'completed', 'Elective'],
                    ['Crim Elective · Cybercrime', 'BSCrim Elective Bank', '22 / 35 seats', 'Open', 'completed', 'Elective'],
                    ['HM Elective · Barista Skills', 'BSHM Elective Bank', '0 / 25 seats', 'Closed', 'pending', 'Elective'],
                ],
                'elective, program bank, or seats'
            ),
            'academic-strand-assignment' => smsPageUiDef(
                'Academic Strand Assignment',
                'Assign academic strands/tracks to SHS and ladderized program cohorts.',
                '+ New Strand Assignment',
                'STR',
                smsStats4('Assignments', '5', 'fa-layer-group', 'Pending', '1', 'fa-clock', 'Assigned', '3', 'fa-user-check', 'Hold', '1', 'fa-pause'),
                smsCols('Assign No.', 'Student / Cohort', 'Strand / Track', 'Program', 'Updated'),
                [
                    ['Grade 12 · STEM Cohort A', 'STEM', 'SHS · STEM', 'Assigned', 'completed', 'Strand'],
                    ['Grade 11 · ABM Cohort B', 'ABM', 'SHS · ABM', 'Assigned', 'completed', 'Strand'],
                    ['Grade 12 · HUMSS Cohort C', 'HUMSS', 'SHS · HUMSS', 'Pending', 'pending', 'Strand'],
                    ['Ladderized IT Track Group', 'IT Ladder', 'BSIT Ladderized', 'Assigned', 'completed', 'Strand'],
                    ['TVL HE Cohort D', 'TVL-HE', 'SHS · TVL', 'On Hold', 'cancelled', 'Strand'],
                ],
                'cohort, strand, or program'
            ),
            'subject-offering-history' => smsPageUiDef(
                'Subject Offering History',
                'Review historical subject offerings by term, section count, and enrollment size.',
                '+ Add Offering Log',
                'OFF',
                smsStats4('Offerings', '5', 'fa-history', 'Current Term', '2', 'fa-calendar', 'Past Term', '2', 'fa-archive', 'Cancelled', '1', 'fa-ban'),
                smsCols('Offering No.', 'Subject', 'Term', 'Sections / Enrolled', 'Logged'),
                [
                    ['IT101 · Intro to Computing', '1st Sem AY 2025-2026', '4 sections · 148 enrolled', 'Current', 'scheduled', 'Offering'],
                    ['EDUC201 · Facilitating Learning', '1st Sem AY 2025-2026', '3 sections · 96 enrolled', 'Current', 'scheduled', 'Offering'],
                    ['ACCTG1 · Fundamentals', '2nd Sem AY 2024-2025', '5 sections · 170 enrolled', 'Past', 'completed', 'Offering'],
                    ['CRIM301 · Criminal Law 1', '1st Sem AY 2024-2025', '2 sections · 70 enrolled', 'Past', 'completed', 'Offering'],
                    ['HM102 · Kitchen Operations', 'Summer 2025', 'Cancelled offering', 'Cancelled', 'cancelled', 'Offering'],
                ],
                'subject, term, or offering no.'
            ),
            'subject-equivalency-tool' => smsPageUiDef(
                'Subject Equivalency Tool',
                'Evaluate transfer and ladderized subject equivalencies against the active curriculum.',
                '+ New Equivalency',
                'EQV',
                smsStats4('Requests', '5', 'fa-exchange-alt', 'For Eval', '2', 'fa-search', 'Credited', '2', 'fa-check', 'Denied', '1', 'fa-times'),
                smsCols('Equiv No.', 'Student', 'External Subject', 'Equivalent To', 'Updated'),
                [
                    ['Mark Villanueva', 'CompProg 1 (Other School)', 'IT101 · Intro to Computing', 'Credited', 'completed', 'Equivalency'],
                    ['Angela Cruz', 'Basic Accounting', 'ACCTG1 · Fundamentals', 'For Evaluation', 'pending', 'Equivalency'],
                    ['Carlos Mendoza', 'Intro to Crim', 'CRIM101 · Intro to Criminology', 'Credited', 'completed', 'Equivalency'],
                    ['Liza Santos', 'Food Lab 1', 'HM102 · Kitchen Operations', 'For Evaluation', 'pending', 'Equivalency'],
                    ['Sofia Reyes', 'Web Design Basics', 'IT105 · Web Systems', 'Denied', 'cancelled', 'Equivalency'],
                ],
                'student, external subject, or equivalent'
            ),
            'grade-weighting-setup' => smsPageUiDef(
                'Grade Weighting Setup',
                'Configure component weights for quizzes, exams, projects, and class standing.',
                '+ New Weight Scheme',
                'WGT',
                smsStats4('Schemes', '5', 'fa-percentage', 'Draft', '1', 'fa-pen', 'Active', '3', 'fa-check', 'Retired', '1', 'fa-archive'),
                smsCols('Scheme No.', 'Scheme Name', 'Applies To', 'Weights', 'Updated'),
                [
                    ['BSIT Default Weighting', 'All BSIT lecture', 'Quiz 20 · Exam 40 · Project 25 · CS 15', 'Active', 'completed', 'Weighting'],
                    ['BSED Outcomes Weighting', 'Education majors', 'Performance 40 · Exam 30 · Portfolio 30', 'Active', 'completed', 'Weighting'],
                    ['Lab-Heavy IT Subjects', 'IT laboratory', 'Lab 50 · Exam 30 · Quiz 20', 'Active', 'completed', 'Weighting'],
                    ['Hospitality Skills Scheme', 'BSHM laboratory', 'Skills 60 · Written 40', 'Draft', 'pending', 'Weighting'],
                    ['Old GE Weighting 2023', 'General Education', 'Quiz 30 · Exam 70', 'Retired', 'cancelled', 'Weighting'],
                ],
                'scheme, program, or weight set'
            ),
            'ched-deped-validator' => smsPageUiDef(
                'CHED/DepEd Validator',
                'Validate curriculum compliance against CHED CMO and DepEd standards.',
                '+ New Compliance Check',
                'CMP',
                smsStats4('Checks', '5', 'fa-clipboard-check', 'Findings', '2', 'fa-exclamation', 'Compliant', '2', 'fa-check', 'Failed', '1', 'fa-times'),
                smsCols('Check No.', 'Program / Standard', 'Checked By', 'Finding', 'Checked'),
                [
                    ['BSIT vs CMO 25 s.2015', 'Academic Affairs', 'Units within range', 'Compliant', 'completed', 'Compliance'],
                    ['BSED vs CMO 74 s.2017', 'QA Desk', 'Missing professional subject', 'Finding', 'pending', 'Compliance'],
                    ['SHS STEM DepEd Mapping', 'SHS Coordinator', 'Strand map complete', 'Compliant', 'completed', 'Compliance'],
                    ['BSBA vs CMO 17 s.2017', 'Academic Affairs', 'Elective bank incomplete', 'Finding', 'progress', 'Compliance'],
                    ['BSHM vs CMO 62 s.2017', 'QA Desk', 'Lab hours below minimum', 'Failed', 'cancelled', 'Compliance'],
                ],
                'program, standard, or finding'
            ),
            'curriculum-export-tool' => smsPageUiDef(
                'Curriculum Export Tool',
                'Export curriculum maps, subject lists, and validation packs for offices and auditors.',
                '+ New Export Job',
                'EXP',
                smsStats4('Exports', '5', 'fa-file-export', 'Queued', '1', 'fa-hourglass', 'Ready', '3', 'fa-download', 'Failed', '1', 'fa-exclamation'),
                smsCols('Export No.', 'Package Name', 'Requested By', 'Format', 'Generated'),
                [
                    ['BSIT Curriculum Pack AY2526', 'Academic Affairs', 'PDF + XLSX', 'Ready', 'completed', 'Export'],
                    ['BSED Subject Matrix', 'Registrar', 'XLSX', 'Ready', 'completed', 'Export'],
                    ['CHED Compliance Bundle', 'QA Office', 'ZIP · PDF', 'Queued', 'pending', 'Export'],
                    ['All Program Maps Snapshot', 'System Admin', 'CSV', 'Ready', 'completed', 'Export'],
                    ['BSHM Ladder Export', 'Academic Affairs', 'PDF', 'Failed', 'cancelled', 'Export'],
                ],
                'export no., package, or format'
            ),
        ];
    }
}

if (!function_exists('smsSchedulingPageUiCatalog')) {
    function smsSchedulingPageUiCatalog(): array
    {
        return [
            'section-assignment-tool' => smsPageUiDef(
                'Section Assignment Tool',
                'Assign students and class blocks to sections with capacity and program rules.',
                '+ New Section Assignment',
                'SEC',
                smsStats4('Sections', '5', 'fa-users', 'Open', '3', 'fa-door-open', 'Full', '1', 'fa-door-closed', 'Closed', '1', 'fa-ban'),
                smsCols('Section No.', 'Section Code', 'Program / Year', 'Capacity', 'Updated'),
                [
                    ['BSIT-1A', 'BSIT · 1st Year', '40 / 40', 'Full', 'cancelled', 'Section'],
                    ['BSED-2B', 'BSED · 2nd Year', '32 / 40', 'Open', 'completed', 'Section'],
                    ['BSBA-1C', 'BSBA · 1st Year', '28 / 40', 'Open', 'completed', 'Section'],
                    ['CRIM-3A', 'BSCrim · 3rd Year', '35 / 40', 'Open', 'completed', 'Section'],
                    ['HM-1A', 'BSHM · 1st Year', '0 / 35', 'Closed', 'pending', 'Section'],
                ],
                'section, program, or capacity'
            ),
            'teacher-schedule-mapping' => smsPageUiDef(
                'Teacher Schedule Mapping',
                'Map faculty teaching loads to days, time blocks, and rooms.',
                '+ New Teacher Map',
                'TCH',
                smsStats4('Maps', '5', 'fa-chalkboard-teacher', 'Conflicts', '1', 'fa-bolt', 'Published', '3', 'fa-check', 'Draft', '1', 'fa-pen'),
                smsCols('Map No.', 'Faculty', 'Subjects', 'Load Hours', 'Updated'),
                [
                    ['Prof. Ana Reyes', 'IT101, IT201', '18 hrs / week', 'Published', 'completed', 'Teacher'],
                    ['Prof. Joel Cruz', 'CRIM301, CRIM302', '15 hrs / week', 'Published', 'completed', 'Teacher'],
                    ['Prof. Clara Santos', 'EDUC201, EDUC301', '21 hrs / week', 'Conflict', 'cancelled', 'Teacher'],
                    ['Prof. Mark Lim', 'ACCTG1, ACCTG2', '12 hrs / week', 'Published', 'completed', 'Teacher'],
                    ['Prof. Liza Gomez', 'HM102 Lab', '9 hrs / week', 'Draft', 'pending', 'Teacher'],
                ],
                'faculty, subject, or load'
            ),
            'conflict-checker' => smsPageUiDef(
                'Conflict Checker',
                'Detect room, teacher, and section schedule conflicts before publishing.',
                '+ Run Conflict Check',
                'CNF',
                smsStats4('Checks', '5', 'fa-exclamation-triangle', 'Conflicts', '2', 'fa-bolt', 'Cleared', '2', 'fa-check', 'Needs Fix', '1', 'fa-wrench'),
                smsCols('Check No.', 'Conflict Item', 'Type', 'Detail', 'Checked'),
                [
                    ['Room 204 · Mon 8-10', 'Room overlap', 'BSIT-1A vs BSED-2B', 'Conflict', 'cancelled', 'Conflict'],
                    ['Prof. Clara Santos overload', 'Teacher load', '21 hrs exceeds cap', 'Needs Fix', 'pending', 'Conflict'],
                    ['Lab 3 · Wed 1-4', 'Room check', 'No overlap found', 'Cleared', 'completed', 'Conflict'],
                    ['CRIM-3A vs PE schedule', 'Section time', 'Resolved by reblock', 'Cleared', 'completed', 'Conflict'],
                    ['HM Kitchen Lab double book', 'Facility', 'Same lab two classes', 'Conflict', 'cancelled', 'Conflict'],
                ],
                'conflict, room, or teacher'
            ),
            'exam-timetable-generator' => smsPageUiDef(
                'Exam Timetable Generator',
                'Generate midterm and final examination timetables by program and room.',
                '+ Generate Exam Grid',
                'EXM',
                smsStats4('Exam Sets', '5', 'fa-file-alt', 'Draft', '1', 'fa-pen', 'Published', '3', 'fa-check', 'Conflict', '1', 'fa-bolt'),
                smsCols('Exam No.', 'Exam Set', 'Coverage', 'Rooms Used', 'Generated'),
                [
                    ['Midterm Exam · BSIT', 'All BSIT year levels', 'Rooms 201-205', 'Published', 'completed', 'Exam'],
                    ['Final Exam · BSED', 'BSED majors', 'Rooms 301-304', 'Published', 'completed', 'Exam'],
                    ['Midterm Exam · BSBA', 'BSBA 1st-2nd year', 'Gym + 110', 'Draft', 'pending', 'Exam'],
                    ['Final Exam · BSCrim', 'All Crim sections', 'Rooms 105-108', 'Published', 'completed', 'Exam'],
                    ['Practical Exam · BSHM', 'Lab subjects', 'Kitchen conflict', 'Conflict', 'cancelled', 'Exam'],
                ],
                'exam set, program, or room'
            ),
            'substitute-assignment-tracker' => smsPageUiDef(
                'Substitute Assignment Tracker',
                'Track substitute teachers for absences, leave, and emergency coverage.',
                '+ New Substitute',
                'SUB',
                smsStats4('Requests', '5', 'fa-user-plus', 'Open', '2', 'fa-clock', 'Covered', '2', 'fa-check', 'Unfilled', '1', 'fa-times'),
                smsCols('Sub No.', 'Absent Faculty', 'Substitute', 'Class Covered', 'Date'),
                [
                    ['Prof. Ana Reyes', 'Prof. Rico Tan', 'IT101 · BSIT-1A', 'Covered', 'completed', 'Substitute'],
                    ['Prof. Joel Cruz', 'Pending assignment', 'CRIM301 · CRIM-3A', 'Open', 'pending', 'Substitute'],
                    ['Prof. Mark Lim', 'Prof. Ella Uy', 'ACCTG1 · BSBA-1C', 'Covered', 'completed', 'Substitute'],
                    ['Prof. Clara Santos', 'No available sub', 'EDUC201 · BSED-2B', 'Unfilled', 'cancelled', 'Substitute'],
                    ['Prof. Liza Gomez', 'Prof. Nina Sy', 'HM102 Lab', 'Open', 'progress', 'Substitute'],
                ],
                'faculty, substitute, or class'
            ),
            'special-class-scheduler' => smsPageUiDef(
                'Special Class Scheduler',
                'Schedule make-up, tutorial, and special class sessions outside regular blocks.',
                '+ New Special Class',
                'SPC',
                smsStats4('Special Classes', '5', 'fa-star', 'Scheduled', '2', 'fa-calendar', 'Done', '2', 'fa-check', 'Cancelled', '1', 'fa-ban'),
                smsCols('Class No.', 'Special Class', 'Faculty', 'Room / Time', 'Schedule'),
                [
                    ['IT101 Make-up Lecture', 'Prof. Ana Reyes', 'Room 204 · Sat 9-12', 'Scheduled', 'scheduled', 'Special'],
                    ['EDUC Remedial Session', 'Prof. Clara Santos', 'Room 301 · Fri 4-6', 'Done', 'completed', 'Special'],
                    ['Accounting Tutorial', 'Prof. Mark Lim', 'Room 110 · Sat 1-3', 'Scheduled', 'scheduled', 'Special'],
                    ['Crim Review Class', 'Prof. Joel Cruz', 'Room 105 · Sun 9-12', 'Done', 'completed', 'Special'],
                    ['HM Skills Catch-up', 'Prof. Liza Gomez', 'Kitchen · Cancelled', 'Cancelled', 'cancelled', 'Special'],
                ],
                'special class, faculty, or room'
            ),
            'room-availability-checker' => smsPageUiDef(
                'Room Availability Checker',
                'Check classroom and laboratory availability by day, time, and capacity.',
                '+ Check Room Slot',
                'ROM',
                smsStats4('Rooms', '5', 'fa-door-open', 'Available', '2', 'fa-check', 'Occupied', '2', 'fa-users', 'Maintenance', '1', 'fa-tools'),
                smsCols('Room No.', 'Room / Lab', 'Capacity', 'Status Detail', 'Checked'),
                [
                    ['Room 204', '40 seats', 'Free · Mon 8-10', 'Available', 'completed', 'Room'],
                    ['Lab 3 · Computer Lab', '40 PCs', 'BSIT-1A occupying', 'Occupied', 'cancelled', 'Room'],
                    ['Room 110', '45 seats', 'Free · Tue 1-3', 'Available', 'completed', 'Room'],
                    ['Kitchen Lab', '25 stations', 'Under maintenance', 'Maintenance', 'pending', 'Room'],
                    ['Gym Multi-purpose', '200 capacity', 'PE classes blocked', 'Occupied', 'progress', 'Room'],
                ],
                'room, capacity, or availability'
            ),
            'schedule-cloning-tool' => smsPageUiDef(
                'Schedule Cloning Tool',
                'Clone previous-term schedules into the new term with controlled adjustments.',
                '+ Clone Schedule Set',
                'CLN',
                smsStats4('Clone Jobs', '5', 'fa-copy', 'Queued', '1', 'fa-hourglass', 'Done', '3', 'fa-check', 'Failed', '1', 'fa-times'),
                smsCols('Job No.', 'Source Term', 'Target Term', 'Programs', 'Ran'),
                [
                    ['1st Sem AY2425 → AY2526', '1st Sem AY 2025-2026', 'BSIT, BSED', 'Done', 'completed', 'Clone'],
                    ['2nd Sem AY2425 → AY2526', '2nd Sem AY 2025-2026', 'BSBA, BSCrim', 'Done', 'completed', 'Clone'],
                    ['Summer 2025 → Summer 2026', 'Summer 2026', 'All programs', 'Queued', 'pending', 'Clone'],
                    ['SHS STEM clone', '1st Sem AY 2025-2026', 'SHS STEM', 'Done', 'completed', 'Clone'],
                    ['BSHM Lab clone', '1st Sem AY 2025-2026', 'BSHM labs', 'Failed', 'cancelled', 'Clone'],
                ],
                'source term, target term, or program'
            ),
            'time-block-generator' => smsPageUiDef(
                'Time Block Generator',
                'Generate standard time blocks for lecture, laboratory, and exam grids.',
                '+ Generate Blocks',
                'BLK',
                smsStats4('Block Sets', '5', 'fa-clock', 'Draft', '1', 'fa-pen', 'Active', '3', 'fa-check', 'Retired', '1', 'fa-archive'),
                smsCols('Block No.', 'Block Set', 'Pattern', 'Applies To', 'Updated'),
                [
                    ['MWF Lecture Blocks', '7:00-8:00 … 4:00-5:00', 'All lecture subjects', 'Active', 'completed', 'Blocks'],
                    ['TTh Long Blocks', '7:00-8:30 … 2:30-4:00', 'Major lecture', 'Active', 'completed', 'Blocks'],
                    ['Lab 3-hour Blocks', '8:00-11:00 / 1:00-4:00', 'IT / HM labs', 'Active', 'completed', 'Blocks'],
                    ['Evening Class Blocks', '5:00-8:00', 'Working students', 'Draft', 'pending', 'Blocks'],
                    ['Old 2019 Block Grid', 'Legacy pattern', 'Archived campus grid', 'Retired', 'cancelled', 'Blocks'],
                ],
                'block set, pattern, or use case'
            ),
            'calendar-integration' => smsPageUiDef(
                'Calendar Integration',
                'Sync class schedules, exams, and special classes to the institutional calendar.',
                '+ Sync Calendar',
                'CAL',
                smsStats4('Sync Jobs', '5', 'fa-calendar-alt', 'Pending', '1', 'fa-clock', 'Synced', '3', 'fa-check', 'Error', '1', 'fa-exclamation'),
                smsCols('Sync No.', 'Calendar Feed', 'Source Module', 'Events', 'Synced'),
                [
                    ['Faculty Teaching Calendar', 'Teacher Schedule Mapping', '182 events', 'Synced', 'completed', 'Calendar'],
                    ['Student Class Calendar', 'Section Assignment', '2,410 events', 'Synced', 'completed', 'Calendar'],
                    ['Exam Calendar Feed', 'Exam Timetable', '96 events', 'Pending', 'pending', 'Calendar'],
                    ['Special Class Feed', 'Special Class Scheduler', '14 events', 'Synced', 'completed', 'Calendar'],
                    ['Room Booking Feed', 'Room Availability', 'API timeout', 'Error', 'cancelled', 'Calendar'],
                ],
                'calendar feed, source, or events'
            ),
        ];
    }
}
