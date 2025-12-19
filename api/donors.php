<?php
/**
 * Donors API Handler
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

$db = getDBConnection();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        getDonor();
        break;
    case 'create':
        createDonor();
        break;
    case 'update':
        updateDonor();
        break;
    case 'delete':
        deleteDonor();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

function getDonor() {
    global $db;
    
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Donor ID required']);
    }
    
    try {
        $stmt = $db->prepare("SELECT * FROM donors WHERE id = ?");
        $stmt->execute([$id]);
        $donor = $stmt->fetch();
        
        if (!$donor) {
            jsonResponse(['success' => false, 'message' => 'Donor not found']);
        }
        
        // Get donation history
        $stmt = $db->prepare("
            SELECT d.*, p.title as project_title
            FROM donations d
            LEFT JOIN projects p ON d.project_id = p.id
            WHERE d.donor_id = ?
            ORDER BY d.donation_date DESC
        ");
        $stmt->execute([$id]);
        $donations = $stmt->fetchAll();
        
        $donor['donations'] = $donations;
        
        jsonResponse(['success' => true, 'donor' => $donor]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Error fetching donor']);
    }
}

function createDonor() {
    global $db;

    if (!isAdmin()) {
        jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
    }

    if (!isset($_POST['csrf_token']) || !checkCsrfToken($_POST['csrf_token'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }
    
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $whatsapp = sanitize($_POST['whatsapp'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $district = sanitize($_POST['district'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($fullName)) {
        jsonResponse(['success' => false, 'message' => 'Full name is required']);
    }
    
    if (!empty($email) && !isValidEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address']);
    }
    
    try {
        // Generate unique donor code
        $donorCode = generateUniqueCode('DNR', 6);
        
        // Check if code already exists
        $stmt = $db->prepare("SELECT id FROM donors WHERE donor_code = ?");
        $stmt->execute([$donorCode]);
        
        while ($stmt->fetch()) {
            $donorCode = generateUniqueCode('DNR', 6);
            $stmt->execute([$donorCode]);
        }
        
        $stmt = $db->prepare("
            INSERT INTO donors (donor_code, full_name, email, phone, whatsapp, address, city, district, postal_code, notes, is_active, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $donorCode, $fullName, $email, $phone, $whatsapp, $address,
            $city, $district, $postalCode, $notes, $isActive, getCurrentUserId()
        ]);
        
        $donorId = $db->lastInsertId();
        
        logActivity($db, 'donor_created', 'donor', $donorId, "Created donor: $fullName");
        
        jsonResponse([
            'success' => true,
            'message' => 'Donor created successfully',
            'donor_id' => $donorId
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Error creating donor']);
    }
}

function updateDonor() {
    global $db;

    if (!isAdmin()) {
        jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
    }

    if (!isset($_POST['csrf_token']) || !checkCsrfToken($_POST['csrf_token'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }
    
    $id = $_POST['donor_id'] ?? 0;
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $whatsapp = sanitize($_POST['whatsapp'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $district = sanitize($_POST['district'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Donor ID required']);
    }
    
    if (empty($fullName)) {
        jsonResponse(['success' => false, 'message' => 'Full name is required']);
    }
    
    if (!empty($email) && !isValidEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address']);
    }
    
    try {
        $stmt = $db->prepare("
            UPDATE donors
            SET full_name = ?, email = ?, phone = ?, whatsapp = ?, address = ?,
                city = ?, district = ?, postal_code = ?, notes = ?, is_active = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $fullName, $email, $phone, $whatsapp, $address,
            $city, $district, $postalCode, $notes, $isActive, $id
        ]);
        
        logActivity($db, 'donor_updated', 'donor', $id, "Updated donor: $fullName");
        
        jsonResponse([
            'success' => true,
            'message' => 'Donor updated successfully'
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Error updating donor']);
    }
}

function deleteDonor() {
    global $db;

    if (!isAdmin()) {
        jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
    }

    if (!isset($_POST['csrf_token']) || !checkCsrfToken($_POST['csrf_token'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Donor ID required']);
    }
    
    try {
        // Check if donor has donations
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM donations WHERE donor_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetch()['count'];
        
        if ($count > 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Cannot delete donor with existing donations. Please deactivate instead.'
            ]);
        }
        
        // Get donor name for log
        $stmt = $db->prepare("SELECT full_name FROM donors WHERE id = ?");
        $stmt->execute([$id]);
        $donor = $stmt->fetch();
        
        // Delete donor
        $stmt = $db->prepare("DELETE FROM donors WHERE id = ?");
        $stmt->execute([$id]);
        
        logActivity($db, 'donor_deleted', 'donor', $id, "Deleted donor: " . ($donor['full_name'] ?? ''));
        
        jsonResponse([
            'success' => true,
            'message' => 'Donor deleted successfully'
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Error deleting donor']);
    }
}
