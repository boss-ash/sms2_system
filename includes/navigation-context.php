<?php
/**
 * SMS 2 - Navigation context helpers for sidebar and layout.
 */

if (!function_exists('smsSidebarMode')) {
    /**
     * Which sidebar template to render for the logged-in role.
     */
    function smsSidebarMode(string $roleKey): string
    {
        $roleKey = function_exists('smsNormalizeRoleKey')
            ? smsNormalizeRoleKey($roleKey)
            : $roleKey;

        if ($roleKey === 'student') {
            return 'student';
        }

        if (in_array($roleKey, ['adviser', 'panel', 'grammarian', 'research_director'], true)) {
            return 'faculty_workspace';
        }

        return 'admin_modules';
    }
}

if (!function_exists('smsResolveActiveModuleFromRequest')) {
    /**
     * Infer module key from the current request path.
     */
    function smsResolveActiveModuleFromRequest(): string
    {
        $scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptPath === '') {
            return '';
        }

        if (preg_match('#/modules/student-portal(?:/|$)#', $scriptPath)) {
            return 'student_portal';
        }

        if (preg_match('#/modules/([a-z0-9_-]+)(?:/|$)#', $scriptPath, $matches)) {
            $folder = (string) ($matches[1] ?? '');
            if ($folder === 'student-portal') {
                return 'student_portal';
            }

            return str_replace('-', '_', $folder);
        }

        if (str_contains($scriptPath, '/account/module-security.php')) {
            $module = (string) ($_GET['module'] ?? '');
            if ($module === 'student-portal') {
                return 'student_portal';
            }

            return $module;
        }

        if (str_ends_with($scriptPath, '/dashboard/index.php')) {
            return 'dashboard';
        }

        return '';
    }
}

if (!function_exists('smsResolveActivePageFromRequest')) {
    /**
     * Infer page slug from the current script basename.
     */
    function smsResolveActivePageFromRequest(): string
    {
        $script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($script === '' || $script === 'index.php') {
            return '';
        }

        return preg_replace('/\.php$/', '', $script) ?? '';
    }
}

if (!function_exists('smsEffectiveActiveModule')) {
    /**
     * Module key used for sidebar highlighting and expansion.
     */
    function smsEffectiveActiveModule(string $activeModule, string $roleKey): string
    {
        $activeModule = trim($activeModule);
        $roleKey = function_exists('smsNormalizeRoleKey')
            ? smsNormalizeRoleKey($roleKey)
            : $roleKey;

        if ($activeModule !== '' && $activeModule !== 'dashboard') {
            return $activeModule;
        }

        $fromRequest = smsResolveActiveModuleFromRequest();
        if ($fromRequest !== '' && $fromRequest !== 'dashboard') {
            return $fromRequest;
        }

        if (!function_exists('smsPrimaryModuleForRole')) {
            require_once __DIR__ . '/security-workflow.php';
        }

        $primary = function_exists('smsPrimaryModuleForRole')
            ? (string) smsPrimaryModuleForRole($roleKey)
            : '';

        if ($primary !== '' && $primary !== 'System') {
            return $primary;
        }

        return $activeModule;
    }
}

if (!function_exists('smsSidebarHighlightModule')) {
    /**
     * Module to expand/highlight in admin_modules sidebar mode.
     */
    function smsSidebarHighlightModule(string $activeModule, string $roleKey): string
    {
        if ($activeModule !== '' && $activeModule !== 'dashboard') {
            return $activeModule;
        }

        if (!function_exists('smsPrimaryModuleForRole')) {
            require_once __DIR__ . '/security-workflow.php';
        }

        $primary = function_exists('smsPrimaryModuleForRole')
            ? (string) smsPrimaryModuleForRole($roleKey)
            : '';

        return ($primary !== '' && $primary !== 'System') ? $primary : $activeModule;
    }
}
