<?php
require_once __DIR__ . '/../../../includes/functions.php';
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (!is_numeric($amount)) {
            $amount = 0;
        }
        return 'Ksh ' . number_format($amount);
    }
}
// Ensure we have the dashboard data
$data = $dashboard_data ?? [];
$total_raised = $data['total_raised'] ?? 0;
$active_campaigns = $data['active_campaigns'] ?? 0;
$total_campaigns = $data['total_campaigns'] ?? 0;
$verification_status = $data['verification_status'] ?? 'pending';
?>

<!-- Stats Cards -->

<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Check if user is logged in and is an orphanage
if (!isLoggedIn() || $_SESSION['user_type'] !== 'orphanage') {
    header('Location: ../../login.php');
    exit;
}

// Get campaign ID from URL
$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch campaign data from database (replace with your actual database logic)
$campaign = [
    'id' => $campaign_id,
    'title' => 'okluiyutyrfghghjk',
    'category' => 'medical',
    'target_amount' => 678909.00,
    'status' => 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data here
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $target_amount = $_POST['target_amount'] ?? '';
    $status = $_POST['status'] ?? '';
    
    // Update campaign in database (add your update logic here)
    // ...
    
    // Redirect after update
    header('Location: my_campaigns.php?updated=1');
    exit;
}
?>

<div class="container mt-5">
    <h2>Edit Campaign</h2>
    <form method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($campaign['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($campaign['category']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="target_amount" class="form-label">Target Amount</label>
            <input type="number" class="form-control" id="target_amount" name="target_amount" value="<?php echo number_format($campaign['target_amount'], 2, '.', ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" <?php if($campaign['status']==='active') echo 'selected'; ?>>Active</option>
                <option value="completed" <?php if($campaign['status']==='completed') echo 'selected'; ?>>Completed</option>
                <option value="pending" <?php if($campaign['status']==='pending') echo 'selected'; ?>>Pending</option>
                <option value="paused" <?php if($campaign['status']==='paused') echo 'selected'; ?>>Paused</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Campaign</button>
    </form>
</div>