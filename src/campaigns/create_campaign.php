<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('orphanage');
$page_title = "Create New Campaign - TrueCare";
include '../../includes/header.php';

// Show top navbar for orphanage users
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'orphanage') {
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="../auth/dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="userNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="analytics_campaign.php"><i class="fas fa-chart-line me-1"></i>Analytics</a></li>
                    <li class="nav-item"><a class="nav-link active" href="create_campaign.php"><i class="fas fa-plus-circle me-1"></i>Create Campaign</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_campaigns.php"><i class="fas fa-list me-1"></i>My Campaigns</a></li>
                </ul>
            </div>
        </div>
    </nav>';
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Campaign</h4>
                </div>
                <div class="card-body">
                    <form action="create_campaign_process.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Campaign Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required minlength="10" placeholder="Enter campaign title">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required minlength="50" placeholder="Describe your campaign"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select category</option>
                                <option value="education">Education</option>
                                <option value="medical">Medical & Healthcare</option>
                                <option value="food">Food & Nutrition</option>
                                <option value="shelter">Shelter & Housing</option>
                                <option value="clothing">Clothing & Essentials</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="target_amount" class="form-label fw-bold">Funding Goal (Ksh) *</label>
                            <input type="number" class="form-control" id="target_amount" name="target_amount" required min="1000" placeholder="Enter target amount">
                        </div>
                        <div class="mb-3">
                            <label for="deadline" class="form-label fw-bold">Deadline *</label>
                            <input type="date" class="form-control" id="deadline" name="deadline" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="beneficiary_count" class="form-label">Number of Beneficiaries</label>
                            <input type="number" class="form-control" id="beneficiary_count" name="beneficiary_count" min="1" placeholder="e.g., 50">
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Nairobi, Kenya">
                        </div>
                        <div class="mb-3">
                            <label for="budget_breakdown" class="form-label fw-bold">Budget Breakdown</label>
                            <textarea class="form-control" id="budget_breakdown" name="budget_breakdown" rows="4" placeholder="Break down how the funds will be used (optional but recommended)"></textarea>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="campaign_terms" name="campaign_terms" required>
                            <label class="form-check-label" for="campaign_terms">
                                I confirm that all information provided is accurate and that funds raised will be used solely for the stated purpose. I agree to provide updates on the campaign progress.
                            </label>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">Reset</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i>Create Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>