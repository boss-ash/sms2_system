<?php
/**
 * SMS 2 – Mail helper (SMTP + password reset emails)
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

    if ($port <= 0) {
        $port = $enc === 'ssl' ? 465 : 587;
    }

    $remote = ($enc === 'ssl' ? 'ssl://' : '') . $host;
    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client(
        $remote . ':' . $port,
        $errno,
        $errstr,
        20,
        STREAM_CLIENT_CONNECT
    );
    if (!$fp) {
        $msg = 'SMTP connect failed: ' . $errstr . ' (' . $errno . ')';
        error_log('SMS2 ' . $msg);
        return ['ok' => false, 'error' => $msg];
    }
    stream_set_timeout($fp, 20);

    $read = static function () use ($fp): string {
        $data = '';
        while (!feof($fp)) {
            $line = fgets($fp, 515);
            if ($line === false) {
                break;
            }
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $expect = static function (string $response, array $codes) use (&$fp): ?string {
        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $codes, true)) {
            return 'Unexpected SMTP response: ' . trim($response);
        }
        return null;
    };
    $write = static function (string $cmd) use ($fp): void {
        fwrite($fp, $cmd . "\r\n");
    };

    $err = $expect($read(), [220]);
    if ($err) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $ehloHost = preg_replace('/[^a-zA-Z0-9.-]/', '', (string) ($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';
    $write('EHLO ' . $ehloHost);
    $err = $expect($read(), [250]);
    if ($err) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    if ($enc === 'tls') {
        $write('STARTTLS');
        $err = $expect($read(), [220]);
        if ($err) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
        if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            return ['ok' => false, 'error' => 'SMTP STARTTLS negotiation failed.'];
        }
        $write('EHLO ' . $ehloHost);
        $err = $expect($read(), [250]);
        if ($err) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
    }

    if ($user !== '') {
        $write('AUTH LOGIN');
        $err = $expect($read(), [334]);
        if ($err) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
        $write(base64_encode($user));
        $err = $expect($read(), [334]);
        if ($err) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
        $write(base64_encode($pass));
        $err = $expect($read(), [235]);
        if ($err) {
            fclose($fp);
            return ['ok' => false, 'error' => 'SMTP authentication failed. Check username/app password.'];
        }
    }

    $write('MAIL FROM:<' . $fromEmail . '>');
    $err = $expect($read(), [250]);
    if ($err) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $write('RCPT TO:<' . $to . '>');
    $err = $expect($read(), [250, 251]);
    if ($err) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $write('DATA');
    $err = $expect($read(), [354]);
    if ($err) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $boundary = 'sms2_' . bin2hex(random_bytes(8));
    $headers = [
        'Date: ' . date('r'),
        'From: ' . smsMailEncodeAddress($fromName, $fromEmail),
        'To: <' . $to . '>',
        'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: SMS2',
    ];

    $dotSafe = static function (string $s): string {
        return preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $s)) ?? $s;
    };

    $message = implode("\r\n", $headers) . "\r\n\r\n"
        . '--' . $boundary . "\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n\r\n"
        . $dotSafe($textBody) . "\r\n\r\n"
        . '--' . $boundary . "\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n\r\n"
        . $dotSafe($htmlBody) . "\r\n\r\n"
        . '--' . $boundary . "--\r\n"
        . '.';

    $write($message);
    $err = $expect($read(), [250]);
    $write('QUIT');
    fclose($fp);

    if ($err) {
        error_log('SMS2 SMTP send failed: ' . $err);
        return ['ok' => false, 'error' => $err];
    }
    return ['ok' => true, 'error' => ''];
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
