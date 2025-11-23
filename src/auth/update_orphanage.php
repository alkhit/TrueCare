<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('orphanage');

$user_id = $_SESSION['user_id'];

// Fetch orphanage details
$stmt = $db->prepare('SELECT * FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    // Save update request to pending_orphanage_changes table
    $stmt = $db->prepare('INSERT INTO pending_orphanage_changes (user_id, name, location, description, status) VALUES (:user_id, :name, :location, :description, "pending")');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    echo showAlert('success', 'Update request submitted. Awaiting admin approval.');
    echo '<script>setTimeout(function(){ window.location.href = "profile.php"; }, 1500);</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Orphanage Details</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Google Maps autocomplete removed for manual address entry -->
</head>
<body>
<?php include '../../includes/header.php'; ?>

<?php
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'orphanage') {
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="userNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="../campaigns/analytics_campaign.php"><i class="fas fa-chart-line me-1"></i>Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="../campaigns/create_campaign.php"><i class="fas fa-plus-circle me-1"></i>Create Campaign</a></li>
                    <li class="nav-item"><a class="nav-link" href="../campaigns/my_campaigns.php"><i class="fas fa-list me-1"></i>My Campaigns</a></li>
                </ul>
            </div>
        </div>
    </nav>';
}
?>
<div class="container mt-5">
    <h2>Update Orphanage Details</h2>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Orphanage Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo e($orphanage['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="<?php echo e($orphanage['location']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo e($orphanage['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Update</button>
    </form>
</div>
<script>
function initAutocomplete() {
    var input = document.getElementById('location');
    var autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        componentRestrictions: { country: 'ke' }
    });
}
window.onload = initAutocomplete;
