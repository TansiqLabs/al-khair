<?php
/**
 * Projects Management - Simplified version
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
$pageTitle = 'Projects';

// Get projects
$projects = $db->query("
    SELECT p.*, COUNT(DISTINCT b.id) as beneficiary_count, COUNT(DISTINCT e.id) as expense_count
    FROM projects p
    LEFT JOIN beneficiaries b ON p.id = b.project_id
    LEFT JOIN project_expenses e ON p.id = e.project_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
")->fetchAll();

include ROOT_PATH . '/dashboard/header.php';
?>

<div class="dashboard-content">
    <div class="page-header">
        <div>
            <h1>Projects Management</h1>
            <p>Track projects and expenses</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="openProjectModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Project
            </button>
        </div>
    </div>

    <div class="projects-grid">
        <?php foreach ($projects as $project): ?>
        <div class="project-card">
            <div class="project-header">
                <span class="badge badge-<?php 
                    echo $project['status'] === 'active' ? 'success' : 
                        ($project['status'] === 'completed' ? 'primary' : 'secondary'); 
                ?>">
                    <?php echo ucfirst($project['status']); ?>
                </span>
                <span class="project-code"><?php echo e($project['project_code']); ?></span>
            </div>
            <h3><?php echo e($project['title']); ?></h3>
            <p class="project-description"><?php echo e(substr($project['description'] ?? '', 0, 100)); ?></p>
            
            <div class="project-progress">
                <div class="progress-info">
                    <span>Spent: <?php echo formatCurrency($project['spent_amount']); ?></span>
                    <span>Target: <?php echo formatCurrency($project['target_amount']); ?></span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?php echo $project['target_amount'] > 0 ? min(($project['spent_amount'] / $project['target_amount']) * 100, 100) : 0; ?>%"></div>
                </div>
            </div>

            <div class="project-stats">
                <div class="stat-mini">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <?php echo $project['beneficiary_count']; ?> Beneficiaries
                </div>
                <div class="stat-mini">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <?php echo $project['expense_count']; ?> Expenses
                </div>
            </div>

            <div class="project-actions">
                <button class="btn-icon" onclick="viewProject(<?php echo $project['id']; ?>)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <button class="btn-icon" onclick="editProject(<?php echo $project['id']; ?>)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($projects)): ?>
        <div class="empty-state" style="grid-column: 1/-1;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            <p>No projects yet</p>
            <button class="btn btn-primary mt-10" onclick="openProjectModal()">Create Your First Project</button>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.project-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.project-code {
    font-size: 12px;
    color: #999;
}

.project-card h3 {
    font-size: 18px;
    margin-bottom: 8px;
    color: #1f2937;
}

.project-description {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 16px;
    min-height: 40px;
}

.project-progress {
    margin-bottom: 16px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 8px;
    color: #6b7280;
}

.progress-bar-bg {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transition: width 0.3s;
}

.project-stats {
    display: flex;
    gap: 16px;
    padding: 12px 0;
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 16px;
}

.stat-mini {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
}

.project-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}
</style>

<script>
function openProjectModal() {
    alert('Project management modal - to be implemented');
}

function viewProject(id) {
    window.location.href = `project_details.php?id=${id}`;
}

function editProject(id) {
    alert('Edit project - to be implemented');
}
</script>

<?php include ROOT_PATH . '/dashboard/footer.php'; ?>
