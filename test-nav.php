<?php
/**
 * Navigation Test
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/includes/functions.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Navigation Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h1 { color: #333; }
        .test { margin: 20px 0; padding: 10px; border: 1px solid #ddd; }
        .pass { background: #d4edda; }
        .fail { background: #f8d7da; }
        a { display: inline-block; margin: 5px 0; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        a:hover { background: #0056b3; }
    </style>
</head>
<body>

<h1>Navigation Test</h1>

<div class="test pass">
    <strong>BASE_URL:</strong> <?php echo BASE_URL; ?>
</div>

<div class="test pass">
    <strong>Test Links (click to navigate):</strong><br>
    <a href="<?php echo BASE_URL; ?>/dashboard/index.php">Dashboard</a>
    <a href="<?php echo BASE_URL; ?>/dashboard/donors.php">Donors</a>
    <a href="<?php echo BASE_URL; ?>/dashboard/donations.php">Donations</a>
    <a href="<?php echo BASE_URL; ?>/dashboard/projects.php">Projects</a>
    <a href="<?php echo BASE_URL; ?>/dashboard/reports.php">Reports</a>
</div>

<div class="test">
    <strong>Debug Info:</strong><br>
    Protocol: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http'; ?><br>
    Host: <?php echo $_SERVER['HTTP_HOST']; ?><br>
    Script Name: <?php echo $_SERVER['SCRIPT_NAME']; ?><br>
    Request URI: <?php echo $_SERVER['REQUEST_URI']; ?>
</div>

</body>
</html>
