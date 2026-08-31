<?php
/**
 * SMS 2 – Per-module security guidance (shared catalog)
 */

declare(strict_types=1);

/**
 * Security checklist / notes for each office module.
 *
 * @return array<string, array{label:string,icon:string,summary:string,checks:list<string>,tips:list<string>}>
 */
function smsModuleSecurityCatalog(): array
{
    return [
        'enrollment' => [
            'label'   => 'Enrollment Management',
            'icon'    => 'fa-user-graduate',
            'summary' => 'Protect applicant data, document uploads, and ID generation workflows.',
            'checks'  => [
                'Only authorized registrar staff can validate enrollments',
                'Uploaded documents must stay private to enrollment officers',
                'ID numbers are generated only after validation',
            ],
            'tips' => [
                'Never share your password with co-staff',
                'Log out when leaving a shared office PC',
                'Report suspicious enrollment changes to IT / Super Admin',
            ],
        ],
        'registrar' => [
            'label'   => 'Registrar',
            'icon'    => 'fa-folder-open',
            'summary' => 'Student records, transcripts, and guardian/health data are highly sensitive.',
            'checks'  => [
                'Access is limited to registrar-authorized roles',
                'Transcript and persona files are audit-sensitive',
                'Export/print actions should be for official use only',
            ],
            'tips' => [
                'Verify identity before releasing student documents',
                'Do not leave student records open on unattended screens',
                'Use Change Password regularly for this high-privilege role',
            ],
        ],
        'curriculum' => [
            'label'   => 'Curriculum & Subjects',
            'icon'    => 'fa-book',
            'summary' => 'Curriculum edits affect enrollment eligibility and academic planning.',
            'checks'  => [
                'Only authorized users can edit curriculum structures',
                'Prerequisite changes should be reviewed before publish',
                'Exports should not include unnecessary student PII',
            ],
            'tips' => [
                'Confirm changes with department heads before finalizing',
                'Keep a note of major curriculum updates for audit',
            ],
        ],
        'scheduling' => [
            'label'   => 'Class Schedule',
            'icon'    => 'fa-calendar-alt',
            'summary' => 'Schedule changes impact students, faculty load, and room use.',
            'checks'  => [
                'Conflict checks should be run before publishing',
                'Only authorized staff can alter live schedules',
            ],
            'tips' => [
                'Avoid last-minute bulk schedule edits without approval',
                'Notify affected offices after major schedule updates',
            ],
        ],
        'payment' => [
            'label'   => 'Payment Management',
            'icon'    => 'fa-credit-card',
            'summary' => 'Financial data requires the strictest handling and audit trail.',
            'checks'  => [
                'Only finance roles can collect or adjust payments',
                'Void/adjust actions must leave an audit reason',
                'Never store card numbers in notes or uploads',
            ],
            'tips' => [
                'Change your password if you suspect account misuse',
                'Do not process payments while another user is logged in',
                'Report mismatched ledgers immediately to Super Admin',
            ],
        ],
        'faculty' => [
            'label'   => 'Faculty Management',
            'icon'    => 'fa-chalkboard-teacher',
            'summary' => 'HR/faculty profiles and payroll setup are confidential.',
            'checks'  => [
                'HR-only access to salary and leave approvals',
                'Faculty evaluations remain confidential',
            ],
            'tips' => [
                'Do not email password resets — use in-system reset via Super Admin',
                'Lock your session before leaving HR workstations',
            ],
        ],
        'cocurricular' => [
            'label'   => 'Co-Curricular',
            'icon'    => 'fa-users',
            'summary' => 'Club budgets and student activity records need controlled access.',
            'checks'  => [
                'Budget approvals are limited to OSA-authorized staff',
                'Membership data should not be shared publicly',
            ],
            'tips' => [
                'Verify officers before approving budget requests',
                'Keep volunteer hour records accurate for audit',
            ],
        ],
        'lms' => [
            'label'   => 'Online Learning & LMS',
            'icon'    => 'fa-laptop',
            'summary' => 'Course materials, quizzes, and grades must stay role-scoped.',
            'checks'  => [
                'Answer keys and quizzes are not publicly linkable',
                'Grading actions are attributed to the logged-in user',
            ],
            'tips' => [
                'Do not share LMS account credentials with students',
                'Use strong passwords — LMS accounts can expose grades',
            ],
        ],
        'crad' => [
            'label'   => 'CRAD',
            'icon'    => 'fa-flask',
            'summary' => 'Research proposals, grants, and repositories are confidential IP.',
            'checks'  => [
                'Proposal files are limited to CRAD-authorized officers',
                'Grant and adviser assignments are audit-sensitive',
                'Repository downloads should be tracked',
            ],
            'tips' => [
                'Do not download research files to personal USB drives',
                'Reset your password if a shared PC was used without logout',
                'Report unauthorized proposal access to Super Admin',
            ],
        ],
        'crad_grant' => [
            'label'   => 'Research Grant',
            'icon'    => 'fa-hand-holding-usd',
            'summary' => 'Research grant applications, evaluations, approvals, releases, and funding reports require controlled access.',
            'checks'  => [
                'Grant applications are limited to authorized research funding staff',
                'Evaluation scores and approval history remain audit-sensitive',
                'Disbursement and fund-release records must be tracked',
            ],
            'tips' => [
                'Verify supporting documents before approving funds',
                'Do not export funding records to personal storage',
                'Report mismatched grant or release amounts immediately',
            ],
        ],
        'accreditation' => [
            'label'   => 'Accreditation Management',
            'icon'    => 'fa-award',
            'summary' => 'Compliance evidence and audit findings are institution-sensitive.',
            'checks'  => [
                'QA-only access to accreditation repositories',
                'Evidence files should not be altered without version notes',
            ],
            'tips' => [
                'Keep compliance uploads organized and labeled',
                'Use official accounts only — no shared QA logins',
            ],
        ],
        'reports-analytics' => [
            'label'   => 'Reports & Analytics',
            'icon'    => 'fa-chart-bar',
            'summary' => 'Reports may include aggregated or sensitive operational data.',
            'checks'  => [
                'Report pages are filtered by your role',
                'Exports should be handled as confidential when they include PII',
            ],
            'tips' => [
                'Do not email raw exports to personal inboxes',
                'Clear downloaded reports from shared computers',
            ],
        ],
        'user-management' => [
            'label'   => 'User Management',
            'icon'    => 'fa-users-cog',
            'summary' => 'Highest privilege area — account creation, RBAC, and audit logs.',
            'checks'  => [
                'Super Admin only',
                'Permission changes are logged',
                'Password resets for other users are admin-controlled',
            ],
            'tips' => [
                'Use a unique strong password for Super Admin',
                'Review activity logs after major access changes',
            ],
        ],
        'student_portal' => [
            'label'   => 'Student Portal',
            'icon'    => 'fa-user-graduate',
            'summary' => 'Students can only view their own records and submissions.',
            'checks'  => [
                'Your account shows only your student data',
                'Document uploads are tied to your student ID',
            ],
            'tips' => [
                'Never share your student login with classmates',
                'Change password immediately if someone else used your account',
            ],
        ],
    ];
}

function smsModuleSecurityInfo(string $moduleKey): ?array
{
    $catalog = smsModuleSecurityCatalog();
    return $catalog[$moduleKey] ?? null;
}
