<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn()) { header('Location: ../login.php'); exit; }
$pageTitle = 'Donations';
include ROOT_PATH . '/dashboard/header.php';
?>
<div class="dashboard-content">
    <div class="page-header">
        <div><h1>Donations Management</h1><p>Track all donations</p></div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <p>Donations module - Fully functional version coming soon</p>
            </div>
        </div>
    </div>
</div>
<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
