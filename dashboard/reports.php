<?php
/**
 * Reports Module
 */

define('ROOT_PATH', dirname(__DIR__));

// Check if installation is complete
if (!file_exists(ROOT_PATH . '/install.lock') || !file_exists(ROOT_PATH . '/config/database.php')) {
    header('Location: ../install/index.php');
    exit;
}

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$db = getDBConnection();
$pageTitle = 'Reports';

// Date range
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Get donation report
$donationReport = $db->prepare("
    SELECT 
        DATE_FORMAT(donation_date, '%Y-%m') as month,
        COUNT(*) as count,
        SUM(amount) as total
    FROM donations
    WHERE donation_date BETWEEN ? AND ?
    GROUP BY month
    ORDER BY month DESC
");
$donationReport->execute([$startDate, $endDate]);
$donations = $donationReport->fetchAll();

// Get project expenses
$expenseReport = $db->prepare("
    SELECT 
        p.title as project_name,
        p.project_code,
        SUM(e.amount) as total_expense,
        COUNT(e.id) as expense_count
    FROM projects p
    LEFT JOIN project_expenses e ON p.id = e.project_id
    WHERE e.expense_date BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_expense DESC
");
$expenseReport->execute([$startDate, $endDate]);
$expenses = $expenseReport->fetchAll();

// Top donors
$topDonors = $db->prepare("
    SELECT 
        d.full_name,
        d.donor_code,
        SUM(don.amount) as total_donated,
        COUNT(don.id) as donation_count
    FROM donors d
    INNER JOIN donations don ON d.id = don.donor_id
    WHERE don.donation_date BETWEEN ? AND ?
    GROUP BY d.id
    ORDER BY total_donated DESC
    LIMIT 10
");
$topDonors->execute([$startDate, $endDate]);
$topDonorsList = $topDonors->fetchAll();

include ROOT_PATH . '/dashboard/header.php';
?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>Reports & Analytics</h1>
            <p>Generate comprehensive financial reports</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-20">
        <div class="card-body">
            <form method="GET" class="search-form">
                <div class="form-group" style="margin: 0;">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo e($startDate); ?>">
                </div>
                <div class="form-group" style="margin: 0;">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo e($endDate); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
                <button type="button" class="btn btn-secondary" onclick="window.print()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                </button>
            </form>
        </div>
    </div>

    <!-- Donation Report -->
    <div class="card mb-20">
        <div class="card-header">
            <h3>Monthly Donations Report</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Donations</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; color:#999;">No data found for selected period</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($donations as $row): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                <td><?php echo number_format($row['count']); ?></td>
                                <td><strong><?php echo formatCurrency($row['total']); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Project Expenses -->
    <div class="card mb-20">
        <div class="card-header">
            <h3>Project Expenses Report</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Total Expenses</th>
                            <th>No. of Expenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#999;">No expenses found for selected period</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $row): ?>
                            <tr>
                                <td><span class="badge badge-secondary"><?php echo e($row['project_code']); ?></span></td>
                                <td><?php echo e($row['project_name']); ?></td>
                                <td><strong><?php echo formatCurrency($row['total_expense'] ?? 0); ?></strong></td>
                                <td><?php echo number_format($row['expense_count']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Donors -->
    <div class="card">
        <div class="card-header">
            <h3>Top 10 Donors</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Donor Code</th>
                            <th>Donor Name</th>
                            <th>Total Donated</th>
                            <th>No. of Donations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topDonorsList)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#999;">No donors found for selected period</td>
                        </tr>
                        <?php else: ?>
                            <?php $rank = 1; foreach ($topDonorsList as $donor): ?>
                            <tr>
                                <td><strong>#<?php echo $rank++; ?></strong></td>
                                <td><span class="badge badge-secondary"><?php echo e($donor['donor_code']); ?></span></td>
                                <td><?php echo e($donor['full_name']); ?></td>
                                <td><strong><?php echo formatCurrency($donor['total_donated']); ?></strong></td>
                                <td><?php echo number_format($donor['donation_count']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .top-nav, .page-header .header-actions, .btn, button {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
}
</style>

<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
