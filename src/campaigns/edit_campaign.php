<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (!isset($db)) {
    $db = get_db();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/header.php';

$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$campaign_id) {
    echo showAlert('danger', 'Invalid campaign ID.');
    exit;
}
$stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
$orphanage_id = $orphanage['orphanage_id'] ?? null;
if (!$orphanage_id) {
    echo showAlert('danger', 'No orphanage found for this user.');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $target_amount = (float)($_POST['target_amount'] ?? 0);
    $status = sanitizeInput($_POST['status'] ?? 'active');
    if ($title === '' || $category === '' || $target_amount < 1000) {
        echo showAlert('danger', 'Please fill all required fields correctly.');
        exit;
    }
    $stmt = $db->prepare('UPDATE campaigns SET title = :title, category = :category, target_amount = :target_amount, status = :status WHERE campaign_id = :id AND orphanage_id = :orphanage_id');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':target_amount', $target_amount);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $campaign_id);
    $stmt->bindParam(':orphanage_id', $orphanage_id);
    if ($stmt->execute()) {
        echo '<script>var modal = new bootstrap.Modal(document.getElementById("editSuccessModal")); modal.show();</script>';
    } else {
        echo showAlert('danger', 'Error updating campaign.');
    }
    exit;
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar removed for orphanage pages -->
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Campaign</h1>
            </div>
            <!-- Success Modal -->
            <div class="modal fade" id="editSuccessModal" tabindex="-1" aria-labelledby="editSuccessLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editSuccessLabel">Campaign Updated</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Your campaign has been updated successfully!</p>
                  </div>
                  <div class="modal-footer">
                    <a href="my_campaigns.php" class="btn btn-success">Back to My Campaigns</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <?php
            $campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (!$campaign_id) {
                echo showAlert('danger', 'Invalid campaign ID.');
                exit;
            }
            $stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
            $orphanage_id = $orphanage['orphanage_id'] ?? null;
            if (!$orphanage_id) {
                echo showAlert('danger', 'No orphanage found for this user.');
                exit;
            }
            $stmt = $db->prepare('SELECT * FROM campaigns WHERE campaign_id = :id AND orphanage_id = :orphanage_id');
            $stmt->bindParam(':id', $campaign_id);
            $stmt->bindParam(':orphanage_id', $orphanage_id);
            $stmt->execute();
            $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$campaign) {
                echo showAlert('danger', 'Campaign not found or access denied.');
                exit;
            }
            ?>
            <form id="editCampaignForm" method="post" class="mt-4">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($campaign['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="education" <?php if($campaign['category']==='education') echo 'selected'; ?>>Education</option>
                        <option value="medical" <?php if($campaign['category']==='medical') echo 'selected'; ?>>Medical</option>
                        <option value="food" <?php if($campaign['category']==='food') echo 'selected'; ?>>Food</option>
                        <option value="shelter" <?php if($campaign['category']==='shelter') echo 'selected'; ?>>Shelter</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="target_amount" class="form-label">Target Amount (Ksh)</label>
                    <input type="number" class="form-control" id="target_amount" name="target_amount" value="<?php echo htmlspecialchars($campaign['target_amount']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?php if($campaign['status']==='active') echo 'selected'; ?>>Active</option>
                        <option value="completed" <?php if($campaign['status']==='completed') echo 'selected'; ?>>Completed</option>
                        <option value="draft" <?php if($campaign['status']==='draft') echo 'selected'; ?>>Draft</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update Campaign</button>
            </form>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('editCampaignForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var formData = new FormData(form);
                        fetch(window.location.pathname + window.location.search, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Show modal on success
                            var modal = new bootstrap.Modal(document.getElementById('editSuccessModal'));
                            modal.show();
                        })
                        .catch(() => {
                            alert('Error updating campaign.');
                        });
                    });
                }
            });
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
