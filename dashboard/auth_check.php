<?php
/**
 * Authentication Check for Dashboard Pages
 */

session_start();

define('ROOT_PATH', dirname(__DIR__));

// Check if installation is complete
if (!file_exists(ROOT_PATH . '/install.lock')) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/../install/index.php');
    exit;
}

// Verify required files exist
if (!file_exists(ROOT_PATH . '/config/database.php')) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/../install/index.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/../login.php');
    exit;
}
