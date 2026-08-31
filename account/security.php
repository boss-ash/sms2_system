<?php
/**
 * Personal /security page.
 * Super Admin → Account Settings (Login Security).
 * Staff/student → their module Security Settings.
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
requireAuth();

$role = getCurrentUserRoleKey();
if (smsIsGrantedAdminRole($role)) {
    header('Location: ' . BASE_URL . '/account/profile.php?tab=security');
} elseif ($role === 'student') {
    header('Location: ' . BASE_URL . '/account/module-security.php?module=student_portal&tab=logs');
} else {
    $map = [
        'admission' => 'enrollment',
        'registrar' => 'enrollment',
        'crad_officer' => 'crad',
        'research_coordinator' => 'crad',
        'research_grant' => 'crad_grant',
        'finance' => 'payment',
        'osa' => 'cocurricular',
        'it_office' => 'lms',
        'qa' => 'accreditation',
        'hr' => 'faculty',
        'adviser' => 'faculty',
        'panel' => 'faculty',
        'research_director' => 'faculty',
    ];
    $mod = $map[$role] ?? 'dashboard';
    if ($mod === 'dashboard') {
        header('Location: ' . BASE_URL . '/dashboard/index.php');
    } else {
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($mod) . '&tab=logs');
    }
}
exit;
