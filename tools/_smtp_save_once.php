<?php
/**
 * One-shot SMTP credential save — delete after use.
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/mail.php';

header('Content-Type: text/plain; charset=utf-8');

$user = 'kennethabejuela0308@gmail.com';
$pass = preg_replace('/\s+/', '', 'cjfa fpmu efzk rtoq') ?? '';

smsSetSetting('smtp_host', 'smtp.gmail.com');
smsSetSetting('smtp_port', '587');
smsSetSetting('smtp_encryption', 'tls');
smsSetSetting('smtp_username', $user);
smsSetSetting('smtp_password', $pass);
smsSetSetting('mail_from_email', $user);
smsSetSetting('mail_from_name', APP_SHORT_NAME);
smsSetSetting('mail_admin_email', $user);
smsSetSetting('mail_show_link_on_failure', '1');

if (isset($GLOBALS['__sms_settings_cache']) && is_array($GLOBALS['__sms_settings_cache'])) {
    unset($GLOBALS['__sms_settings_cache']['smtp_password']);
}

$stored = smsSetting('smtp_password', '');
echo 'encrypt_ok=' . ($stored === $pass ? '1' : '0') . "\n";
echo 'pass_len=' . strlen(smsSmtpPassword()) . "\n";
echo 'user=' . smsSetting('smtp_username') . "\n";
echo 'from=' . smsSetting('mail_from_email') . "\n";

$result = smsSendMail(
    $user,
    APP_SHORT_NAME . ' SMTP ready',
    '<p>SMTP App Password saved. Forgot-password OTP email should work now.</p>',
    'SMTP App Password saved. Forgot-password OTP email should work now.'
);

echo 'send_ok=' . (!empty($result['ok']) ? '1' : '0') . "\n";
echo 'send_error=' . ($result['error'] ?? '') . "\n";
