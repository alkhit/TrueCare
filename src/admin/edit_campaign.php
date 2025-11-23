<?php
session_start();
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Fetch campaign details for editing
if (isset($_GET['id'])) {
    $campaign_id = intval($_GET['id']);
    $stmt = $db->prepare("SELECT * FROM campaigns WHERE campaign_id = ?");
    $stmt->execute([$campaign_id]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$campaign) {
        $_SESSION['error'] = "Campaign not found.";
        header("Location: manage_campaigns.php");
        exit;
    }
} else {
    $_SESSION['error'] = "No campaign selected.";
    header("Location: manage_campaigns.php");
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['category'], $_POST['target_amount'], $_POST['deadline'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $target_amount = floatval($_POST['target_amount']);
    $deadline = $_POST['deadline'];

    $stmt = $db->prepare("UPDATE campaigns SET title = ?, category = ?, target_amount = ?, deadline = ? WHERE campaign_id = ?");
    $stmt->execute([$title, $category, $target_amount, $deadline, $campaign_id]);
    $_SESSION['success'] = "Campaign updated.";
    header("Location: manage_campaigns.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Campaign</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Edit Campaign</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($campaign['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <option value="education" <?php if ($campaign['category'] === 'education') echo 'selected'; ?>>Education</option>
                <option value="food" <?php if ($campaign['category'] === 'food') echo 'selected'; ?>>Food</option>
                <option value="medical" <?php if ($campaign['category'] === 'medical') echo 'selected'; ?>>Medical</option>
                <option value="shelter" <?php if ($campaign['category'] === 'shelter') echo 'selected'; ?>>Shelter</option>
                <option value="clothing" <?php if ($campaign['category'] === 'clothing') echo 'selected'; ?>>Clothing</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="target_amount" class="form-label">Target Amount</label>
            <input type="number" class="form-control" id="target_amount" name="target_amount" value="<?php echo htmlspecialchars($campaign['target_amount']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="deadline" class="form-label">Deadline</label>
            <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo htmlspecialchars($campaign['deadline']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Campaign</button>
        <a href="manage_campaigns.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
