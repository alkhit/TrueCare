<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'orphanage') {
    header("Location: ../../login.php");
    exit;
}
include '../../includes/config.php';
include '../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Create New Campaign</h1>
                <a href="my_campaigns.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold text-primary">Campaign Details</h5>
                        </div>
                        <div class="card-body">
                            <form id="create-campaign-form" action="create_campaign_process.php" method="POST" enctype="multipart/form-data">
                                <!-- Campaign Title -->
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-bold">Campaign Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required 
                                           placeholder="Enter a compelling title for your campaign">
                                    <div class="form-text">Make it descriptive and engaging to attract donors</div>
                                </div>

                                <!-- Campaign Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-bold">Campaign Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="6" required 
                                              placeholder="Describe your campaign in detail. Explain what you're raising funds for, who will benefit, and how the funds will be used."></textarea>
                                    <div class="form-text">Be specific about your goals and how donations will help</div>
                                </div>

                                <!-- Campaign Category -->
                                <div class="mb-4">
                                    <label for="category" class="form-label fw-bold">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select a category</option>
                                        <option value="education">Education</option>
                                        <option value="medical">Medical & Healthcare</option>
                                        <option value="food">Food & Nutrition</option>
                                        <option value="shelter">Shelter & Housing</option>
                                        <option value="clothing">Clothing & Essentials</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Target Amount -->
                                <div class="mb-4">
                                    <label for="target_amount" class="form-label fw-bold">Funding Goal (Ksh) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Ksh</span>
                                        <input type="number" class="form-control" id="target_amount" name="target_amount" 
                                               required min="1000" placeholder="Enter target amount">
                                    </div>
                                    <div class="form-text">Set a realistic funding goal for your campaign</div>
                                </div>

                                <!-- Campaign Deadline -->
                                <div class="mb-4">
                                    <label for="deadline" class="form-label fw-bold">Campaign Deadline *</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline" required 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                    <div class="form-text">Set an end date for your fundraising campaign</div>
                                </div>

                                <!-- Campaign Image (removed, now set by category) -->
                                <!-- Image is now automatically set based on category. -->

                                <!-- Additional Details -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Additional Information</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="beneficiary_count" class="form-label">Number of Beneficiaries</label>
                                                <input type="number" class="form-control" id="beneficiary_count" name="beneficiary_count" 
                                                       min="1" placeholder="e.g., 50">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="location" class="form-label">Location</label>
                                                <input type="text" class="form-control" id="location" name="location" 
                                                       placeholder="e.g., Nairobi, Kenya">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Budget Breakdown -->
                                <div class="mb-4">
                                    <label for="budget_breakdown" class="form-label fw-bold">Budget Breakdown</label>
                                    <textarea class="form-control" id="budget_breakdown" name="budget_breakdown" rows="4" 
                                              placeholder="Break down how the funds will be used (optional but recommended)"></textarea>
                                    <div class="form-text">Example: School fees - 40%, Books - 30%, Uniforms - 20%, Miscellaneous - 10%</div>
                                </div>

                                <!-- Terms -->
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

                <!-- Tips Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-info text-white">
                            <h5 class="m-0 font-weight-bold">Campaign Tips</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6><i class="fas fa-lightbulb me-2 text-warning"></i>Compelling Title</h6>
                                <small class="text-muted">Create a clear, emotional title that explains what you're raising funds for.</small>
                            </div>
                            <!-- Removed image tip: Images are now set by category. -->
                            <div class="mb-3">
                                <h6>🎯 <i class="fas fa-target me-2 text-primary"></i>Realistic Goals</h6>
                                <small class="text-muted">Set achievable funding targets based on actual needs.</small>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-clock me-2 text-danger"></i>Reasonable Timeline</h6>
                                <small class="text-muted">30-60 days is ideal for most campaigns.</small>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-list me-2 text-success"></i>Clear Breakdown</h6>
                                <small class="text-muted">Show donors exactly how their money will be used.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Preview -->
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold text-primary">Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-eye fa-2x text-muted mb-3"></i>
                                <p class="text-muted">Your campaign preview will appear here as you fill out the form.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview removed: image is set by category.

    // Form submission
    document.getElementById('create-campaign-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const category = document.getElementById('category').value;
        const targetAmount = document.getElementById('target_amount').value;
        const deadline = document.getElementById('deadline').value;
        // Image is set by category, no upload needed.
        const terms = document.getElementById('campaign_terms').checked;
        
        // Validation checks
        if (title.length < 10) {
            alert('Please enter a more descriptive title (at least 10 characters)');
            return;
        }
        
        if (description.length < 50) {
            alert('Please provide a more detailed description (at least 50 characters)');
            return;
        }
        
        if (!category) {
            alert('Please select a category for your campaign');
            return;
        }
        
        if (targetAmount < 1000) {
            alert('Minimum funding goal is Ksh 1,000');
            return;
        }
        
        if (!deadline) {
            alert('Please select a campaign deadline');
            return;
        }
        
        // No campaign image upload required.
        
        if (!terms) {
            alert('Please agree to the terms and conditions');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Campaign...';
        
        // Submit the form
        this.submit();
    });

    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('deadline').min = tomorrow.toISOString().split('T')[0];
    
    // Set maximum date to 1 year from now
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);
    document.getElementById('deadline').max = nextYear.toISOString().split('T')[0];
});
</script>

<style>
.form-label.fw-bold {
    color: #2c3e50;
}

.sidebar {
    background: linear-gradient(135deg, #2c5aa0, #1a365f) !important;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.btn-success {
    background: linear-gradient(135deg, #1e8449, #186a3b);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #186a3b, #145a32);
    transform: translateY(-1px);
}
</style>

<?php include '../../includes/footer.php'; ?>