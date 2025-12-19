<?php
/**
 * Update System
 * Check and install updates from GitHub
 */

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db = getDBConnection();

switch ($action) {
    case 'check':
        checkForUpdates();
        break;
    case 'download':
        downloadUpdate();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

function checkForUpdates() {
    global $db;
    
    // GitHub repository information
    $repoOwner = 'TansiqLabs';
    $repoName = 'al-khair';
    $apiUrl = "https://api.github.com/repos/$repoOwner/$repoName/releases/latest";
    
    // Set user agent (required by GitHub API)
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Al-Khair-Update-System\r\n"
        ]
    ]);
    
    try {
        $response = @file_get_contents($apiUrl, false, $context);
        
        if ($response === false) {
            jsonResponse([
                'success' => false,
                'message' => 'Unable to check for updates. Please try again later.'
            ]);
        }
        
        $releaseData = json_decode($response, true);
        
        if (!$releaseData || !isset($releaseData['tag_name'])) {
            jsonResponse([
                'success' => false,
                'message' => 'No releases found.'
            ]);
        }
        
        $latestVersion = ltrim($releaseData['tag_name'], 'v');
        $currentVersion = APP_VERSION;
        
        $updateAvailable = version_compare($latestVersion, $currentVersion, '>');
        
        // Update last check time
        updateSetting($db, 'last_update_check', date('Y-m-d H:i:s'));
        
        jsonResponse([
            'success' => true,
            'update_available' => $updateAvailable,
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'release_name' => $releaseData['name'] ?? 'New Release',
            'release_notes' => $releaseData['body'] ?? '',
            'download_url' => $releaseData['zipball_url'] ?? '',
            'published_at' => $releaseData['published_at'] ?? ''
        ]);
        
    } catch (Exception $e) {
        jsonResponse([
            'success' => false,
            'message' => 'Error checking for updates: ' . $e->getMessage()
        ]);
    }
}

function downloadUpdate() {
    jsonResponse([
        'success' => false,
        'message' => 'Automatic updates will be available in the next release. Please update manually for now.'
    ]);
}
