<?php
/**
 * Al-Khair - Donation Management System
 * 
 * @package    Al-Khair
 * @author     Tansiq Labs
 * @copyright  2025 Tansiq Labs
 * @version    1.0.0
 */

// Start session
session_start();

// Define root path
define('ROOT_PATH', __DIR__);
define('APP_VERSION', '1.0.0');

// Check if installation is required
if (!file_exists(ROOT_PATH . '/install.lock')) {
    header('Location: install/index.php');
    exit;
}

// Verify required files exist
if (!file_exists(ROOT_PATH . '/config/database.php')) {
    header('Location: install/index.php');
    exit;
}

// Load configuration
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

// Initialize database connection
$db = getDBConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Load dashboard
require_once ROOT_PATH . '/dashboard/index.php';
