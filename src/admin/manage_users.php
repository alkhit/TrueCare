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
$query = "SELECT user_id, name, email, role, is_active, phone, created_at FROM users";
if ($search) {
        $query .= " WHERE name LIKE :search OR email LIKE :search";
        $stmt = $db->prepare($query);
        $stmt->execute([':search' => "%$search%"]);
} else {
        $stmt = $db->prepare($query);
        $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container py-5">
    <h2 class="mb-4">Manage Users</h2>
    <form class="mb-3" method="get">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if ($user['user_id'] == $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-warning" disabled>Edit</button>
                            <button class="btn btn-sm btn-danger" disabled>Deactivate</button>
                        <?php else: ?>
                            <form method="POST" action="edit_user_role.php" style="display:inline-block">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <select name="new_role" class="form-select form-select-sm d-inline w-auto" style="min-width:100px;">
                                    <option value="donor" <?php echo $user['role'] === 'donor' ? 'selected' : ''; ?>>Donor</option>
                                    <option value="orphanage" <?php echo $user['role'] === 'orphanage' ? 'selected' : ''; ?>>Orphanage</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-warning ms-1">Change Type</button>
                            </form>
                            <form method="POST" action="manage_user_process.php" style="display:inline-block">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deactivate this user?');">Deactivate</button>
                            </form>
                                <form method="POST" action="manage_user_process.php" style="display:inline-block">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user permanently? This cannot be undone.');">Delete</button>
                                </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
