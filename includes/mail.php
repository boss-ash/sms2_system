<?php
/**
 * SMS 2 – Mail helper (PHPMailer SMTP + password reset / OTP emails)
 */
require_once __DIR__ . '/security.php';

/**
 * @return array{ok:bool,error:string}
 */
function smsSendMail(string $to, string $subject, string $htmlBody, string $textBody = ''): array
{
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Invalid recipient email.'];
    }

    $fromEmail = trim(smsSetting('mail_from_email', 'noreply@bestlink.edu.ph'));
    $fromName = trim(smsSetting('mail_from_name', APP_SHORT_NAME));
    if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $fromEmail = 'noreply@bestlink.edu.ph';
    }
    if ($fromName === '') {
        $fromName = APP_SHORT_NAME;
    }

    if ($textBody === '') {
        $textBody = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody)), ENT_QUOTES | ENT_HTML5));
    }

    $host = trim(smsSetting('smtp_host', ''));
    if ($host === '') {
        return [
            'ok' => false,
            'error' => 'Email is not configured yet. Open System Settings → Notifications / Email and set SMTP (for Gmail: smtp.gmail.com, port 587, TLS, your Gmail + App Password).',
        ];
    }

    return smsSendMailSmtp($to, $subject, $htmlBody, $textBody, $fromEmail, $fromName);
}

function smsMailEncodeAddress(string $name, string $email): string
{
    $name = trim(str_replace(["\r", "\n"], '', $name));
    $email = trim(str_replace(["\r", "\n"], '', $email));
    if ($name === '') {
        return $email;
    }
    return '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
}

/**
 * Send via PHPMailer SMTP using System Settings credentials.
 *
 * @return array{ok:bool,error:string}
 */
function smsSendMailSmtp(
    string $to,
    string $subject,
    string $htmlBody,
    string $textBody,
    string $fromEmail,
    string $fromName
): array {
    $host = trim(smsSetting('smtp_host', ''));
    $port = (int) smsSetting('smtp_port', '587');
    $enc = strtolower(trim(smsSetting('smtp_encryption', 'tls')));
    $user = trim(smsSetting('smtp_username', ''));
    $pass = (string) smsSetting('smtp_password', '');

    if ($host === '') {
        return [
            'ok' => false,
            'error' => 'Email is not configured yet. Open System Settings → Notifications / Email and set SMTP.',
        ];
    }

    if ($port <= 0) {
        $port = $enc === 'ssl' ? 465 : 587;
    }

    $phpmailerRoot = ROOT_PATH . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src';
    $required = [
        $phpmailerRoot . DIRECTORY_SEPARATOR . 'Exception.php',
        $phpmailerRoot . DIRECTORY_SEPARATOR . 'PHPMailer.php',
        $phpmailerRoot . DIRECTORY_SEPARATOR . 'SMTP.php',
    ];
    foreach ($required as $file) {
        if (!is_file($file)) {
            $msg = 'PHPMailer is missing. Expected files under PHPMailer/src/.';
            error_log('SMS2 ' . $msg);
            return ['ok' => false, 'error' => $msg];
        }
    }

    require_once $required[0];
    require_once $required[1];
    require_once $required[2];

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = ($user !== '');
        if ($user !== '') {
            $mail->Username = $user;
            $mail->Password = $pass;
        }

        if ($enc === 'ssl') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($enc === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        $mail->Timeout = 20;
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody !== '' ? $textBody : strip_tags($htmlBody);
        $mail->XMailer = 'SMS2 / PHPMailer';

        $mail->send();

        return ['ok' => true, 'error' => ''];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $detail = trim((string) ($mail->ErrorInfo ?? $e->getMessage()));
        if ($detail === '') {
            $detail = $e->getMessage();
        }
        $msg = 'SMTP send failed: ' . $detail;
        error_log('SMS2 ' . $msg);
        return ['ok' => false, 'error' => $msg];
    } catch (Throwable $e) {
        $msg = 'SMTP send failed: ' . $e->getMessage();
        error_log('SMS2 ' . $msg);
        return ['ok' => false, 'error' => $msg];
    }
}

/**
 * Send password-reset link to the account email (or an explicit recipient).
 *
 * @param array<string,mixed> $user
 * @return array{ok:bool,error:string,to:string}
 */
