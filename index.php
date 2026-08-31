<?php
/**
 * Redirect root to welcome page
 */
require_once __DIR__ . '/config/config.php';
header('Location: ' . BASE_URL . '/welcome/index.php');
exit;
