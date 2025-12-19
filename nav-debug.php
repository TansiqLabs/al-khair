<?php
/**
 * Navigation Debug
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/includes/functions.php';

echo "<h1>Navigation Debug</h1>";
echo "<pre>";
echo "BASE_URL Constant: " . BASE_URL . "\n";
echo "getBaseUrl() Function: " . getBaseUrl() . "\n";
echo "\n";
echo "Current Script: " . $_SERVER['PHP_SELF'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Protocol: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "\n";
echo "\n";
echo "Expected Links:\n";
echo "Dashboard: " . getBaseUrl() . "/dashboard/index.php\n";
echo "Donors: " . getBaseUrl() . "/dashboard/donors.php\n";
echo "Donations: " . getBaseUrl() . "/dashboard/donations.php\n";
echo "Projects: " . getBaseUrl() . "/dashboard/projects.php\n";
echo "Reports: " . getBaseUrl() . "/dashboard/reports.php\n";
echo "</pre>";
?>
