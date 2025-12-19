<?php
/**
 * Settings Management - Admin Only
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$db = getDBConnection();
$pageTitle = 'Settings';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'organization_name' => sanitize($_POST['organization_name'] ?? ''),
        'organization_address' => sanitize($_POST['organization_address'] ?? ''),
        'organization_phone' => sanitize($_POST['organization_phone'] ?? ''),
        'organization_email' => sanitize($_POST['organization_email'] ?? ''),
        'organization_website' => sanitize($_POST['organization_website'] ?? ''),
        'currency_symbol' => sanitize($_POST['currency_symbol'] ?? '৳'),
        'items_per_page' => (int)($_POST['items_per_page'] ?? 20),
    ];

    try {
        foreach ($settings as $key => $value) {
            updateSetting($db, $key, $value);
        }

        logActivity($db, 'settings_updated', null, null, 'System settings updated');
        $message = 'Settings updated successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error updating settings';
        $messageType = 'error';
    }
}

// Get current settings
$currentSettings = [];
$stmt = $db->query("SELECT setting_key, setting_value FROM settings");
while ($row = $stmt->fetch()) {
    $currentSettings[$row['setting_key']] = $row['setting_value'];
}

include ROOT_PATH . '/dashboard/header.php';
?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>System Settings</h1>
            <p>Manage your organization settings</p>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo e($message); ?>
    </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 1fr;">
        <div class="card">
            <div class="card-header">
                <h3>Organization Information</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="organization_name">Organization Name</label>
                        <input type="text" id="organization_name" name="organization_name" 
                               value="<?php echo e($currentSettings['organization_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="organization_address">Address</label>
                        <textarea id="organization_address" name="organization_address" rows="3"><?php echo e($currentSettings['organization_address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="organization_phone">Phone Number</label>
                            <input type="text" id="organization_phone" name="organization_phone" 
                                   value="<?php echo e($currentSettings['organization_phone'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="organization_email">Email Address</label>
                            <input type="email" id="organization_email" name="organization_email" 
                                   value="<?php echo e($currentSettings['organization_email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="organization_website">Website URL</label>
                        <input type="url" id="organization_website" name="organization_website" 
                               value="<?php echo e($currentSettings['organization_website'] ?? ''); ?>">
                    </div>

                    <h4 style="margin: 30px 0 20px; padding-top: 20px; border-top: 2px solid #e5e7eb;">Application Settings</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="currency_symbol">Currency Symbol</label>
                            <input type="text" id="currency_symbol" name="currency_symbol" 
                                   value="<?php echo e($currentSettings['currency_symbol'] ?? '৳'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="items_per_page">Items Per Page</label>
                            <input type="number" id="items_per_page" name="items_per_page" 
                                   value="<?php echo e($currentSettings['items_per_page'] ?? 20); ?>" required min="10" max="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>System Information</h3>
            </div>
            <div class="card-body">
                <div class="detail-item">
                    <span class="detail-label">Application Version:</span>
                    <span class="detail-value"><?php echo APP_VERSION; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PHP Version:</span>
                    <span class="detail-value"><?php echo PHP_VERSION; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Database:</span>
                    <span class="detail-value">MySQL</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Developed By:</span>
                    <span class="detail-value"><strong>Tansiq Labs</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
