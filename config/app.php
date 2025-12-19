<?php
/**
 * Application Configuration
 */

if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Base URL of the application
define('BASE_URL', 'http://localhost/sadaqah-app');

// Application Name
define('APP_NAME', 'Al-Khair');

// Application Version
define('APP_VERSION', '1.0.0');

// Date format
define('DATE_FORMAT', 'd M, Y');

// Datetime format
define('DATETIME_FORMAT', 'd M, Y h:i A');

// Currency symbol
define('CURRENCY_SYMBOL', '৳');

// Items per page for pagination
define('ITEMS_PER_PAGE', 15);

// Max upload size in bytes (e.g., 5MB)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);