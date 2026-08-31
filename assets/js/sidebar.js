/**
 * SMS 2 - Sidebar Toggle & Responsive Behavior
 * Desktop: collapse to icon rail (icons stay visible)
 * Mobile: drawer overlay
 */
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('smsSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (!sidebar) return;

    // ── Scroll position persistence ──────────────────────────────────────────
    // Restore saved scroll position immediately so there's no visible jump.
    (function restoreSidebarScroll() {
        try {
            var saved = sessionStorage.getItem('sidebarScrollTop');
            if (saved !== null) {
                sidebar.scrollTop = parseInt(saved, 10) || 0;
            }
        } catch (e) { /* ignore */ }
    })();

    // Save scroll position before the page unloads (link click or navigation).
    function saveSidebarScroll() {
        try {
            sessionStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
        } catch (e) { /* ignore */ }
    }

    // Capture scroll position when any sidebar link is about to navigate.
    sidebar.addEventListener('click', function (e) {
        var link = e.target.closest('a.nav-link, a.sidebar-sub, a.crad-sidebar-link');
        if (link && link.href && !link.href.startsWith('#')) {
            saveSidebarScroll();
        }
    });

    // Also save on page hide (back/forward navigation, tab close, etc.).
    window.addEventListener('pagehide', saveSidebarScroll);
    // ─────────────────────────────────────────────────────────────────────────

    const DESKTOP_BREAKPOINT = 992;

    function isDesktop() {
        return window.innerWidth >= DESKTOP_BREAKPOINT;
    }

    function openMobileSidebar() {
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';
    }

    function collapseOpenMenus() {
        sidebar.querySelectorAll('.collapse.show').forEach(function (el) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                const instance = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                instance.hide();
            } else {
                el.classList.remove('show');
            }
        });
        sidebar.querySelectorAll('.sidebar-parent[aria-expanded="true"]').forEach(function (link) {
            link.setAttribute('aria-expanded', 'false');
        });
    }

    function restoreActiveMenus() {
        sidebar.querySelectorAll('.sidebar-parent.active, .admin-module-toggle.active').forEach(function (link) {
            const target = link.getAttribute('data-bs-target') || link.getAttribute('href') || '';
            if (!target || target.charAt(0) !== '#') return;
            const el = document.querySelector(target);
            if (!el) return;
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).show();
            } else {
                el.classList.add('show');
            }
            link.setAttribute('aria-expanded', 'true');
        });
    }

    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');
        } catch (e) { /* ignore */ }

        if (collapsed) {
            collapseOpenMenus();
        } else {
            restoreActiveMenus();
        }

        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            toggleBtn.setAttribute(
                'aria-label',
                collapsed ? 'Expand sidebar' : 'Collapse sidebar'
            );
            toggleBtn.setAttribute(
                'title',
                collapsed ? 'Expand sidebar' : 'Collapse sidebar'
            );
        }
    }

    function toggleDesktopSidebar() {
        setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
    }

    function handleToggle() {
        if (isDesktop()) {
            closeMobileSidebar();
            toggleDesktopSidebar();
        } else {
            if (sidebar.classList.contains('show')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', handleToggle);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    // Accordion: opening one module closes the others
    function closeOtherMenus(exceptEl) {
        var selector = sidebar.classList.contains('admin-sidebar-collapsible')
            ? '.admin-module-body.collapse.show, .admin-module-body.collapse.collapsing'
            : '.collapse.show, .collapse.collapsing';
        sidebar.querySelectorAll(selector).forEach(function (el) {
            if (el === exceptEl) return;
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                const instance = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                instance.hide();
            } else {
                el.classList.remove('show');
            }
        });
        sidebar.querySelectorAll('.sidebar-parent[aria-expanded="true"], .admin-module-toggle[aria-expanded="true"]').forEach(function (link) {
            const target = link.getAttribute('href') || link.getAttribute('data-bs-target') || '';
            if (exceptEl && target.charAt(0) === '#' && document.querySelector(target) === exceptEl) {
                return;
            }
            link.setAttribute('aria-expanded', 'false');
        });
    }

    sidebar.querySelectorAll('.collapse').forEach(function (el) {
        el.addEventListener('show.bs.collapse', function (e) {
            if (el.classList.contains('admin-subgroup-body')) {
                e.stopPropagation();
                return;
            }
            closeOtherMenus(el);
        });
        el.addEventListener('hide.bs.collapse', function (e) {
            if (el.classList.contains('admin-subgroup-body')) {
                e.stopPropagation();
            }
        });
    });

    // When collapsed, module icons navigate to overview instead of expanding
    sidebar.querySelectorAll('.sidebar-parent, .admin-module-toggle').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!isDesktop() || !document.body.classList.contains('sidebar-collapsed')) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            const url = link.getAttribute('data-overview-url');
            if (url) {
                window.location.href = url;
            }
        });
    });

    // Admin subgroup toggles — manual expand/collapse (avoids Bootstrap bubble issues)
    if (sidebar.classList.contains('admin-sidebar-collapsible')) {
        sidebar.querySelectorAll('.admin-subgroup-toggle').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var targetSel = btn.getAttribute('data-admin-subgroup');
                if (!targetSel) return;
                var target = document.querySelector(targetSel);
                if (!target) return;
                var willOpen = !target.classList.contains('show');
                target.classList.toggle('show', willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });

        // Prevent module-level collapse from closing when interacting inside it
        sidebar.querySelectorAll('.admin-module-body').forEach(function (moduleBody) {
            moduleBody.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });
    }

    // Close drawer after navigation on mobile/tablet
    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (!isDesktop()) {
                if (link.classList.contains('sidebar-parent')
                    || link.classList.contains('admin-module-toggle')
                    || link.classList.contains('admin-subgroup-toggle')) {
                    return;
                }
                closeMobileSidebar();
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !isDesktop() && sidebar.classList.contains('show')) {
            closeMobileSidebar();
        }
    });

    function applyLayoutForViewport() {
        if (isDesktop()) {
            closeMobileSidebar();
            let preferCollapsed = false;
            try {
                preferCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            } catch (e) { /* ignore */ }
            setCollapsed(preferCollapsed);
        } else {
            document.body.classList.remove('sidebar-collapsed');
            closeMobileSidebar();
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-label', 'Open sidebar');
                toggleBtn.setAttribute('title', 'Open sidebar');
            }
        }
    }

    // Admin collapsible sidebar: sync chevron on module toggles
    if (sidebar.classList.contains('admin-sidebar-collapsible')) {
        sidebar.querySelectorAll('.admin-module-toggle').forEach(function (btn) {
            var targetSel = btn.getAttribute('data-bs-target');
            if (!targetSel) return;
            var target = document.querySelector(targetSel);
            if (!target) return;
            target.addEventListener('show.bs.collapse', function () {
                btn.setAttribute('aria-expanded', 'true');
            });
            target.addEventListener('hide.bs.collapse', function () {
                btn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    applyLayoutForViewport();

    function syncViewportUnit() {
        document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
    }
    syncViewportUnit();

    let resizeTimer;
    let lastDesktop = isDesktop();
    window.addEventListener('resize', function () {
        syncViewportUnit();
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            const nowDesktop = isDesktop();
            if (nowDesktop !== lastDesktop) {
                applyLayoutForViewport();
                lastDesktop = nowDesktop;
            }
        }, 120);
    });

    window.addEventListener('orientationchange', function () {
        setTimeout(function () {
            syncViewportUnit();
            applyLayoutForViewport();
            lastDesktop = isDesktop();
        }, 200);
    });
});
