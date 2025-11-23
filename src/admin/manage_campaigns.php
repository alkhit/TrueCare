<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: ../../login.php");
        exit;
}
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
include '../../includes/header.php';

$user_role = $_SESSION['user_role'] ?? '';
if ($user_role === 'admin') {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="admin.php"><i class="fas fa-cog me-2"></i>Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="verify_orphanages.php"><i class="fas fa-clipboard-check me-1"></i>Verify Orphanages</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog me-1"></i>Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_campaigns.php"><i class="fas fa-hand-holding-heart me-1"></i>Manage Campaigns</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-1"></i>Reports</a></li>
                    </ul>
                </div>
            </div>
        </nav>';
}
$search = trim($_GET['search'] ?? '');
$query = "SELECT c.campaign_id, c.title, c.category, c.target_amount, c.current_amount, c.status, c.deadline, o.name AS orphanage_name FROM campaigns c LEFT JOIN orphanages o ON c.orphanage_id = o.orphanage_id";
if ($search) {
        $query .= " WHERE c.title LIKE :search OR o.name LIKE :search";
        $stmt = $db->prepare($query);
        $stmt->execute([':search' => "%$search%"]);
} else {
        $stmt = $db->prepare($query);
        $stmt->execute();
}
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container py-5">
    <h2 class="mb-4">Manage Campaigns</h2>
    <form class="mb-3" method="get">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by title or orphanage" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Orphanage</th>
                    <th>Category</th>
                    <th>Goal</th>
                    <th>Raised</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $c): ?>
                <tr>
                    <td><?php echo $c['campaign_id']; ?></td>
                    <td><?php echo htmlspecialchars($c['title']); ?></td>
                    <td><?php echo htmlspecialchars($c['orphanage_name']); ?></td>
                    <td><?php echo ucfirst($c['category']); ?></td>
                    <td><?php echo formatCurrency($c['target_amount']); ?></td>
                    <td><?php echo formatCurrency($c['current_amount']); ?></td>
                    <td><span class="badge bg-<?php echo $c['status']==='active'?'success':($c['status']==='pending'?'warning':'secondary'); ?>"><?php echo ucfirst($c['status']); ?></span></td>
                    <td><?php echo htmlspecialchars($c['deadline']); ?></td>
                    <td>
                        <a href="edit_campaign.php?id=<?php echo $c['campaign_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="manage_campaign_process.php" style="display:inline-block">
                            <input type="hidden" name="campaign_id" value="<?php echo $c['campaign_id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this campaign?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
