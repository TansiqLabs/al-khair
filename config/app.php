<?php
/**
 * Application Configuration
 */

if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Auto-detect base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = dirname(dirname($scriptName)); // Go up two levels from current script
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
define('BASE_URL', $protocol . '://' . $host . $baseDir);

// Application settings
define('APP_NAME', 'Al-Khair');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'Asia/Dhaka');

// Date & Time formats
define('DATE_FORMAT', 'd M, Y');
define('DATETIME_FORMAT', 'd M, Y h:i A');
define('TIME_FORMAT', 'h:i A');

// Currency
define('CURRENCY_SYMBOL', '৳');
define('CURRENCY_CODE', 'BDT');

// Pagination
define('ITEMS_PER_PAGE', 15);

// File upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Security settings
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/errors.log');