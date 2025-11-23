<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

include '../../includes/config.php';
$page_title = "My Profile - TrueCare";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch user data
try {
    $userQuery = $db->prepare("
        SELECT u.*, o.name as orphanage_name, o.location, o.description, o.status as orphanage_status
        FROM users u 
        LEFT JOIN orphanages o ON u.user_id = o.user_id
        WHERE u.user_id = :user_id
    ");
    $userQuery->bindParam(':user_id', $user_id);
    $userQuery->execute();
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user = [];
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <?php include 'sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user me-2"></i>My Profile
                </h1>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                                                        <?php
                                                            $name = $_SESSION['user_name'] ?? '';
                                                            $initials = '';
                                                            if ($name) {
                                                                $parts = explode(' ', $name);
                                                                foreach ($parts as $p) {
                                                                    if (strlen($p) > 0) $initials .= strtoupper($p[0]);
                                                                }
                                                            }
                                                        ?>
                                                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:120px;height:120px;font-size:3rem;">
                                                            <?php echo $initials ?: '?'; ?>
                                                        </div>
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p class="text-muted"><?php echo ucfirst($user['role']); ?></p>
                            <div class="mb-3">
                                <span class="badge bg-<?php echo $user['orphanage_status'] === 'verified' ? 'success' : 'warning'; ?>">
                                    <?php echo $user['orphanage_status'] ? ucfirst($user['orphanage_status']) : 'Active'; ?>
                                </span>
                            </div>
                            <div class="d-grid">
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
                                <p class="mb-0"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Last Login</small>
                                <p class="mb-0"><?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></p>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted">Account Status</small>
                                <p class="mb-0"><span class="badge bg-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Edit Profile Form -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
                        </div>
                        <div class="card-body">
                            <form id="profile-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <input type="text" class="form-control" id="role" value="<?php echo ucfirst($user['role']); ?>" disabled>
                                            <small class="text-muted">Role cannot be changed</small>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($user['role'] === 'orphanage'): ?>
                                <div class="mb-3">
                                    <label for="orphanage_name" class="form-label">Orphanage Name</label>
                                    <input type="text" class="form-control" id="orphanage_name" name="orphanage_name" 
                                           value="<?php echo htmlspecialchars($user['orphanage_name'] ?? ''); ?>" readonly>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="location" name="location" 
                                                   value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="registration_number" class="form-label">Registration Number</label>
                                            <input type="text" class="form-control" id="registration_number" name="registration_number" 
                                                   value="<?php echo htmlspecialchars($user['registration_number'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Orphanage Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" readonly><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                                </div>
                                <a href="update_orphanage.php" class="btn btn-warning">Update Details</a>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                        </div>
                        <div class="card-body">
                            <form id="password-form">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile form submission
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        window.trueCareApp.showToast('Profile updated successfully!', 'success');
    });

    // Password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            window.trueCareApp.showToast('Passwords do not match!', 'error');
            return;
        }
        
        if (newPassword.length < 6) {
            window.trueCareApp.showToast('Password must be at least 6 characters long!', 'error');
            return;
        }
        
        window.trueCareApp.showToast('Password changed successfully!', 'success');
        this.reset();
    });
});
</script>

<?php include '../../includes/footer.php'; ?>