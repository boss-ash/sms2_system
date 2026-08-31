<?php
/**
 * SMS 2 – Shared security UI helpers (password show/hide, OTP boxes).
 */

/**
 * Password input with show/hide eye toggle (login-style).
 *
 * @param array{
 *   id?:string,name?:string,value?:string,placeholder?:string,autocomplete?:string,
 *   required?:bool,minlength?:int|string,autofocus?:bool,class?:string,attrs?:string
 * } $opts
 */
function smsPasswordInput(array $opts = []): string
{
    $id = (string) ($opts['id'] ?? 'password');
    $name = (string) ($opts['name'] ?? $id);
    $value = (string) ($opts['value'] ?? '');
    $placeholder = (string) ($opts['placeholder'] ?? '');
    $autocomplete = (string) ($opts['autocomplete'] ?? 'current-password');
    $required = !empty($opts['required']);
    $minlength = $opts['minlength'] ?? null;
    $autofocus = !empty($opts['autofocus']);
    $extraClass = trim((string) ($opts['class'] ?? ''));
    $attrs = trim((string) ($opts['attrs'] ?? ''));

    $class = trim('form-control sms-pw-input ' . $extraClass);
    $html = '<div class="sms-pw-group password-group">';
    $html .= '<input type="password" class="' . e($class) . '" id="' . e($id) . '" name="' . e($name) . '"';
    if ($value !== '') {
        $html .= ' value="' . e($value) . '"';
    }
    if ($placeholder !== '') {
        $html .= ' placeholder="' . e($placeholder) . '"';
    }
    if ($autocomplete !== '') {
        $html .= ' autocomplete="' . e($autocomplete) . '"';
    }
    if ($required) {
        $html .= ' required';
    }
    if ($minlength !== null && $minlength !== '') {
        $html .= ' minlength="' . e((string) $minlength) . '"';
    }
    if ($autofocus) {
        $html .= ' autofocus';
    }
    if ($attrs !== '') {
        $html .= ' ' . $attrs;
    }
    $html .= '>';
    $html .= '<button class="password-toggle sms-pw-toggle" type="button" aria-label="Show password" title="Show password" data-pw-target="' . e($id) . '" aria-pressed="false">';
    $html .= smsIcon('eye', ['aria-hidden' => 'true']);
    $html .= '</button></div>';

    return $html;
}

/**
 * Cursor-style segmented OTP / authenticator code input.
 * Submits a single field named $name with the concatenated digits.
 *
 * @param array{
 *   id?:string,digits?:int,required?:bool,autofocus?:bool,label?:string,hint?:string
 * } $opts
 */
function smsOtpInput(string $name, array $opts = []): string
{
    $id = (string) ($opts['id'] ?? $name);
    $digits = max(4, min(8, (int) ($opts['digits'] ?? 6)));
    $required = !empty($opts['required']);
    $autofocus = !empty($opts['autofocus']);
    $label = (string) ($opts['label'] ?? '');
    $hint = (string) ($opts['hint'] ?? '');

    $html = '<div class="sms-otp" data-sms-otp data-digits="' . $digits . '">';
    if ($label !== '') {
        $html .= '<label class="form-label fw-semibold" for="' . e($id) . '_0">' . e($label) . '</label>';
    }
    $html .= '<div class="sms-otp-boxes" role="group" aria-label="' . e($label !== '' ? $label : 'Verification code') . '">';
    for ($i = 0; $i < $digits; $i++) {
        $boxId = $id . '_' . $i;
        $html .= '<input type="text" class="sms-otp-box" id="' . e($boxId) . '"';
        $html .= ' inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="' . ($i === 0 ? 'one-time-code' : 'off') . '"';
        $html .= ' aria-label="Digit ' . ($i + 1) . '" data-otp-index="' . $i . '"';
        if ($required) {
            $html .= ' required';
        }
        if ($autofocus && $i === 0) {
            $html .= ' autofocus';
        }
        $html .= '>';
    }
    $html .= '</div>';
    $html .= '<input type="hidden" class="sms-otp-value" id="' . e($id) . '" name="' . e($name) . '" value="">';
    if ($hint !== '') {
        $html .= '<div class="sms-otp-hint">' . e($hint) . '</div>';
    }
    $html .= '</div>';

    return $html;
}

/**
 * Open a modern security panel card.
 */
function smsSecCardStart(string $title, string $icon = 'fa-shield-alt', string $badgeHtml = '', string $extraClass = ''): string
{
    $html = '<section class="card sms-sec-card mb-4' . ($extraClass !== '' ? ' ' . e($extraClass) : '') . '">';
    $html .= '<div class="card-body">';
    $html .= '<div class="sms-sec-card-head">';
    $html .= '<div class="sms-sec-card-title">';
    $html .= '<span class="sms-sec-icon">' . smsIcon($icon, ['aria-hidden' => 'true']) . '</span>';
    $html .= '<h2 class="h5 fw-bold mb-0">' . $title . '</h2>';
    $html .= '</div>';
    if ($badgeHtml !== '') {
        $html .= '<div class="sms-sec-card-badge">' . $badgeHtml . '</div>';
    }
    $html .= '</div>';

    return $html;
}

function smsSecCardEnd(): string
{
    return '</div></section>';
}

/**
 * Inner boxed panel used in side-by-side security splits (Password / Auth & Passkey).
 */
function smsSecBoxStart(string $title, string $icon = 'fa-shield-alt', string $badgeHtml = ''): string
{
    $html = '<div class="sms-sec-pw-box h-100">';
    $html .= '<div class="d-flex justify-content-between align-items-start gap-2 mb-2">';
    $html .= '<h3 class="h6 fw-bold mb-0">';
    $html .= smsIcon($icon, ['class' => 'text-sms-primary me-1', 'aria-hidden' => 'true']) . $title;
    $html .= '</h3>';
    if ($badgeHtml !== '') {
        $html .= '<div class="sms-sec-card-badge flex-shrink-0">' . $badgeHtml . '</div>';
    }
    $html .= '</div>';

    return $html;
}

function smsSecBoxEnd(): string
{
    return '</div>';
}

/**
 * Outer card + two-column split for Authenticator and Passkey.
 */
function smsRenderAuthPasskeySplit(
    int $userId,
    string $formActionUrl,
    string $csrfFieldHtml,
    string $csrfToken,
    string $heading = 'Authenticator & Passkey',
    string $lead = 'Manage login second factors for this account.'
): void {
    require_once __DIR__ . '/authenticator-ui.php';
    require_once __DIR__ . '/passkey.php';
    ?>
    <section class="card sms-sec-card mb-4">
        <div class="card-body">
            <div class="sms-sec-card-head">
                <div class="sms-sec-card-title">
                    <span class="sms-sec-icon"><?= smsIcon('fingerprint', ['aria-hidden' => 'true']) ?></span>
                    <div>
                        <h2 class="h5 fw-bold mb-0"><?= e($heading) ?></h2>
                        <p class="sms-sec-lead mb-0 mt-1"><?= e($lead) ?></p>
                    </div>
                </div>
            </div>
            <div class="row g-3 sms-sec-pw-split">
                <div class="col-lg-6">
                    <?php smsRenderAuthenticatorCard($userId, $formActionUrl, $csrfFieldHtml, true); ?>
                </div>
                <div class="col-lg-6">
                    <?php smsRenderPasskeyCard($userId, $csrfToken, true); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}
