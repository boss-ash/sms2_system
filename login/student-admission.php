<?php
/**
 * SMS 2 - Student Admission (in-system wrapper — no external navigation)
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

$admissionEmbedUrl = 'https://bcp-admissions.elearningcommons.com/online-admission';
$pageTitle = 'Student Admission';
$bodyClass = 'admission-embed-page';

require_once ROOT_PATH . '/includes/header.php';
?>

<style>
body.admission-embed-page {
    margin: 0 !important;
    padding: 0 !important;
    min-height: 100vh !important;
    background: #071c48 !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden;
}

.admission-shell {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.admission-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    background: rgba(8, 28, 58, 0.72);
    color: #fff;
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.admission-bar h1 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
}

.admission-bar p {
    margin: 0.15rem 0 0;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.78rem;
    font-weight: 600;
}

.admission-actions {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    flex-wrap: wrap;
}

.admission-actions a,
.admission-actions button {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    font-family: inherit;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.admission-actions a:hover,
.admission-actions a:focus-visible,
.admission-actions button:hover,
.admission-actions button:focus-visible {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    outline: none;
}

.admission-frame-wrap {
    position: relative;
    flex: 1;
    min-height: 0;
    background: #0b1f44;
}

.admission-frame {
    width: 100%;
    height: calc(100vh - 64px);
    border: 0;
    background: #fff;
}

.admission-fallback {
    display: none;
    position: absolute;
    inset: 0;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: rgba(7, 28, 72, 0.92);
    color: #fff;
    text-align: center;
}

.admission-fallback.is-visible {
    display: flex;
}

.admission-fallback-card {
    width: min(460px, 100%);
    padding: 1.35rem 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.admission-fallback-card h2 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: 800;
}

.admission-fallback-card p {
    margin: 0 0 1rem;
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.88rem;
    font-weight: 600;
    line-height: 1.45;
}

.admission-fallback-card a,
.admission-fallback-card button {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin: 0 0.25rem;
    padding: 0.65rem 0.95rem;
    border: 0;
    border-radius: 10px;
    background: #1e40af;
    color: #fff;
    font-family: inherit;
    font-size: 0.86rem;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
}
</style>

<div class="admission-shell">
    <header class="admission-bar">
        <div>
            <h1>Student Admission</h1>
            <p>Opened inside BCP — Bestlink College of the Philippines</p>
        </div>
        <div class="admission-actions">
            <a href="<?= BASE_URL ?>/login/login.php">
                <?= smsIcon('arrow-left', ['aria-hidden' => 'true']) ?>Back to Sign In
            </a>
            <button type="button" id="admissionOpenTabBtn" title="Open this BCP admission page in a new tab">
                <?= smsIcon('external-link-alt', ['aria-hidden' => 'true']) ?>Open in new tab
            </button>
        </div>
    </header>

    <div class="admission-frame-wrap">
        <iframe
            class="admission-frame"
            id="admissionFrame"
            title="BCP Online Admission"
            src="<?= e($admissionEmbedUrl) ?>"
            referrerpolicy="no-referrer-when-downgrade"
            loading="eager"
        ></iframe>
        <div class="admission-fallback" id="admissionFallback" role="status">
            <div class="admission-fallback-card">
                <h2>Admission portal could not be embedded</h2>
                <p>
                    Stay inside BCP. You can return to Sign In or reopen this admission page in a new tab.
                </p>
                <a href="<?= BASE_URL ?>/login/login.php">Back to Sign In</a>
                <button type="button" id="admissionRetryTabBtn">Open in new tab</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const frame = document.getElementById('admissionFrame');
    const fallback = document.getElementById('admissionFallback');
    const openTabBtn = document.getElementById('admissionOpenTabBtn');
    const retryTabBtn = document.getElementById('admissionRetryTabBtn');
    const inSystemUrl = <?= json_encode(BASE_URL . '/login/student-admission.php') ?>;

    function openAdmissionInNewTab() {
        // Always open our SMS 2 page — never navigate the current iframe/window away
        const win = window.open(inSystemUrl, '_blank', 'noopener,noreferrer');
        if (!win) {
            window.location.href = inSystemUrl;
        }
    }

    if (openTabBtn) {
        openTabBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openAdmissionInNewTab();
        });
    }
    if (retryTabBtn) {
        retryTabBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openAdmissionInNewTab();
        });
    }

    if (!frame || !fallback) return;

    window.setTimeout(function () {
        try {
            const doc = frame.contentDocument;
            if (doc && doc.location && doc.location.href === 'about:blank') {
                fallback.classList.add('is-visible');
            }
        } catch (err) {
            // Cross-origin access means the embed likely loaded inside SMS 2.
        }
    }, 4500);
});
</script>

<?php require_once ROOT_PATH . '/includes/scripts.php'; ?>
