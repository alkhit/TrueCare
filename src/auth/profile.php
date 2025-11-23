<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';

$db = get_db();
$user_id = $_SESSION['user_id'];
$password_message = '';

// ---------------------------
// HANDLE PASSWORD CHANGE
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password     = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (strlen($new_password) < 6) {
        $password_message = '<div class="alert alert-danger">Password must be at least 6 characters long.</div>';
    } elseif ($new_password !== $confirm_password) {
        $password_message = '<div class="alert alert-danger">Passwords do not match.</div>';
    } else {
        // Fetch current hash
        $stmt = $db->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current_password, $row['password'])) {
            $password_message = '<div class="alert alert-danger">Incorrect current password.</div>';
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update->execute([$new_hash, $user_id]);
            $password_message = '<div class="alert alert-success">Password updated successfully.</div>';
        }
    }
}

// ---------------------------
// FETCH USER DATA
// ---------------------------
try {
    $query = $db->prepare("
        SELECT u.*, 
               o.name AS orphanage_name,
               o.location AS orphanage_location,
               o.description AS orphanage_description,
               o.registration_number,
               o.status AS orphanage_status
        FROM users u
        LEFT JOIN orphanages o ON u.user_id = o.user_id
        WHERE u.user_id = :id
    ");

    $query->execute([':id' => $user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {
    error_log("Profile error: " . $e->getMessage());
    $user = [];
}

// Sidebar
$sidebar = __DIR__ . '/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php if (file_exists($sidebar)) { include $sidebar; } ?>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

            <div class="pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-user me-2"></i>My Profile</h1>
            </div>

            <div class="row">

                <!-- LEFT COLUMN -->
                <div class="col-lg-4">
                    <!-- Profile Photo -->
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">

                            <?php
                                $name = $user['name'] ?? '';
                                $initials = '';
                                foreach (explode(' ', $name) as $p) {
                                    if ($p !== '') $initials .= strtoupper($p[0]);
                                }
                            ?>

                            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                 style="width:120px; height:120px; font-size:3rem;">
                                <?php echo $initials ?: '?'; ?>
                            </div>

                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p class="text-muted"><?php echo ucfirst($user['role'] ?? ''); ?></p>

                            <?php if (($user['role'] ?? '') === 'orphanage'): ?>
                                <span class="badge bg-<?php echo ($user['orphanage_status'] === 'verified' ? 'success' : 'warning'); ?>">
                                    <?php echo ucfirst($user['orphanage_status'] ?? 'pending'); ?>
                                </span>
                            <?php endif; ?>

                            <div class="d-grid mt-3">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-camera me-2"></i>Change Photo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Member Since</small>
                                <p class="mb-0">
                                    <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Last Login</small>
                                <p class="mb-0">
                                    <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                </p>
                            </div>

                            <div>
                                <small class="text-muted">Account Status</small>
                                <p class="mb-0"><span class="badge bg-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-8">

                    <!-- Edit Profile -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
                        </div>
                        <div class="card-body">
                            <form id="profile-form">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" 
                                               name="name"
                                               value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" 
                                               name="email"
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" 
                                               name="phone"
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" disabled>
                                    </div>
                                </div>

                                <?php if (($user['role'] ?? '') === 'orphanage'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Orphanage Name</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['orphanage_name']); ?>" 
                                               readonly>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Location</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo htmlspecialchars($user['orphanage_location']); ?>" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Registration Number</label>
                                            <input type="text" class="form-control"
                                                   value="<?php echo htmlspecialchars($user['registration_number']); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Orphanage Description</label>
                                        <textarea class="form-control" readonly><?php echo htmlspecialchars($user['orphanage_description']); ?></textarea>
                                    </div>

                                    <a href="update_orphanage.php" class="btn btn-warning">Update Details</a>
                                <?php endif; ?>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card shadow mt-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                        </div>
                        <div class="card-body">

                            <?php echo $password_message; ?>

                            <form method="POST">
                                <input type="hidden" name="change_password" value="1">

                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>

                                <button class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("profile-form").addEventListener("submit", function(e){
        e.preventDefault();
        window.trueCareApp.showToast("Profile updated successfully!", "success");
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
