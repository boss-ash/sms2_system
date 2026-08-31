<?php
/**
 * Role-aware URLs for grant Review & Workflow pages (no database dependencies).
 */
declare(strict_types=1);

if (!function_exists('grantReviewWorkflowModuleKeyForRole')) {
    function grantReviewWorkflowModuleKeyForRole(string $roleKey = ''): string
    {
        if ($roleKey === '' && function_exists('getCurrentUserRoleKey')) {
            $roleKey = getCurrentUserRoleKey();
        }

        return match ($roleKey) {
            'adviser', 'hr'       => 'faculty',
            'vpaa', 'qa'          => 'accreditation',
            'finance'             => 'payment',
            default               => 'crad',
        };
    }
}

if (!function_exists('grantReviewWorkflowPageUrl')) {
    function grantReviewWorkflowPageUrl(string $pageSlug, int $applicationId = 0, ?string $moduleKey = null): string
    {
        $moduleKey = $moduleKey ?? grantReviewWorkflowModuleKeyForRole();

        $base = match ($moduleKey) {
            'faculty'       => BASE_URL . '/modules/faculty/pages/',
            'accreditation' => BASE_URL . '/modules/accreditation/pages/',
            'payment'       => BASE_URL . '/modules/payment/pages/',
            default         => BASE_URL . '/modules/crad/pages/',
        };

        $url = $base . $pageSlug . '.php';
        if ($applicationId > 0) {
            $url .= '?id=' . $applicationId;
        }

        return $url;
    }
}

if (!function_exists('grantReviewWorkflowSidebarItems')) {
    /** @return list<array{slug: string, href: string, icon: string, label: string}> */
    function grantReviewWorkflowSidebarItems(?string $roleKey = null): array
    {
        $roleKey = $roleKey ?? (function_exists('getCurrentUserRoleKey') ? getCurrentUserRoleKey() : '');

        return [
            [
                'slug'  => 'reviewer-evaluation',
                'href'  => grantReviewWorkflowPageUrl('reviewer-evaluation', 0, grantReviewWorkflowModuleKeyForRole($roleKey)),
                'icon'  => 'fa-clipboard-check',
                'label' => 'Reviewer Evaluation',
            ],
            [
                'slug'  => 'approval-workflows',
                'href'  => grantReviewWorkflowPageUrl('approval-workflows', 0, grantReviewWorkflowModuleKeyForRole($roleKey)),
                'icon'  => 'fa-tasks',
                'label' => 'Approval Workflows',
            ],
        ];
    }
}
