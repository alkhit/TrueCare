<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('orphanage');

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $status = 'pending';

    // Check if orphanage already exists for this user
    $stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo showAlert('warning', 'You have already registered an orphanage.');
    } else {
        $stmt = $db->prepare('INSERT INTO orphanages (user_id, name, location, description, status) VALUES (:user_id, :name, :location, :description, :status)');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        echo showAlert('success', 'Orphanage registered successfully. Awaiting verification.');
        echo '<script>setTimeout(function(){ window.location.href = "dashboard.php"; }, 1500);</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Orphanage</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<div class="container mt-5">
    <h2>Register Your Orphanage</h2>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Orphanage Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Register Orphanage</button>
    </form>
</div>
<script>
function initAutocomplete() {
    var input = document.getElementById('location');
    if (!input) return;
    var autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        componentRestrictions: { country: 'KE' }
    });
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            input.value = '';
            alert('Please select a valid location from the dropdown.');
        }
    });
}
document.addEventListener('DOMContentLoaded', initAutocomplete);
</script>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
