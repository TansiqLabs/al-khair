<?php
/**
 * Debug Script - Shows exact errors
 * DELETE THIS FILE AFTER DEBUGGING
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Debug Information</h1>";
echo "<pre>";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . __DIR__ . "\n\n";

// Check if files exist
$files = [
    'install.lock' => __DIR__ . '/install.lock',
    'config/database.php' => __DIR__ . '/config/database.php',
    'config/app.php' => __DIR__ . '/config/app.php',
    'includes/functions.php' => __DIR__ . '/includes/functions.php',
    'install/schema.sql' => __DIR__ . '/install/schema.sql',
];

echo "=== File Existence Check ===\n";
foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    echo sprintf("%-25s: %s %s\n", 
        $name, 
        $exists ? '✓ EXISTS' : '✗ MISSING',
        $readable ? '(readable)' : ($exists ? '(NOT readable)' : '')
    );
}

echo "\n=== Testing login.php ===\n";
try {
    // Simulate what login.php does
    session_start();
    define('ROOT_PATH', __DIR__);
    
    echo "ROOT_PATH defined: " . ROOT_PATH . "\n";
    
    if (!file_exists(ROOT_PATH . '/install.lock')) {
        echo "install.lock NOT FOUND - will redirect to installation\n";
    } else {
        echo "install.lock FOUND\n";
    }
    
    if (!file_exists(ROOT_PATH . '/config/database.php')) {
        echo "database.php NOT FOUND - will redirect to installation\n";
    } else {
        echo "database.php FOUND\n";
    }
    
    if (file_exists(ROOT_PATH . '/config/app.php')) {
        echo "\nIncluding config/app.php...\n";
        require_once ROOT_PATH . '/config/app.php';
        echo "✓ config/app.php included successfully\n";
    }
    
    if (file_exists(ROOT_PATH . '/config/database.php')) {
        echo "\nIncluding config/database.php...\n";
        require_once ROOT_PATH . '/config/database.php';
        echo "✓ config/database.php included successfully\n";
    }
    
    if (file_exists(ROOT_PATH . '/includes/functions.php')) {
        echo "\nIncluding includes/functions.php...\n";
        require_once ROOT_PATH . '/includes/functions.php';
        echo "✓ includes/functions.php included successfully\n";
    }
    
    echo "\n✓ All includes worked! Login.php should work.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== PHP Extensions ===\n";
$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'session'];
foreach ($extensions as $ext) {
    echo sprintf("%-15s: %s\n", $ext, extension_loaded($ext) ? '✓ Loaded' : '✗ Not loaded');
}

echo "\n=== Directory Permissions ===\n";
$dirs = [
    'root' => __DIR__,
    'config' => __DIR__ . '/config',
    'uploads' => __DIR__ . '/uploads',
    'logs' => __DIR__ . '/logs',
    'cache' => __DIR__ . '/cache',
];

foreach ($dirs as $name => $path) {
    $perms = file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    $writable = is_writable($path) ? '✓ Writable' : '✗ Not writable';
    echo sprintf("%-10s: %s %s\n", $name, $perms, $writable);
}

echo "</pre>";

echo "<p><strong>DELETE THIS FILE (debug.php) AFTER DEBUGGING!</strong></p>";
echo "<p><a href='login.php'>Test login.php</a> | <a href='install/index.php'>Go to Installation</a></p>";
?>
