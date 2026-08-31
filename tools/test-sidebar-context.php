<?php
/**
 * CLI smoke test for sidebar navigation context.
 * Run: C:\xampp\php\php.exe tools/test-sidebar-context.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/navigation-context.php';

$roles = [
    'student' => ['mode' => 'student', 'module' => 'student_portal'],
    'registrar' => ['mode' => 'admin_modules', 'module' => 'registrar'],
    'finance' => ['mode' => 'admin_modules', 'module' => 'payment'],
    'crad_officer' => ['mode' => 'admin_modules', 'module' => 'crad'],
    'panel' => ['mode' => 'faculty_workspace', 'module' => 'faculty'],
    'grammarian' => ['mode' => 'faculty_workspace', 'module' => 'faculty'],
    'research_director' => ['mode' => 'faculty_workspace', 'module' => 'faculty'],
    'hr' => ['mode' => 'admin_modules', 'module' => 'faculty'],
    'superadmin' => ['mode' => 'admin_modules', 'module' => 'user-management'],
];

$_SERVER['SCRIPT_NAME'] = '/sms2_system/dashboard/index.php';

$failed = 0;
foreach ($roles as $roleKey => $expected) {
    $mode = smsSidebarMode($roleKey);
    $module = smsEffectiveActiveModule('dashboard', $roleKey);
    $highlight = smsSidebarHighlightModule('dashboard', $roleKey);

    $ok = $mode === $expected['mode']
        && $module === $expected['module']
        && $highlight === $expected['module'];

    if (!$ok) {
        $failed++;
        echo "FAIL {$roleKey}: mode={$mode} module={$module} highlight={$highlight}\n";
        echo "     expected mode={$expected['mode']} module={$expected['module']}\n";
    } else {
        echo "OK   {$roleKey}: {$mode} / {$module}\n";
    }
}

$_SERVER['SCRIPT_NAME'] = '/sms2_system/modules/registrar/pages/student-information-system.php';
$registrarModule = smsEffectiveActiveModule('dashboard', 'registrar');
if ($registrarModule !== 'registrar') {
    $failed++;
    echo "FAIL URL resolver: expected registrar, got {$registrarModule}\n";
} else {
    echo "OK   URL resolver: registrar page => {$registrarModule}\n";
}

exit($failed > 0 ? 1 : 0);
