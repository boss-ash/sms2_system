<?php
/**
 * SMS 2 - Student Portal Pages
 */
$studentPortalPage = $studentPortalPage ?? 'my-profile';

require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once __DIR__ . '/../../includes/breadcrumbs.php';

$studentId = $_SESSION['student_id'] ?? 'S230000001';

$latestTitleApproval = null;
$researchCurrentStatus = 'Not Started';
$researchCurrentTitle = 'No title approval sent yet';
$researchCurrentUpdated = '';
$researchCurrentAdviser = '';
$researchCurrentProgress = 0;

try {
    $cradPdo = getCradDatabaseConnection();
    $titleStmt = $cradPdo->prepare(
        "SELECT proposed_title, status, adviser_name, coordinator_status, crad_status,
                sent_at, reviewed_at, coordinator_reviewed_at, crad_reviewed_at, updated_at
         FROM title_approvals
         WHERE student_id = :student_id
         ORDER BY id DESC
         LIMIT 1"
    );
    $titleStmt->execute([':student_id' => $studentId]);
    $latestTitleApproval = $titleStmt->fetch() ?: null;
} catch (Throwable $e) {
    error_log('Student dashboard title approval status load failed: ' . $e->getMessage());
}

if ($latestTitleApproval) {
    $titleStatus = (string) ($latestTitleApproval['status'] ?? '');
    $coordinatorStatus = (string) ($latestTitleApproval['coordinator_status'] ?? '');
    $cradStatus = (string) ($latestTitleApproval['crad_status'] ?? '');

    if (strcasecmp($titleStatus, 'Returned') === 0 || strcasecmp($coordinatorStatus, 'Returned') === 0 || strcasecmp($cradStatus, 'Returned') === 0) {
        $researchCurrentStatus = 'Returned';
    } elseif (strcasecmp($cradStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'CRAD Approved';
    } elseif (strcasecmp($coordinatorStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'Coordinator Approved';
    } elseif (strcasecmp($titleStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'Adviser Approved';
    } elseif (strcasecmp($titleStatus, 'Pending') === 0 || !empty($latestTitleApproval['sent_at'])) {
        $researchCurrentStatus = 'Document Packet Sent';
    }

    $researchCurrentTitle = trim((string) ($latestTitleApproval['proposed_title'] ?? '')) ?: 'Research title approval';
    $researchCurrentAdviser = trim((string) ($latestTitleApproval['adviser_name'] ?? ''));
    $statusTimeRaw = (string) (
        $latestTitleApproval['crad_reviewed_at']
        ?: $latestTitleApproval['coordinator_reviewed_at']
        ?: $latestTitleApproval['reviewed_at']
        ?: $latestTitleApproval['sent_at']
        ?: $latestTitleApproval['updated_at']
        ?: ''
    );
    $statusTime = $statusTimeRaw !== '' ? strtotime($statusTimeRaw) : false;
    $researchCurrentUpdated = $statusTime ? date('F j, Y h:i A', $statusTime) : '';
}

if (($_GET['ajax'] ?? '') === 'research-status') {
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => true,
        'title' => $researchCurrentTitle,
        'status' => $researchCurrentStatus,
        'adviser' => $researchCurrentAdviser !== '' ? $researchCurrentAdviser : 'For assignment',
        'updated_at' => $researchCurrentUpdated !== '' ? $researchCurrentUpdated : 'No activity yet',
        'progress' => $researchCurrentProgress,
    ]);
    exit;
}

// ── Research Forum payment check ─────────────────────────────────────────────
// In production, query your payments table. Here we check against the
// hardcoded payment history transactions (the "Research Forum" row).
$paymentTransactions = [
    ['ref' => 'OR-2026-0018', 'description' => 'Tuition Down Payment',  'amount' => 5000.00, 'status' => 'Paid', 'date' => 'Jul 5, 2026'],
    ['ref' => 'OR-2026-0009', 'description' => 'Registration Fee',       'amount' => 1500.00, 'status' => 'Paid', 'date' => 'Jun 20, 2026'],
    ['ref' => 'OR-2026-0003', 'description' => 'Laboratory Fee',         'amount' => 2500.00, 'status' => 'Paid', 'date' => 'Jun 15, 2026'],
    ['ref' => 'OR-2026-0001', 'description' => 'Research Forum',         'amount' => 800.00,  'status' => 'Paid', 'date' => 'May 28, 2026'],
];
$researchForumPaid = false;
foreach ($paymentTransactions as $txn) {
    if (
        stripos($txn['description'], 'Research Forum') !== false &&
        strtolower($txn['status']) === 'paid'
    ) {
        $researchForumPaid = true;
        break;
    }
}

$studentProfile = [
    'name' => 'Juan Dela Cruz',
    'student_id' => $studentId,
    'program' => 'Bachelor of Science in Information Technology',
    'year_level' => '4th Year',
    'section' => 'BSIT 4A',
    'semester' => '1st Semester',
    'status' => 'Enrolled',
    'email' => 's230000001@bcp.edu.ph',
    'mobile' => '0917 000 0001',
    'address' => 'Novaliches, Quezon City',
    'guardian' => 'Maria Dela Cruz',
];

$studentPages = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon' => 'fa-tachometer-alt',
        'description' => 'View your enrollment, academic, finance, and research overview.',
    ],
    'my-profile' => [
        'title' => 'My Profile',
        'icon' => 'fa-user',
        'description' => 'Review and maintain your student information.',
    ],
    'student-id' => [
        'title' => 'Student ID',
        'icon' => 'fa-id-card',
        'description' => 'View your official student ID details and request reprint support.',
    ],
    'account-balance' => [
        'title' => 'Account Balance',
        'icon' => 'fa-wallet',
        'description' => 'Track current charges, payments, discounts, and remaining balance.',
    ],
    'class-schedule' => [
        'title' => 'Class Schedule',
        'icon' => 'fa-calendar-alt',
        'description' => 'See your weekly classes, rooms, and time blocks.',
    ],
    'academic-records' => [
        'title' => 'Academic Records',
        'icon' => 'fa-file-alt',
        'description' => 'Check your academic standing, units, and grade summary.',
    ],
    'subjects-professors' => [
        'title' => 'Subject & Professors',
        'icon' => 'fa-chalkboard-teacher',
        'description' => 'View enrolled subjects and assigned professors.',
    ],
    'payment-history' => [
        'title' => 'Payment History',
        'icon' => 'fa-receipt',
        'description' => 'Review official receipt records and payment transactions.',
    ],
    'grades-portal' => [
        'title' => 'Grades Portal',
        'icon' => 'fa-star-half-alt',
        'description' => 'View your official grades per subject, semester, and academic year.',
    ],
    'research-proposal-submission' => [
        'title' => 'Research Proposal Submission',
        'icon' => 'fa-flask',
        'description' => 'Submit your research proposal and track CRAD review status.',
    ],
];

