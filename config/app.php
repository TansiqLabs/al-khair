<?php
/**
 * Application Configuration
 */

if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Application settings
define('APP_NAME', 'Al-Khair');
define('APP_TIMEZONE', 'Asia/Dhaka');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y h:i A');

// Security settings
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Currency
define('CURRENCY_SYMBOL', '৳'); // Bangladeshi Taka
define('CURRENCY_CODE', 'BDT');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/errors.log');
