<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('orphanage');

$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$campaign_id) {
    echo showAlert('danger', 'Invalid campaign ID.');
    exit;
}

// Get orphanage_id for this user
$stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
$orphanage_id = $orphanage['orphanage_id'] ?? null;

if (!$orphanage_id) {
    echo showAlert('danger', 'No orphanage found for this user.');
    exit;
}

// Fetch campaign details
$stmt = $db->prepare('SELECT * FROM campaigns WHERE campaign_id = :id AND orphanage_id = :orphanage_id');
$stmt->bindParam(':id', $campaign_id);
$stmt->bindParam(':orphanage_id', $orphanage_id);
$stmt->execute();
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$campaign) {
    echo showAlert('danger', 'Campaign not found or access denied.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $target_amount = floatval($_POST['target_amount'] ?? 0);
    $status = sanitizeInput($_POST['status'] ?? 'draft');
    $stmt = $db->prepare('UPDATE campaigns SET title = :title, category = :category, target_amount = :target_amount, status = :status WHERE campaign_id = :id AND orphanage_id = :orphanage_id');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':target_amount', $target_amount);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $campaign_id);
    $stmt->bindParam(':orphanage_id', $orphanage_id);
    $stmt->execute();
    echo showAlert('success', 'Campaign updated successfully.');
    // Optionally redirect back to campaigns list
    echo '<script>setTimeout(function(){ window.location.href = "my_campaigns.php"; }, 1500);</script>';
}
?>
<div class="container mt-5">
    <h2>Edit Campaign</h2>
    <form method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo e($campaign['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo e($campaign['category']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="target_amount" class="form-label">Target Amount</label>
            <input type="number" class="form-control" id="target_amount" name="target_amount" value="<?php echo e($campaign['target_amount']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" <?php if($campaign['status']==='active') echo 'selected'; ?>>Active</option>
                <option value="completed" <?php if($campaign['status']==='completed') echo 'selected'; ?>>Completed</option>
                <option value="draft" <?php if($campaign['status']==='draft') echo 'selected'; ?>>Draft</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Campaign</button>
    </form>
</div>
