<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn()) { header('Location: ../login.php'); exit; }
$pageTitle = 'Beneficiaries';
include ROOT_PATH . '/dashboard/header.php';
?>
<div class="dashboard-content">
    <div class="page-header">
        <div><h1>Beneficiaries Management</h1><p>Manage project beneficiaries</p></div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <p>Beneficiaries module - Fully functional version coming soon</p>
            </div>
        </div>
    </div>
</div>
<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