function smsSendPasswordResetEmail(array $user, string $resetUrl, ?string $toOverride = null): array
{
    $to = trim((string) ($toOverride ?? ''));
    if ($to === '') {
        $to = trim((string) ($user['email'] ?? ''));
    }
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'This account has no valid email on file.', 'to' => ''];
    }

    $name = trim((string) ($user['full_name'] ?? 'User'));
    if ($name === '') {
        $name = 'User';
    }

    $subject = APP_SHORT_NAME . ' password reset';
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
    $app = htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8');
    $inst = htmlspecialchars(INSTITUTION, ENT_QUOTES, 'UTF-8');

    $html = '<div style="font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#0f172a;">'
        . '<p>Hi ' . $safeName . ',</p>'
        . '<p>We received a request to reset your password for <strong>' . $app . '</strong>.</p>'
        . '<p><a href="' . $safeUrl . '" style="display:inline-block;padding:12px 18px;background:#294ecb;color:#fff;text-decoration:none;border-radius:8px;font-weight:700;">Reset your password</a></p>'
        . '<p>Or copy this link into your browser:</p>'
        . '<p style="word-break:break-all;color:#1d4ed8;">' . $safeUrl . '</p>'
        . '<p>This link expires in <strong>1 hour</strong>. If you did not request this, you can ignore this email.</p>'
        . '<p style="color:#64748b;font-size:13px;">' . $inst . ' · ' . $app . '</p>'
        . '</div>';

    $text = "Hi {$name},\n\n"
        . "We received a request to reset your password for " . APP_NAME . ".\n\n"
        . "Open this link to reset your password (expires in 1 hour):\n{$resetUrl}\n\n"
        . "If you did not request this, ignore this email.\n\n"
        . INSTITUTION . " · " . APP_NAME . "\n";

    $result = smsSendMail($to, $subject, $html, $text);
    $result['to'] = $to;
    return $result;
}

/**
 * Email a one-time password (OTP) to the user's account email.
 *
 * @param array<string,mixed> $user
 * @return array{ok:bool,error:string,to:string}
 */
function smsSendOtpEmail(array $user, string $code, string $purposeLabel = 'password change', int $ttlMinutes = 10): array
{
    $to = trim((string) ($user['email'] ?? ''));
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'This account has no valid email on file.', 'to' => ''];
    }

    $code = preg_replace('/\D+/', '', $code) ?? '';
    if (strlen($code) !== 6) {
        return ['ok' => false, 'error' => 'Invalid OTP code.', 'to' => $to];
    }

    $name = trim((string) ($user['full_name'] ?? 'User'));
    if ($name === '') {
        $name = 'User';
    }

    $ttlMinutes = max(1, $ttlMinutes);
    $subject = APP_SHORT_NAME . ' verification code';
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    $safePurpose = htmlspecialchars($purposeLabel, ENT_QUOTES, 'UTF-8');
    $app = htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8');
    $inst = htmlspecialchars(INSTITUTION, ENT_QUOTES, 'UTF-8');

    $html = '<div style="font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#0f172a;">'
        . '<p>Hi ' . $safeName . ',</p>'
        . '<p>Your one-time verification code for <strong>' . $safePurpose . '</strong> on <strong>' . $app . '</strong> is:</p>'
        . '<p style="font-size:28px;font-weight:800;letter-spacing:0.2em;margin:16px 0;">' . $safeCode . '</p>'
        . '<p>This code expires in <strong>' . (int) $ttlMinutes . ' minutes</strong>. Do not share it with anyone.</p>'
        . '<p>If you did not request this, you can ignore this email.</p>'
        . '<p style="color:#64748b;font-size:13px;">' . $inst . ' · ' . $app . '</p>'
        . '</div>';

    $text = "Hi {$name},\n\n"
        . "Your one-time verification code for {$purposeLabel} on " . APP_NAME . " is:\n\n"
        . "{$code}\n\n"
        . "This code expires in {$ttlMinutes} minutes. Do not share it with anyone.\n\n"
        . "If you did not request this, ignore this email.\n\n"
        . INSTITUTION . " · " . APP_NAME . "\n";

    $result = smsSendMail($to, $subject, $html, $text);
    $result['to'] = $to;
    return $result;
}
