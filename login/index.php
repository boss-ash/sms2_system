<?php
/**
 * Redirect /login/ to the sign-in page
 */
require_once __DIR__ . '/../config/config.php';
header('Location: ' . BASE_URL . '/login/login.php');
exit;