if (!isset($studentPages[$studentPortalPage])) {
    $studentPortalPage = 'dashboard';
}

$pageMeta = $studentPages[$studentPortalPage];
$processMessages = [
    'profile-update' => 'Profile update request has been prepared for registrar review.',
    'profile-correction' => 'Correction ticket has been submitted to the student records desk.',
    'id-print' => 'Student ID print request is now queued for validation.',
    'id-replacement' => 'Replacement ID request has been prepared for assessment.',
    'pay-now' => 'Payment process has been opened for the current balance.',
    'soa' => 'Statement of Account request has been generated.',
    'download-schedule' => 'Class schedule download has been prepared.',
    'schedule-conflict' => 'Schedule conflict report has been submitted for checking.',
    'copy-grades' => 'Copy of Grades request has been submitted.',
    'transcript' => 'Transcript request has been submitted to Registrar.',
    'consultation' => 'Consultation request has been prepared for your professor.',
    'subject-details' => 'Subject detail view has been opened.',
    'receipt' => 'Receipt download has been prepared.',
    'payment-issue' => 'Payment issue report has been submitted to Finance.',
];
$processKey = $_GET['process'] ?? '';
$processMessage = $processMessages[$processKey] ?? '';
$pageTitle = $pageMeta['title'];
$activeModule = 'student_portal';
$activePage = $studentPortalPage;
$breadcrumbs = [
    ['label' => 'Student Portal', 'url' => BASE_URL . '/modules/student-portal/pages/dashboard.php'],
    ['label' => $pageMeta['title'], 'url' => null],
];
$pageBannerIcon = $pageMeta['icon'];
$pageBannerDescription = $pageMeta['description'];

