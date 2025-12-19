<?php
/**
 * Installation Process Handler
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display in output
ini_set('log_errors', 1);

session_start();
header('Content-Type: application/json');

// Define ROOT_PATH more reliably
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(dirname(__DIR__)));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'check_requirements':
        checkRequirements();
        break;
    
    case 'test_database':
        testDatabase();
        break;
    
    case 'complete_installation':
        completeInstallation();
        break;
    
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

function checkRequirements() {
    $requirements = [
        [
            'name' => 'PHP Version (>= 7.4)',
            'status' => version_compare(PHP_VERSION, '7.4.0', '>=')
        ],
        [
            'name' => 'PDO Extension',
            'status' => extension_loaded('pdo')
        ],
        [
            'name' => 'PDO MySQL Driver',
            'status' => extension_loaded('pdo_mysql')
        ],
        [
            'name' => 'JSON Extension',
            'status' => extension_loaded('json')
        ],
        [
            'name' => 'MBString Extension',
            'status' => extension_loaded('mbstring')
        ],
        [
            'name' => 'Schema File Exists',
            'status' => file_exists(ROOT_PATH . '/install/schema.sql') || file_exists(__DIR__ . '/schema.sql')
        ],
        [
            'name' => 'Config Directory Writable',
            'status' => is_dir(ROOT_PATH . '/config') && is_writable(ROOT_PATH . '/config')
        ],
        [
            'name' => 'Config Template Exists',
            'status' => file_exists(ROOT_PATH . '/config/database.php.template')
        ],
        [
            'name' => 'Uploads Directory Writable',
            'status' => is_dir(ROOT_PATH . '/uploads') && is_writable(ROOT_PATH . '/uploads')
        ],
        [
            'name' => 'Logs Directory Writable',
            'status' => is_dir(ROOT_PATH . '/logs') && is_writable(ROOT_PATH . '/logs')
        ],
        [
            'name' => 'Cache Directory Writable',
            'status' => is_dir(ROOT_PATH . '/cache') && is_writable(ROOT_PATH . '/cache')
        ],
        [
            'name' => 'Root Directory Writable',
            'status' => is_writable(ROOT_PATH)
        ]
    ];

    jsonResponse(['success' => true, 'requirements' => $requirements]);
}

function testDatabase() {
    $host = $_POST['db_host'] ?? '';
    $name = $_POST['db_name'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';

    if (empty($host) || empty($name) || empty($user)) {
        jsonResponse(['success' => false, 'message' => 'Please fill in all required fields']);
    }

    try {
        $dsn = "mysql:host=$host;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '$name'");
        if ($stmt->rowCount() == 0) {
            // Create database
            $pdo->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        // Test connection to the database
        $pdo->exec("USE `$name`");

        jsonResponse([
            'success' => true,
            'message' => 'Database connection successful!'
        ]);
    } catch (PDOException $e) {
        jsonResponse([
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage()
        ]);
    }
}

function completeInstallation() {
    try {
        // Get POST data
        $dbHost = $_POST['db_host'] ?? '';
        $dbName = $_POST['db_name'] ?? '';
        $dbUser = $_POST['db_user'] ?? '';
        $dbPass = $_POST['db_pass'] ?? '';

        $adminName = $_POST['admin_name'] ?? '';
        $adminUsername = $_POST['admin_username'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        $adminPassword = $_POST['admin_password'] ?? '';

        $orgName = $_POST['org_name'] ?? 'Al-Khair Foundation';
        $orgAddress = $_POST['org_address'] ?? '';
        $orgPhone = $_POST['org_phone'] ?? '';
        $orgEmail = $_POST['org_email'] ?? '';

        // Validate inputs
        if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
            throw new Exception('Database information is incomplete');
        }

        if (empty($adminName) || empty($adminUsername) || empty($adminEmail) || empty($adminPassword)) {
            throw new Exception('Admin account information is incomplete');
        }

        // Connect to database
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Read and execute schema (split by semicolon for multiple queries)
        $schemaFile = ROOT_PATH . '/install/schema.sql';
        
        // Try alternate path if first one fails
        if (!file_exists($schemaFile)) {
            $schemaFile = __DIR__ . '/schema.sql';
        }
        
        if (!file_exists($schemaFile)) {
            throw new Exception('Schema file not found. Checked: ' . ROOT_PATH . '/install/schema.sql and ' . __DIR__ . '/schema.sql');
        }
        
        $schema = file_get_contents($schemaFile);
        if ($schema === false) {
            throw new Exception('Failed to read schema file');
        }
        
        // Split queries by semicolon and filter empty ones
        $queries = array_filter(array_map('trim', explode(';', $schema)));
        
        $executedCount = 0;
        foreach ($queries as $query) {
            if (!empty($query) && strlen($query) > 10) {
                try {
                    $pdo->exec($query);
                    $executedCount++;
                } catch (PDOException $e) {
                    // Continue if table already exists, otherwise throw
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw new Exception('Schema execution error at query ' . ($executedCount + 1) . ': ' . $e->getMessage());
                    }
                }
            }
        }

        // Create admin user
        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, full_name, role, is_active)
            VALUES (?, ?, ?, ?, 'admin', 1)
        ");
        $stmt->execute([$adminUsername, $adminEmail, $hashedPassword, $adminName]);

        // Update organization settings
        $settings = [
            'organization_name' => $orgName,
            'organization_address' => $orgAddress,
            'organization_phone' => $orgPhone,
            'organization_email' => $orgEmail
        ];

        $stmt = $pdo->prepare("
            UPDATE settings SET setting_value = ? WHERE setting_key = ?
        ");

        foreach ($settings as $key => $value) {
            $stmt->execute([$value, $key]);
        }

        // Create database config file
        $configDir = ROOT_PATH . '/config';
        $templateFile = $configDir . '/database.php.template';
        $configFile = $configDir . '/database.php';
        
        if (!is_dir($configDir)) {
            throw new Exception('Config directory not found: ' . $configDir);
        }
        
        if (!file_exists($templateFile)) {
            throw new Exception('Config template not found: ' . $templateFile);
        }
        
        if (!is_writable($configDir)) {
            throw new Exception('Config directory is not writable: ' . $configDir);
        }
        
        $configTemplate = file_get_contents($templateFile);
        $configContent = str_replace(
            ['{{DB_HOST}}', '{{DB_NAME}}', '{{DB_USER}}', '{{DB_PASS}}'],
            [$dbHost, $dbName, $dbUser, $dbPass],
            $configTemplate
        );

        if (file_put_contents($configFile, $configContent) === false) {
            throw new Exception('Failed to write config file');
        }

        // Create install.lock file
        $lockFile = ROOT_PATH . '/install.lock';
        if (file_put_contents($lockFile, date('Y-m-d H:i:s')) === false) {
            throw new Exception('Failed to create install.lock file. Check directory permissions.');
        }

        jsonResponse([
            'success' => true,
            'message' => 'Installation completed successfully! Redirecting to login...'
        ]);

    } catch (PDOException $e) {
        jsonResponse([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'code' => $e->getCode(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]);
    } catch (Exception $e) {
        jsonResponse([
            'success' => false,
            'message' => 'Installation failed: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

function jsonResponse($data) {
    echo json_encode($data);
    exit;
}
