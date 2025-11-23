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
$query = "SELECT orphanage_id, name, location, description, status, created_at FROM orphanages WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container py-5">
    <h2 class="mb-4">Verify Orphanages</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $o): ?>
                <tr>
                    <td><?php echo $o['orphanage_id']; ?></td>
                    <td><?php echo htmlspecialchars($o['name']); ?></td>
                    <td><?php echo htmlspecialchars($o['location']); ?></td>
                    <td><?php echo htmlspecialchars($o['description']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
                    <td>
                        <form method="POST" action="verify_orphanage_process.php" style="display:inline-block">
                            <input type="hidden" name="orphanage_id" value="<?php echo $o['orphanage_id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this orphanage?');">Approve</button>
                        </form>
                        <form method="POST" action="verify_orphanage_process.php" style="display:inline-block">
                            <input type="hidden" name="orphanage_id" value="<?php echo $o['orphanage_id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this orphanage?');">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>