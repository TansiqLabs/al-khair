<?php
/**
 * Donors Management
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$db = getDBConnection();
$pageTitle = 'Donors';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Build query
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE (full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR donor_code LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM donors $whereClause";
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalDonors = $stmt->fetch()['total'];

// Get donors
$query = "SELECT * FROM donors $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$donors = $stmt->fetchAll();

$pagination = getPagination($totalDonors, $page, $perPage);

include ROOT_PATH . '/dashboard/header.php';
?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>Donors Management</h1>
            <p>Manage and track all your donors</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Donor
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-20">
        <div class="card-body">
            <form method="GET" class="search-form">
                <div class="search-input-group">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" name="search" placeholder="Search donors by name, email, phone..." value="<?php echo e($search); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="donors.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Donors Table -->
    <div class="card">
        <div class="card-header">
            <h3>All Donors (<?php echo number_format($totalDonors); ?>)</h3>
        </div>
        <div class="card-body p-0">
            <?php if (empty($donors)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <p>No donors found</p>
                    <?php if (!empty($search)): ?>
                        <a href="donors.php" class="btn btn-primary mt-10">View All Donors</a>
                    <?php else: ?>
                        <button class="btn btn-primary mt-10" onclick="openAddModal()">Add Your First Donor</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Donor Code</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Total Donated</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donors as $donor): ?>
                            <tr>
                                <td><span class="badge badge-secondary"><?php echo e($donor['donor_code']); ?></span></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar"><?php echo strtoupper(substr($donor['full_name'], 0, 1)); ?></div>
                                        <span><?php echo e($donor['full_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <?php if ($donor['phone']): ?>
                                            <div><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> <?php echo e($donor['phone']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($donor['email']): ?>
                                            <div><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> <?php echo e($donor['email']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo e($donor['city'] ? $donor['city'] : '-'); ?></td>
                                <td><strong><?php echo formatCurrency($donor['total_donated']); ?></strong></td>
                                <td>
                                    <?php if ($donor['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="viewDonor(<?php echo $donor['id']; ?>)" title="View">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                        <button class="btn-icon" onclick="editDonor(<?php echo $donor['id']; ?>)" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" onclick="deleteDonor(<?php echo $donor['id']; ?>, '<?php echo e($donor['full_name']); ?>')" title="Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['has_previous']): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="pagination-btn">Previous</a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                    </span>

                    <?php if ($pagination['has_next']): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="pagination-btn">Next</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Donor Modal -->
<div id="donorModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Donor</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="donorForm">
            <div class="modal-body">
                <input type="hidden" id="donor_id" name="donor_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp Number</label>
                        <input type="text" id="whatsapp" name="whatsapp">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city">
                    </div>
                    <div class="form-group">
                        <label for="district">District</label>
                        <input type="text" id="district" name="district">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_active" name="is_active" checked>
                        <span>Active Donor</span>
                    </label>
                </div>

                <div id="formMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Donor</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/assets/css/forms.css">
<script src="<?php echo getBaseUrl(); ?>/assets/js/donors.js"></script>

<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
