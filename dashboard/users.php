<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn() || !isAdmin()) { header('Location: ../login.php'); exit; }
$db = getDBConnection();
$pageTitle = 'Users';

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include ROOT_PATH . '/dashboard/header.php';
?>
<div class="dashboard-content">
    <div class="page-header">
        <div><h1>User Management</h1><p>Manage admin and staff users</p></div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['username']); ?></td>
                        <td><?php echo e($user['full_name']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><span class="badge badge-primary"><?php echo ucfirst($user['role']); ?></span></td>
                        <td><span class="badge badge-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>"><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                        <td><?php echo $user['last_login'] ? formatDateTime($user['last_login']) : 'Never'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
