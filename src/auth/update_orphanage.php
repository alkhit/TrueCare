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
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>
</head>
<body>
<?php include '../../includes/header.php'; ?>
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
</script>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
