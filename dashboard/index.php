<?php
/**
 * Main Dashboard
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$db = getDBConnection();
$userId = getCurrentUserId();
$userRole = getCurrentUserRole();
$userName = $_SESSION['full_name'];

// Get statistics
$stats = [
    'total_donors' => 0,
    'total_donations' => 0,
    'total_projects' => 0,
    'active_projects' => 0,
    'total_beneficiaries' => 0,
    'this_month_donations' => 0
];

try {
    // Total donors
    $stmt = $db->query("SELECT COUNT(*) as count FROM donors WHERE is_active = 1");
    $stats['total_donors'] = $stmt->fetch()['count'];

    // Total donations amount
    $stmt = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM donations");
    $stats['total_donations'] = $stmt->fetch()['total'];

    // Total projects
    $stmt = $db->query("SELECT COUNT(*) as count FROM projects");
    $stats['total_projects'] = $stmt->fetch()['count'];

    // Active projects
    $stmt = $db->query("SELECT COUNT(*) as count FROM projects WHERE status = 'active'");
    $stats['active_projects'] = $stmt->fetch()['count'];

    // Total beneficiaries
    $stmt = $db->query("SELECT COUNT(*) as count FROM beneficiaries");
    $stats['total_beneficiaries'] = $stmt->fetch()['count'];

    // This month donations
    $currentMonth = date('Y-m');
    $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE month = ?");
    $stmt->execute([$currentMonth]);
    $stats['this_month_donations'] = $stmt->fetch()['total'];

    // Recent donations
    $recentDonations = $db->query("
        SELECT d.*, don.full_name as donor_name, p.title as project_title
        FROM donations d
        LEFT JOIN donors don ON d.donor_id = don.id
        LEFT JOIN projects p ON d.project_id = p.id
        ORDER BY d.created_at DESC
        LIMIT 10
    ")->fetchAll();

    // Recent activities
    $recentActivities = $db->query("
        SELECT a.*, u.full_name as user_name
        FROM activity_log a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 10
    ")->fetchAll();

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}

// Get organization settings
$orgName = getSetting($db, 'organization_name', 'Al-Khair');

include ROOT_PATH . '/dashboard/header.php';
?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo e($userName); ?>!</p>
        </div>
        <div class="header-actions">
            <span class="current-date">
                <?php echo date('l, F d, Y'); ?>
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card gradient-purple">
            <div class="stat-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['total_donors']); ?></div>
                <div class="stat-label">Total Donors</div>
            </div>
        </div>

        <div class="stat-card gradient-blue">
            <div class="stat-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo formatCurrency($stats['total_donations']); ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
        </div>

        <div class="stat-card gradient-green">
            <div class="stat-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['active_projects']); ?>/<?php echo number_format($stats['total_projects']); ?></div>
                <div class="stat-label">Active Projects</div>
            </div>
        </div>

        <div class="stat-card gradient-orange">
            <div class="stat-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['total_beneficiaries']); ?></div>
                <div class="stat-label">Beneficiaries</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Recent Donations -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Donations</h3>
                <a href="donations.php" class="btn-link">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentDonations)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p>No donations yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Donor</th>
                                    <th>Amount</th>
                                    <th>Project</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDonations as $donation): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar"><?php echo strtoupper(substr($donation['donor_name'] ?? 'U', 0, 1)); ?></div>
                                            <span><?php echo e($donation['donor_name'] ?? 'Unknown'); ?></span>
                                        </div>
                                    </td>
                                    <td><strong><?php echo formatCurrency($donation['amount']); ?></strong></td>
                                    <td><?php echo e($donation['project_title'] ?? 'General'); ?></td>
                                    <td><?php echo formatDate($donation['donation_date']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h3>This Month</h3>
            </div>
            <div class="card-body">
                <div class="metric-item">
                    <div class="metric-label">Total Donations</div>
                    <div class="metric-value"><?php echo formatCurrency($stats['this_month_donations']); ?></div>
                </div>
                <div class="metric-divider"></div>
                <div class="metric-item">
                    <div class="metric-label">Current Month</div>
                    <div class="metric-value"><?php echo date('F Y'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Activity</h3>
        </div>
        <div class="card-body">
            <?php if (empty($recentActivities)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <p>No activity yet</p>
                </div>
            <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                <strong><?php echo e($activity['user_name'] ?? 'System'); ?></strong>
                                <?php echo e($activity['description'] ?? $activity['action']); ?>
                            </div>
                            <div class="activity-time"><?php echo formatDateTime($activity['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
