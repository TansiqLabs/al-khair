<?php
/**
 * Logout Handler
 */

session_start();

define('ROOT_PATH', __DIR__);

if (file_exists(ROOT_PATH . '/config/database.php')) {
    require_once ROOT_PATH . '/config/database.php';
    require_once ROOT_PATH . '/includes/functions.php';
    
    try {
        $db = getDBConnection();
        logActivity($db, 'user_logout', 'user', getCurrentUserId(), 'User logged out');
    } catch (Exception $e) {
        // Silent fail
    }
}

// Destroy session
session_destroy();

// Remove remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login
header('Location: login.php?logout=1');
exit;