require_once __DIR__ . '/../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="student-portal">
    <?php if ($processMessage !== ''): ?>
        <div class="alert alert-success student-process-alert" role="alert">
            <?= smsIcon('check-circle', ['class' => 'me-2']) ?><?= htmlspecialchars($processMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['notice']) && $_GET['notice'] === 'research-forum-required'): ?>
        <div class="alert alert-warning student-process-alert" role="alert">
            <?= smsIcon('lock', ['class' => 'me-2']) ?>You must pay the <strong>Research Forum</strong> fee before submitting research documents. Please complete payment to unlock access.
        </div>
    <?php endif; ?>

    <?php if ($studentPortalPage === 'dashboard'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-3">
                <section class="card stat-card primary">
                    <div class="card-body">
                        <h6 class="text-muted">Enrollment Status</h6>
                        <h4 class="fw-bold mb-0">Enrolled</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card success">
                    <div class="card-body">
                        <h6 class="text-muted">Current GWA</h6>
                        <h4 class="fw-bold mb-0">1.75</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card warning">
                    <div class="card-body">
                        <h6 class="text-muted">Balance</h6>
                        <h4 class="fw-bold mb-0">PHP 8,450.00</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card info">
                    <div class="card-body">
                        <h6 class="text-muted">Current Status</h6>
                        <h4 class="fw-bold mb-0 fs-6"><?= htmlspecialchars($researchCurrentStatus) ?></h4>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Today at a Glance</h5>
                        <div class="student-list">
                            <div><strong>Web Systems and Technologies</strong><span>8:00 AM - 9:30 AM · Lab 204</span><small>Prof. Maria Santos</small></div>
                            <div><strong>Database Management</strong><span>10:00 AM - 11:30 AM · Room 302</span><small>Prof. Carlo Reyes</small></div>
                            <div><strong>Systems Analysis and Design</strong><span>1:00 PM - 4:00 PM · Room 210</span><small>Hybrid session</small></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/class-schedule.php"><?= smsIcon('calendar-alt', ['class' => 'me-2']) ?>View Schedule</a>
                            <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/grades-portal.php"><?= smsIcon('star-half-alt', ['class' => 'me-2']) ?>Check Grades</a>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Quick Actions</h5>
                        <div class="student-process-steps">
                            <div><span>1</span><strong>Submit research proposal</strong><p>Prepare your title proposal for CRAD review.</p></div>
                            <div><span>2</span><strong>Upload required documents</strong><p>Research Forum payment unlocks document submission.</p></div>
                            <div><span>3</span><strong>Monitor records</strong><p>Review balance, receipts, and academic standing.</p></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/research-proposal-submission.php"><?= smsIcon('flask', ['class' => 'me-2']) ?>Research Proposal</a>
                            <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/account-balance.php"><?= smsIcon('wallet', ['class' => 'me-2']) ?>Account Balance</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'my-profile'): ?>
        <div class="row g-3">
            <div class="col-lg-4">
                <section class="card student-profile-card h-100">
                    <div class="card-body">
                        <div class="student-avatar mb-3">
                            <?= smsIcon('user-graduate') ?>
                        </div>
                        <h5 class="fw-semibold mb-1"><?= htmlspecialchars($studentProfile['name']) ?></h5>
                        <p class="text-muted mb-3"><?= htmlspecialchars($studentProfile['program']) ?></p>
                        <span class="badge text-bg-success">Active Student</span>
                    </div>
                </section>
            </div>
            <div class="col-lg-8">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Student Information</h5>
                        <div class="student-record-grid">
                            <div><span>Student ID</span><strong><?= htmlspecialchars($studentProfile['student_id']) ?></strong></div>
                            <div><span>Program</span><strong><?= htmlspecialchars($studentProfile['program']) ?></strong></div>
                            <div><span>Year Level</span><strong><?= htmlspecialchars($studentProfile['year_level']) ?></strong></div>
                            <div><span>Semester</span><strong><?= htmlspecialchars($studentProfile['semester']) ?></strong></div>
                            <div><span>Section</span><strong><?= htmlspecialchars($studentProfile['section']) ?></strong></div>
                            <div><span>Email</span><strong><?= htmlspecialchars($studentProfile['email']) ?></strong></div>
                            <div><span>Mobile</span><strong><?= htmlspecialchars($studentProfile['mobile']) ?></strong></div>
                            <div><span>Address</span><strong><?= htmlspecialchars($studentProfile['address']) ?></strong></div>
                            <div><span>Guardian</span><strong><?= htmlspecialchars($studentProfile['guardian']) ?></strong></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=profile-update"><?= smsIcon('pen', ['class' => 'me-2']) ?>Request Profile Update</a>
                            <a class="btn btn-outline-primary" href="?process=profile-correction"><?= smsIcon('file-signature', ['class' => 'me-2']) ?>Submit Correction Ticket</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'student-id'): ?>
        <div class="row g-3">
            <div class="col-lg-4">
                <section class="card student-id-card h-100">
                    <div class="card-body">
                        <div class="student-id-brand">
                            <?= smsIcon('graduation-cap') ?>
                            <span><?= htmlspecialchars(INSTITUTION) ?></span>
                        </div>
                        <div class="student-id-photo"><?= smsIcon('user-graduate') ?></div>
                        <h5><?= htmlspecialchars($studentProfile['name']) ?></h5>
                        <p><?= htmlspecialchars($studentProfile['program']) ?></p>
                        <div class="student-id-number"><?= htmlspecialchars($studentProfile['student_id']) ?></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-8">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">ID Process</h5>
                        <div class="student-process-steps">
                            <div><span>1</span><strong>Verify student details</strong><p>Name, program, year level, and section are checked before ID printing.</p></div>
                            <div><span>2</span><strong>Validate enrollment status</strong><p>Status must be Enrolled for the current school year.</p></div>
                            <div><span>3</span><strong>Request print or replacement</strong><p>Use this option for first printing, lost ID, or damaged ID replacement.</p></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=id-print"><?= smsIcon('print', ['class' => 'me-2']) ?>Request ID Print</a>
                            <a class="btn btn-outline-primary" href="?process=id-replacement"><?= smsIcon('redo', ['class' => 'me-2']) ?>Request Replacement</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'account-balance'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-4">
                <section class="card stat-card warning"><div class="card-body"><h6 class="text-muted">Total Assessment</h6><h4 class="fw-bold mb-0">PHP 24,950.00</h4></div></section>
            </div>
            <div class="col-md-4">
                <section class="card stat-card success"><div class="card-body"><h6 class="text-muted">Total Paid</h6><h4 class="fw-bold mb-0">PHP 16,500.00</h4></div></section>
            </div>
            <div class="col-md-4">
                <section class="card stat-card primary"><div class="card-body"><h6 class="text-muted">Balance</h6><h4 class="fw-bold mb-0">PHP 8,450.00</h4></div></section>
            </div>
        </div>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Assessment Breakdown</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Fee</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td>Tuition Fee</td><td class="text-end">PHP 18,000.00</td><td><span class="badge text-bg-warning">Partial</span></td></tr>
                            <tr><td>Miscellaneous Fee</td><td class="text-end">PHP 4,450.00</td><td><span class="badge text-bg-warning">Partial</span></td></tr>
                            <tr><td>Laboratory Fee</td><td class="text-end">PHP 2,500.00</td><td><span class="badge text-bg-success">Paid</span></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=pay-now"><?= smsIcon('credit-card', ['class' => 'me-2']) ?>Proceed to Payment</a>
                    <a class="btn btn-outline-primary" href="?process=soa"><?= smsIcon('file-invoice', ['class' => 'me-2']) ?>Request Statement of Account</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'class-schedule'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Weekly Schedule</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject</th><th>Day</th><th>Time</th><th>Room</th><th>Mode</th></tr></thead>
                        <tbody>
                            <tr><td>Web Systems and Technologies</td><td>Mon / Wed</td><td>8:00 AM - 9:30 AM</td><td>Lab 204</td><td>Face to Face</td></tr>
                            <tr><td>Database Management</td><td>Tue / Thu</td><td>10:00 AM - 11:30 AM</td><td>Room 302</td><td>Face to Face</td></tr>
                            <tr><td>Systems Analysis and Design</td><td>Friday</td><td>1:00 PM - 4:00 PM</td><td>Room 210</td><td>Hybrid</td></tr>
                            <tr><td>Physical Education</td><td>Saturday</td><td>9:00 AM - 11:00 AM</td><td>Gym 1</td><td>Face to Face</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=download-schedule"><?= smsIcon('download', ['class' => 'me-2']) ?>Download Schedule</a>
                    <a class="btn btn-outline-primary" href="?process=schedule-conflict"><?= smsIcon('exclamation-triangle', ['class' => 'me-2']) ?>Report Schedule Conflict</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'academic-records'): ?>
        <div class="row g-3">
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Academic Summary</h5>
                        <div class="student-record-grid">
                            <div><span>Current Semester</span><strong>1st Semester</strong></div>
                            <div><span>Completed Units</span><strong>54</strong></div>
                            <div><span>Current Units</span><strong>18</strong></div>
                            <div><span>GWA</span><strong>1.75</strong></div>
                            <div><span>Standing</span><strong>Good Standing</strong></div>
                            <div><span>Deficiencies</span><strong>None</strong></div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Recent Grades</h5>
                        <div class="table-responsive">
                            <table class="table student-table align-middle mb-0">
                                <thead><tr><th>Subject</th><th>Units</th><th>Grade</th><th>Remarks</th></tr></thead>
                                <tbody>
                                    <tr><td>Programming 2</td><td>3</td><td>1.50</td><td>Passed</td></tr>
                                    <tr><td>Data Structures</td><td>3</td><td>1.75</td><td>Passed</td></tr>
                                    <tr><td>Discrete Mathematics</td><td>3</td><td>2.00</td><td>Passed</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=copy-grades"><?= smsIcon('file-download', ['class' => 'me-2']) ?>Request Copy of Grades</a>
                            <a class="btn btn-outline-primary" href="?process=transcript"><?= smsIcon('scroll', ['class' => 'me-2']) ?>Request Transcript</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'subjects-professors'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Enrolled Subjects and Assigned Professors</h5>
                <div class="student-list student-subject-list">
                    <div><strong>Web Systems and Technologies</strong><span>Prof. Maria Santos</span><small>Consultation: Wednesday, 2:00 PM - 4:00 PM</small></div>
                    <div><strong>Database Management</strong><span>Prof. Carlo Reyes</span><small>Consultation: Thursday, 1:00 PM - 3:00 PM</small></div>
                    <div><strong>Systems Analysis and Design</strong><span>Prof. Ana Lim</span><small>Consultation: Friday, 10:00 AM - 12:00 PM</small></div>
                    <div><strong>Networking Fundamentals</strong><span>Prof. Miguel Cruz</span><small>Consultation: Monday, 3:00 PM - 5:00 PM</small></div>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=consultation"><?= smsIcon('envelope', ['class' => 'me-2']) ?>Send Consultation Request</a>
                    <a class="btn btn-outline-primary" href="?process=subject-details"><?= smsIcon('book-reader', ['class' => 'me-2']) ?>View Subject Details</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'payment-history'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Official Payment Transactions</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Date</th><th>Reference No.</th><th>Description</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($paymentTransactions as $txn): ?>
                            <tr>
                                <td><?= htmlspecialchars($txn['date']) ?></td>
                                <td><?= htmlspecialchars($txn['ref']) ?></td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td class="text-end">PHP <?= number_format($txn['amount'], 2) ?></td>
                                <td><span class="badge text-bg-<?= strtolower($txn['status']) === 'paid' ? 'success' : 'warning' ?>"><?= htmlspecialchars($txn['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=receipt"><?= smsIcon('receipt', ['class' => 'me-2']) ?>Download Receipt</a>
                    <a class="btn btn-outline-primary" href="?process=payment-issue"><?= smsIcon('search-dollar', ['class' => 'me-2']) ?>Report Payment Issue</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'grades-portal'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-3">
                <section class="card stat-card primary"><div class="card-body"><h6 class="text-muted">Current GWA</h6><h4 class="fw-bold mb-0">1.75</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card success"><div class="card-body"><h6 class="text-muted">Passed Subjects</h6><h4 class="fw-bold mb-0">18</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card warning"><div class="card-body"><h6 class="text-muted">Current Subjects</h6><h4 class="fw-bold mb-0">6</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card info"><div class="card-body"><h6 class="text-muted">Total Units Earned</h6><h4 class="fw-bold mb-0">54</h4></div></section>
            </div>
        </div>
        <section class="card mb-3">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">1st Semester — S.Y. 2026-2027 (Current)</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject Code</th><th>Subject Title</th><th>Units</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Grade</th><th>Remarks</th></tr></thead>
                        <tbody>
                            <tr><td>CS301</td><td>Web Systems and Technologies</td><td>3</td><td>1.50</td><td>1.75</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS302</td><td>Database Management</td><td>3</td><td>1.75</td><td>2.00</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS303</td><td>Systems Analysis and Design</td><td>3</td><td>1.50</td><td>1.50</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS304</td><td>Networking Fundamentals</td><td>3</td><td>2.00</td><td>2.25</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>PE3</td><td>Physical Education 3</td><td>2</td><td>1.25</td><td>1.50</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>NSTP3</td><td>NSTP 3</td><td>3</td><td>1.00</td><td>1.25</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">2nd Semester — S.Y. 2025-2026</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject Code</th><th>Subject Title</th><th>Units</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Grade</th><th>Remarks</th></tr></thead>
                        <tbody>
                            <tr><td>CS201</td><td>Object-Oriented Programming</td><td>3</td><td>1.25</td><td>1.50</td><td>1.50</td><td>1.50</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>CS202</td><td>Data Structures and Algorithms</td><td>3</td><td>1.50</td><td>1.75</td><td>2.00</td><td>1.75</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>CS203</td><td>Discrete Mathematics</td><td>3</td><td>2.00</td><td>2.00</td><td>2.00</td><td>2.00</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>GE106</td><td>Ethics</td><td>3</td><td>1.75</td><td>2.00</td><td>1.75</td><td>1.75</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>PE2</td><td>Physical Education 2</td><td>2</td><td>1.50</td><td>1.75</td><td>1.50</td><td>1.50</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>NSTP2</td><td>NSTP 2</td><td>3</td><td>1.25</td><td>1.25</td><td>1.00</td><td>1.25</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=copy-grades"><?= smsIcon('file-download', ['class' => 'me-2']) ?>Download Grades Report</a>
                    <a class="btn btn-outline-primary" href="?process=transcript"><?= smsIcon('scroll', ['class' => 'me-2']) ?>Request Official Transcript</a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/layout-end.php'; ?>
