<?php
/**
 * InfinityFree deployment settings (free plan = one MySQL database).
 *
 * Copy to config/local.php on the server and fill in values from vPanel → MySQL Databases.
 */

// One-time web migration token (random string). Used at /setup/deploy-db.php?token=...
define('SMS2_DEPLOY_TOKEN', 'palitan-mo-ng-mahaba-at-random-na-string');

// Leave blank to auto-detect URL path. Use '' when files are in htdocs root.
// define('BASE_URL', '');

// From vPanel → MySQL Databases (host is NOT localhost on InfinityFree).
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_PORT', '3306');
define('DB_NAME', 'if0_42794375_sms2');
define('DB_USER', 'if0_42794375');
define('DB_PASS', 'your_hosting_account_password');
define('DB_CHARSET', 'utf8mb4');

// Free plan: use the SAME database name for every module.
define('CRAD_DB_NAME', 'if0_42794375_sms2');
define('STUDENT_PORTAL_DB_NAME', 'if0_42794375_sms2');
define('REPORTS_DB_NAME', 'if0_42794375_sms2');
define('USERMGMT_DB_NAME', 'if0_42794375_sms2');
